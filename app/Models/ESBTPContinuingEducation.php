<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESBTPContinuingEducation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'esbtp_continuing_education';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'code',
        'type',
        'department_id',
        'coordinator_name',
        'duration',
        'duration_unit',
        'start_date',
        'end_date',
        'price',
        'max_participants',
        'image',
        'is_active',
        'description',
        'objectives',
        'target_audience',
        'prerequisites',
        'certification',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'integer',
        'price' => 'float',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtenir le département associé à cette formation continue.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Obtenir l'URL complète de l'image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        return asset('storage/' . $this->image);
    }

    /**
     * Obtenir la durée formatée.
     *
     * @return string
     */
    public function getFormattedDurationAttribute()
    {
        $units = [
            'hours' => 'heure(s)',
            'days' => 'jour(s)',
            'weeks' => 'semaine(s)',
            'months' => 'mois',
        ];
        
        $unit = $units[$this->duration_unit] ?? $this->duration_unit;
        
        return "{$this->duration} {$unit}";
    }

    /**
     * Obtenir les étudiants inscrits à cette formation continue.
     * 
     * Une formation continue peut avoir plusieurs étudiants (relation many-to-many).
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'esbtp_continuing_education_student', 'continuing_education_id', 'student_id')
            ->withPivot('registration_date', 'status', 'payment_status')
            ->withTimestamps();
    }

    /**
     * Vérifier si la formation continue est en cours.
     */
    public function getIsOngoingAttribute()
    {
        $today = now()->startOfDay();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Vérifier si la formation continue est terminée.
     */
    public function getIsCompletedAttribute()
    {
        return $this->end_date < now()->startOfDay();
    }

    /**
     * Vérifier si la formation continue est à venir.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now()->startOfDay();
    }
}
