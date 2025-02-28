<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPSalle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'e_s_b_t_p_salles';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'type',        // Type de salle (Amphi, TD, TP, etc.)
        'capacity',    // Capacité d'accueil
        'building',    // Bâtiment où se trouve la salle
        'floor',       // Étage de la salle
        'description',
        'is_active',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'capacity' => 'integer',
        'floor' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les inscriptions associées à cette salle.
     * (Si les inscriptions peuvent être liées à des salles)
     */
    public function inscriptions()
    {
        return $this->hasMany(ESBTPInscription::class, 'salle_id');
    }

    /**
     * Vérifie si la salle est disponible à une date donnée et pour une plage horaire spécifique.
     * (Cette fonction pourra être utilisée ultérieurement pour la gestion d'emploi du temps)
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function isAvailable($date, $startTime, $endTime)
    {
        // Logique pour vérifier la disponibilité
        // À implémenter selon les besoins spécifiques
        return true;
    }

    /**
     * Retourne le nom complet de la salle avec son code et bâtiment.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->code}) - Bâtiment {$this->building}, Étage {$this->floor}";
    }
} 