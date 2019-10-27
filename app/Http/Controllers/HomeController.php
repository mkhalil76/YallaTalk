<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Breadcrumbs;
use YallaTalk\Models\Call;
use YallaTalk\Models\Client;
use DB;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\User;
use YallaTalk\Models\Transaction;
use YallaTalk\Models\Packege;
use UserHelper;
use Validator;
use YallaTalk\Models\Refund;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Refund as StripeRefund;
use YallaTalk\Models\ProviderBank;
use YallaTalk\Models\Country;

class HomeController extends Controller
{
    protected $stripe_api_secret;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->stripe_api_secret = config('services.stripe.secret');
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * function to track the calls mony
     *
     * @return  view
     */
    public function trackMoney()
    {
        $calls = Call::where('room_status', '=', 'COMPLETED')->get();
        return view('payments.track-money', compact('calls'));
    }

    /**
     * function to fill money for service provider
     *
     * @param  int $provider_id
     *
     * @return  view
     */
    public function fillMoneyPage($provider_id, $call_id, $call_price)
    {
        $client = Client::findOrFail($provider_id);
        $user = User::findOrFail($client->user_id);

        return view('payments.fill-money-page')->With([
            'provider' => $client,
            'user' => $user,
            'call_id' => $call_id,
            'call_price' => $call_price
        ]);
    }

    /**
     * function to post money for the service provider
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function postFillMoneyPage(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required',
            'call_id' => 'required'
        ];

        $input = $request->only(
            'user_id',
            'amount',
            'call_id',
            'call_price'
        );

        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }
        if ($input['amount'] > $input['call_price']) {
            return redirect('admin/payments/track-money')->with([
                'error' => __('messages.validate_fill_money')
            ]);
        }

        try {
            $amount = $request->amount;
            $user_id = $request->user_id;
            $cents = 100*$amount;
            $yallatalk_account = UserHelper::getYalltalkAccount();
            $yallatalk_user = User::findOrFail($yallatalk_account->id);

            Stripe::setApiKey($this->stripe_api_secret);
            // Charge the user's card:
            $charge = Charge::create(array(
              "amount" => $cents,
              "currency" => "USD",
              "description" => config('service.stripe.description'),
              "customer" => $yallatalk_user->stripe_id,
            ));

            Transaction::where('call_id', '=', $input['call_id'])->update([
                'status' => 1
            ]);

            $yallatalk_user->balance = $yallatalk_user->balance-$amount;
            $yallatalk_user->save();

            $client_user = User::findOrFail($user_id);
            
            $client_user->balance = $client_user->balance-$amount;
            $client_user->save();
        
            return redirect('admin/payments/track-money')->with([
                'success' => __('messages.update_transaction_message')
            ]);
        } catch (\Exception $e) {
            return redirect('admin/payments/track-money')->with([
                'error' => __('messages.fail_charge_message')
            ]);
        }
    }

    /**
     * function to update transaction status
     *
     * @param  int $call_id
     *
     * @return  view
     */
    public function rejectTransaction($call_id)
    {
        $transaction = Transaction::where('call_id', '=', $call_id)
            ->update([
                'status' => 2
            ]);
        return redirect()->back()->with([
            'success' => __('messages.reject_transaction_message')
        ]);
    }

    /**
     * function to show the package index page
     *
     * @return  view
     *
     */
    public function packageIndex()
    {
        $packeges = Packege::all();
        return view('packeges.index')->with(['packeges' => $packeges]);
    }

    /**
     * function to show create packege page
     *
     * @return  view
     */
    public function createPackage()
    {
        return view('packeges.create');
    }

    /**
     * function to post create new packege
     *
     * @param  Request $request
     *
     * @return  view
     */
    public function postCreatePackege(Request $request)
    {
        $rules = [
            'name' => 'required',
            'discount' => 'required',
            'hours' => 'required',
            'price' => 'required',
            'expiry_date' => 'required'
        ];

        $input = $request->only(
            'name',
            'discount',
            'price',
            'hours',
            'expiry_date',
            'description'
        );

        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }
        $expiry_date = date("Y-m-d", strtotime($input['expiry_date']));

        $packege = new Packege;
        $packege->name = $input['name'];
        $packege->discount = $input['discount'];
        $packege->hours = $input['hours'];
        $packege->expiry_date = $expiry_date;
        $packege->price = $input['price'];
        $packege->description = $input['description'];

        if ($packege->save()) {
            return redirect('admin/packeges/index')->with([
                'success' => __('messages.save_packege_message')
            ]);
        } else {
            return redirect('admin/packeges/index')->with([
                'error' => __('messages.faild_save_packege_message')
            ]);
        }
    }

    /**
     * function to show update packege page
     *
     * @param  int $packege_id
     *
     * @return  view
     */
    public function updatePackege($packege_id)
    {
        $packege = Packege::findOrFail($packege_id);

        return view('packeges.update')->with(['packege' => $packege]);
    }

    /**
     * function to post update packege
     *
     * @param  Request $request
     *
     * @return  view
     */
    public function postUpdatePackege(Request $request)
    {
        $rules = [
            'name' => 'required',
            'discount' => 'required',
            'hours' => 'required',
            'price' => 'required',
            'expiry_date' => 'required'
        ];

        $input = $request->only(
            'name',
            'discount',
            'price',
            'hours',
            'expiry_date',
            'description',
            'packege_id'
        );

        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        $expiry_date = date("Y-m-d", strtotime($input['expiry_date']));

        $packege = Packege::findOrFail($input['packege_id']);

        $packege->name = $input['name'];
        $packege->discount = $input['discount'];
        $packege->hours = $input['hours'];
        $packege->expiry_date = $expiry_date;
        $packege->price = $input['price'];
        $packege->description = $input['description'];

        if ($packege->save()) {
            return redirect('admin/packeges/index')->with([
                'success' => __('messages.save_update_packege_message')
            ]);
        } else {
            return redirect('admin/packeges/index')->with([
                'error' => __('messages.faild_save_packege_message')
            ]);
        }
    }

    /**
     * function to delete packege
     *
     * @param  int $packege_id
     *
     * @return  view
     *
     */
    public function deletePackege($packege_id)
    {
        $packege = Packege::findOrFail($packege_id);
        if ($packege->delete()) {
            return redirect('admin/packeges/index')->with([
                'success' => __('messages.delete_packege_message')
            ]);
        } else {
            return redirect('admin/packeges/index')->with([
                'error' => __('messages.faild_save_packege_message')
            ]);
        }
    }

    /**
     * function to show the refund functionality page
     *
     * @return  view
     */
    public function refund()
    {
        $refunds = Refund::where('case', '=', 1)->orderBy('created_at', 'DESC')->get();
        return view('payments.refund', compact('refunds'));
    }

    /**
     * function to accept refund
     *
     * @param  int $refund_id
     *
     * @return view
     */
    public function acceptRefund($refund_id, $type = 1)
    {
        $refund = Refund::findOrFail($refund_id);

        $user = User::findOrFail($refund->user_id);
        $cent = $refund->amount*100;

        try {
            Stripe::setApiKey($this->stripe_api_secret);
            // Charge the user's card:
            $charge = StripeRefund::create(array(
                "amount" => $cent,
                "charge" => $user->charge_id,
            ));
            $yallatalk_user = UserHelper::getYalltalkAccount();
            UserHelper::updateRefund($user->id, $refund->amount);
            UserHelper::updateRefund($yallatalk_user->id, $refund->amount);
            $refund->status = 1;
            $refund->save();
        } catch (\Exception $e) {
            return redirect('admin/payments/refund')
                ->with([
                    'success' => __('messages.fail_charge_message')
                ]);
        }

        if ($type == 1) {
            return redirect('admin/payments/refund');
        } else {
            return redirect('admin/payments/transfer-money');
        }
    }

    /**
     * function to reject refund
     *
     * @param  int $refund_id
     *
     * @return view
     */
    public function rejectRefund($refund_id, $type = 1)
    {
        $refund = Refund::findOrFail($refund_id);

        $refund->status = 2;

        $refund->save();

        if ($type == 1) {
            return redirect('admin/payments/refund');
        } else {
            return redirect('admin/payments/transfer-money');
        }
    }

    /**
     * function to show transfer money page
     *
     * @return view
     *
     */
    public function transferMoney()
    {
        $refunds = Refund::where('case', '=', 2)->orderBy('created_at', 'DESC')->get();
        return view('payments.transfer-money', compact('refunds'));
    }

    /**
     * function to charge the customer
     *
     * @param int $refund_id
     *
     * @return  view
     */
    public function acceptTransfer($refund_id)
    {
        $refund = Refund::findOrFail($refund_id);

        $user = User::findOrFail($refund->user_id);
        $provider_id = UserHelper::getServiceProviderID($refund->user_id);
        $has_bank_info = ProviderBank::where('service_provider_id', '=', $provider_id)->first();
        if (empty($has_bank_info)) {
            return redirect('admin/payments/transfer-money')
                ->with([
                    'error' => __('messages.fail_charge_message')
                ]);
        }
        $cent = $refund->amount*100;
        $refund->status = 1;
        $refund->save();
        $yallatalk_user = UserHelper::getYalltalkAccount();

        UserHelper::updateRefund($user->id, $refund->amount);
        UserHelper::updateRefund($yallatalk_user->id, $refund->amount);

        return redirect('admin/payments/transfer-money');
    }
}
