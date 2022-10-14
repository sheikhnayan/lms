<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Order_item;
use App\Models\Withdraw;
use App\Traits\General;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class FinanceController extends Controller
{
    use General, SendNotification;

    public function analysisIndex()
    {
        $data['navFinanceActiveClass'] = 'has-open';
        $data['subNavAnalysisActiveClass'] = 'active';
        $data['title'] = 'Analysis';

        $data['total_courses'] = Course::whereUserId(auth()->user()->id)->count();

        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::whereStatus(2)->where('send_back_money_status', 1)->whereHas('order', function ($q){
            $q->where('payment_status', 'paid');
        })->pluck('order_item_id')->toArray();
        $orderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
        $cancel_consultation_money = $orderItems->sum('admin_commission') + $orderItems->sum('owner_balance');
        //Start::  Cancel Consultation Money Calculation

        $orderBundleItemsCount = Order_item::where('owner_user_id', Auth::id())->whereNotNull('bundle_id')->whereNull('course_id')
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })->count();

        $orderItems = Order_item::where('owner_user_id', Auth::id())
            ->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        });

        $data['total_earning'] = $orderItems->sum('owner_balance') - $cancel_consultation_money;
        $data['total_enroll'] = $orderItems->count('id') - $orderBundleItemsCount;

        $data['total_withdraw_amount'] = Withdraw::whereUserId(auth()->user()->id)->completed()->sum('amount');
        $data['total_pending_withdraw_amount'] = Withdraw::whereUserId(auth()->user()->id)->pending()->sum('amount');

        //Start:: Sell statistics
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
        //End:: Sell statistics

        return view('instructor.finance.analysis-index')->with($data);
    }

    public function withdrawIndex()
    {
        $data['title'] = 'Withdraw History';
        $data['navFinanceActiveClass'] = 'has-open';
        $data['subNavWithdrawActiveClass'] = 'active';
        $data['withdraws'] = Withdraw::whereUserId(auth()->user()->id)->paginate(20);
        return view('instructor.finance.withdraw-history-index')->with($data);
    }

    public function storeWithdraw(Request $request)
    {
        if ($request->amount > instructor_available_balance())
        {
            $this->showToastrMessage('warning', 'Insufficient balance');
            return redirect()->back();
        } else {

            $withdrow = new Withdraw();
            $withdrow->transection_id = rand(1000000, 9999999);;
            $withdrow->amount = $request->amount;
            $withdrow->payment_method = $request->payment_method;
            $withdrow->save();

            $text = "New Withdraw Request Received";
            $taget_url = route('finance.new-withdraw');
            $this->send($text, 1, $taget_url, null);

            $this->showToastrMessage('warming', 'Withdraw request has been saved');
            return redirect()->back();

        }

    }

    public function downloadReceipt($uuid)
    {
        $withdraw = Withdraw::whereUuid($uuid)->first();

        $invoice_name = 'receipt-' . $withdraw->transection_id. '.pdf';
        // make sure email invoice is checked.
        $customPaper = array(0, 0, 612, 792);
        $pdf = PDF::loadView('instructor.finance.receipt-pdf', ['withdraw' => $withdraw])->setPaper($customPaper, 'portrait');
        $pdf->save(public_path() . '/uploads/receipt/' . $invoice_name);
       // return $pdf->stream($invoice_name);
        return $pdf->download($invoice_name);
    }
}
