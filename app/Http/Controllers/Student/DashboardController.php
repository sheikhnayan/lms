<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ProfileRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Instructor;
use App\Models\InstructorFeature;
use App\Models\InstructorProcedure;
use App\Models\Order_item;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{

    use  ImageSaveTrait, General, SendNotification;

    protected $studentModel;
    protected $instructorModel;

    public function __construct(Student $student, Instructor $instructor)
    {
        $this->studentModel = new Crud($student);
        $this->instructorModel = new Crud($instructor);
    }


    public function dashboard()
    {
        return redirect(route('student.profile'));
    }

    public function profile()
    {
        $data['pageTitle'] = "Profile";
        $data['user'] = auth::user();
        $data['student'] = $data['user']->student;

        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        return view('frontend.student.settings.profile', $data);
    }

    public function changePassword()
    {
        $data['pageTitle'] = "Change Password";
        $data['user'] = auth::user();

        return view('frontend.student.settings.change-password', $data);
    }

    public function changePasswordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::find(Auth::id());

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            $this->showToastrMessage('success', 'Password changed successfully');
            return redirect()->back();
        } else {
            $this->showToastrMessage('error', 'Your old password does not match.');
            return redirect()->back();
        }
    }


    public function becomeAnInstructor()
    {
        if (auth()->user()->role == 2)
        {
            $this->showToastrMessage('error', 'You are already an instructor!');
            return redirect()->back();
        }
        $data['pageTitle'] = 'Become an Instructor';
        $data['instructorFeatures'] = InstructorFeature::take(3)->get();
        $data['instructorProcedures'] = InstructorProcedure::all();
        $data['total_students'] = Student::count();
        $data['total_enrollments'] = Order_item::count();
        $data['total_instructors'] = Instructor::count();

        return view('frontend.student.settings.become-an-instructor', $data);
    }

    public function saveInstructorInfo(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'professional_title' => 'required',
            'about_me' => 'required',
            'cv_file' => 'required|max:5000|mimes:pdf',
        ],
        [
            'required'  => 'The :attribute field is required.',
        ]);

        if (Instructor::where('user_id', Auth::user()->id)->count() > 0)
        {
            $this->showToastrMessage('success', 'Request already send');
            return redirect(route('student.dashboard'));
        } else {
            if (Instructor::where('slug', Str::slug(auth::user()->name))->count() > 0)
            {
                $slug = Str::slug(Auth::user()->name) . '-'. rand(100000, 999999);
            } else {
                $slug = Str::slug(Auth::user()->name);
            }

            $cv_file_data = $this->uploadFileWithDetails('user', $request->cv_file);
            $instructor_data = [
                'user_id' => Auth::user()->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'about_me' => $request->about_me,
                'slug' => $slug,
                'cv_file' => $cv_file_data['path'],
                'cv_filename' => $cv_file_data['original_filename'],
            ];

            $this->instructorModel->create($instructor_data);

            $text = "New instructor request";
            $target_url = route('instructor.pending');
            $this->send($text, 1, $target_url, null);

            $this->showToastrMessage('success', 'Request successfully send');
            return redirect(route('student.dashboard'));
        }


    }


    public function saveProfile(ProfileRequest $request, $uuid)
    {
        $student = $this->studentModel->getRecordByUuid($uuid);

        $user = User::find($student->user_id);
        if (User::where('id', '!=', $user->id)->where('email', $request->email)->count() > 0)
        {
            $this->showToastrMessage('warning', 'Email already exist');
            return redirect()->back();
        } else {
            $user->email = $request->email;
        }
        if ($request->image)
        {
            $this->deleteFile($user->image); // delete file from server

            $image = $this->saveImage('user', $request->image, null, 'null'); // new file upload into server

        } else {
            $image = $user->image;
        }

        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->image = $image;
        $user->save();

        $student_data = [
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'about_me' => $request->about_me,
            'gender' => $request->gender
        ];

        $student = $this->studentModel->updateByUuid($student_data, $uuid); // update category

        $this->showToastrMessage('success', 'Successfully save');
        return redirect()->back();
    }


    public function getStateByCountry($country_id)
    {
        return State::where('country_id', $country_id)->orderBy('name', 'asc')->get()->toJson();
    }

    public function getCityByState($state_id)
    {
        return City::where('state_id', $state_id)->orderBy('name', 'asc')->get()->toJson();
    }

}
