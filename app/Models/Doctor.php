<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $doctor)
 * @method static find($id)
 */
class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'speciality',
        'desc',
        'user_id',
        'department_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diagnosis()
    {
        return $this->hasMany(Diagnosis::class, 'doctor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');

    }
    public function reservation()
    {
        return $this->hasMany(Reservation::class, 'doctor_id');

    }
}
