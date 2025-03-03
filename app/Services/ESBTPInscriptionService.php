<?php

namespace App\Services;

use App\Models\ESBTPEtudiant;
use App\Models\ESBTPParent;
use App\Models\ESBTPInscription;
use App\Models\ESBTPPaiement;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class ESBTPInscriptionService
{
    /**
     * Créer une nouvelle inscription d'un étudiant
     *
     * @param array $etudiantData Les données de l'étudiant
     * @param array $inscriptionData Les données de l'inscription
     * @param array $parentsData Les données des parents (optionnel)
     * @param array $paiementData Les données du paiement initial (optionnel)
     * @param int $userId ID de l'utilisateur qui crée l'inscription
     * @return ESBTPInscription Instance de l'inscription créée
     */
    public function createInscription(array $etudiantData, array $inscriptionData, array $parentsData = [], ?array $paiementData = null, int $userId)
    {
        try {
            DB::beginTransaction();
            
            // Ajout de logs pour déboguer
            Log::info('Début de la création d\'une inscription dans le service', [
                'etudiantData' => $etudiantData,
                'inscriptionData' => $inscriptionData,
                'parentsData' => $parentsData,
                'userId' => $userId
            ]);
            
            // 1. Création de l'étudiant
            $etudiant = $this->createEtudiant($etudiantData, $userId);
            
            // Ajout de logs pour déboguer
            Log::info('Étudiant créé', ['etudiant' => $etudiant]);
            
            // 2. Création de l'inscription
            $inscriptionData['etudiant_id'] = $etudiant->id;
            $inscriptionData['created_by'] = $userId;
            $inscriptionData['updated_by'] = $userId;
            
            // Générer un numéro de reçu pour l'inscription
            if (empty($inscriptionData['numero_recu'])) {
                $anneeCode = substr(date('Y'), 2, 2);
                $annee = ESBTPAnneeUniversitaire::find($inscriptionData['annee_universitaire_id']);
                if ($annee) {
                    $anneeCode = substr($annee->code, 2, 2);
                }
                $numeroRecu = 'INSC' . $anneeCode . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $inscriptionData['numero_recu'] = $numeroRecu;
            }
            
            $inscription = ESBTPInscription::create($inscriptionData);
            
            // 3. Création/Association des parents
            if (!empty($parentsData)) {
                $this->attachParentsToEtudiant($etudiant, $parentsData, $userId);
            }
            
            // 4. Enregistrement du paiement initial (si fourni)
            if ($paiementData && !empty($paiementData)) {
                $paiementData['inscription_id'] = $inscription->id;
                $paiementData['etudiant_id'] = $etudiant->id;
                $paiementData['created_by'] = $userId;
                $paiementData['updated_by'] = $userId;
                
                // Générer un numéro de reçu
                if (empty($paiementData['numero_recu'])) {
                    $paiementData['numero_recu'] = ESBTPPaiement::genererNumeroRecu();
                }
                
                ESBTPPaiement::create($paiementData);
            }
            
            DB::commit();
            
            // Ajout de logs pour déboguer
            Log::info('Inscription créée avec succès', [
                'etudiant' => $etudiant,
                'inscription' => $inscription ?? null
            ]);
            
            return $inscription;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Ajout de logs pour déboguer
            Log::error('Erreur lors de la création de l\'inscription', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Créer un nouvel étudiant et son compte utilisateur
     *
     * @param array $etudiantData Les données de l'étudiant
     * @param int $userId ID de l'utilisateur qui crée l'étudiant
     * @return ESBTPEtudiant Instance de l'étudiant créé
     */
    private function createEtudiant(array $etudiantData, int $userId)
    {
        $etudiantData['created_by'] = $userId;
        $etudiantData['updated_by'] = $userId;
        
        // Création du compte utilisateur
        // Générer un nom d'utilisateur basé sur le prénom et le nom
        $username = ESBTPEtudiant::genererUsername(
            $etudiantData['prenoms'], 
            $etudiantData['nom']
        );
        
        // Générer un mot de passe aléatoire
        $password = ESBTPEtudiant::genererMotDePasse();
        
        // Créer l'email si non fourni
        $email = $etudiantData['email'] ?? ($username . '@esbtp.edu');
        $emailExists = User::where('email', $email)->exists();
        
        if ($emailExists) {
            // Ajouter un suffixe aléatoire à l'email
            $email = $username . '.' . rand(100, 999) . '@esbtp.edu';
        }
        
        $user = User::create([
            'name' => $etudiantData['prenoms'] . ' ' . $etudiantData['nom'],
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'avatar' => null,
            'is_active' => true
        ]);
        
        // Assigner le rôle étudiant
        $role = Role::where('name', 'etudiant')->first();
        if ($role) {
            $user->assignRole($role);
        }
        
        $etudiantData['user_id'] = $user->id;
        
        // Stocker le mot de passe généré dans la session pour l'afficher plus tard
        session(['generated_password' => $password]);
        
        // Créer l'étudiant
        $etudiant = ESBTPEtudiant::create($etudiantData);
        
        return $etudiant;
    }
    
    /**
     * Attache les parents à un étudiant (existants ou nouveaux)
     *
     * @param ESBTPEtudiant $etudiant L'étudiant auquel attacher les parents
     * @param array $parentsData Données des parents
     * @param int $userId ID de l'utilisateur qui fait l'action
     * @return void
     */
    private function attachParentsToEtudiant(ESBTPEtudiant $etudiant, array $parentsData, int $userId)
    {
        foreach ($parentsData as $index => $parentData) {
            $isTuteur = $index === 0; // Le premier parent est le tuteur par défaut
            
            // Parent existant sélectionné
            if (isset($parentData['parent_id']) && !empty($parentData['parent_id'])) {
                $parent = ESBTPParent::findOrFail($parentData['parent_id']);
                
                // Associer le parent existant à l'étudiant
                $etudiant->parents()->syncWithoutDetaching([
                    $parent->id => [
                        'relation' => $parentData['relation'] ?? 'Tuteur',
                        'is_tuteur' => $isTuteur
                    ]
                ]);
            } 
            // Nouveau parent
            elseif (isset($parentData['nom']) && !empty($parentData['nom'])) {
                // Créer le nouveau parent
                $parent = ESBTPParent::create([
                    'nom' => $parentData['nom'],
                    'prenoms' => $parentData['prenoms'],
                    'telephone' => $parentData['telephone'],
                    'email' => $parentData['email'] ?? null,
                    'profession' => $parentData['profession'] ?? null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                
                // Associer le nouveau parent à l'étudiant
                $etudiant->parents()->attach($parent->id, [
                    'relation' => $parentData['relation'] ?? 'Tuteur',
                    'is_tuteur' => $isTuteur
                ]);
            }
        }
    }
    
    /**
     * Valider une inscription
     *
     * @param int $inscriptionId ID de l'inscription à valider
     * @param int $userId ID de l'utilisateur qui valide l'inscription
     * @return array Résultat de l'opération
     */
    public function validerInscription(int $inscriptionId, int $userId)
    {
        try {
            DB::beginTransaction();
            
            $inscription = ESBTPInscription::findOrFail($inscriptionId);
            
            // Ne pas valider une inscription déjà validée
            if ($inscription->status === 'active') {
                return [
                    'success' => false,
                    'message' => 'Cette inscription est déjà validée'
                ];
            }
            
            $inscription->status = 'active';
            $inscription->date_validation = now();
            $inscription->validated_by = $userId;
            $inscription->updated_by = $userId;
            $inscription->save();
            
            // Mettre à jour le statut de l'étudiant
            $etudiant = $inscription->etudiant;
            $etudiant->statut = 'actif';
            $etudiant->updated_by = $userId;
            $etudiant->save();
            
            // Activer le compte utilisateur de l'étudiant
            if ($etudiant->user_id) {
                $user = User::find($etudiant->user_id);
                if ($user) {
                    $user->is_active = true;
                    $user->save();
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'inscription' => $inscription,
                'message' => 'Inscription validée avec succès'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la validation de l\'inscription: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de la validation de l\'inscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Annuler une inscription
     *
     * @param int $inscriptionId ID de l'inscription à annuler
     * @param string $motif Motif de l'annulation
     * @param int $userId ID de l'utilisateur qui annule l'inscription
     * @return array Résultat de l'opération
     */
    public function annulerInscription(int $inscriptionId, string $motif, int $userId)
    {
        try {
            DB::beginTransaction();
            
            $inscription = ESBTPInscription::findOrFail($inscriptionId);
            
            // Ne pas annuler une inscription déjà annulée
            if ($inscription->status === 'annulée') {
                return [
                    'success' => false,
                    'message' => 'Cette inscription est déjà annulée'
                ];
            }
            
            $inscription->status = 'annulée';
            $inscription->observations = ($inscription->observations ? $inscription->observations . "\n" : '') . 
                                        "Annulée le " . now()->format('d/m/Y') . ". Motif: " . $motif;
            $inscription->updated_by = $userId;
            $inscription->save();
            
            // Désactiver l'étudiant si c'est sa seule inscription active
            $etudiant = $inscription->etudiant;
            $hasActiveInscriptions = $etudiant->inscriptions()
                                            ->where('id', '!=', $inscriptionId)
                                            ->where('status', 'active')
                                            ->exists();
            
            if (!$hasActiveInscriptions) {
                $etudiant->statut = 'inactif';
                $etudiant->updated_by = $userId;
                $etudiant->save();
                
                // Désactiver le compte utilisateur de l'étudiant
                if ($etudiant->user_id) {
                    $user = User::find($etudiant->user_id);
                    if ($user) {
                        $user->is_active = false;
                        $user->save();
                    }
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'inscription' => $inscription,
                'message' => 'Inscription annulée avec succès'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'annulation de l\'inscription: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'annulation de l\'inscription: ' . $e->getMessage()
            ];
        }
    }
} 