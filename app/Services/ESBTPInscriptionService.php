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
     * @param array $parentData Les données du parent/tuteur (optionnel)
     * @param array $paiementData Les données du paiement initial (optionnel)
     * @param int $userId ID de l'utilisateur qui crée l'inscription
     * @return array Résultat de l'opération
     */
    public function createInscription(array $etudiantData, array $inscriptionData, ?array $parentData = null, ?array $paiementData = null, int $userId)
    {
        try {
            DB::beginTransaction();
            
            // 1. Création de l'étudiant
            $etudiant = $this->createOrUpdateEtudiant($etudiantData, $userId);
            
            // 2. Création de l'inscription
            $inscriptionData['etudiant_id'] = $etudiant->id;
            $inscriptionData['created_by'] = $userId;
            $inscriptionData['updated_by'] = $userId;
            
            // Générer matricule si nécessaire
            if (empty($etudiantData['matricule'])) {
                $filiere = ESBTPFiliere::find($inscriptionData['filiere_id']);
                $niveau = ESBTPNiveauEtude::find($inscriptionData['niveau_id']);
                $annee = ESBTPAnneeUniversitaire::find($inscriptionData['annee_universitaire_id']);
                
                if ($filiere && $niveau && $annee) {
                    $etudiant->matricule = ESBTPEtudiant::genererMatricule(
                        $filiere->code, 
                        $niveau->code, 
                        substr($annee->code, 2, 2)
                    );
                    $etudiant->save();
                }
            }
            
            // Générer un numéro de reçu pour le paiement initial
            if (!empty($inscriptionData['numero_recu'])) {
                $numeroRecu = $inscriptionData['numero_recu'];
            } else {
                $anneeCode = substr(date('Y'), 2, 2);
                $annee = ESBTPAnneeUniversitaire::find($inscriptionData['annee_universitaire_id']);
                if ($annee) {
                    $anneeCode = substr($annee->code, 2, 2);
                }
                $numeroRecu = 'INSC' . $anneeCode . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
            
            $inscriptionData['numero_recu'] = $numeroRecu;
            
            $inscription = ESBTPInscription::create($inscriptionData);
            
            // 3. Création du parent/tuteur (si fourni)
            if ($parentData && !empty($parentData)) {
                $parent = $this->createOrUpdateParent($parentData, $userId);
                
                // Associer le parent à l'étudiant
                $etudiant->parents()->attach($parent->id, [
                    'relation' => $parentData['relation'] ?? 'tuteur',
                    'is_tuteur' => $parentData['is_tuteur'] ?? true,
                ]);
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
            
            return [
                'success' => true,
                'etudiant' => $etudiant,
                'inscription' => $inscription,
                'message' => 'Inscription créée avec succès'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'inscription: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de l\'inscription: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer ou mettre à jour un étudiant et son compte utilisateur
     *
     * @param array $etudiantData Les données de l'étudiant
     * @param int $userId ID de l'utilisateur qui crée/modifie l'étudiant
     * @return ESBTPEtudiant Instance de l'étudiant créé/mis à jour
     */
    public function createOrUpdateEtudiant(array $etudiantData, int $userId)
    {
        // Si l'étudiant existe déjà (mise à jour)
        if (!empty($etudiantData['id'])) {
            $etudiant = ESBTPEtudiant::findOrFail($etudiantData['id']);
            $etudiant->fill($etudiantData);
            $etudiant->updated_by = $userId;
            $etudiant->save();
            
            // Mettre à jour l'utilisateur associé si nécessaire
            if ($etudiant->user_id) {
                $user = User::find($etudiant->user_id);
                if ($user) {
                    $user->name = $etudiantData['prenoms'] . ' ' . $etudiantData['nom'];
                    $user->email = $etudiantData['email_personnel'] ?? $user->email;
                    $user->save();
                }
            }
            
            return $etudiant;
        }
        
        // Création d'un nouvel étudiant
        $etudiantData['created_by'] = $userId;
        $etudiantData['updated_by'] = $userId;
        
        // Création du compte utilisateur
        $createUser = $etudiantData['creer_compte_utilisateur'] ?? true;
        
        if ($createUser) {
            // Générer un nom d'utilisateur basé sur le prénom et le nom
            $username = ESBTPEtudiant::genererUsername(
                $etudiantData['prenoms'], 
                $etudiantData['nom']
            );
            
            // Générer un mot de passe aléatoire
            $password = ESBTPEtudiant::genererMotDePasse();
            
            $user = User::create([
                'name' => $etudiantData['prenoms'] . ' ' . $etudiantData['nom'],
                'email' => $etudiantData['email_personnel'] ?? $username . '@esbtp.edu',
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
            
            // Stocker le mot de passe en clair pour le premier login
            $etudiantData['password_generated'] = $password;
        }
        
        // Traitement de la photo si présente
        if (isset($etudiantData['photo_file']) && $etudiantData['photo_file']) {
            $photo = $etudiantData['photo_file'];
            $photoPath = $photo->store('public/etudiants/photos');
            $etudiantData['photo'] = Storage::url($photoPath);
            unset($etudiantData['photo_file']);
        }
        
        $etudiant = ESBTPEtudiant::create($etudiantData);
        
        return $etudiant;
    }
    
    /**
     * Créer ou mettre à jour un parent et son compte utilisateur
     *
     * @param array $parentData Les données du parent
     * @param int $userId ID de l'utilisateur qui crée/modifie le parent
     * @return ESBTPParent Instance du parent créé/mis à jour
     */
    public function createOrUpdateParent(array $parentData, int $userId)
    {
        // Si le parent existe déjà (mise à jour)
        if (!empty($parentData['id'])) {
            $parent = ESBTPParent::findOrFail($parentData['id']);
            $parent->fill($parentData);
            $parent->updated_by = $userId;
            $parent->save();
            
            // Mettre à jour l'utilisateur associé si nécessaire
            if ($parent->user_id) {
                $user = User::find($parent->user_id);
                if ($user) {
                    $user->name = $parentData['prenoms'] . ' ' . $parentData['nom'];
                    $user->email = $parentData['email'] ?? $user->email;
                    $user->save();
                }
            }
            
            return $parent;
        }
        
        // Création d'un nouveau parent
        $parentData['created_by'] = $userId;
        $parentData['updated_by'] = $userId;
        
        // Création du compte utilisateur
        $createUser = $parentData['creer_compte_utilisateur'] ?? true;
        
        if ($createUser) {
            // Générer un nom d'utilisateur basé sur le prénom et le nom
            $username = ESBTPParent::genererUsername(
                $parentData['prenoms'], 
                $parentData['nom']
            );
            
            // Générer un mot de passe aléatoire
            $password = ESBTPEtudiant::genererMotDePasse();
            
            $user = User::create([
                'name' => $parentData['prenoms'] . ' ' . $parentData['nom'],
                'email' => $parentData['email'] ?? $username . '@esbtp.edu',
                'username' => $username,
                'password' => Hash::make($password),
                'avatar' => null,
                'is_active' => true
            ]);
            
            // Assigner le rôle parent
            $role = Role::where('name', 'parent')->first();
            if ($role) {
                $user->assignRole($role);
            }
            
            $parentData['user_id'] = $user->id;
            
            // Stocker le mot de passe en clair pour le premier login
            $parentData['password_generated'] = $password;
        }
        
        $parent = ESBTPParent::create($parentData);
        
        return $parent;
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