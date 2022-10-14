<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Instructor;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    use General, ImageSaveTrait;

    protected $studentModel;
    public function __construct( Student $student)
    {
        $this->studentModel = new Crud($student);
    }
    public function index()
    {
        $data['title'] = 'All Student';
        $data['students'] = $this->studentModel->getOrderById('DESC', 25);
        return view('admin.student.list', $data);
    }

    public function create()
    {
        $data['title'] = 'Add Student';
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }
        return view('admin.student.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:2'],
            'phone_number' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

        $user = new User();
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->password = Hash::make($request->password);
        $user->role = 3;
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   null;
        $user->save();

        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
        ];

        $this->studentModel->create($student_data);

        $this->showToastrMessage('success', 'Student created successfully');
        return redirect()->route('student.index');
    }

    public function view($uuid)
    {
        $data['title'] = 'Student Profile';
        $data['student'] = $this->studentModel->getRecordByUuid($uuid);

        $allUserOrder = Order::where('user_id', $data['student']->user_id);
        $paidOrderIds = $allUserOrder->where('payment_status', 'paid')->pluck('id')->toArray();

        $allUserOrder = Order::where('user_id', $data['student']->user_id);
        $freeOrderIds = $allUserOrder->where('payment_status', 'free')->pluck('id')->toArray();

        $orderIds = array_merge($paidOrderIds, $freeOrderIds);

        $data['orderItems'] = Order_item::whereIn('order_id', $orderIds)->latest()->paginate(15);

        return view('admin.student.view', $data);
    }

    public function edit($uuid)
    {
        $data['title'] = 'Edit Student';
        $data['student'] = $this->studentModel->getRecordByUuid($uuid);
        $data['user'] = User::findOrfail($data['student']->user_id);

        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        return view('admin.student.edit', $data);
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

        $student = $this->studentModel->getRecordByUuid($uuid);

        $user = User::findOrfail($student->user_id);
        if (User::where('id', '!=', $student->user_id)->where('email', $request->email)->count() > 0) {
            $this->showToastrMessage('warning', 'Email already exist');
            return redirect()->back();
        }

        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        if ($request->password){
            $request->validate([
                'password' => 'required|string|min:6'
            ]);
            $user->password = Hash::make($request->password);
        }
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   $user->image;
        $user->save();

        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
        ];

        $this->studentModel->updateByUuid($student_data, $uuid);

        $this->showToastrMessage('success', 'Updated Successfully');
        return redirect()->route('student.index');
    }

    public function delete($uuid)
    {
        $student = $this->studentModel->getRecordByUuid($uuid);
        $instructor = Instructor::whereUserId($student->user_id)->first();
        if ($instructor){
            $this->showToastrMessage('error', 'You can\'t delete it. Because this user already an instructor. If you want to delete, at first you delete from instructor.');
            return redirect()->back();
        }
        if ($student){
            $this->deleteFile(@$student->user->image);
        }
        User::find($student->user_id)->delete();
        $this->studentModel->deleteByUuid($uuid);

        $this->showToastrMessage('success', 'Deleted Successfully');
        return redirect()->back();
    }

    public function changeStudentStatus(Request $request)
    {
        $student = Student::findOrFail($request->id);
        $student->status = $request->status;
        $student->save();

        return response()->json([
            'data' => 'success',
        ]);
    }
}
