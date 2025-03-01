<?php

namespace App\Http\Controllers;

use App\Models\ESBTPInscription;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPClasse;
use App\Models\ESBTPPaiement;
use App\Services\ESBTPInscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ESBTPInscriptionController extends Controller
{
    protected $inscriptionService;

    /**
     * Constructeur avec injection du service d'inscription
     */
    public function __construct(ESBTPInscriptionService $inscriptionService)
    {
        $this->inscriptionService = $inscriptionService;
        $this->middleware('auth');
        $this->middleware('permission:inscriptions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:inscriptions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inscriptions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inscriptions.delete', ['only' => ['destroy']]);
        $this->middleware('permission:inscriptions.validate', ['only' => ['valider', 'annuler']]);
    }

    /**
     * Afficher la liste des inscriptions.
     */
    public function index(Request $request)
    {
        // Récupérer les filtres de recherche
        $search = $request->input('search');
        $filiere = $request->input('filiere');
        $niveau = $request->input('niveau');
        $annee = $request->input('annee');
        $status = $request->input('status', 'active');

        // Construire la requête avec les filtres
        $query = ESBTPInscription::query()
            ->with(['etudiant', 'filiere', 'niveau', 'classe', 'anneeUniversitaire']);

        // Appliquer les filtres
        if ($search) {
            $query->whereHas('etudiant', function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%");
            });
        }

        if ($filiere) {
            $query->where('filiere_id', $filiere);
        }

        if ($niveau) {
            $query->where('niveau_id', $niveau);
        }

        if ($annee) {
            $query->where('annee_universitaire_id', $annee);
        } else {
            // Par défaut, filtrer par année en cours
            $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
            if ($anneeEnCours) {
                $query->where('annee_universitaire_id', $anneeEnCours->id);
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Récupérer les inscriptions paginées
        $inscriptions = $query->latest()->paginate(15);

        // Récupérer les listes pour les filtres
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();

        // Calculer les statistiques
        $statsQuery = ESBTPInscription::query();
        
        if ($filiere) {
            $statsQuery->where('filiere_id', $filiere);
        }
        
        if ($niveau) {
            $statsQuery->where('niveau_id', $niveau);
        }
        
        if ($annee) {
            $statsQuery->where('annee_universitaire_id', $annee);
        } elseif ($anneeEnCours) {
            $statsQuery->where('annee_universitaire_id', $anneeEnCours->id);
        }
        
        $stats = [
            'total' => $statsQuery->count(),
            'actives' => (clone $statsQuery)->where('status', 'active')->count(),
            'en_attente' => (clone $statsQuery)->where('status', 'en_attente')->count(),
            'annulees' => (clone $statsQuery)->where('status', 'annulée')->count(),
            'terminees' => (clone $statsQuery)->where('status', 'terminée')->count(),
        ];

        return view('esbtp.inscriptions.index', compact(
            'inscriptions', 
            'filieres', 
            'niveaux', 
            'annees', 
            'search', 
            'filiere', 
            'niveau', 
            'annee', 
            'status',
            'stats',
            'anneeEnCours'
        ));
    }

    /**
     * Afficher le formulaire de création d'inscription.
     */
    public function create()
    {
        // Récupérer les données nécessaires pour le formulaire
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        return view('esbtp.inscriptions.create', compact(
            'filieres', 
            'niveaux', 
            'annees',
            'anneeEnCours'
        ));
    }

    /**
     * Enregistrer une nouvelle inscription.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'classe_id' => 'nullable|exists:esbtp_classes,id',
            'date_inscription' => 'required|date',
            'type_inscription' => 'required|in:première_inscription,réinscription,transfert',
            'montant_scolarite' => 'required|numeric|min:0',
            'frais_inscription' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
            
            // Données pour le paiement initial
            'montant_paiement' => 'nullable|numeric|min:0',
            'mode_paiement' => 'nullable|string|max:255',
            'reference_paiement' => 'nullable|string|max:255',
            'date_paiement' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Vérifier si l'étudiant est déjà inscrit pour cette année
            $etudiantId = $request->input('etudiant_id');
            $anneeId = $request->input('annee_universitaire_id');
            
            $inscriptionExistante = ESBTPInscription::where('etudiant_id', $etudiantId)
                ->where('annee_universitaire_id', $anneeId)
                ->whereIn('status', ['active', 'en_attente'])
                ->first();
                
            if ($inscriptionExistante) {
                throw new \Exception('Cet étudiant est déjà inscrit pour cette année universitaire.');
            }
            
            // Créer l'inscription
            $inscription = new ESBTPInscription();
            $inscription->etudiant_id = $etudiantId;
            $inscription->filiere_id = $request->input('filiere_id');
            $inscription->niveau_id = $request->input('niveau_id');
            $inscription->annee_universitaire_id = $anneeId;
            $inscription->classe_id = $request->input('classe_id');
            $inscription->date_inscription = $request->input('date_inscription');
            $inscription->type_inscription = $request->input('type_inscription');
            $inscription->status = 'en_attente';
            $inscription->montant_scolarite = $request->input('montant_scolarite');
            $inscription->frais_inscription = $request->input('frais_inscription');
            $inscription->observations = $request->input('observations');
            $inscription->numero_recu = ESBTPPaiement::genererNumeroRecu('INSC');
            $inscription->created_by = Auth::id();
            $inscription->save();
            
            // Créer le paiement initial si présent
            if ($request->filled('montant_paiement') && $request->input('montant_paiement') > 0) {
                $paiement = new ESBTPPaiement();
                $paiement->inscription_id = $inscription->id;
                $paiement->etudiant_id = $etudiantId;
                $paiement->montant = $request->input('montant_paiement');
                $paiement->date_paiement = $request->input('date_paiement', now()->format('Y-m-d'));
                $paiement->mode_paiement = $request->input('mode_paiement', 'Espèces');
                $paiement->reference_paiement = $request->input('reference_paiement');
                $paiement->motif = 'Frais d\'inscription + Première tranche scolarité';
                $paiement->tranche = 'Première tranche';
                $paiement->numero_recu = ESBTPPaiement::genererNumeroRecu('PAIE');
                $paiement->status = 'en_attente';
                $paiement->created_by = Auth::id();
                $paiement->save();
            }
            
            // Mettre à jour l'étudiant pour s'assurer qu'il est actif
            $etudiant = ESBTPEtudiant::find($etudiantId);
            if ($etudiant->statut !== 'actif') {
                $etudiant->statut = 'actif';
                $etudiant->save();
            }
            
            DB::commit();
            
            return redirect()
                ->route('inscriptions.show', $inscription->id)
                ->with('success', 'Inscription créée avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la création de l\'inscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'une inscription.
     */
    public function show(ESBTPInscription $inscription)
    {
        // Charger les relations nécessaires
        $inscription->load([
            'etudiant.user',
            'etudiant.parents',
            'filiere',
            'niveau',
            'classe',
            'anneeUniversitaire',
            'paiements' => function($q) {
                $q->orderBy('date_paiement', 'desc');
            },
            'createdBy',
            'updatedBy',
            'validatedBy'
        ]);
        
        return view('esbtp.inscriptions.show', compact('inscription'));
    }

    /**
     * Afficher le formulaire de modification d'une inscription.
     */
    public function edit(ESBTPInscription $inscription)
    {
        // Vérifier si l'inscription peut être modifiée
        if ($inscription->status === 'terminée') {
            return redirect()
                ->route('inscriptions.show', $inscription->id)
                ->with('error', 'Les inscriptions terminées ne peuvent pas être modifiées.');
        }
        
        // Charger les relations nécessaires
        $inscription->load(['etudiant', 'filiere', 'niveau', 'classe', 'anneeUniversitaire']);
        
        // Récupérer les données pour les selects
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $classes = ESBTPClasse::where('is_active', true)
            ->where('filiere_id', $inscription->filiere_id)
            ->where('niveau_etude_id', $inscription->niveau_id)
            ->where('annee_universitaire_id', $inscription->annee_universitaire_id)
            ->get();
            
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        
        return view('esbtp.inscriptions.edit', compact(
            'inscription', 
            'filieres', 
            'niveaux', 
            'classes', 
            'annees'
        ));
    }

    /**
     * Mettre à jour une inscription.
     */
    public function update(Request $request, ESBTPInscription $inscription)
    {
        // Vérifier si l'inscription peut être modifiée
        if ($inscription->status === 'terminée') {
            return redirect()
                ->route('inscriptions.show', $inscription->id)
                ->with('error', 'Les inscriptions terminées ne peuvent pas être modifiées.');
        }
        
        // Validation des données
        $validator = Validator::make($request->all(), [
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_id' => 'required|exists:esbtp_niveau_etudes,id',
            'classe_id' => 'nullable|exists:esbtp_classes,id',
            'date_inscription' => 'required|date',
            'type_inscription' => 'required|in:première_inscription,réinscription,transfert',
            'montant_scolarite' => 'required|numeric|min:0',
            'frais_inscription' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
            'status' => 'required|in:en_attente,active,annulée,terminée',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Mettre à jour l'inscription
            $inscription->filiere_id = $request->input('filiere_id');
            $inscription->niveau_id = $request->input('niveau_id');
            $inscription->classe_id = $request->input('classe_id');
            $inscription->date_inscription = $request->input('date_inscription');
            $inscription->type_inscription = $request->input('type_inscription');
            $inscription->montant_scolarite = $request->input('montant_scolarite');
            $inscription->frais_inscription = $request->input('frais_inscription');
            $inscription->observations = $request->input('observations');
            
            // Mettre à jour le statut et les champs associés
            $nouveauStatut = $request->input('status');
            $ancienStatut = $inscription->status;
            
            if ($nouveauStatut !== $ancienStatut) {
                $inscription->status = $nouveauStatut;
                
                if ($nouveauStatut === 'active' && $ancienStatut !== 'active') {
                    $inscription->date_validation = now();
                    $inscription->validated_by = Auth::id();
                }
                
                // Si l'inscription devient inactive ou annulée, mettre à jour l'étudiant si nécessaire
                if (in_array($nouveauStatut, ['annulée', 'terminée'])) {
                    $etudiant = $inscription->etudiant;
                    $autresInscriptionsActives = $etudiant->inscriptions()
            ->where('id', '!=', $inscription->id)
                        ->whereIn('status', ['active', 'en_attente'])
                        ->exists();
                        
                    if (!$autresInscriptionsActives && $etudiant->statut === 'actif') {
                        if ($nouveauStatut === 'terminée') {
                            $etudiant->statut = 'diplômé';
                        } else {
                            $etudiant->statut = 'inactif';
                        }
                        $etudiant->save();
                    }
                }
            }
            
            $inscription->updated_by = Auth::id();
            $inscription->save();
            
            DB::commit();
            
            return redirect()
                ->route('inscriptions.show', $inscription->id)
                ->with('success', 'Inscription mise à jour avec succès!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Valider une inscription.
     */
    public function valider(Request $request, ESBTPInscription $inscription)
    {
        try {
            $result = $this->inscriptionService->validerInscription($inscription->id, Auth::id());
            
            if ($result['success']) {
                return redirect()
                    ->route('inscriptions.show', $inscription->id)
                    ->with('success', 'Inscription validée avec succès!');
            } else {
                throw new \Exception($result['message']);
            }
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Annuler une inscription.
     */
    public function annuler(Request $request, ESBTPInscription $inscription)
    {
        $validator = Validator::make($request->all(), [
            'motif' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $motif = $request->input('motif');
            $result = $this->inscriptionService->annulerInscription($inscription->id, $motif, Auth::id());
            
            if ($result['success']) {
                return redirect()
                    ->route('inscriptions.show', $inscription->id)
                    ->with('success', 'Inscription annulée avec succès!');
            } else {
                throw new \Exception($result['message']);
            }
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Générer un reçu d'inscription.
     */
    public function recu(ESBTPInscription $inscription)
    {
        // Charger les relations nécessaires
        $inscription->load([
            'etudiant.user',
            'filiere',
            'niveau',
            'classe',
            'anneeUniversitaire',
            'paiements' => function($q) {
                $q->where('status', 'validé')
                  ->orderBy('date_paiement', 'desc');
            },
            'createdBy'
        ]);

        $pdf = PDF::loadView('esbtp.inscriptions.recu', compact('inscription'));
        return $pdf->stream('recu_inscription_' . $inscription->numero_recu . '.pdf');
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
} 