<?php

namespace YallaTalk\Http\Modules\Api\Controllers\Auth;

use YallaTalk\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Validator;
use YallaTalk\Models\User;
use YallaTalk\Mail\PasswordResetEmail;
use Twilio;
use UserHelper;
use YallaTalk\Models\UserProviders;
use Twilio\Rest\Client;

class ForgotPasswordController extends Controller
{
    //twilio api config
    protected $sid;
    protected $token;
    protected $phone_number;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->sid = config('twilio.twilio.connections.twilio.sid');
        $this->token = config('twilio.twilio.connections.twilio.token');
        $this->phone_number = config('twilio.twilio.connections.twilio.from');
        $this->middleware('guest');
    }

    /**
     * function to swend password reset link using email or SMS.
     *
     * @param Request $request (input)
     *
     * @return  void
     */
    public function resetPassword(Request $request)
    {
        $input = $request->only([
                'input',
                'send_type'
        ]);

        // if the user whant to reset by email type = 1
        // if the user whant to reset by msg type = 2
        if ($request->send_type == 1) {
            $rules = [
                'input' => 'required|email'
            ];

            $validator = Validator::make($input, $rules);
            //check the validation rule
            if ($validator->fails()) {
                $error = $validator->messages()->toJson();
                return response()->json(['error'=> $error]);
            }
            $user = User::where('email', $input['input'])->first();
            // check if the email aready exist in db or not.
            if (empty($user)) {
                return response()->json(['error'=> 'sorry the email is not exist in the database']);
            }
            
            $token = $this->broker()->createToken($user);
            // check if the user register using 3rd party service
            //  so return error msg to user
            if ($user->provider != UserProviders::YALLATALK) {
                return response()->json(['error'=>'user isnt registered with username & password 
                    and he should try to login with'.$user->provider]);
            }
            
            \Mail::to($user)->send(new PasswordResetEmail($user, $token));

            return response()->json(['sucess'=> 'email successfuly sent']);
        } else {
            $rules = [
                'input' => 'required|regex:/^[0-9]+$/'
            ];

            $validator = Validator::make($input, $rules);
            //check the validation rule
            if ($validator->fails()) {
                $error = $validator->messages()->toJson();
                return response()->json(['error'=> $error]);
            }
            $input['input'] = "+".$input['input'];

            $user = User::where('mobile', $input['input'])->first();
            // check if the email aready exist in db or not.
            if (empty($user)) {
                return response()->json(['error'=> 'sorry the mobile number is not exist in the database']);
            }
            if ($user->provider != UserProviders::YALLATALK) {
                return response()->json(['error' => 'user isnt registered with username & password 
                    and he should try to login with'.$user->provider]);
            }
            $token = $this->broker()->createToken($user);
           
            $message_body = "use the folowing link to rest your password\n ".
                "yallatalk://token/$token";
            //$mobile_number = $request->mobile_number;
            $mobile_number = $input['input'];

            $client = new Client($this->sid, $this->token);
            $message = $client->messages->create(
                $mobile_number,
                [
                    'from' => $this->phone_number,
                    'body' => $message_body
                ]
            );
            return response()->json(['sucess'=> 'message successfuly sent']);
        }
    }
}
