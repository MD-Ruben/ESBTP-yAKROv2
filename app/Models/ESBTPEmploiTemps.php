<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPEmploiTemps extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_emploi_temps';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'classe_id',
        'semestre',
        'date_debut',
        'date_fin',
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
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec la classe associée à cet emploi du temps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(ESBTPClasse::class, 'classe_id');
    }

    /**
     * Relation avec les séances de cours de cet emploi du temps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seances()
    {
        return $this->hasMany(ESBTPSeanceCours::class, 'emploi_temps_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé l'emploi du temps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour l'emploi du temps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir les séances de cours groupées par jour de la semaine.
     *
     * @return array
     */
    public function getSeancesParJour()
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
        
        $seancesParJour = [];
        
        foreach ($jours as $index => $jour) {
            $seancesParJour[$jour] = $this->seances()
                ->where('jour_semaine', $index)
                ->orderBy('heure_debut')
                ->get();
        }
        
        return $seancesParJour;
    }

    /**
     * Obtenir la période de validité de l'emploi du temps.
     *
     * @return string
     */
    public function getPeriodeValiditeAttribute()
    {
        $debut = $this->date_debut ? $this->date_debut->format('d/m/Y') : 'Non définie';
        $fin = $this->date_fin ? $this->date_fin->format('d/m/Y') : 'Non définie';
        
        return "Du {$debut} au {$fin}";
    }

    /**
     * Vérifie si l'emploi du temps est en cours de validité.
     *
     * @return bool
     */
    public function estEnCours()
    {
        $now = now();
        return $this->is_active 
            && $this->date_debut <= $now 
            && ($this->date_fin === null || $this->date_fin >= $now);
    }

    /**
     * Vérifie s'il y a des conflits d'horaire dans les séances de cours.
     *
     * @return array
     */
    public function verifierConflitsHoraire()
    {
        $conflicts = [];
        $seancesParJour = $this->getSeancesParJour();
        
        foreach ($seancesParJour as $jour => $seances) {
            for ($i = 0; $i < count($seances); $i++) {
                for ($j = $i + 1; $j < count($seances); $j++) {
                    $seance1 = $seances[$i];
                    $seance2 = $seances[$j];
                    
                    // Vérifier si les horaires se chevauchent
                    if (($seance1->heure_debut < $seance2->heure_fin) && 
                        ($seance1->heure_fin > $seance2->heure_debut)) {
                        $conflicts[] = [
                            'jour' => $jour,
                            'seance1' => $seance1,
                            'seance2' => $seance2,
                        ];
                    }
                }
            }
        }
        
        return $conflicts;
    }
} 