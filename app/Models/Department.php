<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the teachers for the department.
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Get the number of teachers in the department.
     */
    public function getTeacherCount()
    {
        return $this->teachers()->count();
    }

    /**
     * Get the list of teacher names in the department.
     */
    public function getTeacherNames()
    {
        return $this->teachers()->with('user')->get()->map(function ($teacher) {
            return $teacher->user->name;
        })->implode(', ');
    }
} 