<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'date',
        'status', // present, absent, late, justified
        'remark',
        'taken_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student that owns the attendance.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that owns the attendance.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the section that owns the attendance.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the user who took the attendance.
     */
    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    /**
     * Scope a query to only include attendances for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include attendances for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to only include attendances for a specific section.
     */
    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Scope a query to only include present attendances.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope a query to only include absent attendances.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope a query to only include late attendances.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope a query to only include justified absences.
     */
    public function scopeJustified($query)
    {
        return $query->where('status', 'justified');
    }

    /**
     * Get the justifications for this attendance.
     */
    public function justifications()
    {
        return $this->hasMany(AbsenceJustification::class);
    }

    /**
     * Check if this attendance has any justification.
     */
    public function hasJustification()
    {
        return $this->justifications()->exists();
    }

    /**
     * Check if this attendance has an approved justification.
     */
    public function hasApprovedJustification()
    {
        return $this->justifications()->where('status', 'approved')->exists();
    }
} 