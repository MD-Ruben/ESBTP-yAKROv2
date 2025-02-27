<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
// Commentons cette ligne car le trait n'est pas trouvé
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // Commentons cette ligne car le trait n'est pas trouvé
    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Vérifier si l'application est installée
        if (!$this->isInstalled()) {
            return redirect()->route('setup.index');
        }

        return view('auth.login');
    }

    /**
     * Gère une demande de connexion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Si la tentative de connexion échoue, nous augmenterons le nombre de tentatives
        // et redirigerons l'utilisateur vers le formulaire de connexion. Bien sûr, lorsque cela
        // l'utilisateur dépasse leur nombre maximum de tentatives, il sera bloqué.
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // Si la tentative de connexion a échoué, nous augmenterons le nombre de tentatives
        // pour connecter l'utilisateur et rediriger l'utilisateur vers le formulaire de connexion.
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Valide la demande de connexion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Tente de connecter l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Obtient les informations d'identification nécessaires pour la tentative de connexion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /**
     * Envoie la réponse après que l'utilisateur se soit connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Obtient le chemin de redirection après la connexion.
     *
     * @return string
     */
    protected function redirectPath()
    {
        return $this->redirectTo;
    }

    /**
     * Envoie la réponse après que la tentative de connexion de l'utilisateur ait échoué.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Vérifie si l'application est installée (si un administrateur existe)
     */
    private function isInstalled()
    {
        // Vérifier si le fichier .env existe
        $envExists = file_exists(base_path('.env'));
        
        // Vérifier si la connexion à la base de données fonctionne
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbConnected = false;
        }
        
        // Vérifier si des utilisateurs existent dans la base de données
        $usersExist = false;
        if ($dbConnected && Schema::hasTable('users')) {
            try {
                $usersExist = DB::table('users')->count() > 0;
            } catch (\Exception $e) {
                $usersExist = false;
            }
        }
        
        return $envExists && $dbConnected && $usersExist;
    }
} 