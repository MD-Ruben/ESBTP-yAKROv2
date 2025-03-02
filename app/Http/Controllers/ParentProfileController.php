<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\ESBTPParent;

class ParentProfileController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Met à jour le profil du parent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Mise à jour du user
        $user->name = $request->prenoms . ' ' . $request->nom;
        $user->email = $request->email;
        $user->save();

        // Mise à jour du parent
        $parent->nom = $request->nom;
        $parent->prenoms = $request->prenoms;
        $parent->telephone = $request->telephone;
        $parent->adresse = $request->adresse;
        $parent->save();

        return redirect()->route('parent.settings')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe du parent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('parent.settings')->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Met à jour la photo de profil du parent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($parent->photo) {
            Storage::delete('public/' . $parent->photo);
        }

        $path = $request->file('photo')->store('parents/photos', 'public');
        $parent->photo = $path;
        $parent->save();

        return redirect()->route('parent.settings')->with('success', 'Photo de profil mise à jour avec succès.');
    }
} 