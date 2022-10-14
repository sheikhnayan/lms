<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';
    protected $fillable = [
        'user_id',
        'country_id',
        'province_id',
        'state_id',
        'city_id',
        'first_name',
        'last_name',
        'professional_title',
        'phone_number',
        'postal_code',
        'address',
        'about_me',
        'social_link',
        'slug',
        'gender',
        'cv_file',
        'cv_filename'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute($value)
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function publishedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id')->where('status', 1);
    }

    public function pendingCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id')->where('status', 2);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function certificates()
    {
        return $this->hasMany(Instructor_certificate::class, 'instructor_id');
    }

    public function awards()
    {
        return $this->hasMany(Instructor_awards::class, 'instructor_id');
    }


    public function getNameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 2);
    }

    public function scopeConsultationAvailable($query)
    {
        return $query->where('consultation_available', 1);
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
        });
    }

}
