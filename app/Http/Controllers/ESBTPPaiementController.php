<?php

namespace App\Http\Controllers;

use App\Models\ESBTPPaiement;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPInscription;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ESBTPPaiementController extends Controller
{
    /**
     * Constructeur du contrôleur.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-paiements', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-paiements', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-paiements', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-paiements', ['only' => ['destroy']]);
        $this->middleware('permission:validate-paiements', ['only' => ['valider', 'rejeter']]);
    }

    /**
     * Affiche la liste des paiements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $search = $request->input('search');
        $status = $request->input('status');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');
        $anneeId = $request->input('annee_id');

        // Récupérer les années universitaires pour le filtre
        $annees = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        
        // Construire la requête
        $query = ESBTPPaiement::with(['etudiant.user', 'inscription.anneeUniversitaire', 'validatedBy'])
            ->orderBy('created_at', 'desc');
        
        // Appliquer les filtres
        if ($search) {
            $query->whereHas('etudiant', function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('matricule', 'like', "%{$search}%");
            })
            ->orWhere('numero_recu', 'like', "%{$search}%")
            ->orWhere('reference_paiement', 'like', "%{$search}%");
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateDebut) {
            $query->whereDate('date_paiement', '>=', $dateDebut);
        }
        
        if ($dateFin) {
            $query->whereDate('date_paiement', '<=', $dateFin);
        }
        
        if ($anneeId) {
            $query->whereHas('inscription', function ($q) use ($anneeId) {
                $q->where('annee_universitaire_id', $anneeId);
            });
        } else {
            // Par défaut, afficher les paiements de l'année en cours
            $query->anneeEnCours();
        }
        
        // Paginer les résultats
        $paiements = $query->paginate(15);
        
        // Statistiques des paiements
        $stats = [
            'total' => $query->count(),
            'montant_total' => $query->sum('montant'),
            'valides' => $query->where('status', 'validé')->count(),
            'montant_valide' => $query->where('status', 'validé')->sum('montant'),
            'en_attente' => $query->where('status', 'en_attente')->count(),
            'montant_en_attente' => $query->where('status', 'en_attente')->sum('montant'),
        ];
        
        return view('esbtp.paiements.index', compact('paiements', 'annees', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'un paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $etudiantId = $request->input('etudiant_id');
        $inscriptionId = $request->input('inscription_id');
        
        $etudiant = null;
        $inscription = null;
        
        // Si un étudiant est spécifié, récupérer ses informations
        if ($etudiantId) {
            $etudiant = ESBTPEtudiant::with(['user', 'inscriptions.anneeUniversitaire', 'inscriptions.filiere', 'inscriptions.niveauEtude'])
                ->findOrFail($etudiantId);
                
            // Si aucune inscription n'est spécifiée, prendre la plus récente
            if (!$inscriptionId && $etudiant->inscriptions->count() > 0) {
                $inscription = $etudiant->inscriptions->sortByDesc('created_at')->first();
            }
        }
        
        // Si une inscription est spécifiée, la récupérer
        if ($inscriptionId) {
            $inscription = ESBTPInscription::with(['etudiant.user', 'anneeUniversitaire', 'filiere', 'niveauEtude'])
                ->findOrFail($inscriptionId);
            
            // Si aucun étudiant n'est spécifié, prendre celui de l'inscription
            if (!$etudiant) {
                $etudiant = $inscription->etudiant;
            }
        }
        
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        return view('esbtp.paiements.create', compact('etudiant', 'inscription', 'anneeEnCours'));
    }

    /**
     * Enregistre un nouveau paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'inscription_id' => 'required|exists:esbtp_inscriptions,id',
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'reference_paiement' => 'nullable|string',
            'tranche' => 'nullable|string',
            'motif' => 'required|string',
            'commentaire' => 'nullable|string',
        ]);
        
        // Vérifier que l'étudiant correspond à l'inscription
        $inscription = ESBTPInscription::findOrFail($validated['inscription_id']);
        if ($inscription->etudiant_id != $validated['etudiant_id']) {
            return redirect()->back()->withErrors(['etudiant_id' => 'L\'étudiant ne correspond pas à l\'inscription sélectionnée.'])->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Générer un numéro de reçu
            $numeroRecu = ESBTPPaiement::genererNumeroRecu();
            
            // Créer le paiement
            $paiement = new ESBTPPaiement($validated);
            $paiement->numero_recu = $numeroRecu;
            $paiement->status = 'en_attente';
            $paiement->created_by = Auth::id();
            $paiement->save();
            
            DB::commit();
            
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('success', 'Paiement enregistré avec succès. Numéro de reçu : ' . $numeroRecu);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement du paiement : ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement du paiement.'])
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un paiement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paiement = ESBTPPaiement::with([
            'etudiant.user', 
            'inscription.anneeUniversitaire', 
            'inscription.filiere', 
            'inscription.niveauEtude',
            'validatedBy',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);
        
        return view('esbtp.paiements.show', compact('paiement'));
    }

    /**
     * Affiche le formulaire de modification d'un paiement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paiement = ESBTPPaiement::with([
            'etudiant.user', 
            'inscription.anneeUniversitaire', 
            'inscription.filiere', 
            'inscription.niveauEtude'
        ])->findOrFail($id);
        
        // Vérifier si le paiement peut être modifié
        if ($paiement->status === 'validé') {
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('error', 'Ce paiement a déjà été validé et ne peut plus être modifié.');
        }
        
        return view('esbtp.paiements.edit', compact('paiement'));
    }

    /**
     * Met à jour un paiement existant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $paiement = ESBTPPaiement::findOrFail($id);
        
        // Vérifier si le paiement peut être modifié
        if ($paiement->status === 'validé') {
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('error', 'Ce paiement a déjà été validé et ne peut plus être modifié.');
        }
        
        // Valider les données du formulaire
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'reference_paiement' => 'nullable|string',
            'tranche' => 'nullable|string',
            'motif' => 'required|string',
            'commentaire' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le paiement
            $paiement->fill($validated);
            $paiement->updated_by = Auth::id();
            $paiement->save();
            
            DB::commit();
            
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('success', 'Paiement mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du paiement : ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du paiement.'])
                ->withInput();
        }
    }

    /**
     * Valide un paiement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function valider($id)
    {
        $paiement = ESBTPPaiement::findOrFail($id);
        
        // Vérifier si le paiement peut être validé
        if ($paiement->status === 'validé') {
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('info', 'Ce paiement a déjà été validé.');
        }
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le statut du paiement
            $paiement->status = 'validé';
            $paiement->date_validation = Carbon::now();
            $paiement->validated_by = Auth::id();
            $paiement->updated_by = Auth::id();
            $paiement->save();
            
            DB::commit();
            
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('success', 'Paiement validé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la validation du paiement : ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue lors de la validation du paiement.'])
                ->withInput();
        }
    }

    /**
     * Rejette un paiement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rejeter(Request $request, $id)
    {
        $paiement = ESBTPPaiement::findOrFail($id);
        
        // Vérifier si le paiement peut être rejeté
        if ($paiement->status === 'validé') {
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('error', 'Ce paiement a déjà été validé et ne peut pas être rejeté.');
        }
        
        // Valider les données du formulaire
        $validated = $request->validate([
            'commentaire' => 'required|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le statut du paiement
            $paiement->status = 'rejeté';
            $paiement->commentaire = $validated['commentaire'];
            $paiement->updated_by = Auth::id();
            $paiement->save();
            
            DB::commit();
            
            return redirect()->route('esbtp.paiements.show', $paiement->id)
                ->with('success', 'Paiement rejeté avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du rejet du paiement : ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue lors du rejet du paiement.'])
                ->withInput();
        }
    }

    /**
     * Génère un reçu de paiement au format PDF.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function genererRecu($id)
    {
        $paiement = ESBTPPaiement::with([
            'etudiant.user', 
            'inscription.anneeUniversitaire', 
            'inscription.filiere', 
            'inscription.niveauEtude',
            'validatedBy'
        ])->findOrFail($id);
        
        // Générer le PDF
        $pdf = PDF::loadView('esbtp.paiements.recu', compact('paiement'));
        
        // Définir le nom du fichier
        $filename = 'Recu_' . $paiement->numero_recu . '.pdf';
        
        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }

    /**
     * Récupère les paiements d'un étudiant.
     *
     * @param  int  $etudiantId
     * @return \Illuminate\Http\Response
     */
    public function paiementsEtudiant($etudiantId)
    {
        $etudiant = ESBTPEtudiant::with(['user', 'inscriptions.anneeUniversitaire'])->findOrFail($etudiantId);
        
        $paiements = ESBTPPaiement::with(['inscription.anneeUniversitaire'])
            ->where('etudiant_id', $etudiantId)
            ->orderBy('date_paiement', 'desc')
            ->get();
        
        // Calculer le total des paiements validés
        $totalValide = $paiements->where('status', 'validé')->sum('montant');
        
        return view('esbtp.paiements.etudiant', compact('etudiant', 'paiements', 'totalValide'));
    }
} 