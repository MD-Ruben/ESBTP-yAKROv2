<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPSeanceCours extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_seance_cours';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'emploi_temps_id',
        'classe_id',
        'matiere_id',
        'enseignant',
        'jour',
        'heure_debut',
        'heure_fin',
        'salle',
        'description',
        'annee_universitaire_id',
        'is_active',
        'type_seance',
        'created_at',
        'updated_at'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec l'emploi du temps associé à cette séance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emploiTemps()
    {
        // Inclure les emplois du temps soft-deleted pour éviter les erreurs
        return $this->belongsTo(ESBTPEmploiTemps::class, 'emploi_temps_id')->withTrashed();
    }

    /**
     * Relation avec la matière associée à cette séance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matiere()
    {
        return $this->belongsTo(ESBTPMatiere::class, 'matiere_id');
    }

    /**
     * Relation avec l'enseignant associé à cette séance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enseignant()
    {
        // Cette relation est définie pour permettre le eager loading,
        // même si le champ 'enseignant' est une chaîne de caractères et non une clé étrangère.
        // Nous utilisons une relation qui ne causera pas d'erreur lors du eager loading.
        return $this->belongsTo(User::class, 'id', 'id')->whereRaw('1=0');
    }

    /**
     * Accesseur pour obtenir le nom de l'enseignant.
     *
     * @return string
     */
    public function getEnseignantNameAttribute()
    {
        return !empty($this->enseignant) ? $this->enseignant : 'Non défini';
    }

    /**
     * Relation avec l'utilisateur qui a créé la séance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour la séance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir le jour de la semaine en texte.
     *
     * @return string
     */
    public function getJourSemaineTexteAttribute()
    {
        $jours = [
            0 => 'Lundi',
            1 => 'Mardi',
            2 => 'Mercredi',
            3 => 'Jeudi',
            4 => 'Vendredi',
            5 => 'Samedi',
            6 => 'Dimanche',
        ];

        return $jours[$this->jour] ?? 'Jour inconnu';
    }

    /**
     * Obtenir la durée de la séance en minutes.
     *
     * @return int
     */
    public function getDureeMinutesAttribute()
    {
        if (!$this->heure_debut || !$this->heure_fin) {
            return 0;
        }

        $debut = $this->heure_debut;
        $fin = $this->heure_fin;

        // Convertir les heures en objets Carbon si ce sont des chaînes
        if (is_string($debut)) {
            $debut = \Carbon\Carbon::createFromFormat('H:i', $debut);
        }

        if (is_string($fin)) {
            $fin = \Carbon\Carbon::createFromFormat('H:i', $fin);
        }

        // Calculer la différence en minutes
        return $fin->diffInMinutes($debut);
    }

    /**
     * Obtenir la plage horaire au format HH:MM - HH:MM.
     *
     * @return string
     */
    public function getPlageHoraireAttribute()
    {
        $debut = $this->heure_debut ? $this->heure_debut->format('H:i') : '--:--';
        $fin = $this->heure_fin ? $this->heure_fin->format('H:i') : '--:--';

        return "{$debut} - {$fin}";
    }

    /**
     * Vérifie si la séance est en conflit avec une autre séance.
     *
     * @param ESBTPSeanceCours $autreSeance
     * @return bool
     */
    public function estEnConflitAvec(ESBTPSeanceCours $autreSeance)
    {
        // Vérifier si les séances sont le même jour
        if ($this->jour !== $autreSeance->jour) {
            return false;
        }

        // Vérifier si les plages horaires se chevauchent
        return ($this->heure_debut < $autreSeance->heure_fin) &&
               ($this->heure_fin > $autreSeance->heure_debut);
    }

    /**
     * Calcule la date réelle de la séance en fonction du jour de la semaine et de la période de l'emploi du temps.
     *
     * @return \Carbon\Carbon|null La date de la séance ou null si l'emploi du temps n'est pas défini
     */
    public function getDateSeance()
    {
        if (!$this->emploiTemps) {
            return null;
        }

        // Récupérer la date de début de l'emploi du temps
        $dateDebut = \Carbon\Carbon::parse($this->emploiTemps->date_debut);

        // Calculer le décalage entre le jour de la semaine de la date de début (1 = lundi, 7 = dimanche)
        // et le jour de la séance (1 = lundi, 7 = dimanche)
        $jourDebutSemaine = $dateDebut->dayOfWeek ?: 7; // Carbon retourne 0 pour dimanche, on le convertit en 7

        // Calculer le nombre de jours à ajouter
        $joursAAjouter = 0;
        if ($this->jour >= $jourDebutSemaine) {
            $joursAAjouter = $this->jour - $jourDebutSemaine;
        } else {
            $joursAAjouter = 7 - $jourDebutSemaine + $this->jour;
        }

        // Si le jour calculé dépasse la date de fin, on retourne null
        $dateSeance = $dateDebut->copy()->addDays($joursAAjouter);
        if ($dateSeance->isAfter($this->emploiTemps->date_fin)) {
            return null;
        }

        return $dateSeance;
    }

    /**
     * Retourne le nom du jour de la semaine.
     *
     * @return string Le nom du jour de la semaine
     */
    public function getNomJour()
    {
        $jours = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        return $jours[$this->jour] ?? 'Jour inconnu';
    }
}
