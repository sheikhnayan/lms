<?php

namespace App\Http\Controllers\Frontend_new;

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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use phpDocumentor\Reflection\Types\Expression;
use Illuminate\Http\Request;

class MainController extends Controller
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
            $data['courses'] = Course::active()->get();
            $data['aboutUsGeneral'] = AboutUsGeneral::first();
            $data['instructorSupports'] = InstructorSupport::all();
            $data['clients'] = ClientLogo::all();
            $data['faqQuestions'] = FaqQuestion::take(3)->get();
            $data['home'] = Home::first();
            $data['instructors'] = Instructor::approved()->take(4)->get();
            $data['bundles'] = Bundle::active()->latest()->take(12)->get();
            $data['consultationInstructors'] = Instructor::approved()->consultationAvailable()->take(8)->get();
            return view('frontend_new.index', $data);
        } else {
            return redirect()->to('/install');
        }
    }
}
