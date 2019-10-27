<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\ServiceProvider;
use Carbon\Carbon;
use YallaTalk\Models\Appointment;
use Validator;
use UserHelper;
use YallaTalk\Models\User;
use Auth;
use YallaTalk\Notifications\ClientNotification;
use YallaTalk\Models\Client;
use YallaTalk\Notifications\ServiceProviderNotification;
use Notification;
use YallaTalk\Models\NativeLanguage;
use YallaTalk\Models\Language;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use YallaTalk\Events\AccountStatus;
use YallaTalk\Models\ServiceProviderLanguage;
use YallaTalk\Models\Call;
use YallaTalk\Models\ServiceProviderRating;
use YallaTalk\Models\ProviderBank;

class ServiceProviderController extends Controller
{

    /**
     * function to get all service providers for the topic
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getAll()
    {
        $service_providers = ServiceProvider::where('account_status', '=', 1)->get();

        return response()->json(['service_provider' => $service_providers]);
    }

    /**
     * function to get all service providers for the topic
     *
     * @param  Request $request
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $status = $request->query('status');
        $name = $request->query('name');
        $topic = $request->query('topic');
        $male = $request->query('male');
        $female = $request->query('female');
        $video_call = $request->query('video_call');
        $voice_call = $request->query('voice_call');
        $gender = $request->query('gender');
        $country = $request->query('country');
        $call_type = $request->query('call_type');
        // where service provider account is active
        $service_providers = ServiceProvider::where('account_status', '=', 1);
        //check if the status(online/offline) is not empty from the request
        if ($status != null) {
            $service_providers->status($status);
        }
        //check if the name is not empty from the request
        if ($name != null) {
            $service_providers->ProviderName($name);
        }
        //check if the topic is not empty from the request
        if ($topic != null) {
            $service_providers->topic($topic);
        }
        //check if the gender is not empty from the request
        if ($gender != null) {
            $service_providers->gender($gender);
        }
        //check if the country is not empty from the request
        if ($country != null) {
            $service_providers->country($country);
        }
        //check if the country is not empty from the request
        //call_type must be 1 - (voice call) or 2 - (video call)
        if ($call_type != null) {
            $service_providers->callType($call_type);
        }
        if ($male != null) {
            $service_providers->male($female);
        }
        if ($female != null) {
            $service_providers->female($male);
        }
        if ($voice_call != null) {
            $service_providers->voiceCall($video_call);
        }
        if ($video_call != null) {
            $service_providers->videoCall($voice_call);
        }
        $service_providers = $service_providers->with('user')->get();

        return response()->json(['service_provider' => $service_providers]);
    }
    /**
     * function to get all service provider info
     *
     * @param  int $service_provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getInfo($service_provider_id)
    {
        try {
            $service_provider = ServiceProvider::findOrFail($service_provider_id);
            $native_languages = NativeLanguage::with('language')
                ->where('user_id', '=', $service_provider->user_id)->get();
            $user = User::findOrFail($service_provider->user_id);
            //get the user age
            $year_of_birth = intval($service_provider->birth_of_date);
            $current_year = intval(date("Y-m-d"));
            $age = $current_year-$year_of_birth;

            return response()->json([
                'service_provider_topics' => $service_provider->topic->toArray(),
                'service_provider_languages' => $service_provider->language->toArray(),
                'service_provider_hobbies' => json_decode($service_provider->hobbis),
                'service_provider_status' => $service_provider->availability,
                'service_provider_image' => $service_provider->image,
                'service_provider_rate' => $service_provider->rating,
                'service_provider_call_type' => $service_provider->call_type,
                'service_provider_birth_of_date' => $service_provider->birth_of_date,
                'houre_rate' => $service_provider->price,
                'age' => $age,
                'balance' => $user->balance,
                'service_provider_short_bio' => $service_provider->short_bio,
                'service_provider_gender' => $service_provider->gender,
                'native_languages' => $native_languages->toArray(),
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no server provider were found '
                ]);
        }
    }

    /**
     * function to get service provider callender date and time
     *
     * @param  int $provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviderCalender($provider_id)
    {
        $appointments = Appointment::where('service_provider_id', '=', $provider_id)
            ->where('status', '=', 'APPROVED')
            ->get();
        // check if the appointments is not empty
        if (!empty($appointments)) {
            // return the response
            return response()->json([
                'success' => true,
                'appointments' => $appointments
            ]);
        } else {
            return response()->json([
                'success' => false,
                'appointments' => __('messages.user_has_no_appointment')
            ]);
        }
        // array to store the date and time
        $data_time_array = [];
        // get all Appointments associate with service provider
        $provider_Appointments = ServiceProvider::findOrFail($provider_id)->Appointments;
        //check if the Appointment is empty or not
        if (!empty($provider_Appointments)) {
            foreach ($provider_Appointments as $Appointment) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $Appointment->start_time)->format('Y-m-d');
                $start_time = Carbon::createFromFormat('Y-m-d H:i:s', $Appointment->start_time)->format('H:i:s');
                $end_time = Carbon::createFromFormat('Y-m-d H:i:s', $Appointment->end_time)->format('H:i:s');
                $data_time_array[$date][] = [$start_time.'-'.$end_time];
            }
        }
        // response with array of date and time
        return response()->json([
            'date_time' => $data_time_array
        ]);
    }

    /**
     * function to post new entry to the callender
     *
     * @param Request $request , int $provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postProviderCalender($provider_id, Request $request)
    {
        $topic_id = $request->topic_id;
        $language_id = $request->language_id;
        $minutes_duration = $request->minutes_duration;

        try {
            //get current login user id
            $user_id = Auth::user()->id;
            $client_id = UserHelper::getClientID($user_id);
            $user = User::findOrFail($user_id);
            $provider_call_type = "";
            if (empty($request->call_type)) {
                $provider_call_type = UserHelper::getProviderCallType($provider_id);
            } else {
                $provider_call_type = $request->call_type;
            }
            
            $input = $request->only([
                'timestamp',
            ]);

            $rules = [
                'timestamp' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            //check the validation rule
            if ($validator->fails()) {
                $error = $validator->messages()->toJson();
                return response()->json([
                    'success' => false,
                    'error' => $error
                ]);
            }
            
            //check if the service provider has another booking in same time
            $provider_Appointments = Appointment::where('service_provider_id', $provider_id)
                ->where('start_time', '=', $input['timestamp'])
                ->first();
            
            if (empty($minutes_duration)) {
                // add and sub 15 minutes from the time to get end time
                $carbon = new Carbon($input['timestamp']);
                $end_time = $carbon->addMinutes(15);
                // get time after 15 minutes and befor 15 minutes
                $carbon_after = new Carbon($input['timestamp']);
                $after_time = $carbon_after->addMinutes(15);
                $carbon_befor = new Carbon($input['timestamp']);
                $befor_time = $carbon_befor->subMinutes(15);
            } else {
                $carbon = new Carbon($input['timestamp']);
                $end_time = $carbon->addMinutes($minutes_duration);
                // get time after 15 minutes and befor 15 minutes
                $carbon_after = new Carbon($input['timestamp']);
                $after_time = $carbon_after->addMinutes(15);
                $carbon_befor = new Carbon($input['timestamp']);
                $befor_time = $carbon_befor->subMinutes(15);
            }
            //check if ther is an appointment at this time
            $provider_Appointments_after_time = Appointment::where('service_provider_id', $provider_id)
                ->where('start_time', '<=', $after_time)
                ->where('start_time', '>=', $befor_time)
                ->first();

            $provider_Appointments_befor_time = Appointment::where('service_provider_id', $provider_id)
                ->where('end_time', '<=', $after_time)
                ->where('end_time', '>=', $befor_time)
                ->first();

            // check if the appointment is reserved
            if (!empty($provider_Appointments) ||
                (!empty($provider_Appointments_befor_time)) ||
                (!empty($provider_Appointments_after_time))) {
                // return false response
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, this appointment is reserved'
                ]);
            }
            // save new appointment
            $appointment = new Appointment;
            $appointment->status = Appointment::APPOINTMENT_PENDING;
            $appointment->start_time = $input['timestamp'];
            $appointment->end_time = $end_time;
            $appointment->topic_id = $topic_id;
            $appointment->praticing_language = $language_id;
            $appointment->service_provider_id = $provider_id;
            $appointment->call_type = $provider_call_type;
            $appointment->client_id = UserHelper::getClientID($user_id);
            // attemp to save new appointment
            if ($appointment->save()) {
                //send notification to service provider
                $service_provider = ServiceProvider::findOrFail($provider_id);
                $service_provider_user = User::findOrFail($service_provider->user_id);
                $user_to_notify = User::findOrFail($service_provider->user_id);

                $notification_details = [
                    'message' => $user->first_name." "."made an appointment with you on the date"." ".$input['timestamp'],
                    'client_id' => UserHelper::getClientID($user_id),
                    'type' => 2,
                    'appointment' => $appointment->id,
                    'appointment_message' => 'Your next call with '.UserHelper::getClientName($appointment->client_id).' practing '.UserHelper::getLanguageName($appointment->praticing_language).' proficiancy '.UserHelper::getProviderRateing($appointment->service_provider_id).' about '.UserHelper::getTopicName($appointment->topic_id),
                    'title' => "YallaTalk - Call with ".UserHelper::getClientName($appointment->client_id)
                ];
                $type = 2;

                $device_token = UserHelper::getDeviceToken($service_provider_user->id);

                UserHelper::sendPushNotification(
                    $device_token,
                    $notification_details['message'],
                    "new appointment",
                    $service_provider_user->platform,
                    $type
                );
                $service_provider_user->is_notification_seen = 0;
                $service_provider_user->save();
                notification::send(
                    $service_provider_user,
                    new ServiceProviderNotification($notification_details)
                );
                //return sucess response
                return response()->json([
                    'success' => true,
                    'message' => 'Your appointment has been successfully booked'
                ]);
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no server provider were found'
                ]);
        }
    }

    /**
     * function to change the states of calendar by service provider
     *
     * @param $provider_id , $request_id , Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCalender($provider_id, $request_id, Request $request)
    {
        $auth_user = Auth::user();
        $notification_type = 4;
        $input = $request->only([
            'status',
            'message',
            'notification_id'
        ]);

        $rules = [
            'status' => 'required',
        ];
        // check for the message
        if (empty($input['message'])) {
            $input['message'] = "";
        }
        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'error' => $error
            ]);
        }

        //update the Appointment status
        $appointment = Appointment::where('service_provider_id', '=', $provider_id)
            ->where('id', '=', $request_id)
            ->update([
                'status' => $request->status,
                'excuse' => $request->message
            ]);

        // check for account status
        // if the the service provider have 3 rejected appointment
        // the account will temporarily frozen
        if ($request->status == Appointment::APPOINTMENT_REJECTED) {
            $provider = ServiceProvider::findOrFail($provider_id);
            $notification_type = 3;
            $status = event(new AccountStatus($provider));

            if ($status[2]['success'] == false) {
                // return the response
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is temporarily frozen ,'
                ]);
            }
        }
        $appointment = Appointment::where('service_provider_id', '=', $provider_id)
            ->where('id', '=', $request_id)
            ->first();
            
        if (!empty($appointment)) {
            $client = $appointment->client;
            $user = $client->user;

            $provider_name = UserHelper::getServiceProviderName($provider_id);
            $details = [
                'message' => $provider_name.' has '.strtolower($appointment->status)." ".
                    'for the call on'." ".$appointment->start_time,
                'type' => $notification_type, // service provider update the appotment request
                'service_provider_id' => $provider_id,
                'comment' => $input['message'],
                'appointment' => $appointment->id,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'provider_name' => $provider_name,
                'appointment_message' => 'Your next call with '.UserHelper::getServiceProviderName($appointment->service_provider_id).' practing '.UserHelper::getLanguageName($appointment->praticing_language).' proficiancy '.UserHelper::getProviderRateing($appointment->service_provider_id).' about '.UserHelper::getTopicName($appointment->topic_id),
                'title' => "YallaTalk - Call with ".UserHelper::getServiceProviderName($appointment->service_provider_id)
            ];

            //send notification
            UserHelper::sendPushNotification(
                $user->device_token,
                $details['message'],
                "appotment update",
                $user->platform,
                "notification",
                json_encode($details)
            );
            $user->is_notification_seen = 0;
            notification::send($user, new ClientNotification($details));
            //update notification read at
            $auth_user->Notifications->where('id', $request->notification_id)->markAsRead();
            // return the response
            
            return response()->json([
                'success' => true,
                'message' => 'status successfully modified',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no appointment found',
            ]);
        }
    }
    
    /**
     *functtion to get the service provider callender
     *
     * @param $user_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCalender($user_id, Request $request)
    {
        // get the user info by it's id
        $user_id = Auth::user()->id;
        try {
            $user_info = User::findOrFail($user_id);
            //check for user type
            if ($user_info->user_type == 2) {
                $provider_id = UserHelper::getServiceProviderID($user_info->id);
                if ($provider_id == null) {
                    return response()->json([
                        'success' => false,
                        'error' => 'there is no service provider were found'
                    ]);
                }
                $appointments = Appointment::where('service_provider_id', '=', $provider_id)
                    ->get();
                // check if the appointments is not empty
                if (!empty($appointments)) {
                    // return the response
                    return response()->json([
                        'success' => true,
                        'appointments' => $appointments
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'appointments' => 'no appointments avaliable'
                    ]);
                }
            } else {
                $client_id = UserHelper::getClientID($user_info->id);
                if ($client_id == null) {
                    return response()->json([
                        'success' => false,
                        'error' => 'there is no service provider were found'
                    ]);
                }
                
                $appointments = Appointment::where('client_id', '=', $client_id)
                    ->get();
                // check if the appointments is not empty
                if (!empty($appointments)) {
                    // return the response
                    return response()->json([
                        'success' => true,
                        'appointments' => $appointments
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'appointments' => 'no appointments avaliable'
                    ]);
                }
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'sucess' => false,
                    'error' => 'there is no appointments with this user id = '.$user_id
                ]);
        }
    }

    /**
     * function to get alternative service providers
     *
     * @param  int $provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlternative($provider_id)
    {
        $service_provider = ServiceProvider::findOrFail($provider_id);
        $service_provider_languages = $service_provider->language->pluck('id')->toArray();
        $service_provider_topics = $service_provider->topic->pluck('id')->toArray();
        

        $alternative_providers = ServiceProviderLanguage::with('serviceProvider')
            ->whereIn('language_id', $service_provider_languages)
            ->where('service_provider_id', '!=', $provider_id)
            ->get();

        $response = [];
        
        foreach ($alternative_providers as $provider) {
            if (!empty($provider)) {
                $service_provider = ServiceProvider::findOrFail($provider->service_provider_id);
                $native_languages = NativeLanguage::with('language')
                ->where('user_id', '=', $service_provider->user_id)->get();
                //get the user age
                $year_of_birth = intval($service_provider->birth_of_date);
                $current_year = intval(date("Y-m-d"));
                $age = $current_year-$year_of_birth;

                $response[$provider->service_provider_id] = [
                    'service_provider_topics' => $service_provider->topic->toArray(),
                    'service_provider_languages' => $service_provider->language->toArray(),
                    'service_provider_hobbies' => json_decode($service_provider->hobbis),
                    'service_provider_status' => $service_provider->availability,
                    'service_provider_image' => utf8_encode($service_provider->image),
                    'service_provider_rate' => $service_provider->rating,
                    'service_provider_call_type' => $service_provider->call_type,
                    'service_provider_birth_of_date' => $service_provider->birth_of_date,
                    'age' => $age,
                    'service_provider_short_bio' => $service_provider->short_bio,
                    'service_provider_gender' => $service_provider->gender,
                    'native_languages' => $native_languages->toArray(),
                ];
            }
        }
        return response()
            ->json([
                'sucess' => true,
                'alternative' => $response
            ]);
    }

    /**
     * function to get service provider statistics
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            $seconds = 0;
            if ($request->user_type == 2) {
                $provider = ServiceProvider::findOrFail($request->provider_id);
                $calls = Call::where('service_provider_id', '=', $request->provider_id)
                    ->where('room_status', '=', Call::ROOM_COMPLETED_STATUS)->get();
        
                $total_calls = 0;
                $number_of_calls = 0;
                foreach ($calls as $call) {
                    $number_of_calls++;
                    $call_duration = $call->getCallDuration();
                    $total_calls += $call_duration;
                    $seconds += $call->getSecondsDuration();
                }
                $total_minutes = $total_calls%60;
                $total_hours = $total_calls - $total_minutes;
                $total_hours = $total_hours/60;
            } elseif ($request->user_type == 1) {
                $provider = Client::findOrFail($request->provider_id);
                $calls = Call::where('client_id', '=', $request->provider_id)
                    ->where('room_status', '=', Call::ROOM_COMPLETED_STATUS)->get();
                $total_calls = 0;
                $number_of_calls = 0;
                foreach ($calls as $call) {
                    $number_of_calls++;
                    $call_duration = $call->getCallDuration();
                    $total_calls += $call_duration;
                    $seconds += $call->getSecondsDuration();
                }
                $total_minutes = $total_calls%60;
                $total_hours = $total_calls - $total_minutes;
                $total_hours = $total_hours/60;
            }
            $minutes_from_seconds = (int)($seconds/60);
            return response()
                ->json([
                    'number_of_conversations' => $number_of_calls,
                    'presonal_rating' => $provider->rating,
                    'total_minutes' => $total_minutes+$minutes_from_seconds,
                    'total_hours' => $total_hours,
                    'rating' => $provider->rating,
                    'number_of_points' => UserHelper::getUserPoints($provider->user_id)
                ]);
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'sucess' => false,
                    'error' => __('messages.no_user_found_msg')
                ]);
        }
    }

    /**
     * function to update the service provider hourly rate
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHoureRate(Request $request)
    {
        $houre_rate = $request->houre_rate;
        if (UserHelper::checkAppointments($request->service_provider_id) == false) {
            return response()
                ->json([
                    'sucess' => false,
                    'message' => __('messages.have_appointment_validation')
                ]);
        }
        if ($houre_rate > 250 || $houre_rate < 10) {
            return response()
                ->json([
                    'sucess' => false,
                    'message' => __('messages.validate_hour_rate_message')
                ]);
        }
        $service_provider_id = $request->service_provider_id;

        $provider = ServiceProvider::findOrFail($service_provider_id);
        $provider->price = $houre_rate;
        $provider->save();
        return response()
            ->json([
                'sucess' => true,
                'message' => __('messages.update_hour_rate_message')
            ]);
    }

    /**
     * function to validate hour rate for service provider
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function checkHourRate(Request $request)
    {
        $rate = $request->hour_rate;
        if ($rate > 250 || $rate < 10) {
            return response()
                ->json([
                    'sucess' => false,
                    'message' => __('messages.validate_hour_rate_message')
                ]);
        }
    }
    /**
     * function to get the service providers calls summary
     *
     * @param  int $provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function providerCallSummary($provider_id)
    {
        $calls = Call::where('service_provider_id', '=', $provider_id)
            ->where('room_status', '=', 'COMPLETED')
            ->get();
        
        $total_duration = 0;
        $duration = 0;

        foreach ($calls as $call) {
            $end = Carbon::parse($call->end_at);
            $start = Carbon::parse($call->start_at);
            $duration += $end->diffInMinutes($start);
        }

        $total_minutes = $duration%60;
        $total_hours = $duration - $total_minutes;
        $total_hours = $duration/60;

        return response()->json([
            'sucess' => true,
            'total_minutes' => $total_minutes,
            'total_hours' => $total_hours,
        ]);
    }

    /**
     * function to get service provider call logs
     *
     * @param int $provider_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCallLogs($provider_id)
    {
        $calls = Call::where('service_provider_id', '=', $provider_id)->get();

        $time = [];
        $duration = [];
        $points = [];
        $client = [];
        $date = [];
        $amount = [];

        foreach ($calls as $call) {
            $service_provider = ServiceProvider::findOrFail($call->service_provider_id);
            $end = Carbon::parse($call->end_at);
            $start = Carbon::parse($call->start_at);
            $call_duration = $end->diffInMinutes($start);
            
            $time[$call->id] = $call->start_at;
            $duration[$call->id] = $call_duration;
            $client[$call->id] = $call->client_id;
            $date[$call->id] = date("Y-m-d", strtotime($call->created_at));
            $amount[$call->id] = $service_provider->price*$call_duration;
        }

        return response()->json([
            'sucess' => true,
            'time' => $time,
            'duration' => $duration,
            'client' => $client,
            'date' => $date,
            'amount' => $amount,
            'points' => $points
        ]);
    }

    /**
     * function to update service provider status (online/ offline )
     *
     * @param  int $service_provider_id, int $status
     *
     * @return  response
     */
    public function updateStatus($status)
    {
        $user = Auth::user()->id;
        $service_provider_id = UserHelper::getServiceProviderID($user);
        try {
            $service_provider = ServiceProvider::findOrFail($service_provider_id);
            $service_provider->availability = $status;
            $service_provider->save();

            return response()->json([
                'sucess' => true,
                'status' => $status,
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
     * function to update the service provider native language
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function updateNativeLanguage(Request $request)
    {
        $user = Auth::user();
        $provider_id = UserHelper::getServiceProviderID($user->id);
        $native = $request->native_language;
        $provider_language = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
            ->update([
                'language_id' => $native,
            ]);
        return response()->json([
            'sucess' => true,
            'message' => __('messages.update-provider-language-message')
        ]);
    }

    /**
     * function to add provider native language
     *
     * @param Request $request
     *
     * @return  response
     */
    public function addNativeLanguage(Request $request)
    {
        $user = Auth::user();
        $language_id = $request->language_id;

        $provider_id = UserHelper::getServiceProviderID($user->id);
        $count_languages = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
            ->count();
        if ($count_languages > 5) {
            return response()->json([
                'success' => false,
                'message' => __('messages.maximum_provider_language'),
            ]);
        }
        $provider_languages = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
            ->where('language_id', '=', $language_id)
            ->first();
        $language = Language::findOrFail($language_id);
        if (empty($provider_languages)) {
            $new_language = new ServiceProviderLanguage;
            $new_language->service_provider_id = $provider_id;
            $new_language->language_id = $language_id;

            if ($new_language->save()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.provider_language_added'),
                    'language' => $language
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.provider_language_exist'),
            ]);
        }
    }

    /**
     * function to get service provider native languages
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function getNativeLanguage(Request $request)
    {
        $user = Auth::user();
        $provider_id = UserHelper::getServiceProviderID($user->id);
        if ($provider_id == false) {
            return response()->json([
                'success' => false,
            ]);
        }
        $provider = ServiceProvider::findOrFail($provider_id);

        return response()->json([
            'success' => true,
            'languages' => $provider->language->toArray(),
        ]);
    }

    /**
     * function to delete provider native language
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function deleteNativeLanguage(Request $request)
    {
        $user = Auth::user();
        $provider_id = UserHelper::getServiceProviderID($user->id);
        $language_id = $request->language_id;
        $exist_language = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
            ->where('language_id', '=', $language_id)->first();
        if (empty($exist_language)) {
            return response()->json([
                'success' => true,
                'message' => __('messages.not_exist_native_language'),
            ]);
        }
        $clinet_languages = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)
            ->where('language_id', '=', $language_id)
            ->forceDelete();
            
        return response()->json([
            'success' => true,
            'message' => __('messages.provider_language_deleted'),
        ]);
    }

    /**
     * function to serach for service provider
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function serviceProviderFilter(Request $request)
    {
        $type = $request->serch_type;
        $value = $request->value;
        $service_providers = ServiceProvider::where('account_status', '=', 1);
        if ($type == 1) {
            $service_providers->where('availability', '=', $value);
        } elseif ($type == 2) {
            $service_providers->where('gender', '=', $value);
        } elseif ($type == 3) {
            $service_providers->where('call_type', '=', $value);
        }
        $service_providers = $service_providers->with('user')->get();
        return response()->json(['service_provider' => $service_providers]);
    }

    /**
     * function to save the service provider bacnk account information
     *
     * @param Request $request
     *
     * @return  response
     */
    public function saveProviderAccountInformation(Request $request)
    {
        // validation rule
        $rules = [
            'bank_name' => 'required|max:255',
            'bank_address' => 'required|max:255',
            'swift_code' => 'required|bic',
            'name_on_the_account' => 'required|max:255',
            'iban_number' => 'required',
        ];

        //  request inputs
        $input = $request->only(
            'bank_name',
            'bank_address',
            'swift_code',
            'name_on_the_account',
            'iban_number',
            'branch_number'
        );

        // get the current authenticate user id
        $user_id = Auth::user()->id;
        $provider_id = UserHelper::getServiceProviderID($user_id);
        $user = User::firstOrNew(['id' => $user_id]);
        $validator = Validator::make($input, $rules);
        //check the validation rule
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'error' => json_decode($error)
            ]);
        }
        $exist_account = ProviderBank::where('service_provider_id', '=', $provider_id)->first();
        if (!empty($exist_account)) {
            ProviderBank::where('service_provider_id', '=', $provider_id)->update([
                'service_provider_id' => $provider_id,
                'bank_name' => $input['bank_name'],
                'bank_address' => $input['bank_address'],
                'swift_code' => $input['swift_code'],
                'name_on_the_account' => $input['name_on_the_account'],
                'iban_number' => $input['iban_number'],
                'branch_number' => @$input['branch_number']
            ]);
            return response()->json([
                'success' => true,
                'message' => __('messages.update_bank_account_info')
            ]);
        } else {
            $new_account = new ProviderBank;
            $new_account->service_provider_id = $provider_id;
            $new_account->bank_name = $input['bank_name'];
            $new_account->bank_address = $input['bank_address'];
            $new_account->swift_code = $input['swift_code'];
            $new_account->name_on_the_account = $input['name_on_the_account'];
            $new_account->iban_number = $input['iban_number'];
            $new_account->branch_number = @$input['branch_number'];
            $new_account->save();
            
            return response()->json([
                'success' => true,
                'message' => __('messages.save_bank_account_info')
            ]);
        }
    }

    /**
     * function to get service provider bank account information
     *
     * @param Request $request
     *
     * @return  response
     */
    public function providerBankInfo(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 2) {
            $provider_id = UserHelper::getServiceProviderID($user->id);
            $bank_info = ProviderBank::where('service_provider_id', '=', $provider_id)->first();
            if (empty($bank_info)) {
                $bank_info = "";
            }
            return response()->json([
                'success' => true,
                'bank_info' => $bank_info
            ]);
        }
    }
}
