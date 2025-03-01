<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPFiliere extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_filieres';

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
        'parent_id',
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
     * Obtenir la filière parente (pour les options).
     * 
     * Une option est liée à une filière principale.
     * Par exemple, "Bâtiments" est une option de la filière "Génie Civil".
     */
    public function parent()
    {
        return $this->belongsTo(ESBTPFiliere::class, 'parent_id');
    }

    /**
     * Obtenir les options (sous-filières) de cette filière.
     * 
     * Une filière principale peut avoir plusieurs options.
     * Par exemple, "Génie Civil" a les options "Bâtiments", "Travaux Publics", etc.
     */
    public function options()
    {
        return $this->hasMany(ESBTPFiliere::class, 'parent_id');
    }

    /**
     * Vérifier si cette filière est une option (sous-filière).
     * 
     * @return bool
     */
    public function isOption()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Vérifier si cette filière est une filière principale.
     * 
     * @return bool
     */
    public function isMainFiliere()
    {
        return is_null($this->parent_id);
    }

    /**
     * Relation avec les formations associées à cette filière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function formations()
    {
        return $this->belongsToMany(ESBTPFormation::class, 'esbtp_filiere_formation', 'filiere_id', 'formation_id')
                    ->withTimestamps();
    }
    
    /**
     * Relation avec les inscriptions associées à cette filière.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscriptions()
    {
        return $this->hasMany(ESBTPInscription::class, 'filiere_id');
    }
}
