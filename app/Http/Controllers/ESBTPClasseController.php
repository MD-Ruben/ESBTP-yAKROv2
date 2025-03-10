<?php

namespace App\Http\Controllers;

use App\Models\ESBTPClasse;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPMatiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESBTPClasseController extends Controller
{
    /**
     * Affiche la liste des classes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = ESBTPClasse::with(['filiere', 'niveau', 'annee'])->get();

        return view('esbtp.classes.index', compact('classes'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle classe.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::where('is_active', true)->get();

        return view('esbtp.classes.create', compact('filieres', 'niveaux', 'annees'));
    }

    /**
     * Enregistre une nouvelle classe dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_classes,code',
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Ajouter les champs de traçabilité
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();

        // Créer la nouvelle classe
        $classe = ESBTPClasse::create($validatedData);

        // Récupérer les matières associées aux niveaux sélectionnés
        $matieres = ESBTPMatiere::whereHas('niveaux', function ($query) use ($request) {
            $query->where('esbtp_niveau_etudes.id', $request->niveau_etude_id);
        })->get();

        // Associer les matières à la classe avec leurs coefficients et heures par défaut
        foreach ($matieres as $matiere) {
            $classe->matieres()->attach($matiere->id, [
                'coefficient' => $matiere->coefficient_default,
                'total_heures' => $matiere->total_heures_default,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une classe spécifique.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPClasse $classe)
    {
        $classe->load(['filiere', 'niveau', 'annee', 'matieres', 'etudiants', 'inscriptions', 'emploisDuTemps']);

        return view('esbtp.classes.show', compact('classe'));
    }

    /**
     * Affiche le formulaire de modification d'une classe existante.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPClasse $classe)
    {
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::where('is_active', true)->get();

        return view('esbtp.classes.edit', compact('classe', 'filieres', 'niveaux', 'annees'));
    }

    /**
     * Met à jour la classe spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPClasse $classe)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_classes,code,' . $classe->id,
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Mettre à jour les champs de traçabilité
        $validatedData['updated_by'] = Auth::id();

        // Mettre à jour la classe
        $classe->update($validatedData);

        // Si le niveau a changé, mettre à jour les matières
        if ($classe->isDirty('niveau_etude_id')) {
            // Récupérer les matières associées au niveau sélectionné
            $matieres = ESBTPMatiere::whereHas('niveaux', function ($query) use ($request) {
                $query->where('esbtp_niveau_etudes.id', $request->niveau_etude_id);
            })->get();

            // Réinitialiser les matières associées à la classe
            $classe->matieres()->detach();

            // Associer les nouvelles matières à la classe
            foreach ($matieres as $matiere) {
                $classe->matieres()->attach($matiere->id, [
                    'coefficient' => $matiere->coefficient_default,
                    'total_heures' => $matiere->total_heures_default,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été mise à jour avec succès.');
    }

    /**
     * Supprime la classe spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPClasse $classe)
    {
        // Vérifier si des étudiants sont inscrits dans cette classe
        if ($classe->inscriptions()->count() > 0) {
            return redirect()->route('esbtp.classes.index')
                ->with('error', 'Impossible de supprimer cette classe car elle contient des étudiants inscrits.');
        }

        // Détacher toutes les matières
        $classe->matieres()->detach();

        // Supprimer la classe
        $classe->delete();

        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été supprimée avec succès.');
    }

    /**
     * Affiche la page de gestion des matières associées à une classe.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function matieres(ESBTPClasse $classe)
    {
        $classe->load('matieres');
        $allMatieres = ESBTPMatiere::where('is_active', true)->get();

        return view('esbtp.classes.matieres', compact('classe', 'allMatieres'));
    }

    /**
     * Met à jour les matières et leurs coefficients pour une classe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function updateMatieres(Request $request, ESBTPClasse $classe)
    {
        $validatedData = $request->validate([
            'matieres' => 'required|array',
            'matieres.*.id' => 'required|exists:esbtp_matieres,id',
            'matieres.*.coefficient' => 'required|numeric|min:0',
            'matieres.*.total_heures' => 'required|integer|min:0',
            'matieres.*.is_active' => 'boolean',
        ]);

        // Réinitialiser les matières existantes
        $classe->matieres()->detach();

        // Ajouter les nouvelles matières avec leurs coefficients
        foreach ($validatedData['matieres'] as $matiere) {
            $classe->matieres()->attach($matiere['id'], [
                'coefficient' => $matiere['coefficient'],
                'total_heures' => $matiere['total_heures'],
                'is_active' => isset($matiere['is_active']) ? $matiere['is_active'] : true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('esbtp.classes.show', $classe)
            ->with('success', 'Les matières ont été mises à jour avec succès.');
    }

    /**
     * Récupère les matières d'une classe pour l'API JavaScript.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function getMatieresForApi(ESBTPClasse $classe)
    {
        // Log pour debugging
        \Log::info('API matières appelée pour la classe ID: ' . $classe->id);
        \Log::info('Classe: ' . $classe->nom . ' (Filière: ' . ($classe->filiere->nom ?? 'N/A') . ', Niveau: ' . ($classe->niveauEtude->nom ?? 'N/A') . ')');

        // Récupérer les matières de la classe
        $matieres = $classe->matieres()->where('esbtp_matieres.is_active', true)->get();
        \Log::info('Matières directement liées à la classe: ' . $matieres->count());

        // Si aucune matière n'est trouvée, essayer de récupérer les matières de la même filière et niveau
        if ($matieres->isEmpty()) {
            \Log::info('Aucune matière directement liée, recherche par filière et niveau...');
            // Récupérer des matières basées sur la filière et le niveau d'étude
            $matieres = \App\Models\ESBTPMatiere::where('is_active', true);

            if ($classe->filiere_id) {
                $matieres = $matieres->whereHas('filieres', function($q) use ($classe) {
                    $q->where('esbtp_filieres.id', $classe->filiere_id);
                });
                \Log::info('Filtrage par filière_id: ' . $classe->filiere_id);
            }

            if ($classe->niveau_etude_id) {
                $matieres = $matieres->whereHas('niveaux', function($q) use ($classe) {
                    $q->where('esbtp_niveaux_etudes.id', $classe->niveau_etude_id);
                });
                \Log::info('Filtrage par niveau_etude_id: ' . $classe->niveau_etude_id);
            }

            $matieres = $matieres->get();
            \Log::info('Matières trouvées par filière et niveau: ' . $matieres->count());
        }

        // Si toujours aucune matière, récupérer toutes les matières actives (pas de limite de 10)
        if ($matieres->isEmpty()) {
            \Log::info('Aucune matière trouvée par filière/niveau, récupération de toutes les matières actives...');
            $matieres = \App\Models\ESBTPMatiere::where('is_active', true)->get();
            \Log::info('Toutes les matières actives trouvées: ' . $matieres->count());
        }

        // Formater les matières pour l'API JavaScript
        $formattedMatieres = $matieres->map(function ($matiere) {
            $formatted = [
                'id' => $matiere->id,
                'name' => $matiere->nom ?? $matiere->name ?? 'Matière ' . $matiere->id,
                'code' => $matiere->code ?? '',
                'coefficient' => $matiere->coefficient ?? 1
            ];
            \Log::info('Matière formatée: ' . json_encode($formatted));
            return $formatted;
        });

        \Log::info('Total matières renvoyées: ' . $formattedMatieres->count());
        return response()->json($formattedMatieres);
    }

    /**
     * Get subjects for a specific class.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMatieres($id)
    {
        try {
            $classe = ESBTPClasse::findOrFail($id);
            $matieres = $classe->matieres()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'nom', 'code']);

            return response()->json($matieres);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des matières'], 500);
        }
    }
}
