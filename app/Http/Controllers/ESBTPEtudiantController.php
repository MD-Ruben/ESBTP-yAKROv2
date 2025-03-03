<?php

namespace App\Http\Controllers;

use App\Models\ESBTPEtudiant;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPClasse;
use App\Models\User;
use App\Services\ESBTPInscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\ESBTPParent;

class ESBTPEtudiantController extends Controller
{
    protected $inscriptionService;

    /**
     * Constructeur avec injection du service d'inscription
     */
    public function __construct(ESBTPInscriptionService $inscriptionService)
    {
        $this->inscriptionService = $inscriptionService;
        $this->middleware('auth');
        $this->middleware('permission:etudiants.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:etudiants.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:etudiants.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:etudiants.delete', ['only' => ['destroy']]);
    }

    /**
     * Afficher la liste des étudiants.
     */
    public function index(Request $request)
    {
        // Récupérer les filtres de recherche
        $search = $request->input('search');
        $filiere = $request->input('filiere');
        $niveau = $request->input('niveau');
        $annee = $request->input('annee');
        $status = $request->input('status');

        // Construire la requête avec les filtres
        $query = ESBTPEtudiant::query()
            ->with(['user', 'inscriptions' => function($q) {
                $q->with(['filiere', 'niveau', 'classe', 'anneeUniversitaire']);
            }]);

        // Appliquer les filtres
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('statut', $status);
        }

        if ($filiere || $niveau || $annee) {
            $query->whereHas('inscriptions', function($q) use ($filiere, $niveau, $annee) {
                if ($filiere) {
                    $q->where('filiere_id', $filiere);
                }
                if ($niveau) {
                    $q->where('niveau_id', $niveau);
                }
                if ($annee) {
                    $q->where('annee_universitaire_id', $annee);
                }
            });
        }

        // Récupérer les étudiants paginés
        $etudiants = $query->latest()->paginate(15);

        // Récupérer les listes pour les filtres
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();

        return view('esbtp.etudiants.index', compact(
            'etudiants', 
            'filieres', 
            'niveaux', 
            'annees', 
            'search', 
            'filiere', 
            'niveau', 
            'annee', 
            'status'
        ));
    }

    /**
     * Afficher le formulaire de création d'étudiant.
     */
    public function create()
    {
        // Rediriger vers la page d'inscription qui est plus complète
        return redirect()->route('esbtp.inscriptions.create')
            ->with('info', 'Veuillez utiliser le formulaire d\'inscription pour ajouter un nouvel étudiant.');
        
        // Code commenté - anciennes données pour le formulaire
        /*
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();

        return view('esbtp.etudiants.create', compact(
            'filieres', 
            'niveaux', 
            'annees',
            'anneeEnCours'
        ));
        */
    }

    /**
     * Enregistrer un nouvel étudiant.
     */
    public function store(Request $request)
    {
        // Ajout de logs pour déboguer
        \Illuminate\Support\Facades\Log::info('Tentative de création d\'un étudiant', ['request' => $request->all()]);
        
        // Validation des données de base de l'étudiant
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'genre' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'ville' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            
            // Données pour l'inscription
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'classe_id' => 'nullable|exists:esbtp_classes,id',
            'date_admission' => 'required|date',
            'statut' => 'required|in:actif,inactif',
            
            // Validation conditionnelle des données des parents
            'parents.*.nom' => 'required_without:parents.*.parent_id|string|max:255|nullable',
            'parents.*.prenoms' => 'required_without:parents.*.parent_id|string|max:255|nullable',
            'parents.*.relation' => 'required_without:parents.*.parent_id|string|max:50|nullable',
            'parents.*.telephone' => 'required_without:parents.*.parent_id|string|max:20|nullable',
            'parents.*.parent_id' => 'nullable|exists:esbtp_parents,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Ajout de logs pour déboguer
            \Illuminate\Support\Facades\Log::info('Début de la transaction pour la création d\'un étudiant');
            
            // Préparer les données de l'étudiant
            $etudiantData = [
                'nom' => $request->nom,
                'prenoms' => $request->prenoms,
                'sexe' => $request->genre,
                'date_naissance' => $request->date_naissance,
                'telephone' => $request->telephone,
                'email_personnel' => $request->email,
                'adresse' => ($request->ville && $request->commune) ? $request->ville . ', ' . $request->commune : null,
                'statut' => $request->statut ?? 'actif',
                'creer_compte_utilisateur' => $request->create_account ? true : false,
            ];
            
            // Gérer la photo si présente
            if ($request->hasFile('photo')) {
                $etudiantData['photo_file'] = $request->file('photo');
            }
            
            // Préparer les données d'inscription
            $inscriptionData = [
                'filiere_id' => $request->filiere_id,
                'niveau_id' => $request->niveau_etude_id,
                'annee_universitaire_id' => $request->annee_universitaire_id,
                'classe_id' => $request->classe_id,
                'date_inscription' => $request->date_admission,
                'type_inscription' => 'première_inscription',
                'status' => 'en_attente',
                'montant_scolarite' => 0, // À définir plus tard
                'frais_inscription' => 0, // À définir plus tard
            ];
            
            // Préparer les données des parents
            $parentsData = [];
            if ($request->has('parents')) {
                foreach ($request->parents as $index => $parentData) {
                    // Vérifier si c'est un parent existant
                    if (isset($parentData['parent_id']) && !empty($parentData['parent_id'])) {
                        $parentsData[] = [
                            'id' => $parentData['parent_id'],
                            'relation' => $parentData['relation'] ?? 'tuteur',
                            'is_tuteur' => $index === 0 ? true : false,
                        ];
                        continue;
                    }
                    
                    // Vérifier si c'est un nouveau parent valide
                    if (!empty($parentData['nom']) && !empty($parentData['prenoms']) && !empty($parentData['telephone'])) {
                        $parentsData[] = [
                            'nom' => $parentData['nom'],
                            'prenoms' => $parentData['prenoms'],
                            'relation' => $parentData['relation'] ?? 'tuteur',
                            'is_tuteur' => $index === 0 ? true : false,
                            'telephone' => $parentData['telephone'],
                            'email' => $parentData['email'] ?? null,
                            'profession' => $parentData['profession'] ?? null,
                            'adresse' => $parentData['adresse'] ?? null,
                            'creer_compte_utilisateur' => false, // Ne pas créer de compte pour les parents par défaut
                        ];
                    }
                }
            }
            
            // Appeler le service pour créer l'inscription
            $parentData = !empty($parentsData) ? $parentsData[0] : null;
            
            // Ajout de logs pour déboguer
            \Illuminate\Support\Facades\Log::info('Appel au service d\'inscription', [
                'etudiantData' => $etudiantData,
                'inscriptionData' => $inscriptionData,
                'parentData' => $parentData
            ]);
            
            $result = $this->inscriptionService->createInscription(
                $etudiantData,
                $inscriptionData,
                $parentData,
                null, // Pas de paiement initial pour le moment
                Auth::id()
            );
            
            // Ajouter les parents supplémentaires si nécessaire
            if ($result['success'] && count($parentsData) > 1) {
                $etudiant = $result['etudiant'];
                
                for ($i = 1; $i < count($parentsData); $i++) {
                    $parentData = $parentsData[$i];
                    
                    // Si c'est un parent existant
                    if (isset($parentData['id'])) {
                        $etudiant->parents()->attach($parentData['id'], [
                            'relation' => $parentData['relation'],
                            'is_tuteur' => $parentData['is_tuteur'],
                        ]);
                    } 
                    // Si c'est un nouveau parent
                    else {
                        $parent = $this->inscriptionService->createOrUpdateParent($parentData, Auth::id());
                        $etudiant->parents()->attach($parent->id, [
                            'relation' => $parentData['relation'],
                            'is_tuteur' => $parentData['is_tuteur'],
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            // Ajout de logs pour déboguer
            \Illuminate\Support\Facades\Log::info('Transaction commitée avec succès', ['result' => $result]);
            
            if ($result['success']) {
                $etudiant = $result['etudiant'];
                
                // Préparer l'affichage des informations du compte généré
                $accountInfo = null;
                if (isset($etudiant->user_id) && $etudiant->user_id) {
                    $user = User::find($etudiant->user_id);
                    $accountInfo = [
                        'username' => $user->username,
                        'password' => $etudiantData['password_generated'] ?? null,
                    ];
                }
                
                return redirect()
                    ->route('esbtp.etudiants.show', $etudiant->id)
                    ->with('success', 'Étudiant inscrit avec succès!')
                    ->with('account_info', $accountInfo);
            } else {
                throw new \Exception($result['message']);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Ajout de logs pour déboguer
            \Illuminate\Support\Facades\Log::error('Erreur lors de la création d\'un étudiant', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'inscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'un étudiant.
     */
    public function show(ESBTPEtudiant $etudiant)
    {
        // Charger les relations nécessaires
        $etudiant->load([
            'user',
            'parents',
            'inscriptions' => function($q) {
                $q->with(['filiere', 'niveau', 'classe', 'anneeUniversitaire'])
                  ->orderBy('date_inscription', 'desc');
            },
            'inscriptions.paiements' => function($q) {
                $q->orderBy('date_paiement', 'desc');
            }
        ]);

        return view('esbtp.etudiants.show', compact('etudiant'));
    }

    /**
     * Afficher le formulaire de modification d'un étudiant.
     */
    public function edit(ESBTPEtudiant $etudiant)
    {
        // Charger les relations nécessaires
        $etudiant->load(['user', 'parents', 'inscriptions.filiere', 'inscriptions.niveau', 'inscriptions.classe']);
        
        // Récupérer les données pour les selects
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $classes = ESBTPClasse::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();

        return view('esbtp.etudiants.edit', compact(
            'etudiant', 
            'filieres', 
            'niveaux', 
            'classes', 
            'annees'
        ));
    }

    /**
     * Mettre à jour un étudiant.
     */
    public function update(Request $request, ESBTPEtudiant $etudiant)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email_personnel' => 'nullable|email|max:255',
            'photo' => 'nullable|image|max:2048',
            'statut' => 'required|in:actif,inactif,diplômé,abandon,exclu',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Mettre à jour les données de l'étudiant
            $etudiantData = $request->only([
                'nom', 'prenoms', 'sexe', 'date_naissance', 'lieu_naissance',
                'nationalite', 'adresse', 'telephone', 'email_personnel', 'statut'
            ]);
            
            // Gérer la photo si présente
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($etudiant->photo && Storage::exists(str_replace('/storage', 'public', $etudiant->photo))) {
                    Storage::delete(str_replace('/storage', 'public', $etudiant->photo));
                }
                
                $photoPath = $request->file('photo')->store('public/etudiants/photos');
                $etudiantData['photo'] = Storage::url($photoPath);
            }
            
            // Mettre à jour l'étudiant
            $etudiant->fill($etudiantData);
            $etudiant->updated_by = Auth::id();
            $etudiant->save();
            
            // Mettre à jour l'utilisateur associé
            if ($etudiant->user_id) {
                $user = User::find($etudiant->user_id);
                if ($user) {
                    $user->name = $etudiantData['prenoms'] . ' ' . $etudiantData['nom'];
                    $user->email = $etudiantData['email_personnel'] ?? $user->email;
                    $user->is_active = ($etudiantData['statut'] === 'actif');
                    $user->save();
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('etudiants.show', $etudiant->id)
                ->with('success', 'Informations de l\'étudiant mises à jour avec succès!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprimer un étudiant.
     */
    public function destroy(ESBTPEtudiant $etudiant)
    {
        try {
            // Vérifier si l'étudiant peut être supprimé
            $hasInscriptions = $etudiant->inscriptions()->exists();
            
            if ($hasInscriptions) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer cet étudiant car il a des inscriptions. Vous pouvez le désactiver à la place.');
            }
            
            DB::beginTransaction();
            
            // Supprimer l'utilisateur associé
            if ($etudiant->user_id) {
                User::destroy($etudiant->user_id);
            }
            
            // Supprimer la photo
            if ($etudiant->photo && Storage::exists(str_replace('/storage', 'public', $etudiant->photo))) {
                Storage::delete(str_replace('/storage', 'public', $etudiant->photo));
            }
            
            // Supprimer l'étudiant
            $etudiant->delete();
            
            DB::commit();
            
            return redirect()
                ->route('etudiants.index')
                ->with('success', 'Étudiant supprimé avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Générer un nouveau mot de passe pour l'étudiant.
     */
    public function resetPassword(ESBTPEtudiant $etudiant)
    {
        try {
            if (!$etudiant->user_id) {
                return redirect()
                    ->back()
                    ->with('error', 'Cet étudiant n\'a pas de compte utilisateur.');
            }
            
            $user = User::find($etudiant->user_id);
            if (!$user) {
                return redirect()
                    ->back()
                    ->with('error', 'Compte utilisateur introuvable.');
            }
            
            // Générer un nouveau mot de passe
            $newPassword = ESBTPEtudiant::genererMotDePasse();
            
            // Mettre à jour le mot de passe
            $user->password = Hash::make($newPassword);
            $user->save();
            
            return redirect()
                ->back()
                ->with('success', 'Mot de passe réinitialisé avec succès!')
                ->with('new_password', $newPassword);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la réinitialisation du mot de passe: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les classes disponibles pour une filière, un niveau et une année donnés.
     */
    public function getClasses(Request $request)
    {
        $filiereId = $request->input('filiere_id');
        $niveauId = $request->input('niveau_id') ?? $request->input('niveau_etude_id');
        $anneeId = $request->input('annee_id') ?? $request->input('annee_universitaire_id');
        $formationId = $request->input('formation_id');
        
        // Ajouter des logs pour debug
        \Illuminate\Support\Facades\Log::info('Récupération des classes (Etudiant)', [
            'filiere_id' => $filiereId,
            'niveau_id' => $niveauId,
            'annee_id' => $anneeId,
            'formation_id' => $formationId,
            'request' => $request->all()
        ]);
        
        $query = ESBTPClasse::select(
                'esbtp_classes.*', 
                'f.name as filiere_name',
                'n.name as niveau_name',
                'a.name as annee_name',
                'fo.name as formation_name'
            )
            ->leftJoin('esbtp_filieres as f', 'esbtp_classes.filiere_id', '=', 'f.id')
            ->leftJoin('esbtp_niveau_etudes as n', 'esbtp_classes.niveau_etude_id', '=', 'n.id')
            ->leftJoin('esbtp_annee_universitaires as a', 'esbtp_classes.annee_universitaire_id', '=', 'a.id')
            ->leftJoin('esbtp_formations as fo', 'esbtp_classes.formation_id', '=', 'fo.id')
            ->where('esbtp_classes.is_active', true);
        
        // Appliquer les filtres seulement s'ils sont fournis
        if ($filiereId) {
            $query->where('esbtp_classes.filiere_id', $filiereId);
        }
        
        if ($niveauId) {
            $query->where('esbtp_classes.niveau_etude_id', $niveauId);
        }
        
        if ($anneeId) {
            $query->where('esbtp_classes.annee_universitaire_id', $anneeId);
        }
        
        if ($formationId) {
            $query->where('esbtp_classes.formation_id', $formationId);
        }
        
        // Log pour vérifier la requête SQL générée
        \Illuminate\Support\Facades\Log::info('Requête SQL pour les classes (Etudiant)', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        $classes = $query->get();
        
        // Log pour vérifier les résultats
        \Illuminate\Support\Facades\Log::info('Classes trouvées (Etudiant)', [
            'count' => $classes->count(),
            'first_few' => $classes->take(3)
        ]);
            
        return response()->json($classes);
    }

    /**
     * Affiche la liste des étudiants sous forme de données JSON (pour les selects dynamiques).
     */
    public function getEtudiants(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        
        $query = ESBTPEtudiant::query()
            ->select('id', 'matricule', 'nom', 'prenoms')
            ->where('statut', 'actif');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%");
            });
        }
        
        $total = $query->count();
        $etudiants = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->map(function($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'text' => "{$etudiant->matricule} - {$etudiant->prenoms} {$etudiant->nom}"
                ];
            });
            
        return response()->json([
            'results' => $etudiants,
            'pagination' => [
                'more' => ($page * $limit) < $total
            ]
        ]);
    }

    /**
     * Recherche d'étudiants pour le formulaire de paiement (API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        
        $query = ESBTPEtudiant::with('user')
            ->where('status', 'actif')
            ->whereHas('inscriptions', function($q) {
                $q->where('status', 'active');
            });
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $total = $query->count();
        $etudiants = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->map(function($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'text' => "{$etudiant->matricule} - {$etudiant->user->name}"
                ];
            });
            
        return response()->json([
            'results' => $etudiants,
            'pagination' => [
                'more' => ($page * $limit) < $total
            ]
        ]);
    }

    /**
     * Récupérer les inscriptions actives d'un étudiant (API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInscriptions(Request $request)
    {
        $etudiantId = $request->input('etudiant_id');
        
        if (!$etudiantId) {
            return response()->json([]);
        }
        
        $etudiant = ESBTPEtudiant::findOrFail($etudiantId);
        
        $inscriptions = $etudiant->inscriptions()
            ->with(['filiere', 'niveauEtude', 'anneeUniversitaire'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'filiere' => $inscription->filiere->name,
                    'niveau' => $inscription->niveauEtude->name,
                    'annee' => $inscription->anneeUniversitaire->libelle,
                    'montant_scolarite' => $inscription->montant_scolarite,
                    'frais_inscription' => $inscription->frais_inscription,
                ];
            });
            
        return response()->json($inscriptions);
    }

    /**
     * Affiche le profil de l'étudiant connecté.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Récupérer l'étudiant associé à l'utilisateur connecté
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();
        
        // Si l'utilisateur n'est pas un étudiant (par exemple, superAdmin, secretaire), afficher une vue alternative
        if (!$etudiant) {
            if ($user->hasRole('superAdmin')) {
                return view('esbtp.admin.profile', compact('user'));
            } elseif ($user->hasRole('secretaire')) {
                return view('esbtp.secretaires.profile', compact('user'));
            } else {
                // Redirection par défaut si le rôle n'est pas reconnu
                return redirect()->route('esbtp.welcome')->with('error', 'Profil non disponible');
            }
        }
        
        // Récupérer l'inscription active de l'étudiant
        $inscriptionActive = DB::table('esbtp_inscriptions')
            ->join('esbtp_annee_universitaires', 'esbtp_inscriptions.annee_universitaire_id', '=', 'esbtp_annee_universitaires.id')
            ->join('esbtp_classes', 'esbtp_inscriptions.classe_id', '=', 'esbtp_classes.id')
            ->join('esbtp_niveau_etudes', 'esbtp_classes.niveau_etude_id', '=', 'esbtp_niveau_etudes.id')
            ->join('esbtp_filieres', 'esbtp_classes.filiere_id', '=', 'esbtp_filieres.id')
            ->select(
                'esbtp_inscriptions.*',
                'esbtp_annee_universitaires.libelle as annee',
                'esbtp_classes.nom as classe',
                'esbtp_niveau_etudes.libelle as niveau',
                'esbtp_filieres.nom as filiere'
            )
            ->where('esbtp_inscriptions.etudiant_id', $etudiant->id)
            ->where('esbtp_annee_universitaires.est_actif', 1)
            ->first();
            
        return view('esbtp.etudiants.profile', compact('etudiant', 'inscriptionActive'));
    }

    /**
     * Recherche des parents pour le formulaire d'ajout d'étudiant (API pour Select2)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchParents(Request $request)
    {
        $search = $request->input('q', '');
        $page = $request->input('page', 1);
        $perPage = 10;
        
        // Si la recherche est trop courte, renvoyer un résultat vide
        if (strlen($search) < 2) {
            return response()->json([
                'items' => [],
                'pagination' => ['more' => false]
            ]);
        }
        
        // Rechercher les parents correspondant à la requête
        $parents = ESBTPParent::where(function($query) use ($search) {
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
        })
        ->select('id', 'nom', 'prenoms', 'telephone')
        ->skip(($page - 1) * $perPage)
        ->take($perPage + 1) // Prendre un de plus pour vérifier s'il y a d'autres pages
        ->get();
        
        $hasMorePages = $parents->count() > $perPage;
        
        if ($hasMorePages) {
            $parents = $parents->take($perPage);
        }
        
        // Formater les résultats pour Select2
        $formattedParents = $parents->map(function($parent) {
            return [
                'id' => $parent->id,
                'nom' => $parent->nom,
                'prenoms' => $parent->prenoms,
                'telephone' => $parent->telephone,
                'text' => $parent->nom . ' ' . $parent->prenoms . ' (' . $parent->telephone . ')'
            ];
        });
        
        return response()->json([
            'items' => $formattedParents,
            'pagination' => ['more' => $hasMorePages]
        ]);
    }

    /**
     * Met à jour le profil de l'administrateur
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAdminProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo de profil si elle existe
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }
            
            // Sauvegarder la nouvelle photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return redirect()->route('esbtp.mon-profil.index')->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Met à jour le mot de passe de l'administrateur
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAdminPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Vérifier si le mot de passe actuel est correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('esbtp.mon-profil.index')->with('success', 'Mot de passe mis à jour avec succès');
    }
} 