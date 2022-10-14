<?php

use App\Events\ChatEvent;
use App\Http\Controllers\Frontend\SupportTicketController;
use App\Http\Controllers\Student\CartManagementController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\MyCourseController;
use App\Http\Controllers\Student\WishlistController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'student.'], function () {

    //Start:: My Learning Course
    Route::get('my-learning', [MyCourseController::class, 'myLearningCourseList'])->name('my-learning');
    Route::get('my-consultation', [MyCourseController::class, 'myConsultationList'])->name('my-consultation');
    Route::get('download-invoice/{item_id}', [MyCourseController::class, 'downloadInvoice'])->name('download-invoice');
    Route::get('my-course/{slug}/{action_type?}/{quiz_uuid?}/{answer_id?}', [MyCourseController::class, 'myCourseShow'])->name('my-course.show');

    Route::get('my-learning-video-completed', [MyCourseController::class, 'videoCompleted'])->name('video.completed')->middleware('isDemo');

    Route::post('save-exam-answer/{course_uuid}/{question_uuid}/{take_exam_id}', [MyCourseController::class, 'saveExamAnswer'])->name('save-exam-answer')->middleware('isDemo');
    Route::post('review-create', [MyCourseController::class, 'reviewCreate'])->name('review.create')->middleware('isDemo');
    Route::post('review-paginate/{courseId}', [MyCourseController::class, 'reviewPaginate'])->name('reviewPaginate');
    Route::post('discussion-create', [MyCourseController::class, 'discussionCreate'])->name('discussion.create')->middleware('isDemo');
    Route::post('discussion-reply/{discussionId}', [MyCourseController::class, 'discussionReply'])->name('discussion.reply')->middleware('isDemo');

    //Star:: Course Assignment
    Route::get('assignment-list', [MyCourseController::class, 'assignmentList'])->name('assignment-list');
    Route::get('assignment-details', [MyCourseController::class, 'assignmentDetails'])->name('assignment-details');
    Route::get('assignment-result', [MyCourseController::class, 'assignmentResult'])->name('assignment-result');
    Route::get('assignment-submit', [MyCourseController::class, 'assignmentSubmit'])->name('assignment-submit')->middleware('isDemo');
    Route::post('assignment-submit/{course_id}/{assignment_id}', [MyCourseController::class, 'assignmentSubmitStore'])->name('assignment-submit.store')->middleware('isDemo');
    //End:: Course Assignment

    //Start:: Student Profile & Change Password
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');

    Route::get('become-an-instructor', [DashboardController::class, 'becomeAnInstructor'])->name('become-an-instructor');
    Route::post('save-instructor-info', [DashboardController::class, 'saveInstructorInfo'])->name('save-instructor-info')->middleware('isDemo');
    Route::post('save-profile/{uuid}', [DashboardController::class, 'saveProfile'])->name('save-profile')->middleware('isDemo');
    Route::get('get-state-by-country/{country_id}', [DashboardController::class, 'getStateByCountry']);
    Route::get('get-city-by-state/{state_id}', [DashboardController::class, 'getCityByState']);

    Route::get('change-password', [DashboardController::class, 'changePassword'])->name('change-password');
    Route::post('change-password', [DashboardController::class, 'changePasswordUpdate'])->name('changePasswordUpdate');
    //End:: Student profile & Change Password

    //Start:: Cart & Wishlist
    Route::get('cart-list', [CartManagementController::class, 'cartList'])->name('cartList');
    Route::post('apply-coupon', [CartManagementController::class, 'applyCoupon'])->name('applyCoupon');
    Route::post('add-to-cart', [CartManagementController::class, 'addToCart'])->name('addToCart');
    Route::post('go-to-checkout', [CartManagementController::class, 'goToCheckout'])->name('goToCheckout');
    Route::delete('cart-delete/{id}', [CartManagementController::class, 'cartDelete'])->name('cartDelete');

    Route::post('add-to-cart-consultation', [CartManagementController::class, 'addToCartConsultation'])->name('addToCartConsultation');


    Route::get('checkout', [CartManagementController::class, 'checkout'])->name('checkout');
    Route::post('razorpay_payment', [CartManagementController::class, 'razorpay_payment'])->name('razorpay_payment');
    Route::post('pay', [CartManagementController::class, 'pay'])->name('pay')->middleware('isDemo');
    Route::get('paypal-payment-status', [CartManagementController::class, 'paypalPaymentStatus'])->name('paypalPaymentStatus');

    Route::get('fetch-bank', [CartManagementController::class, 'fetchBank'])->name('fetchBank');

    // mollie success
    Route::get('payment-success/{id}', [CartManagementController::class, 'molliePaymentSuccess'])->name('payment.success');

    // instamojo success
    Route::get('pay-success/{id}',[CartManagementController::class, 'instamojoPaymentSuccess'])->name('pay.success');

    //Start:: SSLCOMMERZ
    Route::post('/pay-via-ajax', [CartManagementController::class, 'payViaAjax'])->middleware('isDemo');
    Route::post('/success', [CartManagementController::class, 'success']);
    Route::post('/fail', [CartManagementController::class, 'fail']);
    Route::post('/cancel', [CartManagementController::class, 'cancel']);
    Route::post('/ipn', [CartManagementController::class, 'ipn']);
    //End:: SSLCOMMERZ

    // Paystack
    Route::post('paystack/payment', [CartManagementController::class, 'paystackPayment'])->name('paystack_payment');
    Route::get('/payment/callback', [CartManagementController::class, 'handlePaystackGatewayCallback'])->name('paystack_payment.callback');

    Route::get('wishlist', [WishlistController::class, 'wishlist'])->name('wishlist');
    Route::post('add-to-wishlist', [WishlistController::class, 'addToWishlist'])->name('addToWishlist');
    Route::delete('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlistDelete');
    //End:: Cart & Wishlist

    Route::group(['prefix' => 'support-tickets', 'as' => 'support-ticket.'], function () {
        Route::get('create-tickets', [SupportTicketController::class, 'create'])->name('create');
        Route::post('store-tickets', [SupportTicketController::class, 'store'])->name('store')->middleware('isDemo');
        Route::get('show-details/{uuid}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('ticket-message-store', [SupportTicketController::class, 'messageStore'])->name('message.store')->middleware('isDemo');
        Route::get('get-tickets/fetch-data', [SupportTicketController::class, 'paginationFetchData'])->name('fetch-data');
    });

    Route::get('thank-you', [MyCourseController::class, 'thankYou'])->name('thank-you');
});
