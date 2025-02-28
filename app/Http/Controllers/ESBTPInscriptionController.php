<?php

namespace App\Http\Controllers;

use App\Models\ESBTPInscription;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ESBTPInscriptionController extends Controller
{
    /**
     * Affiche la liste des inscriptions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        // Si aucune année n'est définie comme en cours, prendre la plus récente
        if (!$anneeEnCours) {
            $anneeEnCours = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->first();
        }
        
        // Récupérer toutes les années universitaires pour le filtre
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        
        // Récupérer les filières et niveaux d'études pour les filtres
        $filieres = ESBTPFiliere::whereNull('parent_id')->with('options')->get();
        $niveaux = ESBTPNiveauEtude::orderBy('type')->orderBy('year')->get();
        
        // Récupérer les inscriptions de l'année en cours avec leurs relations
        $inscriptions = ESBTPInscription::with(['student', 'filiere', 'niveauEtude', 'anneeUniversitaire'])
            ->when($anneeEnCours, function($query) use ($anneeEnCours) {
                return $query->where('annee_universitaire_id', $anneeEnCours->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('esbtp.inscriptions.index', compact('inscriptions', 'annees', 'filieres', 'niveaux', 'anneeEnCours'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle inscription.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Récupérer les étudiants, filières, niveaux d'études et années universitaires
        $students = Student::orderBy('last_name')->get();
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::orderBy('type')->orderBy('year')->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        
        return view('esbtp.inscriptions.create', compact('students', 'filieres', 'niveaux', 'annees', 'anneeEnCours'));
    }

    /**
     * Enregistre une nouvelle inscription dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'inscription_date' => 'required|date',
            'status' => 'required|string|in:active,completed,abandoned,suspended',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier si l'étudiant est déjà inscrit pour cette année universitaire
        $existingInscription = ESBTPInscription::where('student_id', $request->student_id)
            ->where('annee_universitaire_id', $request->annee_universitaire_id)
            ->first();
        
        if ($existingInscription) {
            return redirect()->back()
                ->withErrors(['student_id' => 'Cet étudiant est déjà inscrit pour cette année universitaire.'])
                ->withInput();
        }
        
        // Créer la nouvelle inscription
        ESBTPInscription::create($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.inscriptions.index')
            ->with('success', 'L\'inscription a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une inscription spécifique.
     *
     * @param  \App\Models\ESBTPInscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPInscription $inscription)
    {
        // Charger les relations
        $inscription->load('student', 'filiere', 'niveauEtude', 'anneeUniversitaire');
        
        return view('esbtp.inscriptions.show', compact('inscription'));
    }

    /**
     * Affiche le formulaire de modification d'une inscription.
     *
     * @param  \App\Models\ESBTPInscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPInscription $inscription)
    {
        // Récupérer les étudiants, filières, niveaux d'études et années universitaires
        $students = Student::orderBy('last_name')->get();
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::orderBy('type')->orderBy('year')->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        
        // Charger les relations de l'inscription
        $inscription->load('student', 'filiere', 'niveauEtude', 'anneeUniversitaire');
        
        return view('esbtp.inscriptions.edit', compact('inscription', 'students', 'filieres', 'niveaux', 'annees'));
    }

    /**
     * Met à jour l'inscription spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPInscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPInscription $inscription)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'inscription_date' => 'required|date',
            'status' => 'required|string|in:active,completed,abandoned,suspended',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier si l'étudiant est déjà inscrit pour cette année universitaire (sauf l'inscription actuelle)
        $existingInscription = ESBTPInscription::where('student_id', $request->student_id)
            ->where('annee_universitaire_id', $request->annee_universitaire_id)
            ->where('id', '!=', $inscription->id)
            ->first();
        
        if ($existingInscription) {
            return redirect()->back()
                ->withErrors(['student_id' => 'Cet étudiant est déjà inscrit pour cette année universitaire.'])
                ->withInput();
        }
        
        // Mettre à jour l'inscription
        $inscription->update($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.inscriptions.index')
            ->with('success', 'L\'inscription a été mise à jour avec succès.');
    }

    /**
     * Supprime l'inscription spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPInscription  $inscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPInscription $inscription)
    {
        // Supprimer l'inscription
        $inscription->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.inscriptions.index')
            ->with('success', 'L\'inscription a été supprimée avec succès.');
    }
} 