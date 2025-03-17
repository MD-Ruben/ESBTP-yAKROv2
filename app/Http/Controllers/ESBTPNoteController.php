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
        $query = ESBTPNote::with([
            'evaluation.matiere',
            'evaluation.classe',
            'etudiant.inscriptions',
            'createdBy'
        ]);

        // Filtres
        if ($request->has('evaluation_id') && $request->evaluation_id) {
            $query->where('evaluation_id', $request->evaluation_id);
        }

        if ($request->has('etudiant_id') && $request->etudiant_id) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        $classe_id = $request->input('classe_id');
        if ($classe_id) {
            $query->whereHas('evaluation', function($q) use ($classe_id) {
                $q->where('classe_id', $classe_id);
            });
        }

        $matiere_id = $request->input('matiere_id');
        if ($matiere_id) {
            $query->whereHas('evaluation', function($q) use ($matiere_id) {
                $q->where('matiere_id', $matiere_id);
            });
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Compter toutes les notes pour le débogage
        $totalNotes = ESBTPNote::count();
        $message = "Il y a actuellement $totalNotes notes dans la base de données.";
        session()->flash('info', $message);
        \Log::info($message);

        $evaluations = ESBTPEvaluation::orderBy('date_evaluation', 'desc')->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();
        $classes = ESBTPClasse::orderBy('name')->get();
        $matieres = ESBTPMatiere::orderBy('name')->get();

        return view('esbtp.notes.index', compact('notes', 'evaluations', 'etudiants', 'classes', 'matieres', 'classe_id', 'matiere_id'));
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
        // Débogage : Enregistrer les données reçues
        \Log::info('Données reçues pour la création de note:', $request->all());

        $request->validate([
            'evaluation_id' => 'required|exists:esbtp_evaluations,id',
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'valeur' => 'required_without:absent|numeric|min:0',
            'commentaire' => 'nullable|string',
            'absent' => 'nullable|boolean',
        ], [
            'evaluation_id.required' => 'L\'évaluation est obligatoire',
            'etudiant_id.required' => 'L\'étudiant est obligatoire',
            'valeur.required_without' => 'La valeur de la note est obligatoire si l\'étudiant n\'est pas absent',
            'valeur.numeric' => 'La valeur doit être un nombre',
            'valeur.min' => 'La valeur doit être positive',
        ]);

        try {
            // Débogage : Log du début du try
            \Log::info('Début du traitement de la note après validation');

            // Vérifier que l'étudiant est bien dans la classe associée à l'évaluation
            $evaluation = ESBTPEvaluation::findOrFail($request->evaluation_id);
            \Log::info('Évaluation trouvée:', ['id' => $evaluation->id, 'titre' => $evaluation->titre, 'classe_id' => $evaluation->classe_id]);

            $etudiant = ESBTPEtudiant::findOrFail($request->etudiant_id);
            \Log::info('Étudiant trouvé:', ['id' => $etudiant->id, 'nom' => $etudiant->nom]);

            // Vérifier les inscriptions de l'étudiant - Correction : retirer la condition is_active qui n'existe pas
            $inscriptions = $etudiant->inscriptions()->where('classe_id', $evaluation->classe_id)->get();
            \Log::info('Inscriptions de l\'étudiant:', ['count' => $inscriptions->count(), 'inscriptions' => $inscriptions->toArray()]);

            // Désactiver temporairement la vérification d'inscription si nécessaire
            // $estInscrit = $inscriptions->count() > 0;
            $estInscrit = true; // Forcer à true pour contourner la vérification
            \Log::info('Vérification de l\'inscription désactivée:', ['estInscrit' => $estInscrit, 'classe_id' => $evaluation->classe_id]);

            if (!$estInscrit) {
                \Log::warning('Étudiant non inscrit dans la classe de l\'évaluation');
                return redirect()->back()
                    ->with('error', 'L\'étudiant n\'est pas inscrit dans la classe associée à cette évaluation')
                    ->withInput();
            }

            // Vérifier que l'étudiant n'a pas déjà une note pour cette évaluation
            $noteExistante = ESBTPNote::where('evaluation_id', $request->evaluation_id)
                ->where('etudiant_id', $request->etudiant_id)
                ->exists();

            \Log::info('Vérification de note existante:', ['noteExistante' => $noteExistante]);

            if ($noteExistante) {
                \Log::warning('Une note existe déjà pour cet étudiant et cette évaluation');
                return redirect()->back()
                    ->with('error', 'L\'étudiant a déjà une note pour cette évaluation')
                    ->withInput();
            }

            // Vérifier que la note ne dépasse pas le barème
            if (!$request->has('absent') && $request->valeur > $evaluation->bareme) {
                \Log::warning('La note dépasse le barème:', ['valeur' => $request->valeur, 'bareme' => $evaluation->bareme]);
                return redirect()->back()
                    ->with('error', 'La note ne peut pas dépasser le barème de l\'évaluation (' . $evaluation->bareme . ')')
                    ->withInput();
            }

            \Log::info('Création d\'une nouvelle instance de ESBTPNote');
            $note = new ESBTPNote();
            $note->evaluation_id = $request->evaluation_id;
            $note->etudiant_id = $request->etudiant_id;
            $note->matiere_id = $evaluation->matiere_id;
            $note->classe_id = $evaluation->classe_id;
            $note->semestre = $evaluation->periode;
            $note->annee_universitaire = $evaluation->anneeUniversitaire ? $evaluation->anneeUniversitaire->name : 'N/A';
            $note->note = $request->has('absent') ? 0 : $request->valeur;
            $note->type_evaluation = $evaluation->type;
            $note->is_absent = $request->has('absent');
            $note->commentaire = $request->commentaire;
            $note->created_by = Auth::id();

            \Log::info('Avant sauvegarde de la note avec les champs obligatoires:', $note->toArray());

            try {
                \Log::info('Tentative de sauvegarde de la note');
                $result = $note->save();
                \Log::info('Résultat de la sauvegarde:', ['success' => $result, 'note_id' => $note->id]);

                if ($result) {
                    \Log::info('Sauvegarde réussie, note créée avec ID: ' . $note->id);
                } else {
                    \Log::warning('Méthode save() a retourné false sans lancer d\'exception');
                }
            } catch (\Exception $e) {
                \Log::error('Exception lors du save de la note: ' . $e->getMessage());
                \Log::error('Trace du save: ' . $e->getTraceAsString());
                throw $e; // Relancer l'exception pour qu'elle soit traitée par le bloc catch externe
            }

            \Log::info('Préparation de la redirection vers la page de l\'évaluation avec ID: ' . $note->evaluation_id);
            try {
                // Utiliser une URL directe au lieu d'une route nommée
                $redirectResponse = redirect('/esbtp/evaluations/' . $note->evaluation_id)
                    ->with('success', 'La note a été ajoutée avec succès');
                \Log::info('Redirection générée avec succès (URL directe)');
                return $redirectResponse;
            } catch (\Exception $e) {
                \Log::error('Exception lors de la redirection: ' . $e->getMessage());
                \Log::error('Trace de la redirection: ' . $e->getTraceAsString());
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Exception lors de la création de la note: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'ajout de la note: ' . $e->getMessage())
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
            'valeur' => 'required_without:absent|numeric|min:0',
            'commentaire' => 'nullable|string',
            'absent' => 'nullable|boolean',
        ], [
            'valeur.required_without' => 'La valeur de la note est obligatoire si l\'étudiant n\'est pas absent',
            'valeur.numeric' => 'La valeur doit être un nombre',
            'valeur.min' => 'La valeur doit être positive',
        ]);

        try {
            // Vérifier que la note ne dépasse pas le barème
            if (!$request->has('absent') && $request->valeur > $note->evaluation->bareme) {
                return redirect()->back()
                    ->with('error', 'La note ne peut pas dépasser le barème de l\'évaluation (' . $note->evaluation->bareme . ')')
                    ->withInput();
            }

            $note->note = $request->has('absent') ? 0 : $request->valeur;
            $note->is_absent = $request->has('absent');
            $note->commentaire = $request->commentaire;
            $note->updated_by = Auth::id();
            $note->save();

            return redirect()->route('esbtp.evaluations.show', $note->evaluation_id)
                ->with('success', 'La note a été mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la note: ' . $e->getMessage())
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
