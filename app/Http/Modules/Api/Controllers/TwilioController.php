<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Twiml;
use YallaTalk\Models\Appointment;
use Carbon\Carbon;
use YallaTalk\Models\Call;
use YallaTalk\Models\User;
use Cache;
use Auth;
use UserHelper;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\Client as UserClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use DB;
use YallaTalk\Models\ServiceProviderRating;
use Notification;
use PushNotification;
use YallaTalk\Models\Transaction;
use Stripe\Stripe;
use Stripe\Charge;
use YallaTalk\Models\CallHistory;

class TwilioController extends Controller
{
    // twilio config
    protected $sid;
    protected $token;
    protected $key;
    protected $secret;
    protected $phone_number;

    // FCM config
    protected $fcm_server_key;
    protected $fcm_sender_id;
    protected $stripe_api_secret;

    public function __construct()
    {
        $this->sid = config('twilio.twilio.connections.twilio.sid');
        $this->token = config('twilio.twilio.connections.twilio.token');
        $this->phone_number = config('twilio.twilio.connections.twilio.from');
        $this->key = config('twilio.twilio.connections.twilio.key');
        $this->secret = config('twilio.twilio.connections.twilio.secret');

        // FCM congiurations
        $this->fcm_server_key = config('services.FCM.FCM_SERVER_KEY');
        $this->fcm_sender_id = config('services.FCM.FCM_SENDER_ID');

        //stripe config
        $this->stripe_api_secret = config('services.stripe.secret');
    }

    /**
     * index function to gt all rooms
     *
     * @return void
     */
    public function index()
    {
        $rooms = [];
        try {
            $client = new Client($this->sid, $this->token);

            $allRooms = $client->video->rooms->read([]);
            $rooms = array_map(function ($room) {
                return $room->uniqueName;
            }, $allRooms);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return response()->json([
            'success' => true,
            'rooms' => $rooms
        ]);
    }
    /**
     * index function
     *
     * @return  void
     */
    public function voicCall(Request $request)
    {
        $client_id = $request->client_id;
        $provider_id = $request->provider_id;

        $client = new Client($this->sid, $this->token);
        // Read TwiML at this URL when a call connects (hold music)
        $call = $client->calls->create(
            '+972569918245',
            $this->phone_number,
            [
                'url' => 'https://demo.twilio.com/docs/voice.xml'
            ]
        );
    }

    /**
     * function to create new video room
     *
     * @param Request $request
     *
     * @return  response
     *
     */
    public function createRoom(Request $request)
    {
        $user = Auth::user();
        $user_type = 0;
        $invited_user = 0;
        $user_id = 0;
        $topic_id = $request->topic_id;
        if (empty($topic_id)) {
            $topic_id = 0;
        }
        $client_time_zone = UserHelper::getTimeZone($user->id);
        $current_time = Carbon::now($client_time_zone);
        $get_partner_info = UserHelper::getPartnerInfo($request->partner_id);
        if ($get_partner_info[0] == 1) {
            return response()->json([
                'success' => false,
                'message' => __('messages.provider_in_call_message')
            ]);
        }
        if ($get_partner_info[1] == 1) {
            $client_id = UserHelper::getClientId($user->id);
            if (UserHelper::userMoney($user->id) != 1) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.user_have_money_message')
                ]);
            }
            $carbon_after = new Carbon($current_time);
            $after_time = $carbon_after->addMinutes(15);
            $carbon_befor = new Carbon($current_time);
            $befor_time = $carbon_befor->subMinutes(15);

            //check if the service provider has another booking in same time
            $provider_Appointments = Appointment::where('service_provider_id', $request->partner_id)
                ->where('start_time', '=', $current_time)
                ->where('client_id', '!=', $client_id)
                ->first();

            //check if ther is an appointment at this time
            $provider_Appointments_after_time = Appointment::where('service_provider_id', $request->partner_id)
                ->where('start_time', '<=', $after_time)
                ->where('start_time', '>=', $befor_time)
                ->where('client_id', '!=', $client_id)
                ->first();
            $provider_Appointments_befor_time = Appointment::where('service_provider_id', $request->partner_id)
                ->where('end_time', '<=', $after_time)
                ->where('end_time', '>=', $befor_time)
                ->where('client_id', '!=', $client_id)
                ->first();
            // check if the appointment is reserved
            if (!empty($provider_Appointments) ||
                (!empty($provider_Appointments_befor_time)) ||
                (!empty($provider_Appointments_after_time))) {

                // return false response
                return response()->json([
                    'success' => false,
                    'message' => __('messages.provider_in_call_message')
                ]);
            }

            $invited_user = ServiceProvider::findOrFail($request->partner_id);
            $user_id = $invited_user->user_id;
            if (empty($request->room_name)) {
                $room_name = str_random(6);
            } else {
                $room_name = $request->room_name;
            }
            Cache::put($user_id, $room_name, 10);
            Cache::put(
                $room_name,
                [$client_id,
                $request->partner_id,
                $request->call_type,
                $topic_id
                ],
                20
            );
            $client = new Client($this->sid, $this->token);
        
            $exists = $client->video->rooms->read([
                'uniqueName' => $room_name
            ]);

            if (empty($exists)) {
                $client->video->rooms->create([
                    'uniqueName' => $room_name,
                    'type' => 'group',
                    'recordParticipantsOnConnect' => false,
                ]);
            }
            $this->sendPushNotification(
                $user_id,
                $room_name,
                $request->call_type,
                $user->id//caller id
            );

            return response()->json([
                'room_name' => $room_name
            ]);
        } else {
            $appointment = Appointment::where('start_time', '<=', $current_time)
                ->where('end_time', '>=', $current_time)
                ->where('call_type', '=', $request->call_type)
                ->where('status', '=', Appointment::APPOINTMENT_APPROVED)
                ->get();

            if ($user->user_type == 1) {
                $client_id = UserHelper::getClientId($user->id);
                $appointment = $appointment->where('client_id', '=', $client_id)
                    ->where('service_provider_id', '=', $request->partner_id)
                    ->first();
                // check if the appointment is empty
                if (empty($appointment)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.no_appotment_message')
                    ]);
                }
                if (UserHelper::userMoney($user->id) != 1) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.user_have_money_message')
                    ]);
                }
                $invited_user = ServiceProvider::findOrFail($appointment->service_provider_id);
                $user_id = $invited_user->user_id;
            } elseif ($user->user_type == 2) {
                $service_provider_id = UserHelper::getServiceProviderId($user->id);
                $appointment = $appointment
                    ->where('service_provider_id', '=', $service_provider_id)
                    ->where('client_id', '=', $request->partner_id)
                    ->first();
                // check if the appointment is empty
                if (empty($appointment)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.no_appotment_message')
                    ]);
                }
                $user_type = 2;
                $invited_user = UserHelper::getClientInfo($appointment->client_id);
                $user_id = $invited_user->user_id;

                if (UserHelper::userMoney($user_id) != 1) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.user_have_money_message')
                    ]);
                }
            }

        
            if (!empty($appointment)) {
                if (empty($request->room_name)) {
                    $room_name = "Room no ".$appointment->id;
                } else {
                    $room_name = $request->room_name;
                }
                Cache::put($user_id, $room_name, 10);
                Cache::put(
                    $room_name,
                    [$appointment->client_id,
                    $appointment->service_provider_id,
                    $appointment->call_type,
                    $topic_id
                    ],
                    20
                );
                $client = new Client($this->sid, $this->token);
        
                $exists = $client->video->rooms->read([
                    'uniqueName' => $room_name
                ]);
 
                if (empty($exists)) {
                    $client->video->rooms->create([
                        'uniqueName' => $room_name,
                        'type' => 'group',
                        'recordParticipantsOnConnect' => false,
                    ]);
                }
                $this->sendPushNotification(
                    $user_id,
                    $room_name,
                    $appointment->call_type,
                    $user->id//caller id
                );

                return response()->json([
                    'room_name' => $room_name
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.no_appotment_message')
                ]);
            }
        }
    }

    /**
     * function to join video call room
     *
     * @param Request $request
     *
     * @return  response
     */
    public function joinVideoRoom(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        if ($user->user_type == 2) {
            $service_provider_id = UserHelper::getServiceProviderId($user->id);
            UserHelper::updateProviderCallStatus($service_provider_id, 1);
        }
        $this->updateCall($user_id, $request->room_name);
        $identity = rand(3, 1000);
        // Create an Access Token
        $token = new AccessToken(
            $this->sid,
            $this->key,
            $this->secret,
            3600,
            $identity
        );
        // Grant access to Video
        $grant = new VideoGrant();
        $grant->setRoom($request->room_name);
        $token->addGrant($grant);
        $call_info = Cache::get($request->room_name);
        $topic_id = $call_info[3];
        $topic_name = "";
        if ($topic_id == 0) {
            $topic_name = "General discussion";
        } else {
            $topic_name = UserHelper::getTopicName($topic_id);
        }
        
        // Serialize the token as a JWT
        return response()->json([
            'token'=> $token->toJWT(),
            'topic_name' => $topic_name
        ]);
    }

    /**
     * function to send twilio message
     *
     * @param string mobile_number
     *
     * @return  response
     */
    public function sendMessage(Request $request)
    {
        $mobile_number = $request->mobile_number;
        $mobile_number = "+".$mobile_number;

        $client = new Client($this->sid, $this->token);
        $message = $client->messages->create(
            $mobile_number,
            [
                'from' => $this->phone_number,
                'body' => $request->message
            ]
        );
    }

    /**
     * function to create call markup file thats control the voice call
     *
     * @param
     *
     * @return response
     */
    public function createTwilm()
    {
        $response = new Twiml();
        $response->say('Hello');
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        print $response;
    }

    /**
     * room created event
     *
     * @param Request $request
     *
     * @return  response
     *
     */
    public function roomCreated(Request $request)
    {
        $call_info = Cache::get($request->RoomName);
        if ($request->StatusCallbackEvent == "participant-connected") {
            $call = new Call();
            $call->room_status = $request->RoomStatus;
            $call->room_name = $request->RoomName;
            $call->start_time = Carbon::now();
            $call->client_id = $call_info[0];
            $call->service_provider_id = $call_info[1];
            $call->call_type = $call_info[2];

            $call->save();
        } elseif ($request->StatusCallbackEvent == "room-ended" && $request->RoomStatus == "completed") {
            $call = Call::where('room_name', '=', $request->RoomName)->update([
                'end_time' => Carbon::now(),
            ]);
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function getRoomName()
    {
        $auth_user = Auth::user()->id;

        $room_name = Cache::get($auth_user);

        if (!empty($room_name)) {
            return response()->json([
                'success' => true,
                'room_name' => $room_name
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.no_room_message')
            ]);
        }
    }

    /**
     * function to send push notification
     *
     * @param  int $user_id, string $room_name
     *
     * @return  response
     */
    public function sendPushNotification($user_id, $room_name, $call_type, $caller_id)
    {
        try {
            $user = User::findOrFail($user_id);
            if ($user->device_token == null || empty($user->device_token)) {
                return false;
            }
            if ($user->platform == "ios") {
                $details = [
                    'room_name' => $room_name,
                    'call_type' => $call_type,
                    'caller_id' => $caller_id,
                    'type' => 'new_call',
                ];

                $message = PushNotification::Message(__('messages.send_push_notificatopn_title'), array(
                    'badge' => 0,
                    'sound' => 'example.aiff',
                    'title' => __('messages.send_push_notification_title'),
                    'body' => __('messages.send_push_notification_body'),
                    'actionLocKey' => __('messages.actionLocKey'),
                    'locKey' => __('messages.locKey'),
                    'locArgs' => array(
                        __('messages.localized-args'),
                        __('messages.localized-args'),
                    ),
                    'launchImage' => '',
                    'data' => json_encode($details),
                    'custom' => array('data' => json_encode($details))
                ));

                PushNotification::app('YALLATALK_IOS')
                    ->to($user->device_token)
                    ->send($message);
            } else {
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);
                $notificationBuilder = new PayloadNotificationBuilder(__('messages.send_push_notificatopn_title'));
                $notificationBuilder->setBody(__('messages.send_push_notification_body'))
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'room_name' => $room_name,
                    'call_type' => $call_type,
                    'caller_id' => $caller_id,
                    'type' => 'new_call',
                ]);
                
                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = $user->device_token;
                
                $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
                
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => __('messages.no_service_provider_found')
                ]);
        }
    }

    /**
     * function to save the call in the database
     *
     * @param  int $user_id
     *
     * @return  response
     *
     */
    public function updateCall($user_id, $room_name)
    {
        $call_info = Cache::get($room_name);
        $user = User::findOrFail($user_id);
        $user_type = $user->user_type;
        $call = Call::where('room_name', '=', $room_name)->first();
        $clinet = UserClient::findOrFail($call_info[0]);
        $user = User::findOrFail($clinet->user_id);

        if (empty($call)) {
            $new_call = new Call;
            $new_call->start_at = Carbon::now($user->time_zone);
            $new_call->room_name = $room_name;
            $new_call->client_id = $call_info[0];
            $new_call->service_provider_id = $call_info[1];
            $new_call->call_type = $call_info[2];
            $new_call->room_status = "WAITING";
            $new_call->topic_id = $call_info[3];
            $new_call->Save();
        } else {
            Call::where('room_name', '=', $room_name)->update([
                'room_status' => 'JOIN',
                'start_at' => Carbon::now($user->time_zone)
            ]);
            $details = [
                'type' => 'participant_joined',
            ];
            $title = "Call Started";
            UserHelper::sendPushNotification($user->device_token, $details, $title, $user->platform, 'participant_joined');
        }
    }

    /**
     * function to end the call
     *
     * @param Request $request
     *
     * @return  response
     *
     */
    public function endCall(Request $request)
    {
        $user_who_end_call = $request->user_id;
        $is_join_room = 0;
        $auth_user = Auth::user();
        $user_id = $auth_user->id;
        $user_type = $auth_user->user_type;
        $client_time_zone = UserHelper::getClientTimaZone($request->room_name);
        $commision = Cache::get('commision');

        $call = Call::where('room_name', '=', $request->room_name)->first();
        UserHelper::updateProviderCallStatus($call->service_provider_id, 0);
        $partner_id = "";
        $service_provider_id = "";
        $client_id = "";
        $check_if_call_joind = Call::where('room_name', '=', $request->room_name)
            ->where('room_status', '=', 'WAITING')
            ->first();
        if (!empty($check_if_call_joind)) {
            UserHelper::endCall($call->id, $user_id, $call->room_name);
        } else {
            DB::table('calls')->where('room_name', '=', $request->room_name)->update([
                'room_status' => "COMPLETED",
                'end_at' => date("Y-m-d H:i:s")
            ]);
            $is_join_room = 1;
            $balance = 0;
            $call_duration = 0;
            $call_price = 0;
            $user_points = 0;
            if ($user_type == 1) {
                $ServiceProvider = ServiceProvider::findOrFail($call->service_provider_id);
                $partner_id = $ServiceProvider->user_id;
                $end = Carbon::parse($call->end_at);
                $start = Carbon::parse($call->start_at);
                $call_duration = $end->diffInMinutes($start);
                $seconds_duration = $end->diffInSeconds($start);
                $price_in_seconds = round(($seconds_duration/3600)*$ServiceProvider->price, 2);
                $user_points = UserHelper::getUserPoints($ServiceProvider->user_id);
                $call_price = (($call_duration/60)*$ServiceProvider->price)+($price_in_seconds);
                $call_price = round($call_price, 2)+($call_price*$commision);
            } elseif ($user_type == 2) {
                $client = UserHelper::getClientInfo($call->client_id);
                $partner_id = $client->user_id;
                $service_provider = ServiceProvider::where('user_id', '=', $auth_user->id)->first();
                $end = Carbon::parse($call->end_at);
                $start = Carbon::parse($call->start_at);
                $call_duration = $end->diffInMinutes($start);
                $seconds_duration = $end->diffInSeconds($start);
                $price_in_seconds = ($seconds_duration/3600)*$service_provider->price;
                $call_price = (($call_duration/60)*$service_provider->price)+($price_in_seconds);
                $call_price = round($call_price, 2)+($call_price*$commision);
            }
            $this->sendEndCallPushNotification(
                $partner_id,
                $user_who_end_call,
                $is_join_room,
                $request->room_name,
                $balance,
                $call_duration,
                $call_price,
                $user_points,
                $call->id
            );
            $cents = $call_price*100;
            $yallatalk_account = UserHelper::getYalltalkAccount();
            $client = UserClient::findOrFail($call->client_id);
            UserHelper::updateBalance($yallatalk_account->id, $call_price);
            UserHelper::updateBalance($client->user_id, $call_price);

            $transaction = new Transaction;
            $transaction->client_id = $call->client_id;
            $transaction->service_provider_id = $call->service_provider_id;
            $transaction->amount = $call_price;
            $transaction->currency = "USD";
            $transaction->call_id = $call->id;
            $transaction->clinet_balance = UserHelper::getClientBalance($call->client_id);
            $transaction->provider_balance = UserHelper::getProviderBalance($call->service_provider_id);
            $transaction->save();

            Stripe::setApiKey($this->stripe_api_secret);
            // Charge the user's card:
            try {
                $charge = Charge::create(array(
                    "amount" => (int)($call_price*100),
                    "currency" => "USD",
                    "description" => __('messages.create_new_charge_description'),
                    "customer" => $yallatalk_account->stripe_id,
                ));
            } catch (\Exception $e) {
                return response()
                    ->json([
                       'success' => false,
                       'message' => __('messages.fail_charge_message')
                    ]);
            }
        }
        return response()
            ->json([
                'success' => true,
                'message' => __('messages.sens_notification_message')
            ]);
    }

    /**
     * function to send push notification for end call
     *
     * @param $device_token
     *
     * @return response
     *
     */
    public function sendEndCallPushNotification(
        $partner_id,
        $user_who_end_call,
        $is_join_room,
        $room_name,
        $balance = null,
        $call_duration = null,
        $call_price = null,
        $user_points = null,
        $call_id = null
    ) {
        $data = [];
        if (!empty($call_duration)) {
            $data = [
                'type' => 'call_ended',
                'call_duration' => $call_duration,
                'call_price' => $call_price,
                'user_type' => 1,
                'points' => $user_points,
                'call_id' => $call_id,
                'balance' => $balance,
                'end_call_user' => $user_who_end_call,
                'is_join_room' => $is_join_room,
                'room_name' => $room_name,
                'content_available' => 0
            ];
        } else {
            $data = [
                'type' => 'call_ended',
                'user_type' => 2,
                'balance' => $balance,
                'end_call_user'=> $user_who_end_call,
                'is_join_room' => $is_join_room,
                'room_name' => $room_name,
                'content_available' => 0
            ];
        }
        $users = User::where('id', '=', $partner_id)->orWhere('id', '=', $user_who_end_call)->get();
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user->platform == "ios") {
                    UserHelper::sendToIos($data, $user->device_token);
                } elseif ($user->platform == "android") {
                    UserHelper::sendToAndroid($data, $user->device_token);
                }
            }
        }
    }

    /**
     * function to get call summary info
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function getCallSummary(Request $request)
    {
        $room_name = $request->room_name;
        $call_info = Cache::get($request->room_name);
        $topic_id = $call_info[3];

        $topic_name = "";
        if ($topic_id == 0) {
            $topic_name = "General discussion";
        } else {
            $topic_name = UserHelper::getTopicName($topic_id);
        }

        $call = Call::where('room_name', '=', $room_name)->first();
        $ServiceProvider = ServiceProvider::findOrFail($call->service_provider_id);
        $end = Carbon::parse($call->end_at);
        $start = Carbon::parse($call->start_at);
        $call_duration = $end->diffInMinutes($start);
        $seconds_duration = $end->diffInSeconds($start);
        $call_price_in_seconds = round(($seconds_duration/3600)*$ServiceProvider->price, 2);
        $user_points = UserHelper::getUserPoints($ServiceProvider->user_id);
        $call_price = round(($call_duration/60)*$ServiceProvider->price, 2);
        $call_price += $call_price_in_seconds;
        $total_minutes = $call_duration%60;
        $total_hours = $call_duration - $total_minutes;
        $total_hours = $total_hours/60;
         
        $user = User::findOrFail($ServiceProvider->user_id);
        
        $seconds = 0;
        if ($call->getSecondsDuration() <= 1) {
            $seconds = 0;
        } else {
            $seconds = $call->getSecondsDuration()-2;
        }
        if ($call->room_status == Call::ROOM_COMPLETED_STATUS) {
            return response()
                ->json([
                    'call_duration' => $call_duration,
                    'total_hours' => $total_hours,
                    'total_minutes' => $total_minutes,
                    'call_price' => $call_price,
                    'user_type' => 1,
                    'points' => $user_points,
                    'seconds_duration' => $seconds,
                    'service_provider_id' => $call->service_provider_id,
                    'topic_name' => $topic_name,
                    'service_provider_name' => $user->first_name
                ]);
        } else {
            return response()
                ->json([
                    'call_duration' => 0,
                    'total_hours' => 0,
                    'total_minutes' => 0,
                    'call_price' => 0,
                    'user_type' => 1,
                    'points' => $user_points,
                    'seconds_duration' => 0,
                    'service_provider_id' => $call->service_provider_id,
                    'topic_name' => $topic_name,
                    'service_provider_name' => $user->first_name
                ]);
        }
    }

    /**
     * function to rate service provider call
     *
     * @param  Request $request
     *
     * @return  response
     *
     */
    public function rateCall(Request $request)
    {
        $rate = new ServiceProviderRating;

        if (empty($request->comment)) {
            $rate->comment = "";
        } else {
            $rate->comment = $request->comment;
        }
        
        if (empty($request->call_rate)) {
            $rate->call_rate = 0;
        } else {
            $rate->call_rate = $request->call_rate;
        }
        
        if (empty($request->provider_rate)) {
            $rate->provider_rate = 0;
        } else {
            $rate->provider_rate = $request->provider_rate;
        }
        
        $rate->service_provider_id = $request->service_provider_id;
        if (empty($request->good_communication_skills)) {
            $rate->good_communication_skills = 0;
        } else {
            $rate->good_communication_skills = $request->good_communication_skills;
        }
        
        if (empty($request->good_teaching_skills)) {
            $rate->good_teaching_skills = 0;
        } else {
            $rate->good_teaching_skills = $request->good_teaching_skills;
        }

        if (empty($request->intersting_conserviation)) {
            $rate->intersting_conserviation = 0;
        } else {
            $rate->intersting_conserviation = $request->intersting_conserviation;
        }
        
        if (empty($request->kind_personality)) {
            $rate->kind_personality = 0;
        } else {
            $rate->kind_personality = $request->kind_personality;
        }
        
        if (empty($request->correcting_my_language)) {
            $rate->correcting_my_language = 0;
        } else {
            $rate->correcting_my_language = $request->correcting_my_language;
        }
        
        $rate->language_id = 1;
        
        $rate->save();

        $provider_rates = ServiceProviderRating::where('service_provider_id', '=', $request->service_provider_id)->get();
        $counter = 0;
        $total_rate = 0.0;
        if (count($provider_rates) > 0) {
            foreach ($provider_rates as $provider_rate) {
                ++$counter;
                $total_rate += $provider_rate->provider_rate;
            }
            $provider = ServiceProvider::findOrFail($request->service_provider_id);
            $provider->rating = $total_rate/$counter;
            $provider->save();
        }
        return response()
            ->json([
                'success' => true,
                'message' => __('messages.call_rate_message'),
                'new_rate' => round($provider->rating, 2),
                'service_provider_id' => $request->service_provider_id
            ]);
    }

    /**
     * public function to update the call start time
     *
     * @param Request $request
     *
     * @return  response
     */
    public function updateCallStartTime(Request $request)
    {
        $room_name = $request->room_name;
        $call = Call::where('room_name', '=', $room_name)->first();
        $client_id = $call->client_id;
        $client_user = UserClient::findOrFail($client_id);
        $user = User::findOrFail($client_user->user_id);
        $time_zone = UserHelper::getTimeZone($user->id);

        if (empty($time_zone)) {
            $time_zone = "UTC";
        }
        $current_time = Carbon::createFromTimestampMs($request->start_time);
        Call::where('room_name', '=', $room_name)->update([
            'start_at' => $current_time
        ]);
    }
}
