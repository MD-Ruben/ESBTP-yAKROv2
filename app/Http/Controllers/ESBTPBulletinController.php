<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPBulletin;
use App\Models\ESBTPResultatMatiere;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPNote;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class ESBTPBulletinController extends Controller
{
    /**
     * Affiche la liste des bulletins avec filtre par année et classe
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        
        // Périodes disponibles (définir les périodes pour la vue)
        $periodes = [
            (object)['id' => 'semestre1', 'nom' => 'Premier Semestre', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)],
            (object)['id' => 'semestre2', 'nom' => 'Deuxième Semestre', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)],
            (object)['id' => 'annuel', 'nom' => 'Annuel', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)]
        ];
        
        // Statistiques pour les widgets
        $stats = [
            'total' => ESBTPBulletin::count(),
            'published' => ESBTPBulletin::where('is_published', true)->count(),
            'pending' => ESBTPBulletin::where('is_published', false)->count(),
            'periodes' => count($periodes)
        ];
        
        // Valeurs par défaut filtre
        $classe_id = $request->input('classe_id');
        $annee_id = $request->input('annee_universitaire_id', 
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode_id = $request->input('periode_id');
        
        $query = ESBTPBulletin::with(['etudiant', 'classe', 'anneeUniversitaire']);
        
        // Application des filtres
        if ($classe_id) {
            $query->where('classe_id', $classe_id);
        }
        
        if ($annee_id) {
            $query->where('annee_universitaire_id', $annee_id);
        }
        
        if ($periode_id) {
            $query->where('periode', $periode_id);
        }
        
        // Utiliser paginate() au lieu de get() pour permettre l'utilisation de appends()
        $bulletins = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('esbtp.bulletins.index', compact(
            'bulletins', 
            'classes', 
            'anneesUniversitaires', 
            'classe_id', 
            'annee_id',
            'periodes',
            'periode_id',
            'stats'
        ));
    }

    /**
     * Affiche le formulaire de sélection d'étudiant pour créer un bulletin
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        return view('esbtp.bulletins.create', compact('classes', 'anneesUniversitaires', 'anneeActuelle'));
    }

    /**
     * Enregistre un nouveau bulletin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'periode' => 'required|in:semestre1,semestre2,annuel',
            'appreciation_generale' => 'nullable|string',
            'decision_conseil' => 'nullable|string',
        ], [
            'etudiant_id.required' => 'L\'étudiant est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'annee_universitaire_id.required' => 'L\'année universitaire est obligatoire',
            'periode.required' => 'La période est obligatoire',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si l'étudiant est bien inscrit dans cette classe pour cette année
            $etudiantInscrit = ESBTPEtudiant::findOrFail($request->etudiant_id)
                ->inscriptions()
                ->where('classe_id', $request->classe_id)
                ->where('annee_universitaire_id', $request->annee_universitaire_id)
                ->exists();
                
            if (!$etudiantInscrit) {
                return redirect()->back()
                    ->with('error', 'L\'étudiant n\'est pas inscrit dans cette classe pour cette année universitaire')
                    ->withInput();
            }
            
            // Vérifier s'il existe déjà un bulletin pour cet étudiant, cette classe, cette année et cette période
            $bulletinExistant = ESBTPBulletin::where('etudiant_id', $request->etudiant_id)
                ->where('classe_id', $request->classe_id)
                ->where('annee_universitaire_id', $request->annee_universitaire_id)
                ->where('periode', $request->periode)
                ->exists();
                
            if ($bulletinExistant) {
                return redirect()->back()
                    ->with('error', 'Un bulletin existe déjà pour cet étudiant pour cette période')
                    ->withInput();
            }
            
            // Créer le bulletin
            $bulletin = new ESBTPBulletin();
            $bulletin->etudiant_id = $request->etudiant_id;
            $bulletin->classe_id = $request->classe_id;
            $bulletin->annee_universitaire_id = $request->annee_universitaire_id;
            $bulletin->periode = $request->periode;
            $bulletin->appreciation_generale = $request->appreciation_generale;
            $bulletin->decision_conseil = $request->decision_conseil;
            $bulletin->user_id = Auth::id();
            $bulletin->save();
            
            // Récupérer toutes les matières de la classe
            $classe = ESBTPClasse::findOrFail($request->classe_id);
            $matieres = $classe->matieres;
            
            // Pour chaque matière, calculer la moyenne et créer un résultat
            foreach ($matieres as $matiere) {
                // Récupérer toutes les évaluations de cette matière pour cette classe
                $evaluations = $matiere->evaluations()
                    ->where('classe_id', $classe->id)
                    ->get();
                
                if ($evaluations->isEmpty()) {
                    continue; // Passer à la matière suivante s'il n'y a pas d'évaluations
                }
                
                // Récupérer les notes de l'étudiant pour ces évaluations
                $notes = ESBTPNote::whereIn('evaluation_id', $evaluations->pluck('id'))
                    ->where('etudiant_id', $request->etudiant_id)
                    ->get();
                
                if ($notes->isEmpty()) {
                    continue; // Passer à la matière suivante s'il n'y a pas de notes
                }
                
                // Calculer la moyenne
                $sommeNotes = 0;
                $sommeCoefficients = 0;
                
                foreach ($notes as $note) {
                    $evaluation = $evaluations->where('id', $note->evaluation_id)->first();
                    $sommeNotes += ($note->valeur / $evaluation->bareme) * 20 * $evaluation->coefficient;
                    $sommeCoefficients += $evaluation->coefficient;
                }
                
                $moyenne = $sommeCoefficients > 0 ? $sommeNotes / $sommeCoefficients : null;
                
                // Récupérer le coefficient de la matière pour cette classe
                $pivotData = $classe->matieres()->where('matiere_id', $matiere->id)->first()->pivot;
                $coefficient = $pivotData->coefficient ?? 1;
                
                // Créer le résultat pour cette matière
                $resultat = new ESBTPResultatMatiere();
                $resultat->bulletin_id = $bulletin->id;
                $resultat->matiere_id = $matiere->id;
                $resultat->moyenne = $moyenne;
                $resultat->coefficient = $coefficient;
                $resultat->commentaire = null;
                $resultat->save();
            }
            
            // Calculer et mettre à jour la moyenne générale du bulletin
            $this->calculerMoyenneGenerale($bulletin);
            
            DB::commit();
            return redirect()->route('bulletins.show', $bulletin)
                ->with('success', 'Le bulletin a été créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du bulletin: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Calcule et met à jour la moyenne générale d'un bulletin
     */
    private function calculerMoyenneGenerale(ESBTPBulletin $bulletin)
    {
        $resultats = $bulletin->resultats;
        
        if ($resultats->isEmpty()) {
            $bulletin->moyenne_generale = null;
            $bulletin->save();
            return;
        }
        
        $sommePoints = 0;
        $sommeCoefficients = 0;
        
        foreach ($resultats as $resultat) {
            if ($resultat->moyenne !== null) {
                $sommePoints += $resultat->moyenne * $resultat->coefficient;
                $sommeCoefficients += $resultat->coefficient;
            }
        }
        
        $moyenneGenerale = $sommeCoefficients > 0 ? $sommePoints / $sommeCoefficients : null;
        
        $bulletin->moyenne_generale = $moyenneGenerale;
        $bulletin->save();
    }

    /**
     * Affiche un bulletin spécifique.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPBulletin $bulletin)
    {
        $bulletin->load(['etudiant', 'classe', 'anneeUniversitaire', 'resultats.matiere', 'user']);
        return view('esbtp.bulletins.show', compact('bulletin'));
    }

    /**
     * Affiche le formulaire de modification d'un bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPBulletin $bulletin)
    {
        $bulletin->load(['etudiant', 'classe', 'anneeUniversitaire', 'resultats.matiere']);
        return view('esbtp.bulletins.edit', compact('bulletin'));
    }

    /**
     * Met à jour un bulletin spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPBulletin $bulletin)
    {
        $request->validate([
            'resultats' => 'required|array',
            'resultats.*.matiere_id' => 'required|exists:esbtp_matieres,id',
            'resultats.*.moyenne' => 'nullable|numeric|min:0|max:20',
            'resultats.*.coefficient' => 'required|numeric|min:0',
            'resultats.*.commentaire' => 'nullable|string',
            'appreciation_generale' => 'nullable|string',
            'decision_conseil' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour les informations du bulletin
            $bulletin->appreciation_generale = $request->appreciation_generale;
            $bulletin->decision_conseil = $request->decision_conseil;
            $bulletin->save();
            
            // Mettre à jour les résultats par matière
            foreach ($request->resultats as $resultatData) {
                $matiereId = $resultatData['matiere_id'];
                $moyenne = $resultatData['moyenne'] !== null && $resultatData['moyenne'] !== '' 
                    ? $resultatData['moyenne'] : null;
                
                $resultat = ESBTPResultatMatiere::where('bulletin_id', $bulletin->id)
                    ->where('matiere_id', $matiereId)
                    ->first();
                
                if ($resultat) {
                    $resultat->moyenne = $moyenne;
                    $resultat->coefficient = $resultatData['coefficient'];
                    $resultat->commentaire = $resultatData['commentaire'] ?? null;
                    $resultat->save();
                } else {
                    $resultat = new ESBTPResultatMatiere();
                    $resultat->bulletin_id = $bulletin->id;
                    $resultat->matiere_id = $matiereId;
                    $resultat->moyenne = $moyenne;
                    $resultat->coefficient = $resultatData['coefficient'];
                    $resultat->commentaire = $resultatData['commentaire'] ?? null;
                    $resultat->save();
                }
            }
            
            // Recalculer la moyenne générale
            $this->calculerMoyenneGenerale($bulletin);
            
            DB::commit();
            return redirect()->route('bulletins.show', $bulletin)
                ->with('success', 'Le bulletin a été mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du bulletin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un bulletin spécifique.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPBulletin $bulletin)
    {
        try {
            $bulletin->delete();
            return redirect()->route('bulletins.index')->with('success', 'Bulletin supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    
    /**
     * Génère un PDF du bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function genererPDF(ESBTPBulletin $bulletin)
    {
        $bulletin->load(['etudiant', 'classe', 'anneeUniversitaire', 'resultats.matiere', 'user']);
        
        // Grouper les résultats par unité d'enseignement si elles existent
        $resultatsGroupes = [];
        foreach ($bulletin->resultats as $resultat) {
            $ue = $resultat->matiere->uniteEnseignement ? $resultat->matiere->uniteEnseignement->nom : 'Sans UE';
            if (!isset($resultatsGroupes[$ue])) {
                $resultatsGroupes[$ue] = [];
            }
            $resultatsGroupes[$ue][] = $resultat;
        }
        
        // Générer le PDF
        $pdf = PDF::loadView('esbtp.bulletins.pdf', compact('bulletin', 'resultatsGroupes'));
        
        // Télécharger le PDF
        return $pdf->download('bulletin_' . $bulletin->etudiant->matricule . '_' . $bulletin->periode . '.pdf');
    }
    
    /**
     * Génère les bulletins pour une classe entière.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function genererClasseBulletins(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:esbtp_classes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'periode' => 'required|in:semestre1,semestre2,annuel',
        ]);
        
        try {
            $classe = ESBTPClasse::findOrFail($request->classe_id);
            $anneeUniversitaire = ESBTPAnneeUniversitaire::findOrFail($request->annee_universitaire_id);
            
            // Récupérer tous les étudiants inscrits dans cette classe pour cette année
            $etudiants = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($request) {
                $query->where('classe_id', $request->classe_id)
                    ->where('annee_universitaire_id', $request->annee_universitaire_id)
                    ->where('is_active', true);
            })->get();
            
            $bulletinsGeneres = 0;
            
            foreach ($etudiants as $etudiant) {
                // Vérifier si un bulletin existe déjà pour cet étudiant
                $bulletinExistant = ESBTPBulletin::where('etudiant_id', $etudiant->id)
                    ->where('classe_id', $request->classe_id)
                    ->where('annee_universitaire_id', $request->annee_universitaire_id)
                    ->where('periode', $request->periode)
                    ->exists();
                    
                if ($bulletinExistant) {
                    continue; // Passer à l'étudiant suivant
                }
                
                // Créer une requête simulée pour réutiliser la méthode store
                $bulletinRequest = new Request([
                    'etudiant_id' => $etudiant->id,
                    'classe_id' => $request->classe_id,
                    'annee_universitaire_id' => $request->annee_universitaire_id,
                    'periode' => $request->periode,
                    'appreciation_generale' => null,
                    'decision_conseil' => null,
                ]);
                
                // Appeler la méthode store mais sans rediriger
                try {
                    DB::beginTransaction();
                    
                    // Créer le bulletin
                    $bulletin = new ESBTPBulletin();
                    $bulletin->etudiant_id = $etudiant->id;
                    $bulletin->classe_id = $request->classe_id;
                    $bulletin->annee_universitaire_id = $request->annee_universitaire_id;
                    $bulletin->periode = $request->periode;
                    $bulletin->appreciation_generale = null;
                    $bulletin->decision_conseil = null;
                    $bulletin->user_id = Auth::id();
                    $bulletin->save();
                    
                    // Récupérer toutes les matières de la classe
                    $matieres = $classe->matieres;
                    
                    // Pour chaque matière, calculer la moyenne et créer un résultat
                    foreach ($matieres as $matiere) {
                        // Récupérer toutes les évaluations de cette matière pour cette classe
                        $evaluations = $matiere->evaluations()
                            ->where('classe_id', $classe->id)
                            ->get();
                        
                        if ($evaluations->isEmpty()) {
                            continue; // Passer à la matière suivante s'il n'y a pas d'évaluations
                        }
                        
                        // Récupérer les notes de l'étudiant pour ces évaluations
                        $notes = ESBTPNote::whereIn('evaluation_id', $evaluations->pluck('id'))
                            ->where('etudiant_id', $etudiant->id)
                            ->get();
                        
                        if ($notes->isEmpty()) {
                            continue; // Passer à la matière suivante s'il n'y a pas de notes
                        }
                        
                        // Calculer la moyenne
                        $sommeNotes = 0;
                        $sommeCoefficients = 0;
                        
                        foreach ($notes as $note) {
                            $evaluation = $evaluations->where('id', $note->evaluation_id)->first();
                            $sommeNotes += ($note->valeur / $evaluation->bareme) * 20 * $evaluation->coefficient;
                            $sommeCoefficients += $evaluation->coefficient;
                        }
                        
                        $moyenne = $sommeCoefficients > 0 ? $sommeNotes / $sommeCoefficients : null;
                        
                        // Récupérer le coefficient de la matière pour cette classe
                        $pivotData = $classe->matieres()->where('matiere_id', $matiere->id)->first()->pivot;
                        $coefficient = $pivotData->coefficient ?? 1;
                        
                        // Créer le résultat pour cette matière
                        $resultat = new ESBTPResultatMatiere();
                        $resultat->bulletin_id = $bulletin->id;
                        $resultat->matiere_id = $matiere->id;
                        $resultat->moyenne = $moyenne;
                        $resultat->coefficient = $coefficient;
                        $resultat->commentaire = null;
                        $resultat->save();
                    }
                    
                    // Calculer et mettre à jour la moyenne générale du bulletin
                    $this->calculerMoyenneGenerale($bulletin);
                    
                    DB::commit();
                    $bulletinsGeneres++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Continuer avec l'étudiant suivant
                }
            }
            
            if ($bulletinsGeneres > 0) {
                return redirect()->route('bulletins.index', [
                    'classe_id' => $request->classe_id,
                    'annee_universitaire_id' => $request->annee_universitaire_id
                ])->with('success', $bulletinsGeneres . ' bulletins ont été générés avec succès');
            } else {
                return redirect()->back()
                    ->with('info', 'Aucun nouveau bulletin n\'a été généré. Tous les bulletins existent déjà ou il n\'y a pas de données suffisantes.');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la génération des bulletins: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la page de sélection pour les bulletins
     *
     * @return \Illuminate\Http\Response
     */
    public function select()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        return view('esbtp.bulletins.select', compact('classes', 'anneesUniversitaires', 'anneeActuelle'));
    }

    /**
     * Affiche les résultats des étudiants
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resultats(Request $request)
    {
        // Récupérer les paramètres de filtre
        $classe_id = $request->input('classe_id');
        $annee_id = $request->input('annee_universitaire_id', 
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode = $request->input('periode', 'semestre1');
        
        // Vérifier si les paramètres sont valides
        if (!$classe_id || !$annee_id) {
            $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
            $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
            return view('esbtp.resultats.index', compact('classes', 'anneesUniversitaires', 'classe_id', 'annee_id', 'periode'));
        }
        
        // Récupérer la classe sélectionnée avec ses étudiants inscrits
        $classe = ESBTPClasse::with([
            'inscriptions' => function($query) use ($annee_id) {
                $query->where('annee_universitaire_id', $annee_id)
                      ->where('is_active', true);
            },
            'inscriptions.etudiant',
            'matieres'
        ])->findOrFail($classe_id);
        
        // Récupérer l'année universitaire
        $anneeUniversitaire = ESBTPAnneeUniversitaire::findOrFail($annee_id);
        
        // Récupérer les bulletins des étudiants pour cette classe, cette année et cette période
        $bulletins = ESBTPBulletin::with(['etudiant', 'resultats.matiere'])
            ->where('classe_id', $classe_id)
            ->where('annee_universitaire_id', $annee_id)
            ->where('periode', $periode)
            ->get();
        
        // Préparer les données pour l'affichage
        $resultatsEtudiants = [];
        foreach ($bulletins as $bulletin) {
            $resultatsEtudiants[$bulletin->etudiant_id] = [
                'etudiant' => $bulletin->etudiant,
                'bulletin' => $bulletin,
                'resultats' => $bulletin->resultats->keyBy('matiere_id'),
                'moyenne' => $bulletin->moyenne_generale,
                'rang' => $bulletin->rang
            ];
        }
        
        // Récupérer les étudiants qui n'ont pas encore de bulletin
        $etudiantsSansBulletin = [];
        foreach ($classe->inscriptions as $inscription) {
            if (!isset($resultatsEtudiants[$inscription->etudiant_id])) {
                $etudiantsSansBulletin[] = $inscription->etudiant;
            }
        }
        
        return view('esbtp.resultats.index', compact(
            'classe', 
            'anneeUniversitaire', 
            'periode', 
            'resultatsEtudiants', 
            'etudiantsSansBulletin',
            'bulletins'
        ));
    }
    
    /**
     * Affiche le bulletin de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function monBulletin(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Récupérer l'étudiant associé à l'utilisateur
        $etudiant = $user->etudiant;
        
        if (!$etudiant) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre compte utilisateur n\'est pas associé à un étudiant.');
        }
        
        // Récupérer les paramètres de filtre
        $anneeId = $request->input('annee_universitaire_id', 
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode = $request->input('periode');
        
        // Récupérer l'inscription active de l'étudiant
        $inscription = $etudiant->inscriptions()
            ->where('annee_universitaire_id', $anneeId)
            ->where('statut', 'active')
            ->first();
            
        if (!$inscription) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas inscrit pour l\'année universitaire sélectionnée.');
        }
        
        // Récupérer la classe de l'étudiant
        $classe = $inscription->classe;
        
        if (!$classe) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre inscription n\'est associée à aucune classe.');
        }
        
        // Récupérer le bulletin de l'étudiant
        $bulletin = ESBTPBulletin::where('etudiant_id', $etudiant->id)
            ->where('annee_universitaire_id', $anneeId);
            
        if ($periode) {
            $bulletin = $bulletin->where('periode', $periode);
        }
        
        $bulletin = $bulletin->first();
        
        // Si le bulletin n'existe pas encore, on affiche un message
        if (!$bulletin) {
            // Récupérer toutes les années universitaires pour le filtre
            $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
            
            return view('esbtp.bulletin.mon-bulletin', compact(
                'etudiant',
                'classe',
                'anneeId',
                'periode',
                'anneesUniversitaires'
            ))->with('warning', 'Le bulletin n\'est pas encore disponible pour la période sélectionnée.');
        }
        
        // Récupérer les détails du bulletin
        $detailsBulletin = ESBTPBulletinDetail::where('bulletin_id', $bulletin->id)
            ->with(['matiere'])
            ->get();
            
        // Regrouper les détails par UE si nécessaire
        $detailsParUE = [];
        
        foreach ($detailsBulletin as $detail) {
            $ueId = $detail->matiere->ue_id ?? 'sans_ue';
            if (!isset($detailsParUE[$ueId])) {
                $detailsParUE[$ueId] = [
                    'ue' => $detail->matiere->ue ?? null,
                    'details' => []
                ];
            }
            $detailsParUE[$ueId]['details'][] = $detail;
        }
        
        // Calculer les statistiques globales
        $moyenneGenerale = $bulletin->moyenne_generale;
        $rangGeneral = $bulletin->rang;
        $effectifClasse = $bulletin->effectif_classe;
        $creditsTotaux = $detailsBulletin->sum('credits_valides');
        $decisionConseil = $bulletin->decision_conseil;
        
        // Récupérer toutes les années universitaires pour le filtre
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        
        return view('esbtp.bulletin.mon-bulletin', compact(
            'etudiant',
            'classe',
            'bulletin',
            'detailsBulletin',
            'detailsParUE',
            'moyenneGenerale',
            'rangGeneral',
            'effectifClasse',
            'creditsTotaux',
            'decisionConseil',
            'anneeId',
            'periode',
            'anneesUniversitaires'
        ));
    }

    /**
     * Affiche les bulletins de l'étudiant connecté.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentBulletins()
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();
        
        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }
        
        $bulletins = ESBTPBulletin::where('etudiant_id', $etudiant->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('etudiants.bulletins', compact('bulletins', 'etudiant'));
    }
} 