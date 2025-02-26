<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'qualification',
        'experience',
        'joining_date',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'phone',
        'emergency_contact',
        'date_of_birth',
        'gender',
        'marital_status',
        'department_id',
        'designation_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the teacher belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the designation that the teacher belongs to.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Get the subjects for the teacher.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id');
    }

    /**
     * Get the timetable entries for the teacher.
     */
    public function timetableEntries()
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * Get the attendances for the teacher.
     */
    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
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
} 