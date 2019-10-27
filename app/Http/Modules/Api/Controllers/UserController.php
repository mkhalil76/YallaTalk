<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use YallaTalk\Models\Appointment;
use Auth;
use Carbon\Carbon;
use YallaTalk\Models\Client;
use YallaTalk\Models\ClientLanguage;
use YallaTalk\Models\Call;
use Dropbox\Client as Dropbox;
use Illuminate\Support\Facades\Input;
use Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use YallaTalk\Models\Language;
use YallaTalk\Models\NativeLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\ServiceProviderLanguage;
use Storage;
use YallaTalk\Models\User;
use UserHelper;
use Validator;
use Dropbox\WriteMode;
use Twilio\Rest\Client as Twilio;
use Mailgun\Mailgun;
use DB;
use PushNotification;
use YallaTalk\Jobs\OnlineUser;
use Hash;
use Cache;
use JWTAuth;
use Redis;
use YallaTalk\Models\ProviderBank;
use YallaTalk\Models\Country;

class UserController extends Controller
{
    //dropbox configuration
    protected $api_key;
    protected $api_secret;
    protected $api_token;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->api_key = config('services.dropbox.API_KEY');
        $this->api_secret = config('services.dropbox.API_SECRET');
        $this->api_token = config('services.dropbox.API_TOKEN');
    }

    /**
     * function to get user profile information
     *
     * @param Integer $user_id
     *
     * @return \Illuminate\Http\JsonResponse with profile info
     *
     */
    public function user($user_id)
    {
        try {
            // get the user info by it's id
            $user_info = User::findOrFail($user_id);
            //get the user avatar
            $avatar = UserHelper::getUserAvatar($user_info->id, $user_info->user_type);
            // get the user practing languages
            $practing_language = UserHelper::getPractingLanguage($user_id, $user_info->user_type);
            // get the user status
            $user_status = UserHelper::getStatus($user_id, $user_info->user_type);
            $id = 0;
            $hour_rate = 0.0;
            $badge = UserHelper::countNotifications($user_id);
            if ($user_info->user_type == 1) {
                $id = UserHelper::getClientID($user_info->id);
            } elseif ($user_info->user_type == 2) {
                $id = UserHelper::getServiceProviderID($user_info->id);
                $provider = ServiceProvider::findOrFail($id);
                if (!empty($provider)) {
                    $provider_bank_info = ProviderBank::where('service_provider_id', '=', $provider->id)->first();
                    $hour_rate = $provider->price;
                    $provider->availability = 1;
                    $provider->save();
                    $data = [
                        'data' => [
                            'provider_id' => $provider->id,
                            'availability' => 1
                        ],
                        'event' => 'user_status'
                    ];
                    try {
                        Redis::publish('yt-channel', json_encode($data));
                    } catch (\Exception $e) {
                    }
                }
            } else {
                $id = null;
            }
            $provider_or_client = 0;

            // return the response with user_id , user info , status
            if ($user_info->user_type == 2) {
                return response()
                    ->json([
                        'user_id' => $user_id,
                        'user_info' => $user_info,
                        'invite_code' => $user_info->invitation_code,
                        'user_points' => UserHelper::getUserPoints($user_id),
                        'avatar' => $avatar,
                        'practing_language' => $practing_language,
                        'status' => $user_status,
                        'user_type' => $user_info->user_type,
                        'id' => $id,
                        'age' => $provider->getProviderAge(),
                        'birth_of_date' => $provider->birth_of_date,
                        'gender' => $provider->gender,
                        'hour_rate' => $hour_rate,
                        'badge' => $badge,
                        'service_provider_topics' => $provider->topic->toArray(),
                        'bank_info' => $provider_bank_info,
                        'language_rating' => UserHelper::getLanguageRating($provider->id),
                        'service_provider_rating' => $provider->rating,
                    ], 200);
            } elseif ($user_info->user_type == 1) {
                $client = Client::findOrFail($id);
                return response()
                    ->json([
                        'user_id' => $user_id,
                        'user_info' => $user_info,
                        'invite_code' => $user_info->invitation_code,
                        'user_points' => UserHelper::getUserPoints($user_id),
                        'avatar' => $avatar,
                        'practing_language' => $practing_language,
                        'status' => $user_status,
                        'user_type' => $user_info->user_type,
                        'gender' => $client->gender,
                        'age' => $client->getClientAge(),
                        'badge' => $badge,
                        'birth_of_date' => $client->birth_of_date,
                        'id' => $id,
                    ], 200);
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to save the user information
     *
     * @param   Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     **/
    public function profile(Request $request)
    {
        // validation rule
        $rules = [
            'mobile' => 'required|regex:/^[0-9]+$/|unique:users',
            'country' => 'required',
            'Language' => 'required',
            'Language_proficency' => 'required',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255'
        ];

        //  request inputs
        $input = $request->only(
            'mobile',
            'country',
            'gender',
            'Language',
            'Language_proficency',
            'hour_rate',
            'first_name',
            'last_name',
            'birth_of_date',
            'address1',
            'address2',
            'organization',
            'organization_url'
        );

        // get the current authenticate user id
        $user_id = Auth::user()->id;

        $user = User::firstOrNew(['id' => $user_id]);
        // check if the profile updated or not
        if ($user->is_updated == 1) {
            return response()->json(['new_user'=> 0]);
        }

        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> json_decode($error)]);
        }
        // if the orginization is set to yes set its value to true
        $organization = @$input['organization']=='yes'?true:false;
        // if the orginazation is yes and set the orginazation_url else it set to null
        $organization_url = @$input['organization']=='yes'?@$input['organization_url']:null;

        // add new info to the user
        $user->first_name = $input['first_name'];
        $user->last_name = $input['last_name'];
        $user->mobile = $input['mobile'];
        $user->country = $input['country'];
        $user->address1 = @$input['address1'];
        $user->address2 = @$input['address1'];
        $user->is_updated = 1;
        $user->save();

        //check user type 1 for client / 2 for service provider
        if ($user->user_type == 1) {
            // create new client
            $client = $user->client()->firstOrNew(['user_id' => $user->id]);
            $client->user_id = $user->id;
            $client->organization = $organization;
            $client->organization_url = $organization_url;
            $client->birth_of_date = @$input['birth_of_date'];
            $client->gender = @$input['gender'];
            $client->save();
            // create new client language
            $language = $client->clientLanguges()->firstOrNew([
                'client_id' => $client->id,
                'language_id' => $input['Language']
            ]);
            $language->client_id = $client->id;
            $language->language_id = $input['Language'];
            $language->Language_proficency = $input['Language_proficency'];
            $language->save();
        } elseif ($user->user_type == 2) {
            // create new service provider
            $service_provider = $user->serviceProvider()->firstOrNew(['user_id' => $user->id]);
            $service_provider->user_id = $user->id;
            $service_provider->birth_of_date = @$input['birth_of_date'];
            $service_provider->gender = @$input['gender'];
            if (@$input['hour_rate'] > 250 || @$input['hour_rate'] < 10) {
                return response()
                    ->json([
                        'sucess' => false,
                        'message' => __('messages.validate_hour_rate_message')
                    ]);
            } else {
                $service_provider->price = @$input['hour_rate'];
            }
            
            $service_provider->call_type = ServiceProvider::VIDEO_CALL_TYPE;
            $service_provider->availability = ServiceProvider::ONLINE_STATUS;
            $service_provider->Save();

            ServiceProviderLanguage::updateOrCreate([
                'language_id' => @$input['Language'],
                'service_provider_id' => $service_provider->id
            ]);
        }
        //return sucess if the user info successfuly updated
        return response()->json(['success'=> true]);
    }

    /**
     * function to get the user notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotification()
    {
        //get the current authenticate user id
        $user_id = Auth::user()->id;
        $badge = UserHelper::countNotifications($user_id);
        try {
            // get the user info
            $user = User::find($user_id);
            $user->is_notification_seen = 1;
            $user->save();
            DB::table('notifications')
                ->where('notifiable_id', '=', $user_id)
                ->update([
                    'is_seen' => 1
                ]);
            $notifications = $user->notifications->toArray();

            if (!empty($notifications)) {
                return response()->json([
                    'success' => true,
                    'notifications' => $notifications,
                    'user_name' => $user->first_name,
                    'badge' => $badge,
                ]);
            } else {
                return response()->json([
                   'success' => false,
                   'badge' => $badge,
                   'message' => 'There are currently no notifications'
                ]);
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no notification with this user id = '.$user_id
                ]);
        }
    }
    
    /**
     * function to add or update native language for user
     *
     * @param  int $user_id , Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function nativeLanguage($user_id, Request $request)
    {
        try {
            $user = User::findOrFail($user_id);
            $user_native_language = $user->NativeLanguage()
                ->where('language_id', '=', $request->native_language)
                ->first();
                
            if (empty($user_native_language)) {
                $user_native_language = new NativeLanguage;
                $user_native_language->user_id = $user_id;
                $user_native_language->language_id = $request->native_language;
                $user_native_language->user_type = $user->user_type;

                $user_native_language->save();
                return response()->json([
                    'success' => true,
                    'message' => 'native language updated successfuly'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'native language already exist'
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to add or update practicing language for user
     *
     * @param  int $user_id , Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function practicingLanguage($user_id, Request $request)
    {
        $rules = [
            'language_id' => 'required',
        ];
        $input = $request->only(['language_id']);

        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> $error]);
        }

        try {
            $user = User::findOrFail($user_id);
            
            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user_id);
                if ($client_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no client were found'
                    ]);
                }
                $cleint_language = ClientLanguage::where('client_id', '=', $client_id)
                    ->where('language_id', '=', $request->language_id)->first();
                
                if (empty($cleint_language)) {
                    $new_client_language = new ClientLanguage;
                    $new_client_language->language_id = $request->language_id;
                    $new_client_language->client_id = $client_id;
                    $new_client_language->Language_proficency = $request->Language_proficency;

                    $new_client_language->save();
                } else {
                    return response()->json([
                        'success'=> false,
                        'message' => 'language already exist'
                    ]);
                }
            } elseif ($user->user_type == 2) {
                $provider_id = UserHelper::getServiceProviderID($user_id);
                // check if there is no service provider found
                if ($provider_id == null) {
                    return response()->json([
                        'success'=> false,
                        'message' => 'cannot found service provider'
                    ]);
                }
                $provider_language = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
                    ->where('language_id', '=', $request->language_id)->first();

                if (empty($provider_language)) {
                    $provider_language = new ServiceProviderLanguage;
                    $provider_language->service_provider_id = $provider_id;
                    $provider_language->language_id = $request->language_id;
                    $provider_language->save();
                } else {
                    return response()->json([
                        'success'=> false,
                        'message' => 'language already exist'
                    ]);
                }
            }

            return response()->json([
                'success'=> true,
                'message' => 'practicing language updated successfuly'
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to change profile avatar
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileAvatar(Request $request)
    {
        $input = $request->only(['avatar']);
        $validator = Validator::make($input, [
            'avatar' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'error' => $error
            ]);
        }
        $avatar = $request->avatar;
        $data = explode(',', $avatar);
        $pos  = strpos($avatar, ';');
        $type = explode(':', substr($request->avatar, 0, $pos))[1];
        $type = explode('/', $type);

        $image = Image::make($avatar);
        $file_name = uniqid();
        $path = public_path()."/uploads/photos";
        $image->save($path."/".$file_name.".".$type[1]);
        $user_id = Auth::user()->id;
        $full_path = asset('/uploads/photos')."/".$file_name.".".$type[1];
        try {
            // get the user info
            $user = User::find($user_id);
            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user_id);
                
                if ($client_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no client were found'
                    ]);
                }
                $client = Client::findOrFail($client_id);
                $client->image = $full_path;
                $client->save();

                $avatar = UserHelper::getUserAvatar($user_id, 1);
                
                return response()->json([
                    'success'=> true,
                    'message' => 'avatar updated successfuly',
                    'avatar' => $full_path
                ]);
            } elseif ($user->user_type == 2) {
                $provider_id = UserHelper::getServiceProviderID($user_id);

                if ($provider_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no service provider were found'
                    ]);
                }
                
                $service_provider = serviceProvider::findOrFail($provider_id);
                $service_provider->image = $full_path;
                $service_provider->save();

                $avatar = UserHelper::getUserAvatar($user_id, 2);

                return response()->json([
                    'success'=> true,
                    'message' => 'avatar updated successfuly',
                    'avatar' => $full_path
                ]);
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to update user status
     *
     * @param  Request $request status 1 for online / 2 for offline
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($user_id, Request $request)
    {
        try {
            $user = User::findOrFail($user_id);

            $input = $request->only(['status']);

            $validator = Validator::make($input, [
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                $error = $validator->messages()->toJson();
                return response()->json([
                    'success'=> false,
                    'error'=> $error
                ]);
            }
            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user_id);
                if ($client_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no client were found'
                    ]);
                }
                $client = Client::findOrFail($client_id);
                $client->availability = $input['status'];
                $client->save();
            } elseif ($user->user_type == 2) {
                $provider_id = UserHelper::getServiceProviderID($user_id);
                if ($provider_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no service provider were found'
                    ]);
                }
                $service_provider = serviceProvider::findOrFail($provider_id);
                $service_provider->availability = $input['status'];
                $service_provider->save();
            }

            return response()->json([
                'success'=> true,
                'message' => 'status updated successfuly'
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to show Rating for user
     *
     * @param  int $user_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRating($user_id, Request $request)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user_id);
            } elseif ($user->user_type == 2) {
                $provider_id = UserHelper::getServiceProviderID($user_id);

                if ($provider_id == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'there is no service provider were found'
                    ]);
                }
                
                $rates = serviceProvider::findOrFail($provider_id)->rate()->get();
                $rating = [];
                $i = 1;
                if (!empty($rates)) {
                    foreach ($rates as $rate) {
                        $i++;
                        @$rating['learning_rate'] += $rate->learning_rate;
                        @$rating['teaching_rate'] += $rate->teaching_rate;
                        @$rating['good_communication_skills'] += $rate->good_communication_skills;
                        @$rating['good_teaching_skills'] += $rate->good_teaching_skills;
                        @$rating['intersting_conserviation'] += $rate->intersting_conserviation;
                        @$rating['correcting_my_language'] += $rate->correcting_my_language;
                    }

                    $rating['learning_rate'] = round($rating['learning_rate']/$i);
                    $rating['teaching_rate'] = round($rating['teaching_rate']/$i);
                    $rating['good_communication_skills'] = round($rating['good_communication_skills']/$i);
                    $rating['good_teaching_skills'] = round($rating['good_teaching_skills']/$i);
                    $rating['intersting_conserviation'] = round($rating['intersting_conserviation']/$i);
                    $rating['correcting_my_language'] = round($rating['correcting_my_language']/$i);

                    $total_rating = round(array_sum($rating)/6);
                }
                return response()->json([
                    'success'=> true,
                    'rating' => $rating,
                    'total_rating' => $total_rating
                ]);
            }

            return response()->json([
                'success'=> false,
                'rating' => [],
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to mark notification as reed
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function markAsRead(Request $request)
    {
        $notification_id = $request->notification_id;
        $user = Auth::user();

        $user->Notifications->where('id', $request->notification_id)->markAsRead();

        return response()
            ->json([
                'sucess' => 'the notification mark as read'
            ]);
    }

    /**
     * function to get the caller info
     *
     * @param  int $user_id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getCallerInfo($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $image = "";
            $client_id = 0;
            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user_id);
                $client_info = Client::findOrFail($client_id);
            
                if (empty($client_info)) {
                    $image = "";
                } else {
                    $image = $client_info->image;
                }
            } elseif ($user->user_type == 2) {
                $service_provider_id = UserHelper::getServiceProviderID($user_id);
                $provider_info = ServiceProvider::findOrFail($service_provider_id);
                if (empty($provider_info)) {
                    $image = "";
                } else {
                    $image = $provider_info->image;
                }
            }
            return response()
                ->json([
                    'sucess' => true,
                    'caller_name' => UserHelper::getClientName($client_id),
                    'image' => $image
                ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no user with this id = '.$user_id
                ]);
        }
    }

    /**
     * function to get profile avatar
     *
     * @param  Request $request
     *
     * @return  Illuminate\Http\JsonResponse
     *
     */
    public function getProfileAvatar(Request $request)
    {
        $path = asset('/uploads').'/'.$request->filename;
        return Response::download($path);
    }

    /**
     * function to get the users next calls
     *
     * @param  int $user_id
     *
     * @return  Illuminate\Http\JsonResponse
     */
    public function getNextCalls($user_id)
    {
        $user = User::findOrFail($user_id);

        if ($user->user_type == 1) {
            $client = Client::where('user_id', '=', $user->id)->first();

            $date = Date("Y-m-d h:i:s");

            $next_appotments = Appointment::where('client_id', '=', $client->id)
                ->where('status', '=', 'APPROVED')
                ->where('start_time', '>', $date)
                ->get();
            
            return response()
                ->json([
                    'sucess' => true,
                    'appointments' => $next_appotments
                ]);
        } elseif ($user->user_type == 2) {
            $provider = ServiceProvider::where('user_id', '=', $user->id)->first();

            $date = Date("Y-m-d h:i:s");

            $next_appotments = Appointment::where('service_provider_id', '=', $provider->id)
                ->where('status', '=', 'APPROVED')
                ->where('start_time', '>', $date)
                ->get();
            
            return response()
                ->json([
                    'sucess' => true,
                    'appointments' => $next_appotments
                ]);
        }
    }

    /**
     * function to validate user mobile number if the mobile number is exist or not
     *
     * @param  string $mobile_number
     *
     * @return  Illuminate\Http\JsonResponse
     */
    public function validateMobileNumber($mobile_number)
    {
        $user = User::where('mobile', '=', $mobile_number)->first();

        if (!empty($user)) {
            return response()->json([
                'is_exist' => 1
            ]);
        } else {
            return response()->json([
                'is_exist' => 0
            ]);
        }
    }

    /**
     * function to get user calender
     *
     * @param  int $user_id
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function getMyCalendar($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($user->user_type == 1) {
                $client = Client::where('user_id', '=', $user->id)->first();
                if (!empty($client)) {
                    $appointments = Appointment::where('client_id', '=', $client->id)
                        ->where('status', '=', 'APPROVED')
                        ->get();
            
                    return response()
                        ->json([
                            'sucess' => true,
                            'appointments' => $appointments
                        ]);
                } else {
                    return response()
                        ->json([
                            'sucess' => false,
                            'appointments' => __('messages.no_user_found_message')
                        ]);
                }
            } elseif ($user->user_type == 2) {
                $provider = ServiceProvider::where('user_id', '=', $user->id)->first();
                if (!empty($provider)) {
                    $appointments = Appointment::where('service_provider_id', '=', $provider->id)
                        ->where('status', '=', 'APPROVED')
                        ->get();
            
                    return response()
                    ->json([
                        'sucess' => true,
                        'appointments' => $appointments
                    ]);
                } else {
                    return response()
                        ->json([
                            'sucess' => false,
                            'appointments' => __('messages.no_user_found_message')
                        ]);
                }
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => __('messages.no_user_found_message')
                ]);
        }
    }

    /**
     * function to update to update the client practing language
     *
     * @param  Request $request
     *
     * @return  reponse
     */
    public function updatePractingLanguage(Request $request)
    {
        $user = Auth::user();
        $client_id = UserHelper::getClientID($user->id);
        if ($client_id == null) {
            return response()
                ->json([
                    'error' => __('messages.no_user_found_message')
                ]);
        }
        $practing_languages = json_decode($request->practing_language);

        $clinet_languages = ClientLanguage::where('client_id', '=', $client_id)->get();

        foreach ($practing_languages as $practing_language) {
            $languages = ClientLanguage::updateOrCreate(
                ['client_id' => $client_id, 'language_id' => $practing_language]
            );
        }

        return response()
            ->json([
                'sucess' => true,
                'message' => __('messages.update_practing_language_message')
            ]);
    }

    /**
     * function to add client practing language
     *
     * @param  Request $request
     *
     * @return response
     */
    public function addPractingLanguage(Request $request)
    {
        $user = Auth::user();
        $language_id = $request->language_id;

        $client_id = UserHelper::getClientID($user->id);

        $clinet_languages = ClientLanguage::where('client_id', '=', $client_id)
            ->where('language_id', '=', $language_id)
            ->first();
        $language = Language::findOrFail($language_id);
        if (empty($clinet_languages)) {
            $new_language = new ClientLanguage;
            $new_language->client_id = $client_id;
            $new_language->language_id = $language_id;

            if ($new_language->save()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.clinet_language_added'),
                    'language' => $language
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.clinet_language_exist'),
            ]);
        }
    }

    /**
     * function to get the client practing language
     *
     * @param Request $request
     *
     * @return  response
     */
    public function getPractingLanguage(Request $request)
    {
        $user = Auth::user();
        $client_id = UserHelper::getClientID($user->id);
        if ($client_id == false) {
            return response()->json([
                'success' => false,
            ]);
        }
        $client = Client::findOrFail($client_id);

        return response()->json([
            'success' => true,
            'languages' => $client->languages->toArray(),
        ]);
    }

    /**
     * function to delete client practing language
     *
     * @param Request $request
     *
     * @return response
     *
     */
    public function deletePractingLanguage(Request $request)
    {
        $user = Auth::user();
        $client_id = UserHelper::getClientID($user->id);
        $language_id = $request->language_id;
        $exist_language = ClientLanguage::where('client_id', '=', $client_id)
            ->where('language_id', '=', $language_id)->first();
        if (empty($exist_language)) {
            return response()->json([
                'success' => true,
                'message' => __('messages.not_exist_practing_language'),
            ]);
        }
        $clinet_languages = ClientLanguage::where('client_id', '=', $client_id)
            ->where('language_id', '=', $language_id)
            ->forceDelete();
            
        return response()->json([
            'success' => true,
            'message' => __('messages.provider_language_deleted'),
        ]);
    }

    /**
     * function to get the user call history
     *
     * @param Request $request
     *
     * @return  response
     */
    public function callHistory(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        
        if ($user->user_type == 1) {
            $client_id = UserHelper::getClientID($user->id);
            $calls = Call::where('room_status', '=', Call::ROOM_COMPLETED_STATUS)
                ->where('client_id', '=', $client_id)
                ->orderBy('created_at', 'DESC')
                ->get();

            $user_name = [];
            $call_duration = [];
            $call_price = [];
            $i = 1;
            $array_result = [];
            foreach ($calls as $call) {
                $array_result[$i]['user_name'] = UserHelper::getServiceProviderName($call->service_provider_id);
                $array_result[$i]['duration'] = UserHelper::formatCallDuration($call);
                $array_result[$i]['call_price'] = round(UserHelper::getCallPrice($call->getCallDuration(), $call->getSecondsDuration(), $call->service_provider_id), 2);
                $array_result[$i]['time'] = date("Y-m-d", strtotime($call->created_at));
                $array_result[$i]['balance'] = UserHelper::getCallBalance(1, $call->id);
                $array_result[$i]['start_time'] = date("H:i:s", strtotime($call->start_at));
                $array_result[$i]['end_time'] = date("H:i:s", strtotime($call->end_at));
                if ($call->topic_id == null || $call->topic_id == 0) {
                    $array_result[$i]['topic_name'] = 'General discussion';
                } else {
                    $array_result[$i]['topic_name'] = UserHelper::getTopicName($call->topic_id);
                }
                $i++;
            }
            return response()->json([
                'success' => true,
                'calls' => array_values($array_result),

            ]);
        } elseif ($user->user_type == 2) {
            $service_provider_id = UserHelper::getServiceProviderID($user->id);
            $calls = Call::where('room_status', '=', Call::ROOM_COMPLETED_STATUS)
                ->where('service_provider_id', '=', $service_provider_id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $user_name = [];
            $call_duration = [];
            $call_price = [];
            $i = 1;
            $array_result = [];
            foreach ($calls as $call) {
                $array_result[$i]['user_name'] = UserHelper::getClientName($call->client_id);
                $array_result[$i]['duration'] = UserHelper::formatCallDuration($call);
                $array_result[$i]['call_price'] = round(UserHelper::getCallPrice($call->getCallDuration(), $call->getSecondsDuration(), $call->service_provider_id), 2);
                $array_result[$i]['time'] = date("Y-m-d", strtotime($call->created_at));
                $array_result[$i]['balance'] = UserHelper::getCallBalance(2, $call->id);
                $array_result[$i]['start_time'] = date("H:i:s", strtotime($call->start_at));
                $array_result[$i]['end_time'] = date("H:i:s", strtotime($call->end_at));
                if ($call->topic_id == null || $call->topic_id == 0) {
                    $array_result[$i]['topic_name'] = 'General discussion';
                } else {
                    $array_result[$i]['topic_name'] = UserHelper::getTopicName($call->topic_id);
                }
                $i++;
            }
            return response()->json([
                'success' => true,
                'calls' => array_values($array_result),
            ]);
        }
    }

    /**
     * function to update user device token
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function updateDeviceToken(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $device_token = $request->device_token;
        if (!empty($device_token)) {
            $user = User::findOrFail($user_id);
            $user->device_token = $device_token;
            $user->save();
        }
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * function to update the user name
     *
     * @param  Request $request
     *
     * @return  response
     *
     */
    public function updateUserName(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            $user = User::findOrFail($user_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => __('messages.update_name_message'),
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => __('messages.no_user_found_message')
                ]);
        }
    }

    /**
     * function to update client status
     *
     * @param Request $request
     *
     * @return  response
     */
    public function updateClientStatus(Request $request)
    {
        $user = Auth::user();
        $client_id = UserHelper::getClientID($user->id);
        try {
            $client = Client::findOrFail($client_id);
            $status = $request->status;
            $client->availability = $status;
            $client->save();
            return response()
                ->json([
                    'sucess' => true,
                    'status' => $request->status,
                    'message' => __('messages.update-status-message')
                ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'message' => __('messages.no_user_found_message')
                ]);
        }
    }

    /**
     * function to update user inforamtoins
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function postUpdateUserInfo(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            $user = User::findOrFail($user_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->country = $request->country;
            $user->address1 = $request->address1;
            $user->address2 = $request->address2;
            $user->mobile = $request->mobile;
            $user->save();
            
            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientID($user->id);
                $client = Client::findOrFail($client_id);
                $client->gender = $request->gender;
                $client->birth_of_date = $request->birth_of_date;
                $client->save();
            } elseif ($user->user_type == 2) {
                $service_provider_id = UserHelper::getServiceProviderID($user->id);
                $provider = ServiceProvider::findOrFail($service_provider_id);
                $provider->gender = $request->gender;
                $provider->birth_of_date = $request->birth_of_date;
                $provider->save();
            }

            return response()
                ->json([
                    'sucess' => true,
                    'message' => __('messages.update-profile-message')
                ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => __('messages.no_user_found_message')
                ]);
        }
    }

    /**
     * function to get the next calls with details
     *
     * @param Request $request
     *
     * @return  response
     */
    public function nextCallsWithDetails(Request $request)
    {
        $user_id = Auth::user()->id;
        $user_time_zone = UserHelper::getTimeZone($user_id);
        $currnet_time = Carbon::now($user_time_zone);
        $user = User::findOrFail($user_id);
        $array_result = [];
        $counter = 1;
        if ($user->user_type == 1) {
            $client_id = UserHelper::getClientID($user->id);
            $appointments = Appointment::where('client_id', '=', $client_id)
                ->where('start_time', '>', $currnet_time)
                ->get();
            foreach ($appointments as $appointment) {
                if ($appointment->topic_id != null) {
                    $array_result[$counter] = "your next call with ".UserHelper::getServiceProviderName($appointment->service_provider_id)." practing ".UserHelper::getLanguageName($appointment->praticing_language)." proficiancy ".UserHelper::getProviderRateing($appointment->service_provider_id)." about ".UserHelper::getTopicName($appointment->topic_id);
                } else {
                    $array_result[$counter] = "your next call with ".UserHelper::getServiceProviderName($appointment->service_provider_id)." practing ".UserHelper::getLanguageName($appointment->praticing_language)." proficiancy ".UserHelper::getProviderRateing($appointment->service_provider_id)." about General discussion";
                }
                $counter++;
            }
        } elseif ($user->user_type == 2) {
            $provider_id = UserHelper::getServiceProviderID($user->id);
            $appointments = Appointment::where('service_provider_id', '=', $provider_id)
                ->where('start_time', '>', $currnet_time)
                ->get();
            foreach ($appointments as $appointment) {
                if ($appointment->topic_id != null) {
                    $array_result[$counter] = "your next call with ".UserHelper::getClientName($appointment->client_id)." practing ".UserHelper::getLanguageName($appointment->praticing_language)." proficiancy ".UserHelper::getProviderRateing($appointment->service_provider_id)." about ".UserHelper::getTopicName($appointment->topic_id);
                } else {
                    $array_result[$counter] = "your next call with ".UserHelper::getClientName($appointment->client_id)." practing ".UserHelper::getLanguageName($appointment->praticing_language)." proficiancy ".UserHelper::getProviderRateing($appointment->service_provider_id)." about General discussion";
                }
                $counter++;
            }
        }
        return response()
            ->json([
                'sucess' => true,
                'next_calls' => array_values($array_result)
            ]);
    }

    /**
     * function to count number of unread user notifications
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function countUnreadNotifications(Request $request)
    {
        $user_id = Auth::user()->id;
        $number_of_notifications = UserHelper::countNotifications($user_id);
        return response()
            ->json([
                'sucess' => true,
                'number_of_notifications' => $number_of_notifications
            ]);
    }

    /**
     * function to update notification
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function updateNotification(Request $request)
    {
        $notification_id = $request->notification_id;
        $user_id = Auth::user()->id;
        $user = User::findOrFail($user_id);
        DB::table('notifications')
            ->where('id', '=', $notification_id)
            ->update(['is_added_to_calender' => 1]);

        return response()
            ->json([
                'sucess' => true,
            ]);
    }

    /**
     * function to set notifications to seen for the user
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function setNotificationAsSeen(Request $request)
    {
        $auth_user_id = Auth::user()->id;
        DB::table('notifications')
            ->where('notifiable_id', '=', $auth_user_id)
            ->update([
                'is_seen' => 1
            ]);
        return response()
            ->json([
                'sucess' => true,
            ]);
    }

    /**
     * function to change the user password
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function changePassword(Request $request)
    {
        $current_user_password = Auth::user()->password;
        $user_id = Auth::user()->id;
        $rules = [
            'current_password' => 'required',
            'password' => 'required|same:password',
            'password_confirmation' => 'required|same:password'
        ];

        //  request inputs
        $input = $request->only(
            'current_password',
            'password',
            'password_confirmation'
        );

        $validator = Validator::make($input, $rules);

        $device_token = $request->device_token;
        $platform = $request->platform;

        //check for validation
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([
                'message' => __('messages.incorrect_confirm_password_message'),
                'success' => false,
                'error' => $error
            ]);
        }

        if (Hash::check($input['current_password'], $current_user_password)) {
            $user = User::findOrFail($user_id);
            $user->password = bcrypt($input['password']);
            $user->save();

            return response()->json([
                'message' => __('messages.password_updatedt_sucessfultty'),
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => __('messages.current_password_not_correct'),
                'success' => false
            ]);
        }
    }

    /**
     * function to set user as offlien
     *
     * @param  Request $request
     */
    public function setUserOffline(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        if ($user->user_type == 2) {
            $provider_id = UserHelper::getServiceProviderID($user_id);
            try {
                $provider = ServiceProvider::findOrFail($provider_id);
                $provider->availability = 0;
                $provider->save();
                $data = [
                    'data' => [
                        'provider_id' => $provider->id,
                        'availability' => 0
                    ],
                    'event' => 'user_status'
                ];
                
                try {
                    Redis::publish('yt-channel', json_encode($data));
                } catch (\Exception $e) {
                    echo $e;
                }
            } catch (ModelNotFoundException $ex) {
                return response()
                    ->json([
                        'error' => __('messages.no_user_found_message')
                    ]);
            }
        }
    }

    /**
     * function to get list of all countries
     *
     * @return  response
     */
    public function getAllCountries()
    {
        $countries = Country::select('name','id')->get()->toArray();
        return response()
            ->json([
                'countries' => $countries
            ]);
    }

    /**
     * function to send push 
     * 
     **/
    public function test()
    {   
        $data = [
            'type' => 'call_ended',
            'user_type' => 2,
            'balance' => 0,
            'end_call_user'=> 1553,
            'is_join_room' => 1,
            'room_name' => 'test'
        ];
        $message = PushNotification::Message("End call", [
            'data' => json_encode($data),
            'custom' => array('data' => json_encode($data))
        ]);
        PushNotification::app('YALLATALK_IOS')
                ->to("2920f13971aade231a12d6869b90288330459f6a39082e9de57f7a8ba6f401f5")
                ->send($message);
    }
}
