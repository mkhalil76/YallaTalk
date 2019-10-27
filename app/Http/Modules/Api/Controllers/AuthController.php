<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use YallaTalk\Http\Modules\Api\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use YallaTalk\Models\User;
use YallaTalk\Models\NativeLanguage;
use Auth;
use Socialite;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\Invite;
use UserHelper;
use YallaTalk\Models\UserProviders;
use YallaTalk\Notifications\ClientNotification;
use Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use YallaTalk\Models\Client;
use YallaTalk\Models\ServiceProviderLanguage;
use YallaTalk\Models\ProviderBank;

class AuthController extends Controller
{
    /** API Login, on success return JWT Auth token
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // validation rule
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        // grab input from the request
        $input = $request->only('email', 'password');

        $validator = Validator::make($input, $rules);

        $device_token = $request->device_token;
        $platform = $request->platform;

        //check for validation
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([
                'message' => __('messages.incorrect_email_password_message'),
                'success' => false,
                'error' => $error
            ]);
        }

        // check if the user account is acctive or not
        $status = UserHelper::checkForActiveAccount($input);
        if ($status['success'] == false) {
            // return response with Fail message
            return response()->json([
               'sucess' => $status['success'],
               'message' => $status['message'],
            ]);
        }
        try {
            // attempt to verify the input and create a token for the user
            if (! $token = JWTAuth::attempt($input)) {
                return response()->json(['error' => 'email or passwor are invalid'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could not create token'], 500);
        }

        if ($request->device_token == null) {
            $device_token = "";
        } else {
            $device_token = $device_token;
        }
        if ($request->platform == null) {
            $platform = "";
        } else {
            $platform = $platform;
        }
        UserHelper::checkDuplicateToken($device_token);
        $user = User::findOrFail(Auth::user()->id);
        $user->device_token = $device_token;
        $user->platform = $platform;
        $user->save();
        $user_id = Auth::user()->id;
        $avatar =  UserHelper::getUserAvatar($user->id, $user->user_type);
        $practing_language = UserHelper::getPractingLanguage($user_id, $user->user_type);
        $user_status = UserHelper::getStatus($user_id, $user->user_type);
        $badge = UserHelper::countNotifications($user_id);

        if ($user->user_type == 1) {
            $id = UserHelper::getClientID($user_id);
            $client = Client::where('id', '=', $id)->first();
            $age = "";
            $bod = "";
            $gender = "";
            if (!empty($client)) {
                $age = $client->getClientAge();
                $bod = $client->birth_of_date;
                $gender = $client->gender;
            }
            return response()
                ->json([
                    'sucess' => true,
                    'token' => $token,
                    'user_id' => Auth::user()->id,
                    'user_info' => $user,
                    'invite_code' => $user->invitation_code,
                    'user_points' => UserHelper::getUserPoints($user_id),
                    'avatar' => $avatar,
                    'practing_language' => $practing_language,
                    'status' => $user_status,
                    'user_type' => $user->user_type,
                    'birth_of_date' => $bod,
                    'gender' => $gender,
                    'id' => $id,
                    'age' => $age ,
                    'token'=> $token,
                    'badge' => $badge,
                    'billing_info' => Auth::guard()->user()->stripe_id == null?false:true,
                    'device_token' => UserHelper::getDeviceToken(Auth::user()->id)
                ], 200);
        } elseif ($user->user_type == 2) {
            $id = UserHelper::getServiceProviderID($user->id);
            $provider = ServiceProvider::where('id', '=', $id)->first();

            $native_languages = "";
            $hour_rate = 0;
            $provider_topics = "";
            $age = "";
            $rating = "";
            $bod = "";
            $gender = "";
            $language_rating = "";
            $provider_bank_info = "";
            if (!empty($provider)) {
                $hour_rate = $provider->price;
                $provider_topics = $provider->topic->toArray();
                $age = $provider->getProviderAge();
                $native_languages = ServiceProviderLanguage::where('service_provider_id', '=', $provider->id)
                ->get();
                $rating = $provider->rating;
                $bod = $provider->birth_of_date;
                $gender = $provider->gender;
                $language_rating = UserHelper::getLanguageRating($provider->id);
                $provider_bank_info = ProviderBank::where('service_provider_id', '=', $provider->id)->first();
            }
            return response()
                ->json([
                    'sucess' => true,
                    'user_id' => $user_id,
                    'user_info' => $user,
                    'invite_code' => $user->invitation_code,
                    'user_points' => UserHelper::getUserPoints($user_id),
                    'avatar' => $avatar,
                    'practing_language' => $practing_language,
                    'status' => $user_status,
                    'user_type' => $user->user_type,
                    'gender' => $gender,
                    'id' => $id,
                    'age' => $age,
                    'birth_of_date' => $bod,
                    'hour_rate' => $hour_rate,
                    'service_provider_topics' => $provider_topics,
                    'service_provider_rating' => $rating,
                    'token'=> $token,
                    'badge' => $badge,
                    'billing_info' => Auth::guard()->user()->stripe_id == null?false:true,
                    'device_token' => UserHelper::getDeviceToken(Auth::user()->id),
                    'language_rating' => $language_rating,
                    'bank_info' => $provider_bank_info
                ], 200);
        }
    }

    /**
     * function to handle user request for registration.
     *
     * @param  Request $request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // $new_user paramter based if the user is already a user or not
        $new_user = 1;
        // rules for parameters
        $rules = [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ];
        // inputs required by request
        $input = $request->only(
            'email',
            'password',
            'invitation_code',
            'device_token',
            'time_zone',
            'user_type'
        );

        if (!empty(@$input['invitation_code']) || @$input['invitation_code'] != null) {
            $inviter_user = User::where('invitation_code', '=', @$input['invitation_code'])->first();
            if (empty($inviter_user)) {
                return response()->json([
                    'sucess' => false,
                    'message' => __('messages.invalid_invitation_code')
                ]);
            }
        }

        if (@$input['device_token'] == null) {
            @$input['device_token'] = "";
        }
        if (@$input['time_zone'] == null) {
            @$input['time_zone'] = "";
        }

        $validator = Validator::make($input, $rules);
        // check for validation
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success' => false, 'error' => $error]);
        }

        $exist_user = User::where('email', '=', $input['email'])->first();
        //check if the email already registerd
        if (!empty($exist_user)) {
            $new_user = 0;
            // generate the token for the user
            $token = JWTAuth::fromUser($exist_user);
            // return response
            return response()->json(['new_user'=> $new_user, 'token'=> $token]);
        }
        $time_zone = @$input['time_zone'];
        if (empty($time_zone) || $time_zone == null) {
            $time_zone = "UTC";
        }
        //create new user
        $user_type = @$input['user_type'];
        $user = new User;
        $user->email = $input['email'];
        $user->provider = UserProviders::YALLATALK;
        $user->password = bcrypt($input['password']);
        $user->invitation_code = str_random(6);
        $user->device_token = $input['device_token'];
        $user->time_zone = $time_zone;
        if (!empty($user_type) || $user_type != null) {
            $user->user_type = $user_type;
        }
        $user->save();
        // check for invitation if user invited by some one create new invite record
        $this->saveInvitation(@$input['invitation_code'], $user->id);
        // generate token for the user
        $token = JWTAuth::fromUser($user);
        // return response with  token and $new_user
        // (1) if the user is new
        // (0) if the user already exist
        return response()->json([
            'new_user' => $new_user,
            'token' => $token,
            'user_info' => $user
        ]);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    /**
     * function check if there is a user account exists with those
     * parameters in the User's table.
     *
     * @param Request $request (provider,provider_user_id)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function callBack(Request $request)
    {
        //input required by request
        $input = $request->only([
            'provider',
            'provider_user_id',
            'email',
            'invitation_code',
            'token'
        ]);
        //get the user by provider and provider_user_id
        $user_account = user::where('provider', '=', $input['provider'])
            ->where('provider_user_id', '=', $input['provider_user_id'])
            ->first();

        // check if the user already exist or not
        if ($user_account) {
            // new user = 0 // the user already exist
            // new user = 1 // new user
            
            // generate JWT auth token
            $token = JWTAuth::fromUser($user_account);
            //return response exist_user based if the user exist or not and
            // the  generated token
            return response()->json([
                'token'=> $token,
                'user_info' => $user_account,
                'status' => 'ok',
                'exist_user' => 1
            ]);
        } else {
            $user = User::where('email', '=', $input['email'])->first();
            // if the user with same email is not exist create new user
            if (!$user) {
                $user = new User;
                $user->email = $input['email'];
                $user->provider_user_id = $input['provider_user_id'];
                $user->provider = $input['provider'];
                $user->password = bcrypt(rand(1, 10000));
               
                $user->save();

                $this->saveInvitation(@$input['invitation_code']);
            }
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'exist_user' => 0,
                'token'=> $token,
                'user_info' => $user,
                'status' => 'ok'
            ]);
        }
    }
    /**
     * function to check for invitation if user invited by some one
     * create new invite record
     *
     * @param  String $invitaion_code, int $new_user
     *
     * @return void
     *
     */
    public function saveInvitation($invitaion_code, $new_user)
    {
        if (!empty($invitaion_code)) {
            // get the user id who send the invitation code
            $inviter_id = UserHelper::getUserIdByInvitaionCode($invitaion_code);
            if ($inviter_id == 0) {
                return response()->json([
                    'sucess' => false,
                    'error'=> 'clouldnt fin the user with invite code '.$invitaion_code
                ]);
            }
            try {
                // add new invitation record user_id is the user how make invitation
                // invited_id new user how accept the invitation
                $user = User::findOrFail($inviter_id);
                $invite = new Invite;
                $invite->email = $user->email;
                $invite->invited_id = $new_user;
                $invite->user_id = $inviter_id;
                $invite->save();

                $details = [
                    'message' => 'Congratulations! your friend accept your 
                        invitation and joind YallaTalk, you earned 50 points',
                    'type' => 1,
                    'user_id' => $new_user,
                    'total_points' => UserHelper::getUserPoints($inviter_id)
                ];
                $type = 1;
                $user->is_notification_seen = 0;
                $user->save();
                //send notification
                UserHelper::sendPushNotification(
                    $user->device_token,
                    $details['message'],
                    "invitation accept",
                    $user->platform,
                    $type
                );
                notification::send($user, new ClientNotification($details));
            } catch (ModelNotFoundException $ex) {
                return response()
                    ->json([
                        'error' => 'there is no user with this id = '.$inviter_id
                    ]);
            }
        }
    }

    /**
     * function to check if the invitation code is valid or not
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function invitationCodeValidation(Request $request)
    {
        $code = $request->code;

        $inviter_user = User::where('invitation_code', '=', $code)->first();
        if (empty($inviter_user)) {
            return response()->json([
                'sucess' => false,
                'message' => __('messages.invalid_invitation_code')
            ]);
        } else {
            return response()->json([
                'sucess' => true,
            ]);
        }
    }
}
