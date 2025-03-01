<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPFormation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_formations';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
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
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation avec les matières associées à cette formation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matieres()
    {
        return $this->belongsToMany(ESBTPMatiere::class, 'esbtp_formation_matiere', 'formation_id', 'matiere_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les filières associées à cette formation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function filieres()
    {
        return $this->belongsToMany(ESBTPFiliere::class, 'esbtp_filiere_formation', 'formation_id', 'filiere_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les niveaux d'études associés à cette formation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function niveauxEtudes()
    {
        return $this->belongsToMany(ESBTPNiveauEtude::class, 'esbtp_formation_niveau', 'formation_id', 'niveau_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec l'utilisateur qui a créé l'enregistrement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour l'enregistrement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 