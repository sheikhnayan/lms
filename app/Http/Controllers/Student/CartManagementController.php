<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Bank;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\BundleCourse;
use App\Models\CartManagement;
use App\Models\City;
use App\Models\ConsultationSlot;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponCourse;
use App\Models\CouponInstructor;
use App\Models\Course;
use App\Models\Order;
use App\Models\Order_billing_address;
use App\Models\Order_item;
use App\Models\Product;
use App\Models\State;
use App\Models\Student;
use App\Models\Withdraw;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Session;
use Redirect;
use Stripe;
use Razorpay\Api\Api;
use Exception;
use DB;
use Mollie\Laravel\Facades\Mollie;
use Unicodeveloper\Paystack\Facades\Paystack;

class CartManagementController extends Controller
{
    use ImageSaveTrait, General, SendNotification;

    private $_api_context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /** Mollie api key **/
        if (env('MOLLIE_KEY')) {
            Mollie::api()->setApiKey(env('MOLLIE_KEY'));
        }

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function cartList()
    {
        $data['pageTitle'] = 'Cart';

        // Start:: Check course, bundle, consultation exists or not. If not exists, Delete it.
        $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
        foreach ($carts as $cart){
            if ($cart->course_id) {
                $course = Course::find($cart->course_id);
                if (!$course){
                    CartManagement::where('course_id', $cart->course_id)->delete();
                }
            } elseif ($cart->bundle_id) {
                $bundle = Bundle::find($cart->bundle_id);
                if (!$bundle){
                    CartManagement::where('bundle_id', $cart->bundle_id)->delete();
                }
            } elseif ($cart->consultation_slot_id) {
                $consultation = ConsultationSlot::find($cart->consultation_slot_id);
                if (!$consultation){
                    CartManagement::where('consultation_slot_id', $cart->consultation_slot_id)->delete();
                }
            }
        }
        // End:: Check course, bundle, consultation exists or not. If not exists, Delete it.

        $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
        foreach ($carts as $cart) {
            $cart = CartManagement::find($cart->id);

            // Start:: Course & Promotion Course Check or not
            $course = Course::find($cart->course_id);
            if ($course) {
                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                $percentage = @$course->promotionCourse->promotion->percentage;
                $promotion_discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

                if (now()->gt($startDate) && now()->lt($endDate)) {
                    $cart->promotion_id = @$course->promotionCourse->promotion->id;
                    $cart->price = $promotion_discount_price;
                } else {
                    $cart->main_price = $course->price;
                    $cart->price = $course->price;
                }
            }
            // End:: Course & Promotion Course Check or not

            //Start:: Bundle Offer Check
            $bundle = Bundle::find($cart->bundle_id);
            if ($bundle) {
                $cart->main_price = $bundle->price;
                $cart->price = $bundle->price;
            }
            //End:: Bundle Offer Check

            //Start:: Consultation Check
            if ($cart->consultation_slot_id){
                $consultationSlot = ConsultationSlot::find($cart->consultation_slot_id);

                if ($consultationSlot){
                    $consultationArray = array();
                    $newConsultationDataArray = [
                        'instructor_user_id' => $consultationSlot->user_id,
                        'student_user_id' => Auth::id(),
                        'consultation_slot_id' => $consultationSlot->id,
                        'date' => $cart->consultation_date,
                        'day' => $consultationSlot->day,
                        'time' => $consultationSlot->time,
                        'duration' => $consultationSlot->duration,
                        'status' => 0,
                    ];

                    $consultationArray[] = $newConsultationDataArray;
                    $cart->consultation_details = $consultationArray;

                    $hour_duration = $consultationSlot->hour_duration;
                    $minute_duration = $consultationSlot->minute_duration;
                    $hourly_rate = @$consultationSlot->user->instructor->hourly_rate;
                    $minuteCost = 0;
                    if ($minute_duration > 0){
                        $minuteCost = ($hourly_rate / (60 / $minute_duration));
                    }
                    $totalCost = ($hour_duration * $hourly_rate) + $minuteCost;

                    $cart->main_price = $totalCost;
                    $cart->price = $totalCost;
                }
            }
            //End:: Consultation Check

            $cart->coupon_id = null;
            $cart->discount = 0;
            $cart->save();
        }

        $data['carts'] = CartManagement::whereUserId(@Auth::user()->id)->get();

        return view('frontend.student.cart.cart-list', $data);
    }

    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = "You need to login first!";
            $response['status'] = 401;
            return response()->json($response);
        }

        if (!$request->coupon_code) {
            $response['msg'] = "Enter coupon code!";
            $response['status'] = 404;
            return response()->json($response);
        }


        if ($request->id) {
            $cart = CartManagement::find($request->id);
            if (!$cart) {
                $response['msg'] = "Cart item not found!";
                $response['status'] = 404;
                return response()->json($response);
            }

            $coupon = Coupon::where('coupon_code_name', $request->coupon_code)->where('start_date', '<=', Carbon::now()->format('Y-m-d'))->where('end_date', '>=', Carbon::now()->format('Y-m-d'))->first();

            if ($coupon) {
                if ($cart->price < $coupon->minimum_amount) {
                    $response['msg'] = "Minimum " . get_currency_code() . $coupon->minimum_amount . " need to buy for use this coupon!";
                    $response['status'] = 402;
                    return response()->json($response);
                }
            }
            if (!$coupon) {
                $response['msg'] = "Invalid coupon code!";
                $response['status'] = 404;
                return response()->json($response);
            }


            if (CartManagement::whereUserId(@Auth::user()->id)->whereCouponId($coupon->id)->count() > 0) {
                $response['msg'] = "You've already used this coupon!";
                $response['status'] = 402;
                return response()->json($response);
            }

            $discount_price = ($cart->price * $coupon->percentage) / 100;

            if ($coupon->coupon_type == 1) {
                $cart->price = round($cart->price - $discount_price);
                $cart->discount = $discount_price;
                $cart->coupon_id = $coupon->id;
                $cart->save();

                $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
                $response['msg'] = "Coupon Applied";
                $response['price'] = $cart->price;
                $response['discount'] = $cart->discount;
                $response['total'] = get_number_format($carts->sum('price'));
                $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                $response['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                $response['status'] = 200;
                return response()->json($response);
            } elseif ($coupon->coupon_type == 2) {
                if ($cart->course) {
                    $user_id = $cart->course->user_id;
                } else {
                    $user_id = $cart->product->user_id;
                }

                $couponInstructor = CouponInstructor::where('coupon_id', $coupon->id)->where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($couponInstructor) {

                    $cart->price = round($cart->price - $discount_price);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                    $cart->save();

                    $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
                    $response['msg'] = "Coupon Applied";
                    $response['price'] = $cart->price;
                    $response['discount'] = $cart->discount;
                    $response['total'] = get_number_format($carts->sum('price'));
                    $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $response['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                    $response['status'] = 200;
                    return response()->json($response);
                } else {
                    $response['msg'] = "Invalid coupon code!";
                    $response['status'] = 404;
                    return response()->json($response);
                }
            } elseif ($coupon->coupon_type == 3) {
                $couponCourse = CouponCourse::where('coupon_id', $coupon->id)->where('course_id', $cart->course_id)->orderBy('id', 'desc')->first();
                if ($couponCourse) {

                    $cart->price = round($cart->price - $discount_price);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                    $cart->save();

                    $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
                    $response['msg'] = "Coupon Applied";
                    $response['price'] = $cart->price;
                    $response['discount'] = $cart->discount;
                    $response['total'] = get_number_format($carts->sum('price'));
                    $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $response['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                    $response['status'] = 200;
                    return response()->json($response);
                } else {
                    $response['msg'] = "Invalid coupon code!";
                    $response['status'] = 404;
                    return response()->json($response);
                }
            } else {
                $response['msg'] = "Invalid coupon code!";
                $response['status'] = 404;
                return response()->json($response);
            }
        } else {
            $response['msg'] = "Cart item not found!";
            $response['status'] = 404;
            return response()->json($response);
        }
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = "You need to login first!";
            $response['status'] = 401;
            return response()->json($response);
        }

        if ($request->course_id) {
            $courseOrderExits = Order_item::whereCourseId($request->course_id)->whereUserId(Auth::user()->id)->first();

            if ($courseOrderExits) {
                $order = Order::find($courseOrderExits->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($courseOrderExits->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } elseif ($order->payment_status == 'pending') {
                        $response['msg'] = "You've already request the course & status is pending!";
                        $response['status'] = 402;
                        return response()->json($response);
                    } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = "You've already purchased the course!";
                        $response['status'] = 402;
                        return response()->json($response);
                    }
                } else {
                    $response['msg'] = "Something is wrong! Try again.";
                    $response['status'] = 402;
                    return response()->json($response);
                }
            }

            $ownCourseCheck = Course::whereUserId(Auth::user()->id)->where('id', $request->course_id)->first();

            if ($ownCourseCheck) {
                $response['msg'] = "This is your course. No need to add to cart.";
                $response['status'] = 402;
                return response()->json($response);
            }

            $courseExits = Course::find($request->course_id);
            if (!$courseExits) {
                $response['msg'] = "Course not found!";
                $response['status'] = 404;
                return response()->json($response);
            }
        }

        if ($request->product_id) {
            $productExits = Product::find($request->product_id);
            if (!$productExits) {
                $response['msg'] = "Product not found!";
                $response['status'] = 404;
                return response()->json($response);
            }
        }

        if ($request->bundle_id) {
            $bundleExits = Bundle::find($request->bundle_id);
            if (!$bundleExits) {
                $response['msg'] = "Bundle not found!";
                $response['status'] = 404;
                return response()->json($response);
            }

            $ownBundleCheck = Bundle::whereUserId(Auth::user()->id)->where('id', $request->bundle_id)->first();
            if ($ownBundleCheck) {
                $response['msg'] = "This is your bundle offer. No need to add to cart.";
                $response['status'] = 402;
                return response()->json($response);
            }

            $bundleOrderExits = Order_item::whereUserId(Auth::user()->id)->where('bundle_id', $request->bundle_id)->first();
            if ($bundleOrderExits) {
                $order = Order::find($bundleOrderExits->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($bundleOrderExits->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } elseif ($order->payment_status == 'pending') {
                        $response['msg'] = "You've already request this bundle & status is pending!";
                        $response['status'] = 402;
                        return response()->json($response);
                    } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = "You've already purchased this bundle!";
                        $response['status'] = 402;
                        return response()->json($response);
                    }
                } else {
                    $response['msg'] = "Something is wrong! Try again.";
                    $response['status'] = 402;
                    return response()->json($response);
                }
            }
        }

        //Start:: Cart Management
        if ($request->course_id) {
            $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($request->course_id)->first();
        } elseif ($request->product_id) {
            $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereProductId($request->product_id)->first();
        } elseif ($request->bundle_id) {
            $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereBundleId($request->bundle_id)->first();
        }

        if ($cartExists) {
            $response['msg'] = "Already added to cart!";
            $response['status'] = 409;
            return response()->json($response);
        }

        if ($request->course_id) {
            if ($courseExits->learner_accessibility == 'free') {
                $order = new Order();
                $order->user_id = Auth::user()->id;
                $order->order_number = rand(100000, 999999);
                $order->payment_status = 'free';
                $order->save();

                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = Auth::user()->id;
                $order_item->course_id = $courseExits->id;
                $order_item->owner_user_id = $courseExits->user_id ?? null;
                $order_item->unit_price = 0;
                $order_item->admin_commission = 0;
                $order_item->owner_balance = 0;
                $order_item->sell_commission = 0;
                $order_item->save();

                $response['msg'] = "Free Course added to your my learning list!";
                $response['status'] = 200;
                return response()->json($response);
            }
        }

        $cart = new CartManagement();
        $cart->user_id = Auth::user()->id;
        $cart->course_id = $request->course_id;
        $cart->product_id = $request->product_id;
        $cart->bundle_id = $request->bundle_id;

        if ($request->course_id) {
            $cart->main_price = $courseExits->price;
            $cart->price = $courseExits->price;
        }

        if ($request->bundle_id) {
            $bundleCourses = BundleCourse::where('bundle_id', $request->bundle_id)->pluck('course_id')->toArray();
            $cart->bundle_course_ids = $bundleCourses;
            $cart->main_price = $bundleExits->price;
            $cart->price = $bundleExits->price;
        }
        if ($request->product_id) {
            $cart->main_price = $productExits->price;
            $cart->price = $productExits->price;
        }

        $cart->save();

        $response['quantity'] = CartManagement::whereUserId(Auth::user()->id)->count();
        $response['msg'] = "Added to cart";
        $response['msgInfoChange'] = "Added to cart";
        $response['status'] = 200;
        //End:: Cart Management
        return response()->json($response);
    }

    public function addToCartConsultation(Request $request)
    {

        if (!Auth::check()) {
            $response['msg'] = "You need to login first!";
            $response['status'] = 401;
            return response()->json($response);
        }

        if ($request->consultation_slot_id) {
            $consultationExit = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId($request->booking_instructor_user_id)->first();

            if (!$consultationExit) {
                $response['msg'] = "Time slot not found!";
                $response['status'] = 404;
                return response()->json($response);
            }

            $ownConsultationSlotCheck = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId(Auth::id())->first();
            if ($ownConsultationSlotCheck) {
                $response['msg'] = "This is your consultation slot. No need to add.";
                $response['status'] = 402;
                return response()->json($response);
            }

            $consultationOrderBooked = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
            if ($consultationOrderBooked){
                $order = Order::find($consultationOrderBooked->order_id);
                if ($order) {
                    if ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = "This slot already purchased. Please try another slot.";
                        $response['status'] = 402;
                        return response()->json($response);
                    }
                } else {
                    $response['msg'] = "Something is wrong! Try again.";
                    $response['status'] = 402;
                    return response()->json($response);
                }

            }

            $consultationOrderExit = Order_item::whereUserId(Auth::user()->id)->whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
            if ($consultationOrderExit) {
                $order = Order::find($consultationOrderExit->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($consultationOrderExit->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } elseif ($order->payment_status == 'pending') {
                        $response['msg'] = "You've already request this slot & status is pending!";
                        $response['status'] = 402;
                        return response()->json($response);
                    } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = "You've already purchased this slot!";
                        $response['status'] = 402;
                        return response()->json($response);
                    }
                } else {
                    $response['msg'] = "Something is wrong! Try again.";
                    $response['status'] = 402;
                    return response()->json($response);
                }
            }

            $consultationOrderExit = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
            if ($consultationOrderExit) {
                $order = Order::find($consultationOrderExit->order_id);
                if ($order) {
                    if ($order->payment_status == 'pending') {
                        $response['msg'] = "Another User already request this slot!";
                        $response['status'] = 402;
                        return response()->json($response);
                    } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = "Another User already purchased this slot!";
                        $response['status'] = 402;
                        return response()->json($response);
                    }
                } else {
                    $response['msg'] = "Something is wrong! Try again.";
                    $response['status'] = 402;
                    return response()->json($response);
                }

            }


            $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
            if ($cartExists) {
                $response['msg'] = "Already added to cart!";
                $response['status'] = 409;
                return response()->json($response);
            }

            $cart = new CartManagement();
            $cart->user_id = Auth::user()->id;
            $cart->consultation_slot_id = $request->consultation_slot_id;

            $consultationArray = array();
            $newConsultationDataArray = [
                'instructor_user_id' => $consultationExit->user_id,
                'student_user_id' => Auth::id(),
                'consultation_slot_id' => $consultationExit->id,
                'date' => $request->bookingDate,
                'day' => $consultationExit->day,
                'time' => $consultationExit->time,
                'duration' => $consultationExit->duration,
                'status' => 0,
            ];

            $consultationArray[] = $newConsultationDataArray;

            $cart->consultation_details = $consultationArray;
            $cart->consultation_date = $request->bookingDate;
            $cart->consultation_available_type = $request->available_type;

            /*
            * Price Calculation
            */
            $hour_duration = $consultationExit->hour_duration;
            $minute_duration = $consultationExit->minute_duration;
            $hourly_rate = @$consultationExit->user->instructor->hourly_rate;
            $minuteCost = 0;
            if ($minute_duration > 0){
                $minuteCost = ($hourly_rate / (60 / $minute_duration));
            }
            $totalCost = ($hour_duration * $hourly_rate) + $minuteCost;

            $cart->main_price = $totalCost;
            $cart->price = $totalCost;
            $cart->save();


            $response['status'] = 200;
            $response['msg'] = "Consultation added to cart";
            $response['redirect_route'] = route('student.cartList');
            return response()->json($response);

        } else {
            $response['msg'] = "Time slot not found!";
            $response['status'] = 404;
            return response()->json($response);
        }

    }

    public function goToCheckout(Request $request)
    {
        if ($request->has('proceed_to_checkout')) {
            return redirect(route('student.checkout'));
        } elseif ($request->has('pay_from_lmszai_wallet')) {
            $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
            if (!is_array($carts)){
                $this->showToastrMessage('error', 'Your cart is empty!');
                return redirect()->back();
            }
            if ($carts->sum('price') > instructor_available_balance()) {
                $this->showToastrMessage('warning', 'Insufficient balance');
                return redirect()->back();
            } else {

                $order = $this->placeOrder('buy');
                $order->payment_status = 'paid';
                $order->save();

                /** ====== Send notification =========*/
                $text = "New student enrolled";
                $target_url = route('instructor.all-student');
                foreach ($order->items as $item) {
                    if ($item->course) {
                        $this->send($text, 2, $target_url, $item->course->user_id);
                    }
                }

                $text = "Course has been sold";
                $this->send($text, 1, null, null);

                /** ====== Send notification =========*/

                $withdrow = new Withdraw();
                $withdrow->transection_id = rand(1000000, 9999999);;
                $withdrow->amount = $carts->sum('price');
                $withdrow->payment_method = 'buy';
                $withdrow->status = 1;
                $withdrow->save();


                $this->showToastrMessage('success', 'Payment has been completed');
                return redirect()->route('student.thank-you');
            }
        } elseif ($request->has('cancel_order')) {
            CartManagement::whereUserId(@Auth::user()->id)->delete();
            $this->showToastrMessage('warning', 'Order has been cancel');
            return redirect(url('/'));
        } else {
            abort(404);
        }
    }

    public function cartDelete($id)
    {
        $cart = CartManagement::findOrFail($id);
        $cart->delete();
        $this->showToastrMessage('success', 'Removed from cart list!');
        return redirect()->back();
    }

    public function fetchBank(Request $request)
    {
        $bank_id = Bank::find($request->bank_id);
        if ($bank_id) {
            return response()->json([
                'account_number' => $bank_id->account_number,
            ]);
        }
    }

    public function checkout()
    {
        $data['pageTitle'] = "Checkout";
        $data['carts'] = CartManagement::whereUserId(@Auth::user()->id)->get();
        $data['student'] = auth::user()->student;
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();
        $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();

        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        $razorpay_grand_total_with_conversion_rate = ($data['carts']->sum('price') + get_platform_charge($data['carts']->sum('price'))) * (get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0);
        $data['razorpay_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($razorpay_grand_total_with_conversion_rate, 2));

        $paystack_grand_total_with_conversion_rate = ($data['carts']->sum('price') + get_platform_charge($data['carts']->sum('price'))) * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
        $data['paystack_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($paystack_grand_total_with_conversion_rate, 2));

        $sslcommerz_grand_total_with_conversion_rate = ($data['carts']->sum('price') + get_platform_charge($data['carts']->sum('price'))) * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
        $data['sslcommerz_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));

        return view('frontend.student.cart.checkout', $data);
    }

    public function razorpay_payment(Request $request)
    {
        $input = $request->all();
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        if (empty(env('RAZORPAY_KEY')) && empty(env('RAZORPAY_SECRET'))) {
            $this->showToastrMessage('error', 'Razorpay payment gateway off!');
            return redirect()->back();
        }

        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
            } catch (Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }
        }

        $order = $this->placeOrder($request->payment_method);
        $order->payment_status = 'paid';
        $order->payment_method = 'razorpay';

        $payment_currency = get_option('razorpay_currency');
        $conversion_rate = get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0;

        $order->payment_currency = $payment_currency;
        $order->conversion_rate = $conversion_rate;
        $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge) * $conversion_rate;
        $order->save();

        /** ====== Send notification =========*/
        $text = "New student enrolled";
        $target_url = route('instructor.all-student');
        foreach ($order->items as $item) {
            if ($item->course) {
                $this->send($text, 2, $target_url, $item->course->user_id);
            }
        }

        $text = "Course has been sold";
        $this->send($text, 1, null, null);

        /** ====== Send notification =========*/

        $this->showToastrMessage('success', 'Payment has been completed');
        return redirect()->route('student.thank-you');
    }

    public function pay(Request $request)
    {
        if (is_null($request->payment_method)) {
            $this->showToastrMessage('warning', 'Please Select Payment Method');
            return redirect()->back();
        }
        if ($request->payment_method == 'bank') {
            if (empty($request->deposit_by) || is_null($request->deposit_slip)) {
                $this->showToastrMessage('error', 'Bank Information Not Valid!');
                return redirect()->back();
            }
        }

        if ($request->payment_method == 'paypal') {
            if (empty(env('PAYPAL_CLIENT_ID')) || empty(env('PAYPAL_SECRET')) || empty(env('PAYPAL_MODE'))) {
                $this->showToastrMessage('error', 'Paypal payment gateway is off!');
                return redirect()->back();
            }
        }

        if ($request->payment_method == 'mollie') {
            if (empty(env('MOLLIE_KEY'))) {
                $this->showToastrMessage('error', 'Mollie payment gateway is off!');
                return redirect()->back();
            }
        }

        if ($request->payment_method == 'instamojo') {
            if (empty(env('IM_API_KEY')) || empty(env('IM_AUTH_TOKEN')) || empty(env('IM_URL'))) {
                $this->showToastrMessage('error', 'Instamojo payment gateway is off!');
                return redirect()->back();
            }
        }

        $order = $this->placeOrder($request->payment_method);
        /** order billing address */

        if (auth::user()->student) {
            $student = Student::find(auth::user()->student->id);
            $student->fill($request->all());
            $student->save();
        }

        if ($request->payment_method == 'paypal') {
            $paypal_grand_total_with_conversion_rate = $order->grand_total * (get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0);
            $paypal_grand_total_with_conversion_rate = (float)preg_replace("/[^0-9.]+/", "", number_format($paypal_grand_total_with_conversion_rate, 2));

            $currency = get_option('paypal_currency');
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item_1 = new Item();
            $item_1->setName('Payment for purchase')
                /** item name **/
                ->setCurrency($currency)
                ->setQuantity(1)
                ->setPrice($paypal_grand_total_with_conversion_rate);
            /** unit price **/

            $item_list = new ItemList();
            $item_list->setItems(array($item_1));

            $amount = new Amount();
            $amount->setCurrency($currency)
                ->setTotal($paypal_grand_total_with_conversion_rate);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Payment for purchase');

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(route('student.paypalPaymentStatus'))
                /** Specify return URL **/
                ->setCancelUrl(route('student.paypalPaymentStatus'));

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));
            /** dd($payment->create($this->_api_context));exit; **/
            try {

                $payment->create($this->_api_context);
            } catch (\PayPal\Exception\PPConnectionException $ex) {

                if (\Config::get('app.debug')) {

                    \Session::put('error', 'Connection timeout');
                    return redirect()->back();
                } else {

                    \Session::put('error', 'Some error occur, sorry for inconvenient');
                    return redirect()->back();
                }
            }

            foreach ($payment->getLinks() as $link) {

                if ($link->getRel() == 'approval_url') {

                    $redirect_url = $link->getHref();
                    break;
                }
            }

            /** add payment ID to session **/
            Session::put('paypal_payment_id', $payment->getId());
            Session::put('order_uuid', $order->uuid);
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return Redirect::away($redirect_url);
            }

            \Session::put('error', 'Unknown error occurred');
            return redirect()->back();
        } else if ($request->payment_method == 'mollie') {

            $mollie_grand_total_with_conversion_rate = $order->grand_total * (get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0);
            $mollie_grand_total_with_conversion_rate = (float)preg_replace("/[^0-9.]+/", "", number_format($mollie_grand_total_with_conversion_rate, 2));

            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'value' => number_format($mollie_grand_total_with_conversion_rate, 2),
                    'currency' => get_option('mollie_currency'),
                ],
                'description' => @Auth::user()->name,
                'redirectUrl' => route('student.payment.success', $order->id),
            ]);
            $payment = Mollie::api()->payments()->get($payment->id);

            // redirect customer to Mollie checkout page
            return redirect($payment->getCheckoutUrl(), 303);
        } else if ($request->payment_method == 'instamojo') {
            $im_grand_total_with_conversion_rate = $order->grand_total * (get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0);
            $im_grand_total_with_conversion_rate = (float)preg_replace("/[^0-9.]+/", "", number_format($im_grand_total_with_conversion_rate, 2));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, get_option('IM_URL'));
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    "X-Api-Key:" . get_option('IM_API_KEY'),
                    "X-Auth-Token:" . get_option('IM_AUTH_TOKEN')
                )
            );
            $payload = array(
                'purpose' => 'Course Purchase',
                'amount' => $im_grand_total_with_conversion_rate,
                'phone' => @Auth::user()->student->phone_number,
                'buyer_name' => @Auth::user()->name,
                'redirect_url' => route('student.pay.success', $order->id),
                'send_email' => true,
                'send_sms' => true,
                'email' => @Auth::user()->email,
                'allow_repeated_payments' => false
            );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);

            // instamojo redirect success
            return redirect($response->payment_request->longurl);
        } else if ($request->payment_method == 'bank') {
            $deposit_by = $request->deposit_by;
            $deposit_slip = $this->uploadFileWithDetails('bank', $request->deposit_slip);

            $order->payment_status = 'pending';
            $order->deposit_by = $deposit_by;
            $order->deposit_slip = $deposit_slip['path'];
            $order->payment_method = 'bank';
            $order->bank_id = $request->bank_id;
            $order->save();

            /** ====== Send notification =========*/
            $text = "New course enrolled pending request";
            $target_url = route('report.order-pending');
            $this->send($text, 1, $target_url, null);
            /** ====== Send notification =========*/

            $this->showToastrMessage('success', 'Request has been Placed! Please Wait for Approve');
            return redirect()->route('student.thank-you');
        } else {

            try {
                $stripe_grand_total_with_conversion_rate = $order->grand_total * (get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0);
                $stripe_grand_total_with_conversion_rate = (float)preg_replace("/[^0-9.]+/", "", number_format($stripe_grand_total_with_conversion_rate, 2));

                $stripeToken = $request->stripeToken;
                Stripe\Stripe::setApiKey(get_option('STRIPE_SECRET_KEY'));
                $charge = Stripe\Charge::create([
                    "amount" => ($stripe_grand_total_with_conversion_rate * 100),
                    "currency" => get_option('stripe_currency'),
                    "source" => $stripeToken,
                    "description" => 'Payment for purchase'
                ]);

                if ($charge->status == 'succeeded') {
                    $order->payment_status = 'paid';
                    $order->payment_method = 'stripe';
                    $order->save();

                    /** ====== Send notification =========*/
                    $text = "New student enrolled";
                    $target_url = route('instructor.all-student');
                    foreach ($order->items as $item) {
                        if ($item->course) {
                            $this->send($text, 2, $target_url, $item->course->user_id);
                        }
                    }

                    $text = "Course has been sold";
                    $this->send($text, 1, null, null);

                    /** ====== Send notification =========*/

                    $this->showToastrMessage('success', 'Payment has been completed');
                    return redirect()->route('student.thank-you');
                }
            } catch (\Stripe\Error\Card $e) {
                // The card has been declined
                $this->showToastrMessage('error', 'Payment has been declined');
                return redirect(url('/'));
            }
        }
    }

    public function instamojoPaymentSuccess(Request $request, $id)
    {
        //  "payment_id"  "payment_status"  "payment_request_id"
        if ($request->payment_status == 'Credit') {
            $order = Order::find($id);
            $order->payment_status = 'paid';
            $order->save();
            $this->showToastrMessage('success', 'Payment has been completed');
            return redirect()->route('student.thank-you');
        }
        $this->showToastrMessage('error', 'Payment has been declined');
        return redirect(url('/'));
    }

    public function molliePaymentSuccess($id)
    {
        $order = Order::find($id);
        $order->payment_status = 'paid';
        $order->save();
        $this->showToastrMessage('success', 'Payment has been completed');
        return redirect()->route('student.thank-you');
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function paystackPayment(Request $request)
    {
        try {
            $order = $this->placeOrder('paystack');
            $order->paystack_reference_number = $request->reference;
            $order->save();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch (\Exception $e) {
            $this->showToastrMessage('error', 'The paystack token has expired. Please refresh the page and try again.');
            return redirect()->back();
        }
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handlePaystackGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want

        if ($paymentDetails) {
            $status = $paymentDetails['data']['status'];
            $reference = $paymentDetails['data']['reference'];
            if ($status == 'success') {
                $order = Order::where('paystack_reference_number', $reference)->first();
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->payment_method = 'paystack';
                    $order->save();

                    /** ====== Send notification =========*/
                    $text = "New student enrolled";
                    $target_url = route('instructor.all-student');
                    foreach ($order->items as $item) {
                        if ($item->course) {
                            $this->send($text, 2, $target_url, $item->course->user_id);
                        }
                    }

                    $text = "Course has been sold";
                    $this->send($text, 1, null, null);

                    /** ====== Send notification =========*/

                    $this->showToastrMessage('success', 'Payment has been completed');
                    return redirect()->route('student.thank-you');
                }

            }
        } else {
            $this->showToastrMessage('error', 'Payment has been failed. Please try again.');
            return redirect()->route('main.index');
        }
    }

    public function paypalPaymentStatus(Request $request)
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        $order_uuid = Session::get('order_uuid');

        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        Session::forget('order_uuid');
        if (empty($request->PayerID) || empty($request->token)) {
            $this->showToastrMessage('error', 'Payment has been declined');
            return redirect(url('/'));
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);


        if ($result->getState() == 'approved') {
            $transactions = $result->getTransactions();
            $order = Order::whereUuid($order_uuid)->firstOrFail();;
            $order->payment_status = 'paid';
            $order->payment_method = 'paypal';
            $order->save();

            /** ====== Send notification =========*/
            $text = "New student enrolled";
            $target_url = route('instructor.all-student');
            foreach ($order->items as $item) {
                if ($item->course) {
                    $this->send($text, 2, $target_url, $item->course->user_id);
                }
            }

            $text = "Course has been sold";
            $this->send($text, 1, null, null);

            /** ====== Send notification =========*/

            $this->showToastrMessage('success', 'Payment has been completed');
            return redirect()->route('student.thank-you');
        }

        $this->showToastrMessage('error', 'Payment has been declined');
        return redirect(url('/'));
    }

    public function payViaAjax(Request $request)
    {
        # Here you have to receive all the order data to initiate the payment.
        # Lets your oder transaction information are saving in a table called "orders"
        # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $order = $this->placeOrder($request->payment_method);

        $cart_json = json_decode($request->cart_json);

        //Start:: Conversion rate
        $sslcommerz_grand_total_with_conversion_rate = $order->grand_total * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
        $sslcommerz_grand_total_with_conversion_rate = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));
        //End:: Conversion rate

        $student = $order->user->student;
        $post_data = array();
        $post_data['total_amount'] = $sslcommerz_grand_total_with_conversion_rate; # You cant not pay less than 10
        $post_data['currency'] = get_option('sslcommerz_currency');
        $post_data['tran_id'] = $order->uuid; // tran_id must be unique
        $post_data['product_category'] = "Payment for purchase";
        $post_data['cus_name'] = $order->user ? $order->user->name : 'Student';

        # CUSTOMER INFORMATION

        $phone = '0170000';
        if($cart_json->cus_phone || $student->phone_number){
            $phone = $cart_json->cus_phone ?? $student->phone_number;
        }

        $post_data['cus_email'] = $cart_json->cus_email ?? $order->user->email;
        $post_data['cus_add1'] = $cart_json->cus_addr1 ?? $student->address;
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = $cart_json->postal_code;
        $post_data['cus_country'] = @$student->country->country_name ?? 'BD';
        $post_data['cus_phone'] = $phone;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = get_option('app_name') ?? 'LMS Store';
        $post_data['ship_add1'] = $cart_json->cus_addr1 ?? $student->address;
        $post_data['ship_add2'] =  $cart_json->cus_addr1 ?? $student->address;
        $post_data['ship_city'] =  $cart_json->cus_addr1 ?? $student->address;
        $post_data['ship_state'] =  $cart_json->cus_addr1 ?? $student->address;
        $post_data['ship_postcode'] = $cart_json->postal_code ?? $student->postal_code;
        $post_data['ship_phone'] = $phone;
        $post_data['ship_country'] = @$student->country->country_name ?? 'BD';

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Course Buy";

        $post_data['product_profile'] = "digital-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function success(Request $request)
    {
        $order = Order::whereUuid($request->input('tran_id'))->first();
        $order->payment_status = 'paid';
        $order->payment_method = 'sslcommerz';

        $payment_currency = get_option('sslcommerz_currency');
        $conversion_rate = get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0;

        $order->payment_currency = $payment_currency;
        $order->conversion_rate = $conversion_rate;
        $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge) * $conversion_rate;
        $order->save();

        /** ====== Send notification =========*/
        $text = "New student enrolled";
        $target_url = route('instructor.all-student');
        foreach ($order->items as $item) {
            if ($item->course) {
                $this->send($text, 2, $target_url, $item->course->user_id);
            }
        }

        $text = "Course has been sold";
        $this->send($text, 1, null, null);

        /** ====== Send notification =========*/

        $this->showToastrMessage('success', 'Payment has been completed');
        return redirect()->route('student.thank-you');
    }

    public function fail(Request $request)
    {
        $order = Order::whereUuid($request->input('tran_id'))->first();
        $order->payment_method = 'sslcommerz';
        $order->save();

        $this->showToastrMessage('error', 'Something is wrong! Try again.');
        return redirect(route('main.index'));
    }

    public function cancel(Request $request)
    {
        $order = Order::whereUuid($request->input('tran_id'))->first();
        $order->payment_method = 'sslcommerz';
        $order->save();

        $this->showToastrMessage('success', 'Your request has been cancel.');
        return redirect(route('main.index'));
    }

    public function ipn(Request $request)
    {
        $order = Order::whereUuid($request->input('tran_id'))->first();
        $order->payment_method = 'sslcommerz';
        $order->save();

        $this->showToastrMessage('success', 'Payment has been completed');
        return redirect(route('student.my-learning'));
    }


    private function placeOrder($payment_method)
    {
        $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->order_number = rand(100000, 999999);
        $order->sub_total = $carts->sum('price');
        $order->discount = $carts->sum('discount');
        $order->platform_charge = get_platform_charge($carts->sum('price'));
        $order->current_currency = get_currency_code();
        $order->grand_total = $order->sub_total + $order->platform_charge;
        $order->payment_method = $payment_method;

        $payment_currency = '';
        $conversion_rate = '';

        if ($payment_method == 'paypal') {
            $payment_currency = get_option('paypal_currency');
            $conversion_rate = get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0;
        } elseif ($payment_method == 'stripe') {
            $payment_currency = get_option('stripe_currency');
            $conversion_rate = get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0;
        } elseif ($payment_method == 'bank') {
            $payment_currency = get_option('bank_currency');
            $conversion_rate = get_option('bank_conversion_rate') ? get_option('bank_conversion_rate') : 0;
        } elseif ($payment_method == 'mollie') {
            $payment_currency = get_option('mollie_currency');
            $conversion_rate = get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0;
        } elseif ($payment_method == 'instamojo') {
            $payment_currency = get_option('im_currency');
            $conversion_rate = get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0;
        } elseif ($payment_method == 'paystack') {
            $payment_currency = get_option('paystack_currency');
            $conversion_rate = get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0;
        }

        $order->payment_currency = $payment_currency;
        $order->conversion_rate = $conversion_rate;
        if ($conversion_rate) {
            $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge) * $conversion_rate;
        }

        $order->save();

        foreach ($carts as $cart) {
            if ($cart->course_id) {
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = Auth::id();
                $order_item->course_id = $cart->course_id;
                $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
                $order_item->unit_price = $cart->price;
                if (get_option('sell_commission')) {
                    $order_item->admin_commission = admin_sell_commission($cart->price);
                    $order_item->owner_balance = $cart->price - admin_sell_commission($cart->price);
                    $order_item->sell_commission = get_option('sell_commission');
                } else {
                    $order_item->owner_balance = $cart->price;
                }

                $order_item->save();
            } elseif ($cart->bundle_id) {
                /*
                 If bundle course add. we only calculate all things in order_items 1 time.
                    and all bundle courses save in order_items table.
                 If any course of bundle already purchased with paid. Those course won't be added again.
                 */
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = Auth::user()->id;
                $order_item->bundle_id = $cart->bundle_id;
                $order_item->owner_user_id = $cart->bundle ? $cart->bundle->user_id : null;
                $order_item->unit_price = $cart->price;
                if (get_option('sell_commission')) {
                    $order_item->admin_commission = admin_sell_commission($cart->price);
                    $order_item->owner_balance = $cart->price - admin_sell_commission($cart->price);
                    $order_item->sell_commission = get_option('sell_commission');
                } else {
                    $order_item->owner_balance = $cart->price;
                }
                $order_item->type = 3; //bundle course
                $order_item->save();

                /*
                 * All bundle course added but not calculate balance, commission etc
                 */
                foreach ($cart->bundle_course_ids ?? [] as $bundle_course_id) {
                    /*
                    need to add bundle course in order list
                    Old paid course check with bundle course
                    */

                    $paidOrderIds = Order::where('user_id', auth()->id())->where('payment_status', 'paid')->pluck('id')->toArray();
                    $freeOrderIds = Order::where('user_id', auth()->id())->where('payment_status', 'free')->pluck('id')->toArray();
                    $orderIds = array_merge($paidOrderIds, $freeOrderIds);
                    $orderCourseIds = Order_item::whereIn('order_id', $orderIds)->pluck('course_id')->toArray();

                    if (in_array($bundle_course_id, $orderCourseIds) == false){
                        $order_item = new Order_item();
                        $order_item->order_id = $order->id;
                        $order_item->user_id = Auth::user()->id;
                        $order_item->bundle_id = $cart->bundle_id;
                        $order_item->course_id = $bundle_course_id;
                        $order_item->owner_user_id = $cart->bundle->user_id;
                        $order_item->type = 3; //bundle course
                        $order_item->save();
                    }
                }

            } elseif ($cart->consultation_slot_id) {
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = Auth::id();
                $order_item->owner_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                $order_item->consultation_slot_id = $cart->consultation_slot_id;
                $order_item->consultation_date = $cart->consultation_date;
                $order_item->unit_price = $cart->price;
                if (get_option('sell_commission')) {
                    $order_item->admin_commission = admin_sell_commission($cart->price);
                    $order_item->owner_balance = $cart->price - admin_sell_commission($cart->price);
                    $order_item->sell_commission = get_option('sell_commission');
                } else {
                    $order_item->owner_balance = $cart->price;
                }
                $order_item->type = 4;
                $order_item->save();

                //Start:: Need to add Booking History
                $booking = new BookingHistory();
                $booking->order_id = $order->id;
                $booking->order_item_id = $order_item->id;
                $booking->instructor_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                $booking->student_user_id = Auth::id();
                $booking->consultation_slot_id = $cart->consultation_slot_id;
                $booking->date = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->date : null;
                $booking->day = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->day : null;
                $booking->time = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->time : null;
                $booking->duration = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->duration : null;
                $booking->type = $cart->consultation_available_type;
                $booking->status = 0; //Pending
                $booking->save();

                //End:: Add Booking History
            }

        }
        CartManagement::whereUserId(@Auth::user()->id)->delete();
        return $order;
    }
}
