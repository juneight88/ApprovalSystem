<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'password', 'department', 'program', 'role', 'subject_handled',
        'first_name', 'last_name', 'profile_picture', 'signature', 'setup_complete'
    ];

    protected $casts = [
        'handled_subjects' => 'array', // Ensures the handled_subjects field is treated as an array (JSON).
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'coordinator_id');
    }
}
