<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     * 
     * @var string
     */
    protected $table = 'attendances';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_session_id',
        'status',
        'arrival_time',
        'departure_time',
        'excuse_reason',
        'has_supporting_document',
        'supporting_document_path',
        'comments',
        'recorded_by',
        'updated_by',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
        'has_supporting_document' => 'boolean',
    ];

    /**
     * Relation avec l'étudiant concerné par cette présence.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relation avec la session de cours concernée.
     */
    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    /**
     * Relation avec l'utilisateur qui a enregistré cette présence.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour cette présence.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Vérifier si l'étudiant était présent.
     * 
     * @return bool
     */
    public function isPresent()
    {
        return $this->status === 'present';
    }

    /**
     * Vérifier si l'étudiant était absent.
     * 
     * @return bool
     */
    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    /**
     * Vérifier si l'étudiant était excusé.
     * 
     * @return bool
     */
    public function isExcused()
    {
        return $this->status === 'excused';
    }

    /**
     * Vérifier si l'étudiant était en retard.
     * 
     * @return bool
     */
    public function isLate()
    {
        return $this->status === 'late';
    }

    /**
     * Calculer la durée de présence en minutes.
     * 
     * @return int|null
     */
    public function getDurationInMinutes()
    {
        if (!$this->arrival_time || !$this->departure_time) {
            return null;
        }
        
        return $this->departure_time->diffInMinutes($this->arrival_time);
    }

    /**
     * Scope pour filtrer les présences par statut.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer les présences par date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereHas('courseSession', function($q) use ($date) {
            $q->whereDate('date', $date);
        });
    }

    /**
     * Scope pour filtrer les présences par période.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereHas('courseSession', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        });
    }

    /**
     * Scope pour filtrer les présences par élément constitutif.
     */
    public function scopeForEC($query, $ecId)
    {
        return $query->whereHas('courseSession', function($q) use ($ecId) {
            $q->where('element_constitutif_id', $ecId);
        });
    }

    /**
     * Scope pour filtrer les présences par unité d'enseignement.
     */
    public function scopeForUE($query, $ueId)
    {
        return $query->whereHas('courseSession.elementConstitutif', function($q) use ($ueId) {
            $q->where('unite_enseignement_id', $ueId);
        });
    }

    /**
     * Scope pour filtrer les présences par enseignant.
     */
    public function scopeWithTeacher($query, $teacherId)
    {
        return $query->whereHas('courseSession', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });
    }
} 