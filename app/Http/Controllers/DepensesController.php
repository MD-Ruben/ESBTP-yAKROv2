<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\CategorieDepense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepensesController extends Controller
{
    /**
     * Display a listing of the expenses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depenses = Depense::with('categorie')
            ->orderBy('date_depense', 'desc')
            ->paginate(10);
        
        $categories = CategorieDepense::all();
        
        return view('esbtp.comptabilite.depenses.index', compact('depenses', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'categorie_id' => 'required|exists:esbtp_categories_depense,id',
            'commentaire' => 'nullable|string',
        ]);
        
        $validated['created_by'] = Auth::id();
        
        Depense::create($validated);
        
        return redirect()->route('comptabilite.depenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    /**
     * Display the specified expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $depense = Depense::with('categorie', 'createdBy')->findOrFail($id);
        return view('esbtp.comptabilite.depenses.show', compact('depense'));
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $depense = Depense::findOrFail($id);
        
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'categorie_id' => 'required|exists:esbtp_categories_depense,id',
            'commentaire' => 'nullable|string',
        ]);
        
        $depense->update($validated);
        
        return redirect()->route('comptabilite.depenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $depense = Depense::findOrFail($id);
        $depense->delete();
        
        return redirect()->route('comptabilite.depenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }
    
    /**
     * Display the expense categories management page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $categories = CategorieDepense::withCount('depenses')
            ->orderBy('nom')
            ->get();
            
        return view('esbtp.comptabilite.depenses.categories', compact('categories'));
    }
    
    /**
     * Store a new expense category.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:esbtp_categories_depense,nom',
            'description' => 'nullable|string',
        ]);
        
        $validated['created_by'] = Auth::id();
        
        CategorieDepense::create($validated);
        
        return redirect()->route('comptabilite.depenses.categories')
            ->with('success', 'Catégorie de dépense créée avec succès.');
    }
    
    /**
     * Update an expense category.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(Request $request, $id)
    {
        $category = CategorieDepense::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:esbtp_categories_depense,nom,'.$id,
            'description' => 'nullable|string',
        ]);
        
        $category->update($validated);
        
        return redirect()->route('comptabilite.depenses.categories')
            ->with('success', 'Catégorie de dépense mise à jour avec succès.');
    }
    
    /**
     * Delete an expense category.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCategory($id)
    {
        $category = CategorieDepense::findOrFail($id);
        
        // Check if the category has expenses
        if ($category->depenses()->count() > 0) {
            return redirect()->route('comptabilite.depenses.categories')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est associée à des dépenses.');
        }
        
        $category->delete();
        
        return redirect()->route('comptabilite.depenses.categories')
            ->with('success', 'Catégorie de dépense supprimée avec succès.');
    }
} 