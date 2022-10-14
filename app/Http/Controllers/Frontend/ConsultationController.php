<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BookingHistory;
use App\Models\ConsultationSlot;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\InstructorConsultationDayStatus;
use App\Models\Order;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    private $consultationPaginateValue = 9;

    public function getInstructorBookingTime(Request $request)
    {
        $date = $request->bookingDate;
        $data['bookingDay'] = date('l', strtotime($date));
        $day = 7;
        if ($data['bookingDay'] == 'Sunday') {
            $day = 0;
        } elseif ($data['bookingDay'] == 'Monday') {
            $day = 1;
        } elseif ($data['bookingDay'] == 'Tuesday') {
            $day = 2;
        } elseif ($data['bookingDay'] == 'Wednesday') {
            $day = 3;
        } elseif ($data['bookingDay'] == 'Thursday') {
            $day = 4;
        } elseif ($data['bookingDay'] == 'Friday') {
            $day = 5;
        } elseif ($data['bookingDay'] == 'Saturday') {
            $day = 6;
        }

        $orderIds =  Order::where('payment_status', 'paid')->orWhere('payment_status', 'free')->pluck('id')->toArray();

        $consultationSlotIds = BookingHistory::whereIn('order_id', $orderIds)->where('instructor_user_id', $request->user_id)->where('date', $date)->pluck('consultation_slot_id')->toArray();
        $data['slots'] = ConsultationSlot::whereNotIn('id', $consultationSlotIds);
        $data['slots'] = $data['slots']->where('user_id', $request->user_id)->where('day', $day)->get();

        return view('frontend.home.partial.consultation-booking-day-time', $data);
    }

    public function consultationInstructorList()
    {
        $data['consultationInstructors'] = Instructor::approved()->consultationAvailable()->paginate($this->consultationPaginateValue);
        $data['highest_price'] = Instructor::max('hourly_rate');
        return view('frontend.consultation.instructor-consultation-list', $data);
    }

    public function paginationFetchData(Request $request)
    {
        $data['consultationInstructors'] = $this->filterConsultationInstructorData($request);
        if ($request->ajax()) {
            $data['consultationInstructors'] = $data['consultationInstructors']->paginate($this->consultationPaginateValue);
            return view('frontend.consultation.render-consultation-instructor-list')->with($data);
        }
    }

    public function getFilterConsultationInstructor(Request $request)
    {
        $data['consultationInstructors'] = $this->filterConsultationInstructorData($request);
        $data['consultationInstructors'] = $data['consultationInstructors']->paginate($this->consultationPaginateValue);
        return view('frontend.consultation.render-consultation-instructor-list')->with($data);
    }

    public function filterConsultationInstructorData($request)
    {
        $min_hourly_rate = $request->min_hourly_rate;
        $max_hourly_rate = $request->max_hourly_rate;
        $sortBy_id = $request->sortBy_id;
        $ratingIds = $request->ratingIds ?? [];
        $search_name = $request->search_name;
        $typeIds = $request->typeIds ?? [];

        $data['consultationInstructors'] = Instructor::query()->approved()->consultationAvailable();
        $data['consultationInstructors'] = $data['consultationInstructors']->where(function ($q) use ($min_hourly_rate, $max_hourly_rate, $ratingIds, $search_name, $typeIds) {
            if ($search_name){
                $q->where('first_name', 'LIKE', '%' . $search_name. '%')->orWhere('last_name', 'LIKE', '%' . $search_name. '%');
            }
            if ($typeIds){
                if (count($typeIds) == 2){
                    $q->whereIn('available_type', [1,2,3]);
                } else {

                    $q->whereIn('available_type', $typeIds);
                }
            }


            if ($min_hourly_rate && $max_hourly_rate) {
                $q->whereBetween('hourly_rate', [$min_hourly_rate, $max_hourly_rate]);
            } else if ($min_hourly_rate) {
                $q->where('hourly_rate', '>=', $min_hourly_rate);
            } else if ($max_hourly_rate) {
                $q->where('hourly_rate', '<=', $max_hourly_rate);
            }
        });

        if ($ratingIds) {
            $instructors = Instructor::query()->approved()->consultationAvailable()->get();
            $instructorIds = collect([]);

            $averageRatingArray = array();
            foreach ($instructors as $instructor) {
                $average_rating = Course::where('instructor_id', $instructor->id)->avg('average_rating');
                $newDataArray = array(
                    'instructor_id' => $instructor->id,
                    'average_rating' => $average_rating,
                );
                $averageRatingArray[] = $newDataArray;
            }

            $averageRatingArray = collect($averageRatingArray)->sortBy('average_rating');
            foreach ($averageRatingArray as $averageRating) {
                foreach ($ratingIds as $rating){
                    if ($rating <= $averageRating['average_rating']){
                        $instructorIds->push($averageRating['instructor_id']);
                    }
                }
            }

            $data['consultationInstructors'] = $data['consultationInstructors']->whereIn('id', $instructorIds);
        }

        if ($sortBy_id == 2 || $sortBy_id == 1) {
            $data['consultationInstructors'] = $data['consultationInstructors']->orderBy('id', 'DESC');
        } elseif ($sortBy_id == 3) {
            $data['consultationInstructors'] = $data['consultationInstructors']->orderBy('id', 'ASC');
        }

        return $data['consultationInstructors'];
    }

    public function getOffDays($user_id)
    {
        $data['days'] = InstructorConsultationDayStatus::whereUserId($user_id)->pluck('day')->toArray();
        return response()->json($data);
    }
}
