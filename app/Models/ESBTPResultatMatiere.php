<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESBTPResultatMatiere extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_resultat_matieres';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'bulletin_id',
        'matiere_id',
        'moyenne',
        'coefficient',
        'rang',
        'appreciation',
        'total_notes',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'moyenne' => 'float',
        'coefficient' => 'float',
        'rang' => 'integer',
        'total_notes' => 'integer',
    ];

    /**
     * Indique si les timestamps sont utilisés.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relation avec le bulletin associé à ce résultat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bulletin()
    {
        return $this->belongsTo(ESBTPBulletin::class, 'bulletin_id');
    }

    /**
     * Relation avec la matière associée à ce résultat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matiere()
    {
        return $this->belongsTo(ESBTPMatiere::class, 'matiere_id');
    }

    /**
     * Obtenir la moyenne pondérée (moyenne * coefficient).
     *
     * @return float
     */
    public function getMoyennePondereeAttribute()
    {
        return round($this->moyenne * $this->coefficient, 2);
    }

    /**
     * Obtenir la mention associée à la moyenne.
     *
     * @return string
     */
    public function getMentionAttribute()
    {
        $moyenne = $this->moyenne;
        
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
} 