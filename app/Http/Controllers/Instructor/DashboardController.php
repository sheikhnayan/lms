<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Order_item;
use App\Models\RankingLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Dashboard';

        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::whereStatus(2)->where('send_back_money_status', 1)
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q){
            $q->where('payment_status', 'paid');
        })->pluck('order_item_id')->toArray();
        $consultationOrderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
        //End::  Cancel Consultation Money Calculation

        $userCourseIds = Course::whereUserId(auth()->user()->id)->pluck('id')->toArray();

        $orderBundleItemsCount = Order_item::where('owner_user_id', Auth::id())->whereNotNull('bundle_id')->whereNull('course_id')
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })->count();

        $orderItems = Order_item::where('owner_user_id', Auth::id())
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            });
        $data['total_earning_this_month'] = $orderItems->sum('owner_balance') - $consultationOrderItems->sum('owner_balance');
        $data['total_enroll_this_month'] = $orderItems->count('id') - $orderBundleItemsCount - count($cancelConsultationOrderItemIds);

        $data['best_selling_course'] = Order_item::whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->groupBy("course_id")->selectRaw("COUNT(course_id) as total_course_id, course_id")->orderByRaw("COUNT(course_id) DESC")->first();

        $data['recentCourses'] = Course::whereUserId(auth()->user()->id)->latest()->limit(2)->get();
        $data['updatedCourses'] = Course::whereUserId(auth()->user()->id)->orderBy('updated_at', 'desc')->limit(2)->get();

        // Start:: Sale Statistics
        $months = collect([]);
        $totalPrice = collect([]);

        Order_item::whereNotIn('id', $cancelConsultationOrderItemIds)->where('owner_user_id', Auth::id())->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->select(DB::raw('SUM(owner_balance) as total'), DB::raw('MONTHNAME(created_at) month'))
            ->groupby('month')
            ->get()
            ->map(function ($q) use ($months, $totalPrice) {
                $months->push($q->month);
                $totalPrice->push($q->total);
            });

        $data['months'] = $months;
        $data['totalPrice'] = $totalPrice;
        //End:: Sale Statistics

        //Start:: ranking Level
        $allOrderItems = Order_item::where('owner_user_id', Auth::id())->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        });

        $data['grand_total_earning'] = $allOrderItems->sum('owner_balance');
        $data['grand_total_enroll'] = $allOrderItems->count('id') - $orderBundleItemsCount;

        $data['levels'] = RankingLevel::orderBy('serial_no', 'DESC')->get();
        //End:: ranking Level
        return view('instructor.dashboard', $data);
    }

    public function rankingLevelList()
    {
        $data['title'] = 'Dashboard';
        $data['navDashboardActiveClass'] = 'active';
        $data['levels'] = RankingLevel::orderBy('serial_no', 'asc')->get();
        return view('instructor.ranking-badge-list', $data);
    }
}
