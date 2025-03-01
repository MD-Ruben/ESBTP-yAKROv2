<?php

namespace App\Http\Controllers;

use App\Models\ESBTPSalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ESBTPSalleController extends Controller
{
    /**
     * Liste de tous les types de salles disponibles
     */
    private $types = [
        'Amphithéâtre',
        'Salle de cours',
        'Salle de TD',
        'Laboratoire',
        'Salle informatique',
        'Atelier',
        'Bureau',
        'Autre'
    ];

    /**
     * Affiche la liste des salles de classe.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer toutes les salles, triées par bâtiment puis par étage
        $salles = ESBTPSalle::orderBy('building')->orderBy('floor')->orderBy('name')->get();
        
        return view('esbtp.salles.index', compact('salles'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle salle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = $this->types;
        return view('esbtp.salles.create', compact('types'));
    }

    /**
     * Enregistre une nouvelle salle dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_salles,code',
            'type' => 'required|string|in:' . implode(',', $this->types),
            'capacity' => 'required|integer|min:0',
            'building' => 'nullable|string|max:100',
            'floor' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('esbtp.salles.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Création de la salle
        $salle = ESBTPSalle::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'building' => $request->building,
            'floor' => $request->floor,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('esbtp.salles.show', $salle)
            ->with('success', 'La salle a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une salle spécifique.
     *
     * @param  \App\Models\ESBTPSalle  $salle
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPSalle $salle)
    {
        // Charger les relations si nécessaire
        // $salle->load(['inscriptions']);
        
        return view('esbtp.salles.show', compact('salle'));
    }

    /**
     * Affiche le formulaire de modification d'une salle.
     *
     * @param  \App\Models\ESBTPSalle  $salle
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPSalle $salle)
    {
        $types = $this->types;
        return view('esbtp.salles.edit', compact('salle', 'types'));
    }

    /**
     * Met à jour une salle spécifique dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPSalle  $salle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPSalle $salle)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_salles,code,' . $salle->id,
            'type' => 'required|string|in:' . implode(',', $this->types),
            'capacity' => 'required|integer|min:0',
            'building' => 'nullable|string|max:100',
            'floor' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('esbtp.salles.edit', $salle)
                ->withErrors($validator)
                ->withInput();
        }

        // Mise à jour de la salle
        $salle->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'building' => $request->building,
            'floor' => $request->floor,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('esbtp.salles.show', $salle)
            ->with('success', 'La salle a été mise à jour avec succès.');
    }

    /**
     * Supprime une salle spécifique de la base de données.
     *
     * @param  \App\Models\ESBTPSalle  $salle
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPSalle $salle)
    {
        // Vérifier si la salle est utilisée par des inscriptions
        if ($salle->inscriptions()->count() > 0) {
            return redirect()->route('esbtp.salles.show', $salle)
                ->with('error', 'Impossible de supprimer cette salle car elle est utilisée par des inscriptions.');
        }

        // Supprimer la salle
        $salle->delete();

        return redirect()->route('esbtp.salles.index')
            ->with('success', 'La salle a été supprimée avec succès.');
    }
} 