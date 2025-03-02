<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPPaiement;

class ParentPaymentController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche la liste des paiements pour les enfants du parent
     */
    public function index()
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Récupérer tous les étudiants liés à ce parent
        $etudiants = $parent->etudiants;

        // Récupérer les paiements associés à ces étudiants
        $etudiantIds = $etudiants->pluck('id')->toArray();
        $paiements = ESBTPPaiement::whereIn('etudiant_id', $etudiantIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('etudiant_id');

        return view('parent.payments.index', [
            'parent' => $parent,
            'etudiants' => $etudiants,
            'paiements' => $paiements
        ]);
    }

    /**
     * Affiche le détail d'un paiement spécifique
     */
    public function show($id)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Récupérer les IDs des étudiants liés à ce parent
        $etudiantIds = $parent->etudiants->pluck('id')->toArray();

        // Récupérer le paiement et vérifier qu'il appartient à un des enfants du parent
        $paiement = ESBTPPaiement::with('etudiant')->findOrFail($id);

        if (!in_array($paiement->etudiant_id, $etudiantIds)) {
            abort(403, 'Accès non autorisé à ce paiement');
        }

        return view('parent.payments.show', [
            'parent' => $parent,
            'paiement' => $paiement
        ]);
    }

    /**
     * Télécharge le reçu d'un paiement
     */
    public function downloadReceipt($id)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Récupérer les IDs des étudiants liés à ce parent
        $etudiantIds = $parent->etudiants->pluck('id')->toArray();

        // Récupérer le paiement et vérifier qu'il appartient à un des enfants du parent
        $paiement = ESBTPPaiement::with('etudiant')->findOrFail($id);

        if (!in_array($paiement->etudiant_id, $etudiantIds)) {
            abort(403, 'Accès non autorisé à ce paiement');
        }

        // Vérifier si le paiement a un reçu
        if (empty($paiement->receipt_path)) {
            return back()->with('error', 'Aucun reçu disponible pour ce paiement');
        }

        // Télécharger le reçu
        $path = storage_path('app/' . $paiement->receipt_path);
        
        if (!file_exists($path)) {
            return back()->with('error', 'Le fichier de reçu est introuvable');
        }

        return response()->download($path, 'recu-paiement-' . $paiement->reference . '.pdf');
    }

    /**
     * Affiche l'historique des paiements pour un étudiant spécifique
     */
    public function studentHistory($etudiantId)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Vérifier que l'étudiant appartient à ce parent
        $etudiant = $parent->etudiants()->findOrFail($etudiantId);

        // Récupérer tous les paiements de cet étudiant
        $paiements = ESBTPPaiement::where('etudiant_id', $etudiantId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('parent.payments.student_history', [
            'parent' => $parent,
            'etudiant' => $etudiant,
            'paiements' => $paiements
        ]);
    }

    /**
     * Initier un nouveau paiement (redirection vers une passerelle de paiement)
     */
    public function initiate(Request $request)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')->with('error', 'Profil parent introuvable');
        }

        // Valider les données
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'montant' => 'required|numeric|min:1000',
            'mode_paiement' => 'required|in:mobile_money,carte_credit,virement',
            'description' => 'nullable|string|max:255'
        ]);

        // Vérifier que l'étudiant appartient à ce parent
        $etudiantIds = $parent->etudiants->pluck('id')->toArray();
        if (!in_array($validated['etudiant_id'], $etudiantIds)) {
            abort(403, 'Accès non autorisé à cet étudiant');
        }

        // Préparer les données du paiement
        $reference = 'PAY-' . time() . '-' . rand(1000, 9999);
        $paiementData = [
            'etudiant_id' => $validated['etudiant_id'],
            'montant' => $validated['montant'],
            'reference' => $reference,
            'mode_paiement' => $validated['mode_paiement'],
            'status' => 'en_attente',
            'description' => $validated['description'] ?? 'Paiement de frais scolaires',
            'parent_id' => $parent->id
        ];

        // Enregistrer le paiement en attente
        $paiement = ESBTPPaiement::create($paiementData);

        // Rediriger vers la passerelle de paiement appropriée
        // Note: Ceci est un exemple, l'implémentation réelle dépendra de votre passerelle de paiement
        switch ($validated['mode_paiement']) {
            case 'mobile_money':
                return redirect()->route('payment.mobile_money.checkout', ['reference' => $reference]);
            case 'carte_credit':
                return redirect()->route('payment.credit_card.checkout', ['reference' => $reference]);
            case 'virement':
                return redirect()->route('payment.bank_transfer.instructions', ['reference' => $reference]);
            default:
                return redirect()->route('parent.payments.index')->with('error', 'Mode de paiement non pris en charge');
        }
    }
} 