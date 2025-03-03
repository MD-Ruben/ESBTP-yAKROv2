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
use App\Models\ESBTPClasse;
use Illuminate\Support\Str;

class ESBTPInscriptionService
{
    /**
     * Créer une nouvelle inscription d'étudiant
     *
     * @param array $etudiantData Données de l'étudiant
     * @param array $inscriptionData Données de l'inscription
     * @param array $parentsData Données des parents [optionnel]
     * @param array|null $paiementData Données de paiement initial [optionnel]
     * @param int $userId ID de l'utilisateur qui crée l'inscription
     * @return ESBTPInscription
     */
    public function createInscription(array $etudiantData, array $inscriptionData, array $parentsData = [], ?array $paiementData = null, int $userId = null)
    {
        try {
            DB::beginTransaction();
            
            // Ajout de logs pour déboguer
            Log::info('Début de création de l\'inscription', [
                'etudiantData' => $etudiantData, 
                'inscriptionData' => $inscriptionData
            ]);
            
            // 1. Vérification des données minimales requises
            if (empty($etudiantData['nom']) || empty($etudiantData['prenoms'])) {
                throw new \Exception("Les informations de base de l'étudiant sont manquantes");
            }
            
            if (empty($inscriptionData['classe_id'])) {
                throw new \Exception("Une classe doit être sélectionnée pour l'inscription");
            }
            
            // 2. Récupération des données de la classe pour remplir les données de l'inscription
            $classe = ESBTPClasse::with(['filiere', 'niveau', 'annee'])->findOrFail($inscriptionData['classe_id']);
            
            // S'assurer que les données de filière, niveau et année sont disponibles
            if (!$classe->filiere_id || !$classe->niveau_etude_id || !$classe->annee_universitaire_id) {
                throw new \Exception("La classe sélectionnée n'a pas toutes les informations requises");
            }
            
            // 3. Préparer les données de l'étudiant pour la création
            $etudiantData['filiere_id'] = $classe->filiere_id;
            $etudiantData['niveau_etude_id'] = $classe->niveau_etude_id;
            $etudiantData['annee_universitaire_id'] = $classe->annee_universitaire_id;
            $etudiantData['created_by'] = $userId;
            $etudiantData['updated_by'] = $userId;
            
            // Convertir sexe en genre si nécessaire
            if (isset($etudiantData['sexe']) && !isset($etudiantData['genre'])) {
                $etudiantData['genre'] = $etudiantData['sexe'];
            }
            
            // Statut par défaut pour un nouvel étudiant
            $etudiantData['statut'] = 'actif';
            
            // 4. Créer l'étudiant et récupérer son instance
            $etudiant = $this->createEtudiant($etudiantData, $userId);
            
            // Si le statut est 'actif', on active également le compte utilisateur
            if (isset($inscriptionData['status']) && $inscriptionData['status'] === 'active') {
                $etudiant->statut = 'actif';
                $etudiant->save();
                
                if ($etudiant->user_id) {
                    $user = User::find($etudiant->user_id);
                    if ($user) {
                        $user->is_active = true;
                        $user->save();
                    }
                }
            }
            
            // 5. Préparer les données d'inscription
            $inscriptionData['etudiant_id'] = $etudiant->id;
            $inscriptionData['annee_universitaire_id'] = $classe->annee_universitaire_id;
            $inscriptionData['filiere_id'] = $classe->filiere_id;
            $inscriptionData['niveau_id'] = $classe->niveau_etude_id;
            $inscriptionData['date_inscription'] = $inscriptionData['date_inscription'] ?? now()->format('Y-m-d');
            $inscriptionData['type_inscription'] = $inscriptionData['type_inscription'] ?? 'PREMIERE';
            $inscriptionData['status'] = $inscriptionData['status'] ?? 'en_attente';
            $inscriptionData['created_by'] = $userId;
            $inscriptionData['updated_by'] = $userId;
            
            // Générer un numéro de reçu si nécessaire
            if (empty($inscriptionData['numero_recu'])) {
                $annee = date('Y');
                $anneeCode = $classe->annee->code ?? $annee;
                $inscriptionData['numero_recu'] = 'INSC-' . $anneeCode . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
            
            // 6. Créer l'inscription
            $inscription = ESBTPInscription::create($inscriptionData);
            
            // 7. Traiter les parents s'ils sont fournis
            if (!empty($parentsData)) {
                $this->attachParentsToEtudiant($etudiant, $parentsData, $userId);
            }
            
            // 8. Enregistrement du paiement initial (si fourni)
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
                Log::info('Paiement créé pour l\'inscription', ['paiement' => $paiementData]);
            } else {
                Log::info('Aucun paiement fourni pour cette inscription');
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
                'trace' => $e->getTraceAsString(),
                'etudiantData' => $etudiantData ?? null,
                'inscriptionData' => $inscriptionData ?? null,
                'parentsData' => $parentsData ?? null,
                'paiementData' => $paiementData ?? null
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
        // Générer un username unique basé sur le prénom et le nom
        $prenoms = explode(' ', $etudiantData['prenoms']);
        $prenom = strtolower($prenoms[0] ?? '');
        $nom = strtolower($etudiantData['nom'] ?? '');
        
        // Créer un username basé sur le prénom et le nom
        $baseUsername = $prenom . '.' . $nom;
        $baseUsername = preg_replace('/[^a-z0-9.]/', '', $baseUsername); // Supprime les caractères spéciaux
        $username = $baseUsername;
        
        // Si le username existe déjà, ajouter un nombre aléatoire
        $count = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '.' . $count;
            $count++;
        }
        
        // Générer un email basé sur le username
        $baseEmail = $username . '@esbtp.edu';
        $email = $baseEmail;
        $count = 1;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', '.' . $count . '@', $baseEmail);
            $count++;
        }
        
        // Générer un mot de passe aléatoire
        $password = Str::random(10);
        
        $user = User::create([
            'name' => $etudiantData['prenoms'] . ' ' . $etudiantData['nom'],
            'first_name' => $etudiantData['prenoms'],
            'last_name' => $etudiantData['nom'],
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'is_active' => true
        ]);
        
        // Enregistrer le mot de passe généré en session pour l'afficher plus tard
        session()->put('generated_password', $password);
        
        // Assigner le rôle étudiant
        $role = Role::where('name', 'etudiant')->first();
        if ($role) {
            $user->assignRole($role);
        }
        
        $etudiantData['user_id'] = $user->id;
        
        // Générer un matricule unique pour l'étudiant si ce n'est pas déjà fait
        if (empty($etudiantData['matricule'])) {
            // Récupérer les références nécessaires pour générer le matricule
            $filiereId = $etudiantData['filiere_id'] ?? null;
            $niveauId = $etudiantData['niveau_etude_id'] ?? null;
            $anneeId = $etudiantData['annee_universitaire_id'] ?? null;
            
            // Si anneeId est null, essayer de récupérer l'année universitaire active
            if ($anneeId === null) {
                $anneeActive = ESBTPAnneeUniversitaire::where('is_current', true)->first();
                if ($anneeActive) {
                    $anneeId = $anneeActive->id;
                    $etudiantData['annee_universitaire_id'] = $anneeId;
                }
            }
            
            $filiere = $filiereId ? ESBTPFiliere::find($filiereId) : null;
            $niveau = $niveauId ? ESBTPNiveauEtude::find($niveauId) : null;
            $annee = $anneeId ? ESBTPAnneeUniversitaire::find($anneeId) : null;
            
            // Générer les codes pour le matricule
            $filiereCode = $filiere ? ($filiere->code ?? substr($filiere->nom, 0, 2)) : 'XX';
            $niveauCode = $niveau ? ($niveau->code ?? ($niveau->annee ?? 'XX')) : 'XX';
            $anneeCode = $annee ? substr($annee->code ?? date('Y'), 2, 2) : date('y');
            
            // Construire le préfixe du matricule
            $matriculePrefix = strtoupper($filiereCode . $niveauCode . $anneeCode);
            
            // Trouver le dernier numéro séquentiel pour cette combinaison
            $lastMatricule = ESBTPEtudiant::where('matricule', 'LIKE', $matriculePrefix . '%')
                                        ->orderBy('matricule', 'desc')
                                        ->first();
            
            $sequence = 1;
            if ($lastMatricule) {
                $lastSequence = (int) substr($lastMatricule->matricule, strlen($matriculePrefix));
                $sequence = $lastSequence + 1;
            }
            
            // Générer le matricule final
            $matricule = $matriculePrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // Journaliser la génération du matricule
            Log::info('Matricule généré pour l\'étudiant', [
                'nom' => $etudiantData['nom'],
                'prenoms' => $etudiantData['prenoms'],
                'matricule' => $matricule
            ]);
            
            $etudiantData['matricule'] = $matricule;
        }
        
        // Assurer que toutes les données requises sont présentes
        $etudiantData['statut'] = $etudiantData['statut'] ?? 'en_attente';
        
        // Créer l'étudiant
        $etudiant = ESBTPEtudiant::create($etudiantData);
        
        // Journaliser la création de l'étudiant
        Log::info('Étudiant créé', ['etudiant' => $etudiant]);
        
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
        Log::info('Début attachement des parents', ['parentsData' => $parentsData]);
        
        foreach ($parentsData as $index => $parentData) {
            $isTuteur = $index === 0; // Le premier parent est le tuteur par défaut
            
            try {
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
                    
                    Log::info('Parent existant attaché à l\'étudiant', [
                        'parent_id' => $parent->id,
                        'etudiant_id' => $etudiant->id
                    ]);
                } 
                // Nouveau parent
                elseif (isset($parentData['nom']) && !empty($parentData['nom'])) {
                    // Créer le nouveau parent
                    $parent = ESBTPParent::create([
                        'nom' => $parentData['nom'],
                        'prenoms' => $parentData['prenoms'],
                        'telephone' => $parentData['telephone'] ?? null,
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
                    
                    Log::info('Nouveau parent créé et attaché à l\'étudiant', [
                        'parent_id' => $parent->id,
                        'etudiant_id' => $etudiant->id
                    ]);
                } else {
                    Log::warning('Données de parent incomplètes ignorées', ['parentData' => $parentData]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'attachement d\'un parent', [
                    'message' => $e->getMessage(),
                    'parentData' => $parentData
                ]);
                // On continue malgré l'erreur pour traiter les autres parents
            }
        }
        
        Log::info('Fin attachement des parents pour l\'étudiant', ['etudiant_id' => $etudiant->id]);
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