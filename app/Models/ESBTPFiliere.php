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
        'option_filiere',
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
     * Get the study levels associated with this filière.
     */
    public function niveaux()
    {
        return $this->belongsToMany(ESBTPNiveauEtude::class, 'esbtp_filiere_niveau', 'filiere_id', 'niveau_etude_id');
    }

    /**
     * Alias for niveaux() for backward compatibility.
     * @deprecated Use niveaux() instead
     */
    public function niveauxEtudes()
    {
        return $this->niveaux();
    }

    /**
     * Get the subjects associated with this filière.
     */
    public function matieres()
    {
        return $this->belongsToMany(ESBTPMatiere::class, 'esbtp_matiere_filiere', 'filiere_id', 'matiere_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Get the classes associated with this filière.
     */
    public function classes()
    {
        return $this->hasMany(ESBTPClasse::class, 'filiere_id');
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

    /**
     * Vérifie si cette filière est un descendant d'une autre filière.
     * Utilisé pour éviter les cycles dans la hiérarchie des filières.
     *
     * @param ESBTPFiliere $filiere La filière potentiellement ancêtre
     * @return bool
     */
    public function isDescendantOf(ESBTPFiliere $filiere)
    {
        // Si cette filière est une option directe de la filière passée en paramètre
        if ($this->parent_id == $filiere->id) {
            return true;
        }

        // Vérifier récursivement si l'un des parents est un descendant
        if ($this->parent_id) {
            $parent = $this->parent;
            if ($parent && $parent->id != $this->id) { // Éviter les boucles infinies
                return $parent->isDescendantOf($filiere);
            }
        }

        return false;
    }
}
