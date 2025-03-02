<?php

namespace App\Http\Controllers\ESBTP;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SecretaireAdminController extends Controller
{
    /**
     * Constructeur qui applique le middleware auth et role:superAdmin.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:superAdmin']);
    }

    /**
     * Affiche la liste des secrétaires.
     */
    public function index()
    {
        $secretaires = User::role('secretaire')->latest()->paginate(10);
        return view('esbtp.secretaires.index', compact('secretaires'));
    }

    /**
     * Affiche le formulaire de création d'un secrétaire.
     */
    public function create()
    {
        return view('esbtp.secretaires.create');
    }

    /**
     * Enregistre un nouveau secrétaire.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Création de l'utilisateur
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $request->phone ?? null,
                'address' => $request->address ?? null,
                'is_active' => true,
            ]);

            // Attribution du rôle secrétaire
            $role = Role::where('name', 'secretaire')->first();
            $user->assignRole($role);

            DB::commit();

            return redirect()->route('secretaires.index')
                ->with('success', 'Le compte secrétaire a été créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Une erreur est survenue lors de la création du compte: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'un secrétaire.
     */
    public function show($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        return view('esbtp.secretaires.show', compact('secretaire'));
    }

    /**
     * Affiche le formulaire d'édition d'un secrétaire.
     */
    public function edit($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        return view('esbtp.secretaires.edit', compact('secretaire'));
    }

    /**
     * Met à jour les informations d'un secrétaire.
     */
    public function update(Request $request, $id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);

        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $secretaire->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Mise à jour du compte
        try {
            $secretaire->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $request->phone ?? $secretaire->phone,
                'address' => $request->address ?? $secretaire->address,
                'is_active' => $request->has('is_active'),
            ]);

            // Mise à jour du mot de passe si fourni
            if ($request->filled('password') && $request->filled('password_confirmation')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $secretaire->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            return redirect()->route('secretaires.index')
                ->with('success', 'Le compte secrétaire a été mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du compte: ' . $e->getMessage()]);
        }
    }

    /**
     * Supprime un secrétaire.
     */
    public function destroy($id)
    {
        $secretaire = User::role('secretaire')->findOrFail($id);
        try {
            $secretaire->delete();
            return redirect()->route('secretaires.index')
                ->with('success', 'Le compte secrétaire a été supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression du compte: ' . $e->getMessage()]);
        }
    }
} 