<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\ESBTPParent;

class ParentSettingsController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche la page des paramètres
     */
    public function index()
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Récupérer les étudiants liés à ce parent
        $etudiants = $parent->etudiants;

        return view('parent.settings.index', [
            'parent' => $parent,
            'user' => $user,
            'etudiants' => $etudiants
        ]);
    }

    /**
     * Met à jour les informations de profil du parent
     */
    public function updateProfile(Request $request)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Valider les données
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenoms' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:100',
            'genre' => 'required|in:M,F'
        ]);

        // Mettre à jour le profil parent
        $parent->update($validated);

        return redirect()->route('parent.settings.index')->with('success', 'Votre profil a été mis à jour avec succès');
    }

    /**
     * Met à jour la photo de profil du parent
     */
    public function updatePhoto(Request $request)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Valider la photo
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Supprimer l'ancienne photo si elle existe
        if ($parent->photo_url && Storage::exists('public/profiles/' . basename($parent->photo_url))) {
            Storage::delete('public/profiles/' . basename($parent->photo_url));
        }

        // Stocker la nouvelle photo
        $path = $request->file('profile_photo')->store('public/profiles');
        $url = Storage::url($path);

        // Mettre à jour le chemin de la photo
        $parent->photo_url = $url;
        $parent->save();

        return redirect()->route('parent.settings.index')->with('success', 'Votre photo de profil a été mise à jour avec succès');
    }

    /**
     * Met à jour le mot de passe du parent
     */
    public function updatePassword(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Valider les données
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'new_password_confirmation' => 'required'
        ]);

        // Mettre à jour le mot de passe
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('parent.settings.index')->with('success', 'Votre mot de passe a été mis à jour avec succès');
    }

    /**
     * Met à jour les préférences de notifications du parent
     */
    public function updateNotifications(Request $request)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Récupérer les préférences actuelles ou initialiser un nouvel objet
        $settings = $parent->settings ?? new \stdClass();

        // Mettre à jour les préférences
        $settings->notify_absences = $request->has('notify_absences');
        $settings->notify_grades = $request->has('notify_grades');
        $settings->notify_messages = $request->has('notify_messages');
        $settings->notify_payments = $request->has('notify_payments');
        $settings->notify_announcements = $request->has('notify_announcements');
        $settings->channels = $request->input('channels', ['app']);

        // Sauvegarder les préférences
        $parent->settings = $settings;
        $parent->save();

        return redirect()->route('parent.settings.index')->with('success', 'Vos préférences de notifications ont été mises à jour avec succès');
    }
} 