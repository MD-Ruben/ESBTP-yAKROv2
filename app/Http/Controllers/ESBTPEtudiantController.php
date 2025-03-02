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
        // Récupérer les données nécessaires pour le formulaire
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
    }

    /**
     * Enregistrer un nouvel étudiant.
     */
    public function store(Request $request)
    {
        // Validation des données de l'étudiant
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
            
            // Données pour l'inscription
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'classe_id' => 'nullable|exists:esbtp_classes,id',
            'date_inscription' => 'required|date',
            'type_inscription' => 'required|in:première_inscription,réinscription,transfert',
            'montant_scolarite' => 'required|numeric|min:0',
            'frais_inscription' => 'required|numeric|min:0',
            
            // Données pour le paiement initial
            'montant_paiement' => 'nullable|numeric|min:0',
            'mode_paiement' => 'nullable|string|max:255',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'nullable|date',
            
            // Données pour le parent/tuteur
            'parent_nom' => 'nullable|string|max:255',
            'parent_prenoms' => 'nullable|string|max:255',
            'parent_sexe' => 'nullable|in:M,F',
            'parent_profession' => 'nullable|string|max:255',
            'parent_adresse' => 'nullable|string',
            'parent_telephone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'parent_relation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Préparer les données de l'étudiant
            $etudiantData = $request->only([
                'nom', 'prenoms', 'sexe', 'date_naissance', 'lieu_naissance',
                'nationalite', 'adresse', 'telephone', 'email_personnel'
            ]);
            
            // Gérer la photo si présente
            if ($request->hasFile('photo')) {
                $etudiantData['photo_file'] = $request->file('photo');
            }
            
            // Préparer les données d'inscription
            $inscriptionData = $request->only([
                'filiere_id', 'niveau_id', 'annee_universitaire_id', 'classe_id',
                'date_inscription', 'type_inscription', 'montant_scolarite',
                'frais_inscription'
            ]);
            
            // Préparer les données du paiement initial (si présent)
            $paiementData = null;
            if ($request->filled('montant_paiement') && $request->input('montant_paiement') > 0) {
                $paiementData = [
                    'montant' => $request->input('montant_paiement'),
                    'date_paiement' => $request->input('date_paiement', now()->format('Y-m-d')),
                    'mode_paiement' => $request->input('mode_paiement', 'Espèces'),
                    'reference_paiement' => $request->input('reference_paiement'),
                    'motif' => 'Frais d\'inscription + Première tranche scolarité',
                    'tranche' => 'Première tranche',
                    'status' => 'en_attente',
                ];
            }
            
            // Préparer les données du parent/tuteur (si présent)
            $parentData = null;
            if ($request->filled('parent_nom') && $request->filled('parent_prenoms')) {
                $parentData = [
                    'nom' => $request->input('parent_nom'),
                    'prenoms' => $request->input('parent_prenoms'),
                    'sexe' => $request->input('parent_sexe'),
                    'profession' => $request->input('parent_profession'),
                    'adresse' => $request->input('parent_adresse'),
                    'telephone' => $request->input('parent_telephone'),
                    'email' => $request->input('parent_email'),
                    'relation' => $request->input('parent_relation', 'tuteur'),
                    'is_tuteur' => true,
                    'creer_compte_utilisateur' => $request->input('parent_creer_compte', true),
                ];
            }
            
            // Appeler le service pour créer l'inscription
            $result = $this->inscriptionService->createInscription(
                $etudiantData,
                $inscriptionData,
                $parentData,
                $paiementData,
                Auth::id()
            );
            
            DB::commit();
            
            if ($result['success']) {
                $etudiant = $result['etudiant'];
                $inscription = $result['inscription'];
                
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
                    ->route('etudiants.show', $etudiant->id)
                    ->with('success', 'Étudiant inscrit avec succès!')
                    ->with('account_info', $accountInfo);
            } else {
                throw new \Exception($result['message']);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
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
        $niveauId = $request->input('niveau_id');
        $anneeId = $request->input('annee_id');
        
        $classes = ESBTPClasse::where('is_active', true)
            ->where('filiere_id', $filiereId)
            ->where('niveau_etude_id', $niveauId)
            ->where('annee_universitaire_id', $anneeId)
            ->get(['id', 'name', 'code', 'capacity']);
            
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
} 