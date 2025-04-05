<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPAnnonce;
use App\Models\ESBTPClasse;
use App\Models\ESBTPEtudiant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use Carbon\Carbon;
use App\Notifications\ESBTPNotification;

class ESBTPAnnonceController extends Controller
{
    /**
     * Affiche la liste des annonces.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $annonces = ESBTPAnnonce::with(['classes', 'etudiants', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Préparation des statistiques
        $stats = [
            'total' => ESBTPAnnonce::count(),
            'published' => ESBTPAnnonce::where('is_published', true)->count(),
            'pending' => ESBTPAnnonce::where('is_published', false)->count(),
            'urgent' => ESBTPAnnonce::where('priorite', 2)->count()
        ];

        return view('esbtp.annonces.index', compact('annonces', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'une annonce.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();
        $filieres = ESBTPFiliere::where('is_active', true)->orderBy('name')->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->orderBy('name')->get();

        return view('esbtp.annonces.create', compact('classes', 'etudiants', 'filieres', 'niveaux'));
    }

    /**
     * Enregistre une nouvelle annonce.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date_publication' => 'nullable|date',
            'date_expiration' => 'required|date|after_or_equal:date_publication',
            'type' => 'required|in:general,classe,etudiant',
            'priorite' => 'required|in:0,1,2',
            'classes' => 'required_if:type,classe|array',
            'etudiants' => 'required_if:type,etudiant|array',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'contenu.required' => 'Le contenu est obligatoire',
            'date_expiration.required' => 'La date d\'expiration est obligatoire',
            'date_expiration.after_or_equal' => 'La date d\'expiration doit être postérieure ou égale à la date de publication',
            'priorite.required' => 'La priorité est obligatoire',
            'classes.required_if' => 'Veuillez sélectionner au moins une classe',
            'etudiants.required_if' => 'Veuillez sélectionner au moins un étudiant',
        ]);

        DB::beginTransaction();
        try {
            $annonce = new ESBTPAnnonce();
            $annonce->titre = $request->titre;
            $annonce->contenu = $request->contenu;
            $annonce->date_publication = $request->date_publication ?? now();
            $annonce->date_expiration = $request->date_expiration;
            $annonce->type = $request->type;
            $annonce->priorite = $request->priorite;
            $annonce->is_published = $request->has('is_published');
            $annonce->created_by = Auth::id();
            $annonce->save();

            // Attacher les classes ou les étudiants selon le type
            if ($request->type == 'classe' && $request->has('classes')) {
                $annonce->classes()->attach($request->classes);
            } elseif ($request->type == 'etudiant' && $request->has('etudiants')) {
                $annonce->etudiants()->attach($request->etudiants);
            }

            // Envoyer des notifications si l'annonce est publiée
            if ($annonce->is_published && $annonce->date_publication <= now()) {
                $this->sendAnnonceNotification($annonce);
            }

            DB::commit();
            return redirect()->route('esbtp.annonces.index')
                ->with('success', 'L\'annonce a été créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'annonce: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche une annonce spécifique.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPAnnonce $annonce)
    {
        $annonce->load(['classes', 'etudiants', 'user']);
        return view('esbtp.annonces.show', compact('annonce'));
    }

    /**
     * Affiche le formulaire de modification d'une annonce.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPAnnonce $annonce)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();

        $classeIds = $annonce->classes->pluck('id')->toArray();
        $etudiantIds = $annonce->etudiants->pluck('id')->toArray();

        return view('esbtp.annonces.edit', compact('annonce', 'classes', 'etudiants', 'classeIds', 'etudiantIds'));
    }

    /**
     * Met à jour une annonce spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPAnnonce $annonce)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date_publication' => 'nullable|date',
            'date_expiration' => 'required|date|after_or_equal:date_publication',
            'type' => 'required|in:general,classe,etudiant',
            'priorite' => 'required|in:0,1,2',
            'classes' => 'required_if:type,classe|array',
            'etudiants' => 'required_if:type,etudiant|array',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'contenu.required' => 'Le contenu est obligatoire',
            'date_expiration.required' => 'La date d\'expiration est obligatoire',
            'date_expiration.after_or_equal' => 'La date d\'expiration doit être postérieure ou égale à la date de publication',
            'priorite.required' => 'La priorité est obligatoire',
            'classes.required_if' => 'Veuillez sélectionner au moins une classe',
            'etudiants.required_if' => 'Veuillez sélectionner au moins un étudiant',
        ]);

        DB::beginTransaction();
        try {
            $wasPublished = $annonce->is_published;

            $annonce->titre = $request->titre;
            $annonce->contenu = $request->contenu;
            $annonce->date_publication = $request->date_publication ?? $annonce->date_publication ?? now();
            $annonce->date_expiration = $request->date_expiration;
            $annonce->type = $request->type;
            $annonce->priorite = $request->priorite;
            $annonce->is_published = $request->has('is_published');
            $annonce->updated_by = Auth::id();
            $annonce->save();

            // Mettre à jour les associations
            $annonce->classes()->detach();
            $annonce->etudiants()->detach();

            if ($request->type == 'classe' && $request->has('classes')) {
                $annonce->classes()->attach($request->classes);
            } elseif ($request->type == 'etudiant' && $request->has('etudiants')) {
                $annonce->etudiants()->attach($request->etudiants);
            }

            // Envoyer des notifications si l'annonce devient publiée et est prévue pour maintenant ou le passé
            if ($annonce->is_published && !$wasPublished && $annonce->date_publication <= now()) {
                $this->sendAnnonceNotification($annonce);
            }

            DB::commit();
            return redirect()->route('esbtp.annonces.index')
                ->with('success', 'L\'annonce a été mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'annonce: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une annonce spécifique.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPAnnonce $annonce)
    {
        try {
            // Détacher d'abord toutes les relations
            $annonce->classes()->detach();
            $annonce->etudiants()->detach();

            // Puis supprimer l'annonce
            $annonce->delete();

            return redirect()->route('esbtp.annonces.index')
                ->with('success', 'L\'annonce a été supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('esbtp.annonces.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'annonce: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les messages pour un étudiant
     *
     * @return \Illuminate\Http\Response
     */
    public function studentMessages()
    {
        // Récupérer l'étudiant connecté
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->firstOrFail();

        // Récupérer la classe active de l'étudiant
        $classeActive = $etudiant->classe_active;
        $classeId = $classeActive ? $classeActive->id : null;

        // Récupérer tous les messages pertinents pour l'étudiant
        $query = ESBTPAnnonce::where('is_published', true)
            ->where('date_publication', '<=', now())
            ->where(function($q) use ($classeId, $etudiant) {
                // Messages généraux
                $q->where('type', 'general');

                // Messages pour la classe de l'étudiant (si disponible)
                if ($classeId) {
                    $q->orWhere(function($sq) use ($classeId) {
                        $sq->where('type', 'classe')
                           ->whereHas('classes', function($cq) use ($classeId) {
                                $cq->where('esbtp_classes.id', $classeId);
                           });
                    });
                }

                // Messages spécifiques à l'étudiant
                $q->orWhere(function($sq) use ($etudiant) {
                    $sq->where('type', 'etudiant')
                       ->whereHas('etudiants', function($eq) use ($etudiant) {
                            $eq->where('esbtp_etudiants.id', $etudiant->id);
                       });
                });
            })
            ->where(function($q) {
                // Seulement les messages non expirés ou sans date d'expiration
                $q->whereNull('date_expiration')
                  ->orWhere('date_expiration', '>=', now());
            });

        // Tri par priorité (descendant) puis par date (plus récent d'abord)
        $messages = $query->orderBy('priorite', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        // Pour chaque message, déterminer s'il a été lu par l'étudiant
        foreach ($messages as $message) {
            if ($message->type == 'etudiant') {
                $pivot = $message->etudiants()->wherePivot('etudiant_id', $etudiant->id)->first();
                if ($pivot) {
                    $message->is_read = $pivot->pivot->is_read;
                    $message->read_at = $pivot->pivot->read_at;
                } else {
                    $message->is_read = false;
                    $message->read_at = null;
                }
            } else {
                // Pour les messages généraux et de classe, vérifier dans la table pivot
                $readStatus = DB::table('esbtp_annonce_lectures')
                    ->where('annonce_id', $message->id)
                    ->where('etudiant_id', $etudiant->id)
                    ->first();

                $message->is_read = $readStatus ? true : false;
                $message->read_at = $readStatus ? $readStatus->read_at : null;
            }
        }

        // Statistiques des messages
        $stats = [
            'total' => $messages->total(),
            'unread' => $query->whereDoesntHave('lectures', function($q) use ($etudiant) {
                $q->where('etudiant_id', $etudiant->id);
            })->count(),
            'urgent' => $query->where('priorite', 2)->count()
        ];

        return view('esbtp.annonces.student-messages', compact('messages', 'stats', 'etudiant'));
    }

    /**
     * Marque un message comme lu par l'étudiant connecté
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->firstOrFail();
        $annonce = ESBTPAnnonce::findOrFail($id);

        try {
            DB::beginTransaction();

            if ($annonce->type == 'etudiant') {
                // Pour les messages spécifiques aux étudiants
                $annonce->marquerCommeLue($etudiant->id);
            } else {
                // Pour les messages généraux et de classe
                // Vérifier si une entrée existe déjà
                $exists = DB::table('esbtp_annonce_lectures')
                    ->where('annonce_id', $id)
                    ->where('etudiant_id', $etudiant->id)
                    ->exists();

                if (!$exists) {
                    // Créer une nouvelle entrée
                    DB::table('esbtp_annonce_lectures')->insert([
                        'annonce_id' => $id,
                        'etudiant_id' => $etudiant->id,
                        'read_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Message marqué comme lu.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Marque tous les messages comme lus par l'étudiant connecté
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->firstOrFail();

        try {
            DB::beginTransaction();

            // Récupérer tous les messages non lus de l'étudiant
            $query = ESBTPAnnonce::where('is_published', true)
                ->where('date_publication', '<=', now())
                ->where(function($q) use ($etudiant) {
                    // Messages généraux
                    $q->where('type', 'general');

                    // Messages pour la classe de l'étudiant
                    if ($etudiant->classe_active) {
                        $q->orWhere(function($sq) use ($etudiant) {
                            $sq->where('type', 'classe')
                               ->whereHas('classes', function($cq) use ($etudiant) {
                                    $cq->where('esbtp_classes.id', $etudiant->classe_active->id);
                               });
                        });
                    }

                    // Messages spécifiques à l'étudiant
                    $q->orWhere(function($sq) use ($etudiant) {
                        $sq->where('type', 'etudiant')
                           ->whereHas('etudiants', function($eq) use ($etudiant) {
                                $eq->where('esbtp_etudiants.id', $etudiant->id);
                           });
                    });
                })
                ->whereDoesntHave('lectures', function($q) use ($etudiant) {
                    $q->where('etudiant_id', $etudiant->id);
                });

            $messages = $query->get();

            // Marquer tous les messages comme lus
            foreach ($messages as $message) {
                if ($message->type == 'etudiant') {
                    $message->marquerCommeLue($etudiant->id);
                } else {
                    // Pour les messages généraux et de classe
                    DB::table('esbtp_annonce_lectures')->insert([
                        'annonce_id' => $message->id,
                        'etudiant_id' => $etudiant->id,
                        'read_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Tous les messages ont été marqués comme lus.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Envoie des notifications aux étudiants concernés par une annonce
     *
     * @param ESBTPAnnonce $annonce
     * @return void
     */
    private function sendAnnonceNotification(ESBTPAnnonce $annonce)
    {
        try {
            // Récupérer la liste des étudiants à notifier en fonction du type d'annonce
            $etudiants = collect();

            if ($annonce->type == 'general') {
                // Pour les annonces générales, notifier tous les étudiants actifs
                // Suppression de whereHas('user') pour envoyer à tous les étudiants
                $etudiants = ESBTPEtudiant::all();
            } elseif ($annonce->type == 'classe') {
                // Pour les annonces de classe, notifier les étudiants des classes concernées
                // Suppression de whereHas('user') pour envoyer à tous les étudiants des classes concernées
                $etudiants = ESBTPEtudiant::whereHas('classe_active', function($query) use ($annonce) {
                    $query->whereIn('id', $annonce->classes->pluck('id'));
                })
                ->get();
            } elseif ($annonce->type == 'etudiant') {
                // Pour les annonces destinées à des étudiants spécifiques
                // Suppression de whereHas('user') pour envoyer à tous les étudiants sélectionnés
                $etudiants = $annonce->etudiants()->get();
            }

            // Déterminer le type de notification en fonction de la priorité
            $notificationType = 'info';
            if ($annonce->priorite == 1) {
                $notificationType = 'warning';
            } elseif ($annonce->priorite == 2) {
                $notificationType = 'danger';
            }

            // Notifier chaque étudiant
            $notifiedCount = 0;
            foreach ($etudiants as $etudiant) {
                // N'envoyer la notification que si l'étudiant a un compte utilisateur
                if ($etudiant->user) {
                    try {
                        $etudiant->user->notify(new ESBTPNotification(
                            'Nouvelle annonce: ' . $annonce->titre,
                            $annonce->contenu,
                            $notificationType,
                            ['annonce_id' => $annonce->id]
                        ));
                        $notifiedCount++;
                    } catch (\Exception $e) {
                        \Log::error("Erreur lors de l'envoi de la notification à l'étudiant #{$etudiant->id}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            \Log::info("Notifications envoyées pour l'annonce #{$annonce->id}", [
                'titre' => $annonce->titre,
                'type' => $annonce->type,
                'etudiants_notifies' => $notifiedCount,
                'total_etudiants' => $etudiants->count()
            ]);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'envoi des notifications pour l'annonce #{$annonce->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
