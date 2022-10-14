<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\CartManagement;
use App\Models\Course;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Product;
use App\Models\Wishlist;
use App\Traits\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    use General;

    public function wishlist()
    {
        $data['pageTitle'] = 'Wishlist';
        // Start:: Check course, bundle exists or not. If not exists, Delete it.
        $wishlists = Wishlist::whereUserId(Auth::user()->id)->get();
        foreach ($wishlists as $wishlist){
            if ($wishlist->course_id) {
                $course = Course::find($wishlist->course_id);
                if (!$course){
                    Wishlist::where('course_id', $wishlist->course_id)->delete();
                }
            } elseif ($wishlist->bundle_id) {
                $bundle = Bundle::find($wishlist->bundle_id);
                if (!$bundle){
                    Wishlist::where('bundle_id', $wishlist->bundle_id)->delete();
                }
            }
        }
        // End:: Check course, bundle exists or not. If not exists, Delete it.
        $data['wishlists'] = Wishlist::whereUserId(Auth::user()->id)->paginate();

        return view('frontend.student.wishlist.wishlist', $data);
    }

    public function addToWishlist(Request $request)
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
                if ($order)
                {
                    if ($order->payment_status == 'due')
                    {
                        Order_item::whereOrderId($courseOrderExits->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } else {
                        $response['msg'] = "You've already purchased the course!";
                        $response['status'] = 404;
                        return response()->json($response);
                    }
                }
            }

            $ownCourseCheck = Course::whereUserId(Auth::user()->id)->where('id', $request->course_id)->first();

            if ($ownCourseCheck) {
                $response['msg'] = "This is your course. No need to add to wishlist.";
                $response['status'] = 404;
                return response()->json($response);
            }

            $courseExits = Course::find($request->course_id);
            if (!$courseExits) {
                $response['msg'] = "Course not found!";
                $response['status'] = 404;
                return response()->json($response);
            }
            $wishListExists = WishList::whereUserId(Auth::user()->id)->where('course_id', $request->course_id)->first();
        }

        if ($request->product_id) {
            $productExits = Product::find($request->product_id);
            if (!$productExits) {
                $response['msg'] = "Product not found!";
                $response['status'] = 404;
                return response()->json($response);
            }
            $wishListExists = WishList::whereUserId(Auth::user()->id)->where('product_id', $request->product_id)->first();
        }

        if ($request->bundle_id) {
            $bundleExits = Bundle::find($request->bundle_id);
            if (!$bundleExits) {
                $response['msg'] = "Bundle not found!";
                $response['status'] = 404;
                return response()->json($response);
            }
            $wishListExists = WishList::whereUserId(Auth::user()->id)->where('bundle_id', $request->bundle_id)->first();
        }

        if ($wishListExists) {
            $response['msg'] = "Already added to wishlist!";
            $response['status'] = 409;
            return response()->json($response);
        }

        $wishlist = new Wishlist();
        $wishlist->user_id = Auth::user()->id;
        $wishlist->course_id = $request->course_id;
        $wishlist->product_id = $request->product_id;
        $wishlist->bundle_id = $request->bundle_id;
        $wishlist->save();

        $response['quantity'] = Wishlist::whereUserId(Auth::user()->id)->count();
        $response['msg'] = "Added to wishlist";
        $response['status'] = 200;

        return response()->json($response);
    }

    public function wishlistDelete($id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->delete();
        $this->showToastrMessage('success', 'Removed from wishlist!');
        return redirect()->back();
    }
}
