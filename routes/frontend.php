<?php

use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BundleCourseController;
use App\Http\Controllers\Frontend\ConsultationController;
use App\Http\Controllers\Instructor\ConsultationController as InstructorConsultationController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\Frontend\ForumController;
use App\Http\Controllers\Frontend\MainIndexController;
use App\Http\Controllers\Frontend\RegistrationController;
use App\Http\Controllers\Frontend\SupportTicketController;
use App\Http\Controllers\Instructor\LiveClassController;
use App\Http\Controllers\Student\MyCourseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
//new frontend 
use App\Http\Controllers\Frontend_new\MainController;


Route::group(['middleware' => 'guest'], function () {
    Route::get('sign-up', [RegistrationController::class, 'signUp'])->name('sign-up');
    Route::post('store-sign-up', [RegistrationController::class, 'storeSignUp'])->name('store.sign-up')->middleware('isDemo');
});

Route::get('forget-password', [LoginController::class, 'forgetPassword'])->name('forget-password');
Route::post('forget-password', [LoginController::class, 'forgetPasswordEmail'])->name('forget-password.email')->middleware('isDemo');
Route::get('reset-password', [LoginController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password', [LoginController::class, 'resetPasswordCheck'])->name('reset-password.check')->middleware('isDemo');

Route::get('user/email/verify/{token}', [RegistrationController::class, 'emailVerification'])->name('user.email.verification')->middleware('isDemo');


Route::get('/', [MainIndexController::class, 'index'])->name('main.index');

Route::get('/index-new',[MainController::class,'index']);

Route::get('about-us', [MainIndexController::class, 'aboutUs'])->name('about');
Route::get('contact-us', [MainIndexController::class, 'contactUs'])->name('contact');
Route::post('contact-store', [MainIndexController::class, 'contactUsStore'])->name('contact.store')->middleware('isDemo');
Route::get('faq', [MainIndexController::class, 'faq'])->name('faq');
Route::get('instructor/{id}/{slug}', [MainIndexController::class, 'instructorDetails'])->name('instructorDetails');
Route::post('instructor-course-paginate/{id}', [MainIndexController::class, 'instructorCoursePaginate'])->name('instructorCoursePaginate');

Route::get('all-instructor', [MainIndexController::class, 'allInstructor'])->name('allInstructor');

// Start:: Course
Route::get('courses', [CourseController::class, 'allCourses'])->name('courses');
Route::get('course-details/{slug}', [CourseController::class, 'courseDetails'])->name('course-details');

Route::get('category/courses/{slug}', [CourseController::class, 'categoryCourses'])->name('category-courses');
Route::get('subcategory/courses/{slug}', [CourseController::class, 'subCategoryCourses'])->name('subcategory-courses');

Route::get('get-sub-category-courses/fetch-data', [CourseController::class, 'paginationFetchData'])->name('course.fetch-data');
Route::get('get-filter-courses', [CourseController::class, 'getFilterCourse'])->name('getFilterCourse');
Route::post('review-paginate/{courseId}', [CourseController::class, 'reviewPaginate'])->name('frontend.reviewPaginate');

Route::get('search-course-list', [CourseController::class, 'searchCourseList'])->name('search-course.list');
// End:: Course

//Start:: Bundle
Route::get('bundles', [BundleCourseController::class, 'allBundles'])->name('bundles');
Route::get('bundle-details/{uuid}/{slug?}', [BundleCourseController::class, 'bundleDetails'])->name('bundle-details');

Route::get('get-bundle-courses/fetch-data', [BundleCourseController::class, 'paginationFetchData'])->name('bundle-course.fetch-data');
Route::get('get-filter-bundle-courses', [BundleCourseController::class, 'getFilterBundleCourse'])->name('getFilterBundleCourse');
//End:: Bundle

// Start:: Blog & Comment
Route::get('blogs', [BlogController::class, 'blogAll'])->name('blogs');
Route::get('blog-details/{slug}', [BlogController::class, 'blogDetails'])->name('blog-details');

Route::get('category-blogs/{slug}', [BlogController::class, 'categoryBlogs'])->name('categoryBlogs');

Route::post('blog-comment', [BlogController::class, 'blogCommentStore'])->name('blog-comment.store')->middleware('isDemo');
Route::post('blog-comment-reply', [BlogController::class, 'blogCommentReplyStore'])->name('blog-comment-reply.store')->middleware('isDemo');

Route::get('search-blog-list', [BlogController::class, 'searchBlogList'])->name('search-blog.list');
// End:: Blog & Comment


Route::get('terms-conditions', [MainIndexController::class, 'termConditions'])->name('terms-conditions');
Route::get('privacy-policy', [MainIndexController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('cookie-policy', [MainIndexController::class, 'cookiePolicy'])->name('cookie-policy');

Route::get('support-ticket-faq',[SupportTicketController::class, 'supportTicketFAQ'])->name('support-ticket-faq');

Route::get('student-join-bbb-meeting/{live_class_id}', [MyCourseController::class, 'bigBlueButtonJoinMeeting'])->name('student.join-bbb-meeting');
Route::get('instructor-join-bbb-meeting/{live_class_id}', [LiveClassController::class, 'bigBlueButtonJoinMeeting'])->name('instructor.join-bbb-meeting');
Route::get('join-jitsi-meeting/{live_class_uuid}', [LiveClassController::class, 'jitsiJoinMeeting'])->name('join-jitsi-meeting');

// Consultation Online Meet
Route::get('student-consultation-join-bbb-meeting/{live_class_id}', [InstructorConsultationController::class, 'studentBigBlueButtonJoinMeeting'])->name('student.consultation.join-bbb-meeting');
Route::get('instructor-consultation-join-bbb-meeting/{live_class_id}', [InstructorConsultationController::class, 'instructorBigBlueButtonJoinMeeting'])->name('instructor.consultation.join-bbb-meeting');
Route::get('consultation-join-jitsi-meeting/{uuid}', [InstructorConsultationController::class, 'jitsiJoinMeeting'])->name('consultation.join-jitsi-meeting');
// Consultation Online Meet

Route::get('migrate', function (){
    Artisan::call('migrate');
    return redirect()->back();
})->middleware('isDemo');

Route::get('get-instructor-booking-times', [ConsultationController::class, 'getInstructorBookingTime'])->name('getInstructorBookingTime');
Route::get('consultation-instructor-list', [ConsultationController::class, 'consultationInstructorList'])->name('consultationInstructorList');

Route::get('get-consultation-instructor/fetch-data', [ConsultationController::class, 'paginationFetchData'])->name('consultation-instructor.fetch-data');
Route::get('get-filter-consultation-instructor', [ConsultationController::class, 'getFilterConsultationInstructor'])->name('getFilterConsultationInstructor');
Route::get('get-off-days/{user_id}', [ConsultationController::class, 'getOffDays'])->name('getOffDays');

Route::group(['prefix' => 'forum', 'as' => 'forum.'], function (){
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('ask-a-question', [ForumController::class, 'askQuestion'])->name('askQuestion');
    Route::post('ask-a-question-store', [ForumController::class, 'questionStore'])->name('question.store');
    Route::get('forum-post-details/{uuid}', [ForumController::class, 'forumPostDetails'])->name('forumPostDetails');
    Route::post('forum-post-comment-store', [ForumController::class, 'forumPostCommentStore'])->name('forumPostComment.store');
    Route::post('forum-post-comment-reply-store', [ForumController::class, 'forumPostCommentReplyStore'])->name('forumPostCommentReply.store');
    Route::get('render-forum-category-posts', [ForumController::class, 'renderForumCategoryPosts'])->name('renderForumCategoryPosts');
    Route::get('forum-category-posts/{uuid}', [ForumController::class, 'forumCategoryPosts'])->name('forumCategoryPosts');
    Route::get('forum-leaderboard', [ForumController::class, 'forumLeaderboard'])->name('forumLeaderboard');
    Route::get('search-forum-list', [ForumController::class, 'searchForumList'])->name('search-forum.list');
});

// Google login
Route::get('login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Facebook login
Route::get('login/facebook', [LoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);

// Twitter login
Route::get('login/twitter', [LoginController::class, 'redirectToTwitter'])->name('login.twitter');
Route::get('login/twitter/callback', [LoginController::class, 'handleTwitterCallback']);

Route::get('page/{slug?}',[PageController::class, 'pageShow'])->name('page');
