<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use Auth;
use Carbon\Carbon;
use DB;
use YallaTalk\Models\Refund;
use Illuminate\Http\Request;
use YallaTalk\Models\Transaction;
use YallaTalk\Models\User;
use UserHelper;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Customer;

class PaymentController extends Controller
{
    // stripe config
    protected $api_key;
    protected $api_secret;

    public function __construct()
    {
        // stripe congiurations
        $this->api_key = config('services.stripe.key');
        $this->api_secret = config('services.stripe.secret');
    }

    /**
     * function to charge the payment
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function charge(Request $request)
    {
        \Stripe\Stripe::setApiKey($this->api_secret);
        $cents = $request->amount*100;
        $user = Auth::user();
        // Charge the user's card:
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => $cents,
                "currency" => "USD",
                "description" => __('messages.create_new_charge_description'),
                "customer" => $user->stripe_id,
            ));

            $user = User::findOrFail($user->id);
            $user->charge_id = $charge->id;
            $user->balance = $user->balance+$request->amount;
            $user->save();
            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.sucessfully_charged_msg')
                ]);
        } catch (\Exception $e) {
            return response()
                ->json([
                   'success' => false,
                   'message' => __('messages.fail_charge_message')
                ]);
        }
    }

    /**
     * function to save Customer
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCustomer(Request $request)
    {
        $user = Auth::user();
        
        $token = $request->stripe_token;

        Stripe::setApiKey($this->api_secret);
        
        try {
            $token = Token::Create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc
                ]
            ]);
        
            $customer = Customer::create(array(
                "email" => $user->email,
                "source" => $token,
                "description" => $user->first_name." ".$user->last_name
            ));
            //comput the expire date
            $time_stamp = @$customer->sources['data'][0]['exp_year']."-".@$customer->sources['data'][0]['exp_month']."-"."01"." "."00:00:00";

            $user = User::findOrFail($user->id);
            $user->stripe_id = $customer->id;
            $user->card_brand = $customer->sources['data'][0]['brand'];
            $user->card_last_four = $customer->sources['data'][0]['last4'];
            $user->trial_ends_at = $time_stamp;
            $user->save();

            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.save_customer_message')
                ]);
        } catch (\Exception $e) {
            return response()
                ->json([
                   'success' => false,
                   'message' => __('messages.fail_charge_message')
                ]);
        }
    }


    /**
     *
     * function rechrge the customer
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     **/
    public function customerPay(Request $request)
    {
        $total_to_pay = $request->amount;
        $user = User::findOrFail($request->user_id);

        $customer_id = $user->stripe_id;
        \Stripe\Stripe::setApiKey($this->api_secret);
        // Charge the user's card:
        $charge = \Stripe\Charge::create(array(
          "amount" => $total_to_pay,
          "currency" => "USD",
          "description" => __('messages.create_new_charge_description'),
          "customer" => $customer_id,
        ));
        return response()
            ->json([
               'success' => true
            ]);
    }

    /**
     * function to reguest refund by service provider / client
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestRefund(Request $request)
    {
        $user = Auth::user();

        $user_id = $user->id;
        $amount = $request->refund_amount;
        if ($amount <= 0) {
            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.validate_refund_request_message')
                ]);
        }
        $refund = new Refund;

        $refund->user_id = $user_id;
        $refund->amount = $amount;
        $refund->case = 1;
        $refund->current_balance = $user->balance;

        if ($refund->save()) {
            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.send_refund_request_message')
                ]);
        }
    }

    /**
     * function to request transfer money for the service provider
     *
     * @param  Request $request
     *
     * @return response
     */
    public function transferMoney(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $amount = $request->amount;

        if ($amount <= 0) {
            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.validate_transfer_request_message')
                ]);
        }

        $refund = new Refund;

        $refund->user_id = $user_id;
        $refund->amount = $amount;
        $refund->case = 2;
        $refund->current_balance = $user->balance;

        if ($refund->save()) {
            return response()
                ->json([
                   'success' => true,
                   'message' => __('messages.send_transfer_request_message'),
                   'refund' => $refund
                ]);
        }
    }

    /**
     * function to show the history of request money for service privider
     *
     * @param  Request $request
     *
     * @return response
     */
    public function transferMoneyHistory(Request $request)
    {
        $auth_user = Auth::user();

        $refunds = Refund::where('user_id', '=', $auth_user->id)
            ->orderBy('created_at', 'desc')
            ->where('case', '=', 2)
            ->get();
        return response()
            ->json([
               'success' => true,
               'refunds' => $refunds
            ]);
    }
}
