<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPNiveauEtude extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_niveau_etudes';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'type',
        'year',
        'description',
        'is_active',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir les étudiants inscrits à ce niveau d'études.
     * 
     * Un niveau d'études peut avoir plusieurs étudiants.
     * Par exemple, "Première année BTS" peut avoir plusieurs étudiants inscrits.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'niveau_etude_id');
    }

    /**
     * Obtenir le nom complet du niveau d'études.
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->type . ' - ' . $this->name;
    }
}
