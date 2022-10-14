<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\CourseUpdateCategoryRequest;
use App\Http\Requests\Instructor\StoreCourseRequest;
use App\Models\CartManagement;
use App\Models\Category;
use App\Models\Course;
use App\Models\Course_language;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\Course_lesson;
use App\Models\CourseUploadRule;
use App\Models\Difficulty_level;
use App\Models\LearnKeyPoint;
use App\Models\Order_item;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Models\Wishlist;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    use General, ImageSaveTrait, SendNotification;
    protected $model, $lectureModel, $lessonModel;

    public function __construct(Course $course, Course_lesson $course_lesson,  Course_lecture $course_lecture)
    {
        $this->model = new Crud($course);
        $this->lectureModel = new Crud($course_lecture);
        $this->lessonModel = new Crud($course_lesson);
    }

    public function index()
    {
        $data['title'] = 'My Course';
        $data['courses'] = Course::where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate();
        $data['navCourseActiveClass'] = 'active';
        $data['number_of_course'] = Course::where('user_id', auth()->id())->count();
        return view('instructor.course.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Upload Course';
        $data['navCourseUploadActiveClass'] = 'active';
        $data['rules'] = CourseUploadRule::all();
        return view('instructor.course.create', $data);
    }

    public function store(StoreCourseRequest $request)
    {
        if (Course::where('slug', Str::slug($request->title))->count() > 0)
        {
            $slug = Str::slug($request->title) . '-'. rand(100000, 999999);
        } else {
            $slug = Str::slug($request->title);
        }

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'slug' => $slug,
            'status' => 4,
            'description' => $request->description,
        ];

        $course = $this->model->create($data);

        if ($request['key_points']) {
            if (count(@$request['key_points']) > 0) {
                foreach ($request['key_points'] as $item) {
                    if (@$item['name']){
                        $key_point = new LearnKeyPoint();
                        $key_point->course_id = $course->id;
                        $key_point->name = @$item['name'];
                        $key_point->save();
                    }
                }
            }
        }
        return redirect(route('instructor.course.edit', [$course->uuid, 'step=category']));
    }

    public function edit($uuid)
    {
        $data['navCourseUploadActiveClass'] = 'active';
        $data['title'] = 'Upload Course';
        $data['rules'] = CourseUploadRule::all();
        $data['course'] = $this->model->getRecordByUuid($uuid);
        $data['keyPoints'] = LearnKeyPoint::whereCourseId($data['course']->id)->get();

        if (\request('step') == 'category')
        {
            $data['categories'] = Category::active()->orderBy('name', 'asc')->select('id', 'name')->get();
            $data['tags'] = Tag::orderBy('name', 'asc')->select('id', 'name')->get();
            $data['course_languages'] = Course_language::orderBy('name', 'asc')->select('id', 'name')->get();
            $data['difficulty_levels'] = Difficulty_level::orderBy('name', 'asc')->select('id', 'name')->get();
            if (old('category_id'))
            {
                $data['subcategories'] = Subcategory::where('category_id', old('category_id'))->select('id', 'name')->orderBy('name', 'asc')->get();
            } elseif ($data['course']->category_id)
            {
                $data['subcategories'] = Subcategory::where('category_id', $data['course']->category_id)->select('id', 'name')->orderBy('name', 'asc')->get();
            } else {
                $data['subcategories'] = [];
            }

            $selected_tags = [];

            if (old('tag'))
            {
                $selected_tags = old('tag');

            } elseif ($data['course']->tags->count() > 0)
            {
                foreach ($data['course']->tags as $tag)
                {
                    $selected_tags[] = $tag->id;
                }
            } else {
                $selected_tags = [];
            }

            $data['selected_tags'] = $selected_tags;

            return view('instructor.course.edit-category', $data);

        } elseif (\request('step') == 'lesson') {

            return view('instructor.course.lesson', $data);

        } elseif (\request('step') == 'submit') {

            return view('instructor.course.submit-lesson', $data);

        } else {
            return view('instructor.course.edit', $data);
        }
    }

    public function updateOverview(StoreCourseRequest $request, $uuid)
    {
        $data['navCourseUploadActiveClass'] = 'active';
        $course = $this->model->getRecordByUuid($uuid);
        if (Course::where('slug', Str::slug($request->title))->where('id', '!=', $course->id)->count() > 0)
        {
            $slug = Str::slug($request->title) . '-'. rand(100000, 999999);
        } else {
            $slug = Str::slug($request->title);
        }

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'slug' => $slug,
            'description' => $request->description,
        ];

        $this->model->updateByUuid($data, $uuid); // update category

        $now = now();
        if ($request['key_points']) {
            if (count(@$request['key_points']) > 0) {
                foreach ($request['key_points'] as $item) {
                    if (@$item['name']){
                        if (@$item['id']) {
                            $key_point = LearnKeyPoint::find($item['id']);
                        } else {
                            $key_point = new LearnKeyPoint();
                        }
                        $key_point->course_id = $course->id;
                        $key_point->name = @$item['name'];
                        $key_point->updated_at = $now;
                        $key_point->save();
                    }
                }
            }
        }

        LearnKeyPoint::where('course_id', $course->id)->where('updated_at', '!=', $now)->get()->map(function ($q) {
            $q->delete();
        });

        if ($course->status != 0)
        {
            $text = "Course overview has been updated";
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }

        return redirect(route('instructor.course.edit', [$course->uuid, 'step=category']));
    }

    public function updateCategory(Request $request, $uuid)
    {
        $course = $this->model->getRecordByUuid($uuid);

        if ($request->image) {
            $request->validate([
                'image' => 'dimensions:min_width=575,min_height=450,max_width=575,max_height=450'
            ]);
            $this->deleteFile($course->image); // delete file from server
            $image = $this->saveImage('course', $request->image, null, null); // new file upload into server
        } else {
            $image = $course->image;
        }

        if ($request->video) {
            $this->deleteVideoFile($course->video); // delete file from server
            $video = $this->uploadFile('course', $request->video); // new file upload into server
        } else {
            $video = $course->video;
        }

        $data = [
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'price' => $request->price,
            'course_language_id' => $request->course_language_id,
            'difficulty_level_id' => $request->difficulty_level_id,
            'learner_accessibility' => $request->learner_accessibility,
            'image' => $image ?? null,
            'video' => $video ?? null,
            'intro_video_check' => $request->intro_video_check,
            'youtube_video_id' => $request->youtube_video_id ?? null,
        ];

        $this->model->updateByUuid($data, $uuid); // update category

        if ($request->tag) {
            $course->tags()->sync($request->tag);
        }

        if ($course->status != 0) {
            $text = "Course category has been updated";
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }


        return redirect(route('instructor.course.edit', [$course->uuid, 'step=lesson']));

    }

    public function uploadFinished($uuid)
    {
        $course = $this->model->getRecordByUuid($uuid);
        if ($course->status == 1) {
            $course->status = 1;
        } else {
            $course->status = 2;
        }
        $course->save();
        return redirect(route('instructor.course'));
    }

    public function getSubcategoryByCategory($category_id)
    {
        return Subcategory::where('category_id', $category_id)->select('id', 'name')->get()->toJson();
    }

    public function delete($uuid)
    {
        $course = $this->model->getRecordByUuid($uuid);
        $order_item = Order_item::whereCourseId($course->id)->first();
        if ($order_item)
        {
            $this->showToastrMessage('error', 'You can not deleted. Because already student purchased this course!');
            return redirect()->back();
        }

        //start:: Course lesson delete
        $lessons = Course_lesson::where('course_id', $course->id)->get();
        if (count($lessons) > 0)
        {
            foreach ($lessons as $lesson)
            {
                //start:: lecture delete
                $lectures = Course_lecture::where('lesson_id', $lesson->id)->get();
                if (count($lectures) > 0)
                {
                    foreach ($lectures as $lecture)
                    {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture)
                        {
                            $this->deleteFile($lecture->file_path); // delete file from server

                            if ($lecture->type == 'vimeo')
                            {
                                if ($lecture->url_path)
                                {
                                    $this->deleteVimeoVideoFile($lecture->url_path);
                                }
                            }

                            Course_lecture_views::where('course_lecture_id', $lecture->id)->get()->map(function ($q) {
                                $q->delete();
                            });

                            $this->lectureModel->delete($lecture->id); // delete record
                        }
                    }
                }
                //end:: lecture delete
                $this->lessonModel->delete($lesson->id);
            }
        }
        //end: lesson delete

        //Start:: Delete this course Wishlist, CartManagement
        Wishlist::where('course_id', $course->id)->delete();
        CartManagement::where('course_id', $course->id)->delete();
        //End:: Delete this course wishList and addToCart

        $this->deleteFile($course->image);
        $this->deleteVideoFile($course->video);
        $course->delete();
        $this->showToastrMessage('success', 'Course has been deleted.');
        return redirect()->back();
    }
}
