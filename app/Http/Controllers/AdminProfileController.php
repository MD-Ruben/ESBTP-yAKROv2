<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superAdmin|secretaire');
    }

    /**
     * Affiche le profil de l'administrateur ou du secrétaire.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        return view('admin.profile', compact('user'));
    }

    /**
     * Met à jour le profil de l'administrateur ou du secrétaire.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->birth_date = $request->birth_date;

        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo de profil si elle existe
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }

            // Sauvegarder la nouvelle photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Met à jour les informations professionnelles de l'administrateur.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfessionalInfo(Request $request)
    {
        $request->validate([
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'office_location' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:50',
            'appointment_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $user->position = $request->position;
        $user->department = $request->department;
        $user->office_location = $request->office_location;
        $user->employee_id = $request->employee_id;
        $user->appointment_date = $request->appointment_date;

        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Informations professionnelles mises à jour avec succès');
    }

    /**
     * Met à jour le mot de passe de l'administrateur ou du secrétaire.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Vérifier si le mot de passe actuel est correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Mot de passe mis à jour avec succès');
    }
}
