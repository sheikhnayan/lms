<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function allStudentIndex(Request $request)
    {
        $data['title'] = 'All Student';
        $data['navAllStudentActiveClass'] = 'active';

        $data['courses'] = Course::whereUserId(auth()->user()->id)->get();
        $userCourseIds = Course::whereUserId(auth()->user()->id)->pluck('id')->toArray();

        $orderItems = Order_item::whereIn('course_id', $userCourseIds);

        //Start:: Student search
        $orderIds = $orderItems->pluck('order_id')->toArray();
        $userIds = Order::whereIn('id', $orderIds)->pluck('user_id')->toArray();
        $userIds = User::whereIn('id', $userIds)->where('name', 'LIKE', "%{$request->search_name}%")->pluck('id')->toArray();
        $userIds = Order::whereIn('user_id', $userIds)->pluck('user_id')->toArray();
        //End:: Student search

        //Start:: Course search
        if ($request->course_id){
            $orderItems = $orderItems->whereCourseId($request->course_id);
        }
        //End:: Course search

        $data['orderItems'] = $orderItems->whereHas('order', function ($q) use ($userIds, $request){
            $q->where('payment_status', 'paid');
            if ($request->search_name){
                $q->whereIn('user_id', $userIds);
            }
        })->paginate();

        return view('instructor.all-student')->with($data);
    }

}
