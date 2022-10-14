<?php

use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Currency;
use App\Models\ForumPostComment;
use App\Models\Instructor;
use App\Models\InstructorConsultationDayStatus;
use App\Models\Language;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\RankingLevel;
use App\Models\Review;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\ZoomSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

function staticMeta($id)
{
    $meta = \App\Models\Meta::find($id);
    $metaData = [];
    if ($meta)
    {
        $metaData['title'] = $meta->meta_title;
        $metaData['meta_description'] = $meta->meta_description;
        $metaData['meta_keyword'] = $meta->meta_keyword;
    }

    return $metaData;
}

function active_if_match($path)
{
    if (auth::user()->is_admin()) {
        return Request::is($path . '*') ? 'mm-active' : '';
    } else {
        return Request::is($path . '*') ? 'active' : '';
    }

}

function active_if_full_match($path)
{
    if (auth::user()->is_admin()) {
        return Request::is($path) ? 'mm-active' : '';
    } else {
        return Request::is($path) ? 'active' : '';
    }

}

function open_if_full_match($path)
{
    return Request::is($path) ? 'has-open' : '';
}


function toastrMessage($message_type, $message)
{
    Toastr::$message_type($message, '', ['progressBar' => true, 'closeButton' => true, 'positionClass' => 'toast-top-right']);
}

function get_option($option_key)
{
    $system_settings = config('settings');

    if ($option_key && isset($system_settings[$option_key])) {
        return $system_settings[$option_key];
    } else {
        return '';
    }
}

function zoom_status()
{
    $zoom = ZoomSetting::whereUserId(Auth::id())->first();
    $status = 0;
    if ($zoom){
        $status = $zoom->status;
    }

    return $status;
}

function get_default_language()
{
    $language = Language::where('default_language', 'on')->first();
    if ($language)
    {
        $iso_code = $language->iso_code ;
        return $iso_code;
    }

    return 'en';
}

function get_currency_symbol()
{
    $currency = Currency::where('current_currency', 'on')->first();
    if ($currency)
    {
        $symbol = $currency->symbol;
        return $symbol;
    }

    return '';
}

function get_currency_code()
{
    $currency = Currency::where('current_currency', 'on')->first();
    if ($currency)
    {
        $currency_code = $currency->currency_code;
        return $currency_code;
    }

    return '';
}

function get_currency_placement()
{
    $currency = Currency::where('current_currency', 'on')->first();
    $placement = 'before';
    if ($currency)
    {
        $placement = $currency->currency_placement;
        return $placement;
    }

    return $placement;
}

function get_platform_charge($sub_total)
{
    return ($sub_total * get_option('platform_charge')) / 100;
}

function admin_sell_commission($amount)
{
    return ($amount * get_option('sell_commission')) / 100;
}


function instructor_available_balance()
{
    //Start::  Cancel Consultation Money Calculation
    $cancelConsultationOrderItemIds = BookingHistory::whereStatus(2)->where('send_back_money_status', 1)->whereHas('order', function ($q){
        $q->where('payment_status', 'paid');
    })->pluck('order_item_id')->toArray();
    $orderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
    $cancel_consultation_money = $orderItems->sum('admin_commission') + $orderItems->sum('owner_balance');
    //Start::  Cancel Consultation Money Calculation

    $total_balance = Order_item::where('owner_user_id', Auth::id())->whereHas('order', function ($q) {
        $q->where('payment_status', 'paid');
    })->sum('owner_balance');
    $total_withdraw_balance = Withdraw::where('user_id', auth()->user()->id)->whereIn('status', [0, 1])->sum('amount');
    $available_balance = $total_balance - $total_withdraw_balance - $cancel_consultation_money;
    return number_format($available_balance, 2);
}


function get_number_format($amount)
{
    return number_format($amount, 2);
}

function appLanguages()
{
    return Language::where('status', 1)->get();
}

function selectedLanguage()
{
    $language = Language::where('iso_code', config('app.locale'))->first();
    if (!$language){
        $language = Language::find(1);
        if ($language){
            $ln = $language->iso_code;
            session(['local' => $ln]);
            App::setLocale(session()->get('local'));
        }
    }

    return $language;
}

function take_exam($exam_id)
{
    if (\App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->count() > 0) {
        return 'yes';
    } else {
        return 'no';
    }
}


function get_answer_class($exam_id, $question_id, $question_option_id)
{
    if (\App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)->count() > 0) {
        $answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)->orderBy('id', 'DESC')->first();
        if ($answer->is_correct == 'yes') {
            return 'given-answer-right';
        } else {
            return 'given-answer-wrong';
        }
    } else {
        $option = \App\Models\Question_option::find($question_option_id);
        if ($option->is_correct_answer == 'yes') {
            return 'correct-answer-was';
        } else {
            return '';
        }
    }
}

function get_total_score($exam_id)
{
    $exam = \App\Models\Exam::find($exam_id);
    return $exam->marks_per_question * $exam->questions->count();
}

function get_student_score($exam_id)
{
    $exam = \App\Models\Exam::find($exam_id);
    $number_of_correct_answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereIsCorrect('yes')->count();
    return $exam->marks_per_question * $number_of_correct_answer;
}

function get_student_by_student_score($exam_id, $user_id)
{
    $exam = \App\Models\Exam::find($exam_id);
    $number_of_correct_answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereIsCorrect('yes')->count();
    return $exam->marks_per_question * $number_of_correct_answer;
}

function get_position($exam_id)
{
    $take_exams = \App\Models\Take_exam::whereExamId($exam_id)->orderBy('number_of_correct_answer', 'DESC')->get();
    $list = [];
    foreach ($take_exams as $key => $take_exam) {
        $list[$take_exam->user_id] = $key + 1;
    }

    if (array_key_exists(auth()->user()->id, $list)) {
        return $list[auth()->user()->id];
    } else {
        return '0';
    }


}

function get_instructor_ranking_level($instructor_id)
{
    $userCourseIds = Course::whereUserId($instructor_id)->pluck('id')->toArray();
    $userBundleIds = Bundle::whereUserId($instructor_id)->pluck('id')->toArray();

    $orderBundleItemsCount = Order_item::whereIn('bundle_id', $userBundleIds)->where('course_id', null)
        ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        ->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->count();

    $allOrderItems = Order_item::whereIn('course_id', $userCourseIds)->orWhereIn('bundle_id', $userBundleIds)->whereHas('order', function ($q) {
        $q->where('payment_status', 'paid');
    });

    $grand_total_earning = $allOrderItems->sum('owner_balance');
    $grand_total_enroll = $allOrderItems->count('id') - $orderBundleItemsCount;

    $levels = RankingLevel::orderBy('serial_no', 'asc')->get();
    $rankLevel = true;
    foreach (@$levels as $level) {
        if ($level->earning <= $grand_total_earning && $level->student <= $grand_total_enroll) {
            $level_serial_no = $level->serial_no;
            $rankLevel = false;
        }
    }

    if ($rankLevel) {
        return null;
    } else {
        $next_level = \App\Models\RankingLevel::where('serial_no', @$level_serial_no)->first();
        return $next_level->name;
    }

}

function getImageFile($file)
{
    return asset($file);
}

function getVideoFile($file)
{
    if (env('STORAGE_DRIVER') == "s3") {
        if (Storage::disk('s3')->exists($file)) {
            $s3 = Storage::disk('s3');
            return $s3->url($file);
        }
    }

    return asset($file);
}

function notificationForUser()
{
    $instructor_notifications = \App\Models\Notification::where('user_id', auth()->user()->id)->where('user_type', 2)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->get();
    $student_notifications = \App\Models\Notification::where('user_id', auth()->user()->id)->where('user_type', 3)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->get();
    return array('instructor_notifications' => $instructor_notifications, 'student_notifications' => $student_notifications);
}

function adminNotifications()
{
    return \App\Models\Notification::where('user_type', 1)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->paginate(5);
}

function studentCourseProgress($course_id)
{
    $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
    $number_of_total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('user_id', auth()->user()->id)->count();
    $result = 0;
    if ($number_of_total_lecture) {
        $result = (($number_of_total_view_lecture * 100) / $number_of_total_lecture ?? 1);
    }

    return $result;
}


function getLeftDuration($start_date, $end_date)
{
    $startDate = date('d-m-Y H:i:s', strtotime($start_date));
    $endDate = date('d-m-Y H:i:s', strtotime($end_date));

    $secondsDifference = strtotime($endDate) - strtotime($startDate);

    //converting seconds to hours, minutes, seconds.
    $day = floor($secondsDifference / 86400);
    $hour = floor(($secondsDifference - ($day * 86400)) / 3600);
    $minute = floor(($secondsDifference / 60) % 60);
    $second = floor($secondsDifference % 60);

    if ($day > 0) {
        $day = $day . ($day > 1 ? ' days ' : ' day ');
        if ($hour > 0) {
            $hour = $hour . ($hour > 1 ? ' hours ' : ' hour ');
            return $day . $hour;
        }
        return $day;
    } elseif ($hour > 0) {
        $hour = $hour . ($hour > 1 ? ' hours ' : ' hour ');
        if ($minute) {
            $minute = $minute . ($minute > 1 ? ' minutes ' : ' minute ');
            return $hour . $minute;
        }
        return $hour;
    } elseif ($minute > 0) {
        $minute = $minute . ($minute > 1 ? ' minutes ' : ' minute ');
        return $minute;
    } elseif ($second > 0) {
        return $second;
    }

}

function lessonVideoDuration($course_id, $lesson_id)
{
    $lectures = \App\Models\Course_lecture::where('course_id', $course_id)->where('lesson_id', $lesson_id)->get();
    $video_duration = 0;
    $total_video_duration_in_seconds = 0;

    if ($lectures->count() > 0)
    {
        foreach ($lectures as $lecture)
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

function checkStudentCourseView($course_id, $lecture_id)
{
    $views = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('course_lecture_id', $lecture_id)->first();

    return $views;
}

function studentCoursesCount($user_id)
{
    $allUserOrder = Order::where('user_id', $user_id);
    $paidOrderIds = $allUserOrder->where('payment_status', 'paid')->pluck('id')->toArray();

    $allUserOrder = Order::where('user_id', $user_id);
    $freeOrderIds = $allUserOrder->where('payment_status', 'free')->pluck('id')->toArray();

    $orderIds = array_merge($paidOrderIds, $freeOrderIds);

    $orderItems = Order_item::whereIn('order_id', $orderIds)->count();

    return $orderItems;
}

function countUserReplies($user_id=null)
{
    return ForumPostComment::whereUserId($user_id)->count();
}

function getDayAvailableStatus($day)
{
    $item = InstructorConsultationDayStatus::where('user_id', Auth::id())->where('day', $day)->first();
    if ($item){
        $status = 1;
    } else {
        $status = 0;
    }

    return $status;
}

function getInstructorTotalReview($user_id)
{
    $courseIds = Course::where('user_id', $user_id)->pluck('id')->toArray();
    return Review::whereIn('course_id', $courseIds)->count();
}

function getInstructorName($id)
{
    $user = Instructor::whereUserId($id)->first();
    return @$user->full_name ?? '';
}

function getBookingHistoryDetails($consultation_slot_id)
{
    $booking = BookingHistory::where('consultation_slot_id', $consultation_slot_id)->first();
    $bookingArray = [
        'time' => $booking->time ?? '',
        'type' => $booking->type ?? ''
    ];

    return $bookingArray;
}

function getBundleDetails($id)
{
    $bundle = Bundle::find($id);
    return $bundle;
}

function getUserAverageRating($user_id)
{
    $courseIds = Course::where('user_id', $user_id)->pluck('id')->toArray();


    $data['five_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(5)->count();
    $data['four_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(4)->count();
    $data['three_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(3)->count();
    $data['two_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(2)->count();
    $data['first_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(1)->count();

    $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
        (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
    $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];

    if ($data['total_user_review'] > 0) {
        $average_rating = $data['total_reviews'] / $data['total_user_review'];
    } else {
        $average_rating = 0;
    }

    return $average_rating;
}

function courseStudents($course_id)
{
    $paidOrderIds = Order_item::where('course_id', $course_id)->whereHas('order', function ($q){
        $q->where('payment_status', 'paid');
    })->count();

    $freeOrderIds = Order_item::where('course_id', $course_id)->whereHas('order', function ($q){
        $q->where('payment_status', 'free');
    })->count();

    $total_course_students = $paidOrderIds + $freeOrderIds;
    return $total_course_students;
}


