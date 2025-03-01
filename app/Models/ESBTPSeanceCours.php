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
        'matiere_id',
        'enseignant_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'salle',
        'details',
        'type_seance',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'jour_semaine' => 'integer', // 0 = Lundi, 1 = Mardi, etc.
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'is_active' => 'boolean',
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
        return $this->belongsTo(ESBTPEmploiTemps::class, 'emploi_temps_id');
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
        return $this->belongsTo(User::class, 'enseignant_id');
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
        
        return $jours[$this->jour_semaine] ?? 'Jour inconnu';
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
        if ($this->jour_semaine !== $autreSeance->jour_semaine) {
            return false;
        }
        
        // Vérifier si les plages horaires se chevauchent
        return ($this->heure_debut < $autreSeance->heure_fin) && 
               ($this->heure_fin > $autreSeance->heure_debut);
    }
} 