<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_id',
        'semester_id',
        'grade_value',
        'remarks',
        'teacher_id',
    ];

    /**
     * Get the student that owns the grade.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject that owns the grade.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the exam that owns the grade.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the semester that owns the grade.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the teacher that created the grade.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the letter grade based on the grade value
     */
    public function getLetterGrade()
    {
        $value = $this->grade_value;

        if ($value >= 90) {
            return 'A+';
        } elseif ($value >= 80) {
            return 'A';
        } elseif ($value >= 70) {
            return 'B';
        } elseif ($value >= 60) {
            return 'C';
        } elseif ($value >= 50) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Check if the student passed the subject
     */
    public function isPassed()
    {
        return $this->grade_value >= 50;
    }
} 