<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPEtudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPAnneeUniversitaire;

class ESBTPEvaluationController extends Controller
{
    /**
     * Affiche la liste des évaluations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = ESBTPEvaluation::with(['classe', 'matiere', 'createdBy'])
            ->orderBy('date_evaluation', 'desc');
        
        // Filtres
        if (request()->has('classe_id') && request('classe_id') != '') {
            $query->where('classe_id', request('classe_id'));
        }
        
        if (request()->has('matiere_id') && request('matiere_id') != '') {
            $query->where('matiere_id', request('matiere_id'));
        }
        
        if (request()->has('type') && request('type') != '') {
            $query->where('type', request('type'));
        }
        
        if (request()->has('is_published') && request('is_published') != '') {
            $query->where('is_published', request('is_published'));
        }
        
        if (request()->has('date_debut') && request('date_debut') != '') {
            $query->where('date_evaluation', '>=', request('date_debut'));
        }
        
        if (request()->has('date_fin') && request('date_fin') != '') {
            $query->where('date_evaluation', '<=', request('date_fin'));
        }
        
        // Paginer les résultats
        $evaluations = $query->paginate(15);
        
        // Statistiques
        $totalEvaluations = ESBTPEvaluation::count();
        $evaluationsPubliees = ESBTPEvaluation::where('is_published', true)->count();
        $examens = ESBTPEvaluation::where('type', 'examen')->count();
        $devoirs = ESBTPEvaluation::where('type', 'devoir')->count();
        
        // Récupération des classes pour le filtre
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        
        // Récupération des matières pour le filtre
        $matieres = ESBTPMatiere::orderBy('name')->get();
        
        // Récupération des types d'évaluation pour le filtre
        $types = ESBTPEvaluation::select('type')->distinct()->pluck('type');
        
        return view('esbtp.evaluations.index', compact(
            'evaluations', 
            'classes', 
            'matieres', 
            'types', 
            'totalEvaluations', 
            'evaluationsPubliees', 
            'examens', 
            'devoirs'
        ));
    }

    /**
     * Affiche le formulaire de création d'une évaluation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        
        // Vérifier s'il y a des matières disponibles, sinon en créer une pour les tests
        $matieresCount = ESBTPMatiere::count();
        
        if ($matieresCount == 0) {
            // Créer une matière de test si aucune n'existe
            try {
                $testMatiere = new ESBTPMatiere();
                $testMatiere->name = 'Matière Test';
                $testMatiere->nom = 'Matière Test';
                $testMatiere->code = 'TEST001';
                $testMatiere->coefficient = 1;
                $testMatiere->is_active = true;
                $testMatiere->save();
                
                \Log::info('Matière de test créée avec succès');
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création de la matière de test: ' . $e->getMessage());
            }
        }
        
        $matieres = ESBTPMatiere::where('is_active', true)->orderBy('name')->get();
        
        // Si toujours pas de matières, créer une entrée directement en base
        if ($matieres->isEmpty()) {
            try {
                \DB::table('esbtp_matieres')->insert([
                    'name' => 'Matière Test',
                    'nom' => 'Matière Test',
                    'code' => 'TEST001',
                    'coefficient' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $matieres = ESBTPMatiere::all();
                \Log::info('Matière insérée directement en base');
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'insertion directe: ' . $e->getMessage());
            }
        }
        
        // Préparer les matières pour le JavaScript
        $matieresJson = $matieres->map(function ($matiere) {
            return [
                'id' => $matiere->id,
                'name' => $matiere->nom ?? $matiere->name ?? 'Matière ' . $matiere->id,
                'code' => $matiere->code ?? '',
                'coefficient' => $matiere->coefficient ?? 1
            ];
        });
        
        return view('esbtp.evaluations.create', compact('classes', 'matieres', 'matieresJson'));
    }

    /**
     * Enregistre une nouvelle évaluation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        \Log::info('Données du formulaire d\'évaluation:', $request->all());
        
        // Log l'état de la classe ESBTPEvaluation
        \Log::info('Attributs attendus dans ESBTPEvaluation:', [
            'fillable' => (new \App\Models\ESBTPEvaluation())->getFillable(),
            'colonnes_table' => \Schema::getColumnListing('esbtp_evaluations')
        ]);
        
        $validator = \Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:devoir,examen,projet,tp,controle,quiz',
            'date_evaluation' => 'required|date',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'coefficient' => 'required|numeric|min:0',
            'bareme' => 'required|numeric|min:0',
            'duree_minutes' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'type.required' => 'Le type d\'évaluation est obligatoire',
            'type.in' => 'Le type d\'évaluation doit être valide',
            'date_evaluation.required' => 'La date est obligatoire',
            'date_evaluation.date' => 'Le format de la date est invalide',
            'classe_id.required' => 'La classe est obligatoire',
            'classe_id.exists' => 'La classe sélectionnée n\'existe pas',
            'matiere_id.required' => 'La matière est obligatoire',
            'matiere_id.exists' => 'La matière sélectionnée n\'existe pas',
            'coefficient.required' => 'Le coefficient est obligatoire',
            'bareme.required' => 'Le barème est obligatoire',
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation échouée:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $evaluation = new ESBTPEvaluation();
            $evaluation->titre = $request->titre;
            $evaluation->description = $request->description;
            $evaluation->type = $request->type;
            $evaluation->date_evaluation = $request->date_evaluation;
            $evaluation->coefficient = $request->coefficient;
            $evaluation->bareme = $request->bareme;
            $evaluation->duree_minutes = $request->duree_minutes;
            $evaluation->classe_id = $request->classe_id;
            $evaluation->matiere_id = $request->matiere_id;
            $evaluation->created_by = \Auth::id();
            $evaluation->is_published = $request->has('is_published') ? 1 : 0;
            
            \Log::info('Tentative de sauvegarde de l\'évaluation:', [
                'titre' => $evaluation->titre,
                'matiere_id' => $evaluation->matiere_id,
                'classe_id' => $evaluation->classe_id,
                'date_evaluation' => $evaluation->date_evaluation,
                'created_by' => $evaluation->created_by,
                'duree_minutes' => $evaluation->duree_minutes,
                'is_published' => $evaluation->is_published
            ]);
            
            // Log aussi les attributs du modèle avant sauvegarde
            \Log::info('Attributs du modèle avant sauvegarde:', $evaluation->getAttributes());
            
            $evaluation->save();
            
            \Log::info('Évaluation créée avec succès. ID: ' . $evaluation->id);
            
            return redirect()->route('esbtp.evaluations.index')
                ->with('success', 'L\'évaluation a été créée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'évaluation: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'évaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'une évaluation spécifique.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPEvaluation $evaluation)
    {
        $evaluation->load(['classe', 'matiere', 'createdBy', 'notes.etudiant']);
        
        // Récupérer les étudiants qui n'ont pas encore de note pour cette évaluation
        $etudiantsAvecNote = $evaluation->notes->pluck('etudiant_id')->toArray();
        $etudiantsSansNote = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($evaluation) {
                $query->where('classe_id', $evaluation->classe_id);
            })
            ->whereNotIn('id', $etudiantsAvecNote)
            ->orderBy('nom')
            ->get();
        
        return view('esbtp.evaluations.show', compact('evaluation', 'etudiantsSansNote'));
    }

    /**
     * Affiche le formulaire de modification d'une évaluation.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPEvaluation $evaluation)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $matieres = ESBTPMatiere::orderBy('name')->get();
        
        return view('esbtp.evaluations.edit', compact('evaluation', 'classes', 'matieres'));
    }

    /**
     * Met à jour une évaluation spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPEvaluation $evaluation)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:devoir,examen,projet,tp,controle',
            'date_evaluation' => 'required|date',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'coefficient' => 'required|numeric|min:0',
            'bareme' => 'required|numeric|min:0',
            'duree_minutes' => 'nullable|integer|min:0',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'type.required' => 'Le type d\'évaluation est obligatoire',
            'date_evaluation.required' => 'La date est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'matiere_id.required' => 'La matière est obligatoire',
            'coefficient.required' => 'Le coefficient est obligatoire',
            'bareme.required' => 'Le barème est obligatoire',
        ]);

        try {
            $hasNotes = $evaluation->notes()->count() > 0;
            
            // Si l'évaluation a déjà des notes et que l'utilisateur essaie de changer la classe ou la matière
            if ($hasNotes && ($evaluation->classe_id != $request->classe_id || $evaluation->matiere_id != $request->matiere_id)) {
                return redirect()->back()
                    ->with('error', 'Impossible de modifier la classe ou la matière car des notes sont déjà associées à cette évaluation')
                    ->withInput();
            }
            
            $evaluation->titre = $request->titre;
            $evaluation->description = $request->description;
            $evaluation->type = $request->type;
            $evaluation->date_evaluation = $request->date_evaluation;
            $evaluation->coefficient = $request->coefficient;
            $evaluation->bareme = $request->bareme;
            $evaluation->duree_minutes = $request->duree_minutes;
            
            // Mettre à jour la classe et la matière uniquement s'il n'y a pas de notes
            if (!$hasNotes) {
                $evaluation->classe_id = $request->classe_id;
                $evaluation->matiere_id = $request->matiere_id;
            }
            
            $evaluation->save();
            
            return redirect()->route('esbtp.evaluations.show', $evaluation)
                ->with('success', 'L\'évaluation a été mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'évaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une évaluation spécifique.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPEvaluation $evaluation)
    {
        try {
            $evaluation->delete();
            return redirect()->route('esbtp.evaluations.index')->with('success', 'Évaluation supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les examens de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function etudiant(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Récupérer l'étudiant associé à l'utilisateur
        $etudiant = $user->etudiant;
        
        if (!$etudiant) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre compte utilisateur n\'est pas associé à un étudiant.');
        }
        
        // Récupérer la classe de l'étudiant
        $inscription = $etudiant->inscriptions()->where('statut', 'active')->first();
        
        if (!$inscription || !$inscription->classe) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes inscrit dans aucune classe pour le moment.');
        }
        
        $classe = $inscription->classe;
        
        // Récupérer les paramètres de filtre
        $anneeId = $request->input('annee_universitaire_id', 
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode = $request->input('periode');
        $statut = $request->input('statut');
        
        // Initialiser la requête pour récupérer les évaluations
        $query = ESBTPEvaluation::with(['matiere', 'classe'])
            ->where('classe_id', $classe->id);
        
        // Filtrer par année universitaire
        if ($anneeId) {
            $query->where('annee_universitaire_id', $anneeId);
        }
        
        // Filtrer par période
        if ($periode) {
            $query->where('periode', $periode);
        }
        
        // Filtrer par statut
        if ($statut) {
            if ($statut === 'passees') {
                $query->where('date_evaluation', '<', now());
            } elseif ($statut === 'a_venir') {
                $query->where('date_evaluation', '>=', now());
            }
        }
        
        // Récupérer les évaluations paginées
        $evaluations = $query->orderBy('date_evaluation', 'asc')->paginate(10);
        
        // Récupérer toutes les années universitaires pour le filtre
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        
        // Compter les évaluations passées et à venir
        $evaluationsPassees = ESBTPEvaluation::where('classe_id', $classe->id)
            ->where('date_evaluation', '<', now())->count();
            
        $evaluationsAVenir = ESBTPEvaluation::where('classe_id', $classe->id)
            ->where('date_evaluation', '>=', now())->count();
        
        // Prochaine évaluation
        $prochaineEvaluation = ESBTPEvaluation::where('classe_id', $classe->id)
            ->where('date_evaluation', '>=', now())
            ->orderBy('date_evaluation', 'asc')
            ->first();
        
        return view('esbtp.evaluations.etudiant', compact(
            'etudiant',
            'classe',
            'evaluations',
            'anneesUniversitaires',
            'anneeId',
            'periode',
            'statut',
            'evaluationsPassees',
            'evaluationsAVenir',
            'prochaineEvaluation'
        ));
    }
} 