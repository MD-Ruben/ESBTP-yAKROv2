<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ESBTPMatiere;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ESBTPEnseignantController extends Controller
{
    /**
     * Constructeur avec middleware d'authentification
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superAdmin');
    }

    /**
     * Affiche la liste des enseignants
     */
    public function index()
    {
        $enseignants = User::role('enseignant')->orderBy('name')->paginate(10);
        return view('esbtp.enseignants.index', compact('enseignants'));
    }

    /**
     * Affiche le formulaire de création d'un enseignant
     */
    public function create()
    {
        $matieres = ESBTPMatiere::orderBy('nom')->get();
        return view('esbtp.enseignants.create', compact('matieres'));
    }

    /**
     * Enregistre un nouvel enseignant
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'specialite' => 'nullable|string|max:255',
            'matieres' => 'nullable|array',
            'matieres.*' => 'exists:esbtp_matieres,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'specialite' => $request->specialite,
            'is_active' => true,
        ]);

        // Assigner le rôle enseignant
        $role = Role::firstOrCreate(['name' => 'enseignant']);
        $user->assignRole($role);

        // Assigner les matières si fournies
        if ($request->has('matieres')) {
            // Ici, vous pouvez gérer l'association des matières avec l'enseignant
            // selon votre structure de données
        }

        return redirect()->route('esbtp.enseignants.index')
            ->with('success', 'Enseignant créé avec succès');
    }

    /**
     * Affiche les détails d'un enseignant
     */
    public function show($id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);
        return view('esbtp.enseignants.show', compact('enseignant'));
    }

    /**
     * Affiche le formulaire d'édition d'un enseignant
     */
    public function edit($id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);
        $matieres = ESBTPMatiere::orderBy('nom')->get();
        return view('esbtp.enseignants.edit', compact('enseignant', 'matieres'));
    }

    /**
     * Met à jour un enseignant
     */
    public function update(Request $request, $id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'specialite' => 'nullable|string|max:255',
            'matieres' => 'nullable|array',
            'matieres.*' => 'exists:esbtp_matieres,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mettre à jour l'utilisateur
        $enseignant->name = $request->name;
        $enseignant->email = $request->email;
        $enseignant->username = $request->username;
        if ($request->filled('password')) {
            $enseignant->password = Hash::make($request->password);
        }
        $enseignant->telephone = $request->telephone;
        $enseignant->adresse = $request->adresse;
        $enseignant->specialite = $request->specialite;
        $enseignant->save();

        // Mettre à jour les matières si fournies
        if ($request->has('matieres')) {
            // Ici, vous pouvez gérer la mise à jour des matières associées à l'enseignant
            // selon votre structure de données
        }

        return redirect()->route('esbtp.enseignants.index')
            ->with('success', 'Enseignant mis à jour avec succès');
    }

    /**
     * Supprime un enseignant
     */
    public function destroy($id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);
        
        // Supprimer les associations avec les matières si nécessaire
        
        // Supprimer l'utilisateur
        $enseignant->delete();

        return redirect()->route('esbtp.enseignants.index')
            ->with('success', 'Enseignant supprimé avec succès');
    }

    /**
     * Promouvoir un enseignant au rang de Super Admin
     */
    public function promoteToAdmin($id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);
        
        // Vérifier si l'enseignant a déjà le rôle de superAdmin
        if ($enseignant->hasRole('superAdmin')) {
            return redirect()->back()->with('warning', 'Cet enseignant est déjà un Super Admin.');
        }
        
        // Attribuer le rôle de superAdmin tout en conservant le rôle d'enseignant
        $enseignant->assignRole('superAdmin');
        
        return redirect()->route('esbtp.enseignants.index')
            ->with('success', 'L\'enseignant a été promu au rang de Super Admin avec succès.');
    }

    /**
     * Rétrograder un Super Admin-Enseignant au rang d'enseignant simple
     */
    public function demoteFromAdmin($id)
    {
        $enseignant = User::role('enseignant')->findOrFail($id);
        
        // Vérifier si l'enseignant a le rôle de superAdmin
        if (!$enseignant->hasRole('superAdmin')) {
            return redirect()->back()->with('warning', 'Cet enseignant n\'est pas un Super Admin.');
        }
        
        // Retirer le rôle de superAdmin
        $enseignant->removeRole('superAdmin');
        
        return redirect()->route('esbtp.enseignants.index')
            ->with('success', 'L\'enseignant a été rétrogradé avec succès.');
    }
} 