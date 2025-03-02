<?php

namespace App\Http\Controllers;

use App\Models\ESBTPAttendance;
use App\Models\ESBTPClasse;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ESBTPAttendanceController extends Controller
{
    /**
     * Affiche la liste des présences.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Initialiser la requête
        $query = ESBTPAttendance::with(['etudiant', 'seanceCours', 'createdBy']);
        
        // Filtrer par classe
        if ($request->filled('classe_id')) {
            $query->parClasse($request->classe_id);
        }
        
        // Filtrer par étudiant
        if ($request->filled('etudiant_id')) {
            $query->parEtudiant($request->etudiant_id);
        }
        
        // Filtrer par date
        if ($request->filled('date')) {
            $query->parDate($request->date);
        }
        
        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->parStatut($request->statut);
        }
        
        // Récupérer les données
        $attendances = $query->orderBy('date', 'desc')->paginate(15);
        
        // Récupérer les données pour les filtres
        $classes = ESBTPClasse::all();
        $etudiants = ESBTPEtudiant::all();
        
        return view('esbtp.attendances.index', compact('attendances', 'classes', 'etudiants'));
    }

    /**
     * Affiche le formulaire pour marquer les présences d'une séance.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Récupérer les classes pour le filtre
        $classes = ESBTPClasse::all();
        
        // Si une classe est sélectionnée, récupérer les séances de cours
        $seances = collect();
        if ($request->filled('classe_id')) {
            $seances = ESBTPSeanceCours::whereHas('emploiTemps', function($query) use ($request) {
                $query->where('classe_id', $request->classe_id);
            })->get();
        }
        
        // Si une séance est sélectionnée, récupérer les étudiants de la classe
        $etudiants = collect();
        if ($request->filled('seance_id')) {
            $seance = ESBTPSeanceCours::findOrFail($request->seance_id);
            $classe = $seance->emploiTemps->classe;
            $etudiants = $classe->etudiants;
        }
        
        return view('esbtp.attendances.create', compact('classes', 'seances', 'etudiants'));
    }

    /**
     * Enregistre les présences des étudiants.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données
        $validatedData = $request->validate([
            'seance_cours_id' => 'required|exists:esbtp_seance_cours,id',
            'date' => 'required|date',
            'statuts' => 'required|array',
            'statuts.*' => 'required|in:present,absent,retard,excuse',
            'commentaires' => 'nullable|array',
            'commentaires.*' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($validatedData['statuts'] as $etudiantId => $statut) {
                // Vérifier si l'enregistrement existe déjà
                $attendance = ESBTPAttendance::where([
                    'seance_cours_id' => $validatedData['seance_cours_id'],
                    'etudiant_id' => $etudiantId,
                    'date' => $validatedData['date']
                ])->first();
                
                $commentaire = $validatedData['commentaires'][$etudiantId] ?? null;
                
                if ($attendance) {
                    // Mettre à jour l'enregistrement existant
                    $attendance->update([
                        'statut' => $statut,
                        'commentaire' => $commentaire,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Créer un nouvel enregistrement
                    ESBTPAttendance::create([
                        'seance_cours_id' => $validatedData['seance_cours_id'],
                        'etudiant_id' => $etudiantId,
                        'date' => $validatedData['date'],
                        'statut' => $statut,
                        'commentaire' => $commentaire,
                        'created_by' => Auth::id()
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('esbtp.attendances.index')
                ->with('success', 'Les présences ont été enregistrées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des présences: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPAttendance $attendance)
    {
        return view('esbtp.attendances.show', compact('attendance'));
    }

    /**
     * Affiche le formulaire pour modifier une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPAttendance $attendance)
    {
        return view('esbtp.attendances.edit', compact('attendance'));
    }

    /**
     * Met à jour une présence.
     *
     * @param Request $request
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPAttendance $attendance)
    {
        // Valider les données
        $validatedData = $request->validate([
            'statut' => 'required|in:present,absent,retard,excuse',
            'commentaire' => 'nullable|string'
        ]);
        
        // Ajouter l'identifiant de l'utilisateur qui modifie
        $validatedData['updated_by'] = Auth::id();
        
        // Mettre à jour l'enregistrement
        $attendance->update($validatedData);
        
        return redirect()->route('esbtp.attendances.index')
            ->with('success', 'La présence a été mise à jour avec succès.');
    }

    /**
     * Supprime une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPAttendance $attendance)
    {
        try {
            $attendance->delete();
            return redirect()->route('esbtp.attendances.index')->with('success', 'Présence supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Génère un rapport de présence.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function rapport(Request $request)
    {
        // Valider les données
        $validatedData = $request->validate([
            'classe_id' => 'required|exists:esbtp_classes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);
        
        // Récupérer la classe
        $classe = ESBTPClasse::findOrFail($validatedData['classe_id']);
        
        // Récupérer les étudiants de la classe
        $etudiants = $classe->etudiants;
        
        // Récupérer les séances de cours de la classe
        $seances = ESBTPSeanceCours::whereHas('emploiTemps', function($query) use ($classe) {
            $query->where('classe_id', $classe->id);
        })->get();
        
        // Récupérer les présences pour chaque étudiant
        $statistiques = [];
        
        foreach ($etudiants as $etudiant) {
            $attendances = ESBTPAttendance::where('etudiant_id', $etudiant->id)
                ->whereHas('seanceCours.emploiTemps', function($query) use ($classe) {
                    $query->where('classe_id', $classe->id);
                })
                ->whereBetween('date', [$validatedData['date_debut'], $validatedData['date_fin']])
                ->get();
            
            // Calculer les statistiques
            $totalSeances = $seances->count();
            $present = $attendances->where('statut', 'present')->count();
            $absent = $attendances->where('statut', 'absent')->count();
            $retard = $attendances->where('statut', 'retard')->count();
            $excuse = $attendances->where('statut', 'excuse')->count();
            
            $tauxPresence = $totalSeances > 0 ? round(($present / $totalSeances) * 100, 2) : 0;
            
            $statistiques[$etudiant->id] = [
                'etudiant' => $etudiant,
                'present' => $present,
                'absent' => $absent,
                'retard' => $retard,
                'excuse' => $excuse,
                'taux_presence' => $tauxPresence
            ];
        }
        
        return view('esbtp.attendances.rapport', compact('classe', 'etudiants', 'statistiques', 'validatedData'));
    }
    
    /**
     * Affiche le formulaire pour générer un rapport.
     *
     * @return \Illuminate\Http\Response
     */
    public function rapportForm()
    {
        $classes = ESBTPClasse::all();
        
        return view('esbtp.attendances.rapport-form', compact('classes'));
    }
    
    /**
     * Affiche les présences de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function studentAttendance(Request $request)
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();
        
        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }
        
        $absences = ESBTPAttendance::where('etudiant_id', $etudiant->id)
            ->where('status', 'absent')
            ->orderBy('date', 'desc')
            ->get();
        
        $presences = ESBTPAttendance::where('etudiant_id', $etudiant->id)
            ->where('status', 'present')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('etudiants.attendances', compact('absences', 'presences', 'etudiant'));
    }
} 