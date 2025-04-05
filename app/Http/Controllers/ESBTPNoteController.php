<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPNote;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPAnneeUniversitaire;

class ESBTPNoteController extends Controller
{
    /**
     * Affiche la liste des notes avec filtre par classe et matière
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Initialize query with proper eager loading
        $query = ESBTPNote::whereHas('evaluation')  // Only fetch notes with valid evaluations
            ->with([
                'evaluation.matiere',
                'evaluation.classe',
                'etudiant',
                'createdBy'
            ]);

        // Apply filters
        if ($request->filled('classe_id')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        if ($request->filled('matiere_id')) {
            $query->whereHas('evaluation', function($q) use ($request) {
                $q->whereHas('matiere', function($mq) use ($request) {
                    $mq->where('id', $request->matiere_id);
                });
            });
        }

        // Get the paginated results
        $notes = $query->latest()->paginate(50);

        // Get filter options
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $matieres = ESBTPMatiere::orderBy('name')->get();

        return view('esbtp.notes.index', compact('notes', 'classes', 'matieres'));
    }

    /**
     * Affiche le formulaire de création d'une note.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $evaluations = ESBTPEvaluation::with(['classe', 'matiere'])
            ->orderBy('date_evaluation', 'desc')
            ->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();

        // Ajouter un message flash pour tester
        session()->flash('info', 'Formulaire de création de note chargé. Veuillez remplir tous les champs requis.');

        return view('esbtp.notes.create', compact('evaluations', 'etudiants'));
    }

    /**
     * Enregistre une nouvelle note.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'evaluation_id' => 'required|exists:esbtp_evaluations,id',
            'note' => 'required_unless:is_absent,1|numeric|min:0',
            'is_absent' => 'sometimes|boolean',
            'commentaire' => 'nullable|string',
        ], [
            'etudiant_id.required' => 'L\'étudiant est obligatoire',
            'evaluation_id.required' => 'L\'évaluation est obligatoire',
            'note.required_unless' => 'La note est obligatoire si l\'étudiant n\'est pas absent',
            'note.numeric' => 'La note doit être un nombre',
            'note.min' => 'La note doit être positive',
            'is_absent.boolean' => 'Le statut d\'absence doit être un booléen',
        ]);

        try {
            // Débogage : Log du début du try
            \Log::info('Début du traitement de la note après validation');

            // Vérifier si l'étudiant a déjà une note pour cette évaluation
            $existingNote = ESBTPNote::where('etudiant_id', $request->etudiant_id)
                ->where('evaluation_id', $request->evaluation_id)
                ->first();

            if ($existingNote) {
                return redirect()->back()
                    ->with('error', 'Cet étudiant a déjà une note pour cette évaluation.')
                    ->withInput();
            }

            // Récupérer l'évaluation pour obtenir le barème et la classe
            $evaluation = ESBTPEvaluation::findOrFail($request->evaluation_id);

            // Récupérer la classe associée à l'évaluation
            $classe_id = $evaluation->classe_id;

            // Récupérer la période de l'évaluation
            $semestre = $evaluation->periode;

            // Créer la note
            $note = new ESBTPNote();
            $note->etudiant_id = $request->etudiant_id;
            $note->evaluation_id = $request->evaluation_id;
            $note->classe_id = $classe_id; // Utiliser la classe de l'évaluation
            $note->matiere_id = $evaluation->matiere_id; // Ajouter le matiere_id de l'évaluation
            $note->semestre = $semestre; // Utiliser la période de l'évaluation
            $note->note = $request->is_absent ? 0 : $request->note;
            $note->is_absent = $request->has('is_absent') ? 1 : 0;
            $note->commentaire = $request->commentaire;
            $note->created_by = Auth::id();
            $note->type_evaluation = $evaluation->type; // Ajouter le type d'évaluation
            $note->annee_universitaire = $evaluation->anneeUniversitaire ? $evaluation->anneeUniversitaire->name : 'N/A'; // Ajouter l'année universitaire
            $note->save();

            // Débogage : Log des détails de la note créée
            \Log::info('Note créée', [
                'id' => $note->id,
                'etudiant_id' => $note->etudiant_id,
                'evaluation_id' => $note->evaluation_id,
                'note' => $note->note,
                'is_absent' => $note->is_absent,
                'classe_id' => $note->classe_id,
                'semestre' => $note->semestre
            ]);

            return redirect()->route('esbtp.notes.index')
                ->with('success', 'Note créée avec succès.');
        } catch (\Exception $e) {
            // Débogage : Log de l'erreur
            \Log::error('Erreur lors de la création de la note : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la note : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche une note spécifique.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPNote $note)
    {
        $note->load(['evaluation.matiere', 'evaluation.classe', 'etudiant', 'createdBy', 'updatedBy']);
        return view('esbtp.notes.show', compact('note'));
    }

    /**
     * Affiche le formulaire de modification d'une note.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPNote $note)
    {
        $note->load(['evaluation.matiere', 'evaluation.classe', 'etudiant']);
        return view('esbtp.notes.edit', compact('note'));
    }

    /**
     * Met à jour une note spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPNote $note)
    {
        $request->validate([
            'note' => 'required_unless:is_absent,1|numeric|min:0',
            'is_absent' => 'sometimes|boolean',
            'commentaire' => 'nullable|string',
        ]);

        try {
            // Récupérer l'évaluation associée à cette note
            $evaluation = $note->evaluation;

            if (!$evaluation) {
                return redirect()->back()
                    ->with('error', 'Évaluation introuvable pour cette note.')
                    ->withInput();
            }

            // Synchroniser le semestre avec la période de l'évaluation
            $note->semestre = $evaluation->periode;

            // Mettre à jour les autres champs
            $note->note = $request->is_absent ? 0 : $request->note;
            $note->is_absent = $request->has('is_absent') ? 1 : 0;
            $note->commentaire = $request->commentaire;
            $note->updated_by = Auth::id();
            $note->save();

            // Débogage : Log des détails de la note mise à jour
            \Log::info('Note mise à jour', [
                'id' => $note->id,
                'etudiant_id' => $note->etudiant_id,
                'evaluation_id' => $note->evaluation_id,
                'note' => $note->note,
                'is_absent' => $note->is_absent,
                'semestre' => $note->semestre
            ]);

            return redirect()->route('esbtp.notes.index')
                ->with('success', 'Note mise à jour avec succès.');
        } catch (\Exception $e) {
            // Débogage : Log de l'erreur
            \Log::error('Erreur lors de la mise à jour de la note : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la note : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une note spécifique.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPNote $note)
    {
        try {
            $note->delete();
            return redirect()->route('esbtp.notes.index')->with('success', 'Note supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de la note: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la page de saisie rapide des notes pour une évaluation.
     *
     * @param ESBTPEvaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function saisieRapide(ESBTPEvaluation $evaluation)
    {
        $evaluation->load(['classe', 'matiere', 'notes.etudiant']);

        // Récupérer tous les étudiants de la classe
        $etudiants = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($evaluation) {
                $query->where('classe_id', $evaluation->classe_id);
            })
            ->with(['notes' => function($query) use ($evaluation) {
                $query->where('evaluation_id', $evaluation->id);
            }])
            ->orderBy('nom')
            ->get();

        // Récupérer toutes les notes de l'évaluation
        $notes = $evaluation->notes;

        return view('esbtp.notes.saisie-rapide', compact('evaluation', 'etudiants', 'notes'));
    }

    /**
     * Enregistre les notes saisies en masse pour une évaluation.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function enregistrerSaisieRapide(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|exists:esbtp_evaluations,id',
            'notes' => 'required|array',
            'notes.*.etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'notes.*.valeur' => 'required_without:notes.*.absent|nullable|numeric|min:0',
            'notes.*.commentaire' => 'nullable|string',
            'notes.*.absent' => 'nullable|boolean',
        ], [
            'notes.*.valeur.required_without' => 'La valeur de la note est obligatoire si l\'étudiant n\'est pas absent',
            'notes.*.valeur.numeric' => 'La valeur doit être un nombre',
            'notes.*.valeur.min' => 'La valeur doit être positive',
        ]);

        $evaluation = ESBTPEvaluation::findOrFail($request->evaluation_id);

        DB::beginTransaction();
        try {
            foreach ($request->notes as $noteData) {
                // Vérifier si nous avons une valeur de note ou si l'étudiant est marqué comme absent
                $hasValue = isset($noteData['valeur']) && $noteData['valeur'] !== null && $noteData['valeur'] !== '';
                $isAbsent = isset($noteData['absent']) && $noteData['absent'] == '1';

                // Ignorer les entrées sans valeur et non marquées comme absentes
                if (!$hasValue && !$isAbsent) {
                    continue;
                }

                $etudiantId = $noteData['etudiant_id'];

                // Vérifier si l'étudiant a déjà une note pour cette évaluation
                $note = ESBTPNote::where('evaluation_id', $evaluation->id)
                    ->where('etudiant_id', $etudiantId)
                    ->first();

                if ($note) {
                    // Mise à jour de la note existante
                    $note->note = $isAbsent ? 0 : $noteData['valeur'];
                    $note->is_absent = $isAbsent;
                    $note->commentaire = $noteData['commentaire'] ?? null;
                    $note->updated_by = Auth::id();

                    // S'assurer que tous les champs requis sont définis
                    if (!$note->matiere_id) {
                        $note->matiere_id = $evaluation->matiere_id;
                    }
                    if (!$note->classe_id) {
                        $note->classe_id = $evaluation->classe_id;
                    }
                    if (!$note->semestre) {
                        $note->semestre = $evaluation->periode;
                    }
                    if (!$note->annee_universitaire) {
                        $note->annee_universitaire = $evaluation->anneeUniversitaire ? $evaluation->anneeUniversitaire->name : 'N/A';
                    }
                    if (!$note->type_evaluation) {
                        $note->type_evaluation = $evaluation->type;
                    }

                    $note->save();
                } else {
                    // Création d'une nouvelle note
                    $note = new ESBTPNote();
                    $note->evaluation_id = $evaluation->id;
                    $note->etudiant_id = $etudiantId;
                    $note->matiere_id = $evaluation->matiere_id;
                    $note->classe_id = $evaluation->classe_id;
                    $note->semestre = $evaluation->periode;
                    $note->annee_universitaire = $evaluation->anneeUniversitaire ? $evaluation->anneeUniversitaire->name : 'N/A';
                    $note->note = $isAbsent ? 0 : $noteData['valeur'];
                    $note->type_evaluation = $evaluation->type;
                    $note->is_absent = $isAbsent;
                    $note->commentaire = $noteData['commentaire'] ?? null;
                    $note->created_by = Auth::id();
                    $note->save();
                }
            }

            DB::commit();
            return redirect()->route('esbtp.evaluations.show', $evaluation)
                ->with('success', 'Les notes ont été enregistrées avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des notes: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les notes de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function studentGrades(Request $request)
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();

        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }

        $notes = ESBTPNote::where('etudiant_id', $etudiant->id)
            ->with(['evaluation', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('esbtp.etudiants.notes', compact('notes', 'etudiant'));
    }

    /**
     * Affiche le formulaire de saisie rapide des notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function saisieRapideForm()
    {
        return view('esbtp.notes.saisie-rapide-form');
    }
}
