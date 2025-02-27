<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPCycle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_cycles';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'duration',
        'diploma_awarded',
        'is_active',
        'description',
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
     * Obtenir les spécialités associées à ce cycle.
     * 
     * Un cycle peut avoir plusieurs spécialités (relation one-to-many).
     * Par exemple, le cycle Ingénieurs peut avoir les spécialités Génie Civil, Génie Minier, etc.
     */
    public function specialties()
    {
        return $this->hasMany(ESBTPSpecialty::class, 'cycle_id');
    }

    /**
     * Obtenir les années d'études associées à ce cycle.
     * 
     * Un cycle est composé de plusieurs années d'études (relation one-to-many).
     * Par exemple, le cycle Ingénieurs a 3 années d'études.
     */
    public function studyYears()
    {
        return $this->hasMany(ESBTPStudyYear::class, 'cycle_id');
    }
}
