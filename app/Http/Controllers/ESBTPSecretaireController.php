<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ESBTPSecretaireController extends Controller
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
     * Affiche la liste des secrétaires
     */
    public function index()
    {
        $secretaires = User::role('secretaire')->orderBy('name')->paginate(10);
        return view('esbtp.secretaires.index', compact('secretaires'));
    }

    /**
     * Affiche le formulaire de création d'un secrétaire
     */
    public function create()
    {
        return view('esbtp.secretaires.create');
    }

    /**
     * Enregistre un nouveau secrétaire
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
            'is_active' => true,
        ]);

        // Assigner le rôle secrétaire
        $role = Role::firstOrCreate(['name' => 'secretaire']);
        $user->assignRole($role);

        return redirect()->route('secretaires.index')
            ->with('success', 'Secrétaire créé avec succès');
    }

    /**
     * Affiche les détails d'un secrétaire
     */
    public function show($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        return view('esbtp.secretaires.show', compact('secretaire'));
    }

    /**
     * Affiche le formulaire d'édition d'un secrétaire
     */
    public function edit($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        return view('esbtp.secretaires.edit', compact('secretaire'));
    }

    /**
     * Met à jour un secrétaire
     */
    public function update(Request $request, $id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mettre à jour l'utilisateur
        $secretaire->name = $request->name;
        $secretaire->email = $request->email;
        $secretaire->username = $request->username;
        if ($request->filled('password')) {
            $secretaire->password = Hash::make($request->password);
        }
        $secretaire->telephone = $request->telephone;
        $secretaire->adresse = $request->adresse;
        $secretaire->save();

        return redirect()->route('secretaires.index')
            ->with('success', 'Secrétaire mis à jour avec succès');
    }

    /**
     * Supprime un secrétaire
     */
    public function destroy($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        $secretaire->delete();

        return redirect()->route('secretaires.index')
            ->with('success', 'Secrétaire supprimé avec succès');
    }
} 