<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPMessage;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ParentMessageController extends Controller
{
    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche la liste des messages du parent.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les messages
        $messages = ESBTPMessage::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('parent.messages.index', compact('messages', 'parent'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau message.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les administrateurs pouvant être contactés
        $admins = User::role('admin')->get();
        
        // Récupérer les professeurs des étudiants du parent
        $etudiantIds = $parent->etudiants()->pluck('esbtp_etudiants.id')->toArray();
        $enseignants = collect();
        
        // Récupérer les étudiants du parent pour le formulaire
        $etudiants = $parent->etudiants;
        
        return view('parent.messages.create', compact('parent', 'admins', 'enseignants', 'etudiants'));
    }

    /**
     * Enregistre un nouveau message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'etudiant_id' => 'nullable|exists:esbtp_etudiants,id',
        ]);

        $message = new ESBTPMessage();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->subject = $request->subject;
        $message->content = $request->content;
        $message->etudiant_id = $request->etudiant_id;
        $message->read_at = null;
        $message->save();

        return redirect()->route('parent.messages.index')
            ->with('success', 'Votre message a été envoyé avec succès.');
    }

    /**
     * Affiche un message spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer le message s'il appartient au parent
        $message = ESBTPMessage::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->findOrFail($id);
            
        // Marquer comme lu si le parent est le destinataire et que le message n'est pas déjà lu
        if ($message->receiver_id === $user->id && !$message->read_at) {
            $message->read_at = now();
            $message->save();
        }
        
        // Récupérer les réponses à ce message
        $replies = ESBTPMessage::where('parent_id', $id)->orderBy('created_at', 'asc')->get();
            
        return view('parent.messages.show', compact('message', 'parent', 'replies'));
    }

    /**
     * Affiche le formulaire pour répondre à un message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reply($id)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer le message s'il appartient au parent
        $originalMessage = ESBTPMessage::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->findOrFail($id);
            
        return view('parent.messages.reply', compact('originalMessage', 'parent'));
    }

    /**
     * Enregistre une réponse à un message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeReply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $originalMessage = ESBTPMessage::findOrFail($id);

        $message = new ESBTPMessage();
        $message->sender_id = Auth::id();
        $message->receiver_id = $originalMessage->sender_id === Auth::id() 
            ? $originalMessage->receiver_id 
            : $originalMessage->sender_id;
        $message->subject = 'RE: ' . $originalMessage->subject;
        $message->content = $request->content;
        $message->parent_id = $id;
        $message->etudiant_id = $originalMessage->etudiant_id;
        $message->read_at = null;
        $message->save();

        return redirect()->route('parent.messages.show', $id)
            ->with('success', 'Votre réponse a été envoyée avec succès.');
    }

    /**
     * Marque un message comme lu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        
        // Récupérer le message s'il appartient au parent et s'il est destiné au parent
        $message = ESBTPMessage::where('receiver_id', $user->id)->findOrFail($id);
        
        // Marquer comme lu
        $message->read_at = now();
        $message->save();
        
        return redirect()->back()->with('success', 'Message marqué comme lu.');
    }

    /**
     * Marque tous les messages comme lus.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Marquer tous les messages non lus destinés au parent comme lus
        ESBTPMessage::whereNull('read_at')
            ->where('receiver_id', $user->id)
            ->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Tous les messages ont été marqués comme lus.');
    }
} 