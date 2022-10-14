<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Traits\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PayoutController extends Controller
{
    use General;

    public function newWithdraw()
    {
        if (!Auth::user()->can('payout')) {
            abort('403');
        } // end permission checking


        $data['title'] = 'New Withdraw Request';
        $data['navFinanceParentActiveClass'] = 'mm-active';
        $data['navFinanceShowClass'] = 'mm-show';
        $data['subNavFinanceNewWithdrawActiveClass'] = 'mm-active';
        $data['withdraws'] = Withdraw::whereStatus(0)->orderBy('id', 'DESC')->paginate(20);
        return view('admin.payout.new-withdraw', $data);

    }

    public function completeWithdraw()
    {
        if (!Auth::user()->can('payout')) {
            abort('403');
        } // end permission checking


        $data['title'] = 'Complete Withdraw';
        $data['navFinanceParentActiveClass'] = 'mm-active';
        $data['navFinanceShowClass'] = 'mm-show';
        $data['subNavFinanceCompleteWithdrawActiveClass'] = 'mm-active';
        $data['withdraws'] = Withdraw::whereStatus(1)->orderBy('id', 'DESC')->paginate(20);
        return view('admin.payout.complete-withdraw', $data);
    }

    public function rejectedWithdraw()
    {
        if (!Auth::user()->can('payout')) {
            abort('403');
        } // end permission checking


        $data['title'] = 'Rejected Withdraw';
        $data['navFinanceParentActiveClass'] = 'mm-active';
        $data['navFinanceShowClass'] = 'mm-show';
        $data['subNavFinancerejectedWithdrawActiveClass'] = 'mm-active';
        $data['withdraws'] = Withdraw::whereStatus(2)->orderBy('id', 'DESC')->paginate(20);
        return view('admin.payout.rejected-withdraw', $data);
    }

    public function changeWithdrawStatus(Request $request)
    {
        if ($request->status == 1 || $request->status == 2)
        {
            $withdraw = Withdraw::whereUuid($request->uuid)->first();
            if ($withdraw)
            {
                $withdraw->status = $request->status;
                $withdraw->note = $request->note;
                $withdraw->save();

                $this->showToastrMessage('success', 'Status has been changed');
                return redirect()->back();
            } else {
                abort(404);
            }

        } else {
            abort(404);
        }

    }
}
