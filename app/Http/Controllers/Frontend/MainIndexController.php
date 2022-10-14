<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutUsGeneral;
use App\Models\Assignment;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\City;
use App\Models\ClientLogo;
use App\Models\ContactUs;
use App\Models\ContactUsIssue;
use App\Models\Country;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Exam;
use App\Models\FaqQuestion;
use App\Models\Home;
use App\Models\Instructor;
use App\Models\InstructorSupport;
use App\Models\OurHistory;
use App\Models\Policy;
use App\Models\Review;
use App\Models\State;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use phpDocumentor\Reflection\Types\Expression;

class MainIndexController extends Controller
{
    public function index()
    {
        if (file_exists(storage_path('installed'))) {
            $data['pageTitle'] = "Home";
            $data['metaData'] = staticMeta(1);
            $data['featureCategories'] = Category::with('activeCourses')->active()->feature()->get()->map(function ($q) {
                $q->setRelation('courses', $q->courses->where('status', 1)->take(12));
                return $q;
            });
            $data['firstFourCategories'] = Category::feature()->active()->take(4)->get();
            $data['aboutUsGeneral'] = AboutUsGeneral::first();
            $data['instructorSupports'] = InstructorSupport::all();
            $data['clients'] = ClientLogo::all();
            $data['faqQuestions'] = FaqQuestion::take(3)->get();
            $data['home'] = Home::first();
            $data['instructors'] = Instructor::approved()->take(4)->get();
            $data['bundles'] = Bundle::active()->latest()->take(12)->get();
            $data['consultationInstructors'] = Instructor::approved()->consultationAvailable()->take(8)->get();
            return view('frontend.home.home', $data);
        } else {
            return redirect()->to('/install');
        }
    }

    public function aboutUs()
    {
        $data['pageTitle'] = 'About Us';
        $data['metaData'] = staticMeta(7);
        $data['aboutUsGeneral'] = AboutUsGeneral::first();
        $data['ourHistories'] = OurHistory::take(4)->get();
        $data['teamMembers'] = TeamMember::all();
        $data['instructorSupports'] = InstructorSupport::all();
        $data['clients'] = ClientLogo::all();

        return view('frontend.about', $data);
    }

    public function contactUs()
    {
        $data['pageTitle'] = 'Contact Us';
        $data['metaData'] = staticMeta(8);
        $data['contactUsIssues'] = ContactUsIssue::all();
        return view('frontend.contact', $data);
    }

    public function contactUsStore(Request $request)
    {
        $contact = new ContactUs();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->contact_us_issue_id = $request->contact_us_issue_id;
        $contact->message = $request->message;
        $contact->save();

        $response['msg'] = 'Message sent successfully.';
        return response()->json($response);
    }

    public function faq()
    {
        $data['pageTitle'] = 'FAQ';
        $data['metaData'] = staticMeta(12);
        $data['faqs'] = FaqQuestion::all();
        return view('frontend.faq', $data);
    }

    public function instructorDetails($id, $slug)
    {
        $data['pageTitle'] = 'Instructor Details';
        $data['userInstructor'] = User::findOrFail($id);
        $data['courses'] = Course::whereUserId($id)->paginate(6);
        $data['loadMoreButtonShowCourses'] = Course::whereUserId($id)->paginate(7);
        $data['average_rating'] = getUserAverageRating($id);
        $courseIds = Course::where('user_id', $id)->where('status', 1)->pluck('id')->toArray();
        $data['total_rating'] = Review::whereIn('course_id', $courseIds)->count();

        $data['total_assignments'] = Assignment::whereIn('course_id', $courseIds)->count();
        $data['total_lectures'] = Course_lecture::whereIn('course_id', $courseIds)->count();
        $data['total_quizzes'] = Exam::whereIn('course_id', $courseIds)->count();
        return view('frontend.instructor.instructor-details', $data);
    }

    public function instructorCoursePaginate(Request $request, $id)
    {
        $data['courses'] = Course::whereUserId($id)->paginate(6);
        $response['appendInstructorCourses'] = View::make('frontend.instructor.render-instructor-courses', $data)->render();
        $response['courses'] = Course::whereUserId($id)->paginate(6);
        return response()->json($response);
    }

    public function allInstructor()
    {
        $data['pageTitle'] = "All Instructor";
        $data['instructors'] = Instructor::approved()->paginate(16);
        return view('frontend.instructor.all-instructor', $data);
    }

    public function termConditions()
    {
        $data['pageTitle'] = "Terms & Conditions";
        $data['policy'] = Policy::whereType(3)->first();

        return view('frontend.terms-conditions', $data);
    }

    public function privacyPolicy()
    {
        $data['pageTitle'] = "Privacy Policy";
        $data['metaData'] = staticMeta(10);
        $data['policy'] = Policy::whereType(1)->first();

        return view('frontend.privacy-policy', $data);
    }

    public function cookiePolicy()
    {
        $data['pageTitle'] = "Cookie Policy";
        $data['metaData'] = staticMeta(11);
        $data['policy'] = Policy::whereType(2)->first();

        return view('frontend.cookie-policy', $data);
    }
}
