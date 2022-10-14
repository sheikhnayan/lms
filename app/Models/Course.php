<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'instructor_id',
        'category_id',
        'subcategory_id',
        'course_language_id',
        'difficulty_level_id',
        'title',
        'subtitle',
        'description',
        'description_footer',
        'feature_details',
        'price',
        'learner_accessibility',
        'image',
        'video',
        'slug',
        'status',
        'intro_video_check',
        'youtube_video_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function language()
    {
        return $this->belongsTo(Course_language::class, 'course_language_id');
    }

    public function difficultyLevel()
    {
        return $this->belongsTo(Difficulty_level::class, 'difficulty_level_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tags', 'course_id', 'tag_id');
    }

    public function keyPoints()
    {
        return $this->hasMany(LearnKeyPoint::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(Course_lesson::class, 'course_id');
    }

    public function lectures()
    {
        return $this->hasMany(Course_lecture::class, 'course_id');
    }

    public function notices()
    {
        return $this->hasMany(NoticeBoard::class, 'course_id');
    }

    public function liveClasses()
    {
        return $this->hasMany(LiveClass::class, 'course_id');
    }

    public function orderItems()
    {
        return $this->hasMany(Order_item::class);
    }

    public function studentCertificate()
    {
        return $this->hasOne(Student_certificate::class, 'course_id');
    }

    public function resources()
    {
        return $this->hasMany(CourseResource::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function assignmentFiles()
    {
        return $this->hasManyThrough(AssignmentFile::class, Assignment::class);
    }

    /*
     * Video duration For All without filter in frontend
     */
    public function getVideoDurationAttribute()
    {
        $video_duration = 0;
        $total_video_duration_in_seconds = 0;

        if ($this->lectures->count() > 0)
        {
            foreach ($this->lectures as $lecture)
            {
                if ($lecture->file_duration_second)
                {
                    $total_video_duration_in_seconds +=  $lecture->file_duration_second;
                }
            }

            $h = floor($total_video_duration_in_seconds / 3600);
            $m = floor($total_video_duration_in_seconds % 3600 / 60 );
            $s = floor($total_video_duration_in_seconds % 3600 % 60);

            if($h > 0){
                return "$h h $m m $s s";
            } elseif ($m > 0) {
                return "$m min $s sec";
            } elseif ($s > 0) {
                return "$s sec";
            }
        }

        return $video_duration;
    }

    /*
     * for filter in front
     */

    public function getFilterVideoDurationAttribute()
    {
        $video_duration = 0;
        $total_video_duration_seconds = 0;

        if ($this->lectures->count() > 0)
        {
            foreach ($this->lectures as $lecture)
            {
                if ($lecture->file_duration_second)
                {
                    $total_video_duration_seconds +=  $lecture->file_duration_second;
                }
            }

            $h = floor($total_video_duration_seconds / 3600);

            return $h;
        }

        return $video_duration;
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'course_id');
    }

    public function publishedExams()
    {
        return $this->hasMany(Exam::class, 'course_id')->where('status', 1);
    }

    public function quizzes()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }



    public function lectureViews()
    {
        return $this->hasMany(Course_lecture_views::class, 'course_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'course_id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getImagePathAttribute()
    {
        if ($this->image)
        {
            return $this->image;
        } else {
            return 'uploads/default/course.jpg';
        }
    }

    public function certificate()
    {
        return $this->hasOne(Certificate_by_instructor::class, 'course_id');
    }

    public function promotionCourse()
    {
        return $this->hasOne(PromotionCourse::class);
    }

    public function specialPromotionTagCourse()
    {
        return $this->hasOne(SpecialPromotionTagCourse::class);
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
            $model->user_id =  auth()->id();
            $model->instructor_id =  Auth::user()->instructor ? Auth::user()->instructor->id : null;
        });
    }
}
