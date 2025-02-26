<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'admission_no',
        'roll_no',
        'class_id',
        'section_id',
        'session_id',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'religion',
        'admission_date',
        'blood_group',
        'height',
        'weight',
        'guardian_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the student belongs to.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the section that the student belongs to.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the session that the student belongs to.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the guardian that the student belongs to.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Get the attendances for the student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades for the student.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the certificates for the student.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Calculate attendance percentage for a specific period
     */
    public function calculateAttendancePercentage($startDate, $endDate)
    {
        $totalDays = $this->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        if ($totalDays === 0) {
            return 0;
        }

        $presentDays = $this->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();

        return ($presentDays / $totalDays) * 100;
    }

    /**
     * Calculate average grade for a specific semester
     */
    public function calculateAverageGrade($semesterId)
    {
        $grades = $this->grades()->where('semester_id', $semesterId)->get();
        
        if ($grades->isEmpty()) {
            return 0;
        }

        $sum = $grades->sum('grade_value');
        return $sum / $grades->count();
    }
} 