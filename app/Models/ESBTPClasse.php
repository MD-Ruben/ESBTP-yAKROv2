<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPClasse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_classes';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'filiere_id',
        'niveau_etude_id',
        'annee_universitaire_id',
        'capacity',
        'description',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Les relations qui doivent toujours être chargées.
     *
     * @var array
     */
    protected $with = ['filiere', 'niveau', 'annee'];

    /**
     * Relation avec la filière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filiere()
    {
        return $this->belongsTo(ESBTPFiliere::class, 'filiere_id');
    }

    /**
     * Relation avec le niveau d'études.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function niveau()
    {
        return $this->belongsTo(ESBTPNiveauEtude::class, 'niveau_etude_id');
    }

    /**
     * Relation avec l'année universitaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function annee()
    {
        return $this->belongsTo(ESBTPAnneeUniversitaire::class, 'annee_universitaire_id');
    }

    /**
     * Relation avec les inscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscriptions()
    {
        return $this->hasMany(ESBTPInscription::class, 'classe_id');
    }

    /**
     * Relation avec les emplois du temps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emploisDuTemps()
    {
        return $this->hasMany(ESBTPEmploiTemps::class, 'classe_id');
    }

    /**
     * Relation avec les évaluations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluations()
    {
        return $this->hasMany(ESBTPEvaluation::class, 'classe_id');
    }

    /**
     * Relation avec les matières associées à cette classe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matieres()
    {
        return $this->belongsToMany(ESBTPMatiere::class, 'esbtp_classe_matiere', 'classe_id', 'matiere_id')
                    ->withPivot('coefficient', 'total_heures', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Récupérer les étudiants inscrits dans cette classe.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function etudiants()
    {
        return $this->hasManyThrough(
            User::class,
            ESBTPInscription::class,
            'classe_id', // Clé étrangère sur la table inscriptions
            'id', // Clé primaire sur la table users
            'id', // Clé primaire sur la table classes
            'student_id' // Clé étrangère sur la table inscriptions
        );
    }

    /**
     * Nombre d'étudiants actuellement inscrits dans cette classe.
     *
     * @return int
     */
    public function getNombreEtudiantsAttribute()
    {
        return $this->inscriptions()->where('status', 'active')->count();
    }

    /**
     * Places encore disponibles dans cette classe.
     *
     * @return int
     */
    public function getPlacesDisponiblesAttribute()
    {
        $placesOccupees = $this->nombre_etudiants;
        return max(0, $this->capacity - $placesOccupees);
    }

    /**
     * Nom complet de la classe (exemple: "GC-BAT BTS1 2023-2024").
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        $filiere = $this->filiere ? $this->filiere->code : '';
        $niveau = $this->niveau ? $this->niveau->code : '';
        $annee = $this->annee ? $this->annee->name : '';
        
        return "{$filiere} {$niveau} {$annee}";
    }

    /**
     * Utilisateur qui a créé l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Utilisateur qui a mis à jour l'entrée.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Alias pour la relation avec le niveau d'étude.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function niveauEtude()
    {
        return $this->niveau();
    }

    /**
     * Alias pour la relation avec l'année universitaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anneeUniversitaire()
    {
        return $this->annee();
    }
} 