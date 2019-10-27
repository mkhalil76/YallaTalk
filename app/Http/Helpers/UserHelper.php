<?php

namespace YallaTalk\Http\Helpers;

use YallaTalk\Models\Appointment;
use \Stripe\Balance;
use Carbon\Carbon;
use YallaTalk\Models\Call;
use YallaTalk\Models\ClientLanguage;
use YallaTalk\Models\Client;
use DB;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use YallaTalk\Models\Packege;
use PushNotification;
use Illuminate\Http\Request;
use \Stripe\Stripe;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\ServiceProviderLanguage;
use YallaTalk\Models\Transaction;
use YallaTalk\Models\User;
use YallaTalk\Models\Invite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use YallaTalk\Models\Language;
use YallaTalk\Models\Topic;
use Auth;
use Twilio\Rest\Client as TwilioClient;
use YallaTalk\Jobs\CheckNotJoindCalls;

class UserHelper
{
    // FCM config
    protected $fcm_server_key;
    protected $fcm_sender_id;
    /**
     * function to get the user id by it's invitation code
     *
     * @param  String $invitaion_code
     *
     * @return  Integer  UserID
     */
    public function getUserIdByInvitaionCode($invitaion_code)
    {
        $user = User::where('invitation_code', $invitaion_code)->first();
        if (!empty($user)) {
            return $user->id;
        } else {
            return 0;
        }
    }

    /**
     * function to get the user total earn points
     *
     * @param  Integer $user_id
     *
     * @return Integer total_points
     *
     * */
    public function getUserPoints($user_id)
    {
        $total_point = 0;

        $point_by_accept_invitation_from_users = Invite::where('user_id', $user_id)->count()*50;

        $pints_by_invited_by_users = Invite::where('invited_id', $user_id)->count()*10;

        return $point_by_accept_invitation_from_users+$pints_by_invited_by_users;
    }

    /**
     * function to get user provider by gevin user_id
     *
     * @param  int  $user_id
     *
     * @return string   provider
     *
     */
    public function getUserProvider($user_id)
    {
        return User::where('id', '=', $user_id)->first()->provider;
    }

    /**
     * function to get service provider id from user id
     *
     * @param int  $user_id
     *
     * @return int
     */
    public function getServiceProviderID($user_id)
    {
        $provider = ServiceProvider::where('user_id', '=', $user_id)->first();

        if (!empty($provider)) {
            return $provider->id;
        } else {
            return false;
        }
    }
    /**
     * function to get client id from user id
     *
     * @param int  $user_id
     *
     * @return int
     */
    public function getClientID($user_id)
    {
        $client = Client::where('user_id', '=', $user_id)->first();
        if (!empty($client)) {
            return $client->id;
        } else {
            return false;
        }
    }
    /**
     * function to get service provider name
     *
     * @param int $service_provider_id
     *
     * @return string
     */
    public function getServiceProviderName($service_provider_id)
    {
        $provider = ServiceProvider::where('id', '=', $service_provider_id)
            ->first()
            ->user;
        return $provider->first_name." ".$provider->last_name;
    }
    /**
     * function to get client name
     *
     * @param int $service_provider_id
     *
     * @return string
     */
    public function getClientName($client_id)
    {
        $client_user = Client::where('id', '=', $client_id)
           ->first()
           ->user;
        $client = Client::where('id', '=', $client_id)
           ->first();
        if ($client->availability == 1) {
            return $client_user->first_name." ".$client_user->last_name;
        } elseif ($client->availability == 0) {
            return $client_user->first_name;
        }
    }
    /**
     * function to get user avatar
     *
     * @param int $user_id , int $user_type
     *
     * @return  blob
     */
    public function getUserAvatar($user_id, $user_type)
    {
        if ($user_type == 1) {
            $client = Client::where('user_id', '=', $user_id)->first();
            
            if (!empty($client)) {
                return $client->image;
            } else {
                return null;
            }
        } elseif ($user_type == 2) {
            $provider = ServiceProvider::where('user_id', '=', $user_id)->first();
            if (!empty($provider)) {
                return $provider->image;
            } else {
                return null;
            }
        }
    }

    /**
     * function to get user practing language
     *
     * @param  int $user_id, int $user_type
     *
     * @return  Array
     *
     */
    public function getPractingLanguage($user_id, $user_type)
    {
        if ($user_type == 1) {
            $client = Client::where('user_id', '=', $user_id)->first();
            if (!empty($client)) {
                $languages = ClientLanguage::with('language')
                    ->where('client_id', '=', $client->id)
                    ->get()
                    ->toArray();
                return $languages;
            } else {
                return null;
            }
        } elseif ($user_type == 2) {
            $provider = ServiceProvider::where('user_id', '=', $user_id)->first();
            if (!empty($provider)) {
                $language = ServiceProviderLanguage::with('language')
                    ->where('service_provider_id', '=', $provider->id)
                    ->get()
                    ->toArray();
                return $language;
            } else {
                return null;
            }
        }
    }

    /**
     * function to get user status
     *
     * @param  int $user_id, int $user_type
     *
     * @return  int
     *
     */
    public function getStatus($user_id, $user_type)
    {
        if ($user_type == 1) {
            $client = Client::where('user_id', '=', $user_id)->first();
            if (!empty($client)) {
                return $client->availability;
            } else {
                return null;
            }
        } elseif ($user_type == 2) {
            $provider = ServiceProvider::where('user_id', '=', $user_id)->first();
            if (!empty($provider)) {
                return $provider->availability;
            } else {
                return null;
            }
        }
    }

    /**
     * function to get the user availability status
     *
     * @param int $status
     *
     * @return  string
     */
    public function getUserAvailability($status)
    {
        if ($status == 1) {
            return "<span class='label label-success'>online</span>";
        } else {
            return "<span class='label label-default'>offline</span>";
        }
    }

    /**
     * function to get the user availability status
     *
     * @param int $status
     *
     * @return  string
     */
    public function getAcountStatus($status)
    {
        if ($status == 1) {
            return "<span class='label label-success'>Active</span>";
        } elseif ($status == 2) {
            return "<span class='label label-primary'>Freez</span>";
        } else {
            return "<span class='label label-default'>Inactive</span>";
        }
    }

    /**
     * function to get the call type
     *
     * @param int $type
     *
     * @return  string
     */
    public function getCallType($type)
    {
        if ($type == 1) {
            return "voice";
        } elseif ($type == 2) {
            return "video";
        }
    }

    /**
     * function to get the user gender
     *
     * @param int $gender
     *
     * @return  string
     */
    public function getUserGender($gender)
    {
        if ($gender == "M") {
            return "Male";
        } elseif ($gender == "F") {
            return "Female";
        } else {
            return "Other";
        }
    }

    /**
     * function to get user info
     *
     * @param int $user_id
     *
     * @return YallaTalk\Models\User
     */
    public function getUserInfo($user_id)
    {
        return User::findOrFail($user_id);
    }


    /**
     * function to get client list
     *
     * @return  array
     */
    public function getClientNameMapById()
    {
        $clients = Client::all();
        $client_list = [];

        foreach ($clients as $client) {
            $client_list[$client->id] = $client->id;
        }
        
        return $client_list;
    }

    /**
     * function to get Service Provider list
     *
     *  @return  array
     */
    public function getServiceProviderNameById()
    {
        $providers = ServiceProvider::all();
        $provider_list = [];

        foreach ($providers as $provider) {
            $provider_list[$provider->id] = $provider->id;
        }
        
        return $provider_list;
    }

    /**
     * function to check if the account is active or not
     *
     * @param  array $input
     *
     * @return  response
     */
    public function checkForActiveAccount($input)
    {
        $user = User::where('email', '=', $input['email'])->first();
        if (empty($user)) {
            return [
                'success' => false,
                'message' => 'The email address is invalid',
            ];
        }
        // check for user type
        if ($user->user_type == 1) {
            $client = Client::where('user_id', '=', $user->id)->first();
            // check for account status
            // 1 - active account
            // 2 - freez account
            // 3 - inactive account
            
            if (!empty($client)) {
                if ($client->account_status == 2) {
                    return [
                       'success'=> false,
                       'message' => 'Your account is temporarily frozen',
                    ];
                } elseif ($client->account_status == 3) {
                    return [
                       'success'=> false,
                       'message' => 'Your account is temporarily inactive',
                    ];
                } elseif ($client->account_status == 1) {
                    return [
                        'success'=> true,
                    ];
                }
            } else {
                return [
                   'success'=> true,
                ];
            }
        } elseif ($user->user_type == 2) {
            $provider = ServiceProvider::where('user_id', '=', $user->id)->first();
            if (!empty($provider)) {
                // check for account status
                // 1 - active account
                // 2 - freez account
                // 3 - inactive account
            
                if ($provider->account_status == 2) {
                    return [
                       'success'=> false,
                       'message' => 'Your account is temporarily frozen',
                    ];
                } elseif ($provider->account_status == 3) {
                    return [
                       'success'=> false,
                       'message' => 'Your account is temporarily inactive',
                    ];
                } elseif ($provider->account_status == 1) {
                    return [
                       'success'=> true,
                    ];
                }
            } else {
                return [
                   'success'=> true,
                ];
            }
        } else {
            return [
                   'success'=> true,
                ];
        }
    }

    /**
     * function to get client info by id
     *
     * @param  int $client_id
     *
     * @return YallaTalk\Models\Client
     */
    public function getClientInfo($client_id)
    {
        return Client::findOrFail($client_id);
    }

    /**
     * function to get the device token
     *
     * @param  int $user_id
     *
     * @return  token
     */
    public function getDeviceToken($user_id)
    {
        $user = User::findOrFail($user_id);
        return $user->device_token;
    }

    /**
     * function to get the service provider call type
     *
     * @param  int
     *
     * @return  call_type
     */
    public function getProviderCallType($service_provider_id)
    {
        $provider = ServiceProvider::findOrFail($service_provider_id);

        return $provider->call_type;
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
    public function customerPay($user_id, $price)
    {
        $total_to_pay = $price;
        $user = User::findOrFail($user_id);

        $customer_id = $user->stripe_id;
        \Stripe\Stripe::setApiKey($this->api_secret);
        // Charge the user's card:
        $charge = \Stripe\Charge::create(array(
          "amount" => $total_to_pay,
          "currency" => "USD",
          "description" => "Price of service",
          "customer" => $customer_id,
        ));
        return response()
            ->json([
               'success' => true
            ]);
    }

    /**
     * function to send push notification
     *
     * @param  string $device_token
     *
     * @return  response
     */
    public function sendPushNotification($device_token, $notification_text, $title, $platform, $type, $details = null)
    {
        if (empty($device_token)) {
            return false;
        }
        $user_id = Auth::user()->id;
        $badge = $this->countNotifications($user_id);
        $this->fcm_server_key = config('services.FCM.FCM_SERVER_KEY');
        $this->fcm_sender_id = config('services.FCM.FCM_SENDER_ID');

        if ($platform == "ios") {
            $details = [
                'message' => $notification_text,
                'type' => "notification",
                'details' => $details
            ];

            $message = PushNotification::Message(__('messages.send_push_notificatopn_title'), array(
                'badge' => $badge,
                'sound' => 'example.aiff',
                'title' => $title,
                'body' => $notification_text,
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
                    ->to($device_token)
                    ->send($message);
        } else {
            $data = [
                'message' => $notification_text,
                'type' => "notification",
                'details' => $details
            ];

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder
                ->setBody($notification_text)
                ->setSound('default')
                ->setBadge($badge);
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($data);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            $downstreamResponse = FCM::sendTo($device_token, $option, $notification, $data);

            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }

    /**
     * function to compute money for service provider
     *
     * @param  time $duration, int $provider_id
     *
     * @return  float
     *
     */
    public function computeMoneyForCall($start, $end, $provider_id)
    {
        $startTime = Carbon::parse($start);
        $finishTime = Carbon::parse($end);

        $duration = $finishTime->diffInMinutes($startTime);
        $duration_in_hours = $duration/60;

        $provider = ServiceProvider::findOrFail($provider_id);
        $provider_rate = $provider->price;
        
        return $provider_rate*$duration_in_hours;
    }

    /**
     *
     * function to get service provider balance
     *
     * @param  int $service_provider_id
     *
     * @return  float
     *
     */
    public function getProviderBalance($service_provider_id)
    {
        $service_provider = ServiceProvider::findOrFail($service_provider_id);
        $user = User::findOrFail($service_provider->user_id);

        return $user->balance;
    }

    /**
     *
     * function to get Client balance
     *
     * @param  int $service_provider_id
     *
     * @return  float
     *
     */
    public function getClientBalance($service_provider_id)
    {
        $service_provider = Client::findOrFail($service_provider_id);
        $user = User::findOrFail($service_provider->user_id);

        return $user->balance;
    }

    /**
     * function to cheack if the user has balance or not
     *
     * @param  $user_id
     *
     * @return int   1 -> if the balance less than 1$
     *               2 -> if the user has enough balance
     *               3 -> if there is no user exisؤt
     *
     */
    public function userMoney($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            if ($user->balance <= 1.0) {
                return 2;
            } else {
                return 1;
            }
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    /**
     * function to update the user balance
     *
     * @param int $user_id
     *
     * @return bpplean true -> if the balance updated sucessfuly
     *                 false -> if the user not exist
     */
    public function updateBalance($user_id, $amount)
    {
        try {
            $user = User::findOrFail($user_id);
            // if the user is client (user_type = 1) the balance will be decreases
            // if the user is provider (user_type = 2) the balance will be increase
            if ($user->user_type == 1) {
                $user->balance -= $amount;
            } elseif ($user->user_type == 3) {
                $user->balance += $amount;
            }
            $user->save();
            return true;
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    /**
     * function to get yallatalk account
     *
     * @return YallaTalk\Models\User
     *
     */
    public function getYalltalkAccount()
    {
        $user = User::where('email', '=', "YallaTalk@info.com")->first();

        return $user;
    }

    /**
     * function to get transfer mony status
     *
     * @param  int $call_id
     *
     * @return  string
     */
    public function getTransferStatus($call_id, $type = 1)
    {
        $transaction = Transaction::where('call_id', '=', $call_id)->first();
        
        if (!empty($transaction)) {
            if ($type == 1) {
                $html = "";
                if ($transaction->status == 0) {
                    $html = "<span class='label label-primary'>".__('messages.wating')."</span>";
                } elseif ($transaction->status == 1) {
                    $html = "<span class='label label-success'>".__('messages.approved')."</span>";
                } elseif ($transaction->status == 2) {
                    $html = "<span class='label label-danger'>".__('messages.rejected')."</span>";
                }

                echo $html;
            } else {
                return $transaction->status;
            }
        }
    }

    /**
     * function to parse timestamp using carbon
     *
     * @param  timestamp $start_at, timestamp $end_at
     *
     * @return  time
     *
     */
    public function parseCallTime($start_at, $end_at)
    {
        $startTime = Carbon::parse($start_at);
        $finishTime = Carbon::parse($end_at);
        $duration = $finishTime->diffInSeconds($startTime);

        return gmdate('H:i:s', $duration);
    }

    /**
     * function to count number of freezed account
     *
     * @return  int
     */
    public function countFreezdAccounts()
    {
        return ServiceProvider::where('account_status', '=', 2)->count();
    }

    /**
     * function to get the number of packeges
     *
     * @return int
     */
    public function packegesCount()
    {
        return Packege::count();
    }

    /**
     * function to count active calls
     *
     * @return  count
     */
    public function countActiveCalls()
    {
        return Call::where('room_status', '=', 'JOIN')->count();
    }

    /**
     * function to get appotment statistic
     *
     * @return  array
     */
    public function appotmentStatistic()
    {
        $counts = [];
        $counts['APPROVED'] = Appointment::where('status', '=', 'APPROVED')->count();
        $counts['PENDING'] = Appointment::where('status', '=', 'PENDING')->count();
        $counts['REJECTED'] = Appointment::where('status', '=', 'REJECTED')->count();
        $counts['ALL'] = Appointment::count();
        return $counts;
    }

    /**
     * function to get refund status
     *
     * @param  int $status
     *
     * @return  string
     */
    public function getRefundStatus($status)
    {
        $html = "";
        if ($status == 0) {
            $html = "<span class='label label-primary'>".__('messages.wating')."</span>";
        } elseif ($status == 1) {
            $html = "<span class='label label-success'>".__('messages.approved')."</span>";
        } elseif ($status == 2) {
            $html = "<span class='label label-danger'>".__('messages.rejected')."</span>";
        }

        echo $html;
    }

    /**
     * function to update the user balance
     *
     * @param int $user_id
     *
     * @return boolean
     */
    public function updateRefund($user_id, $amount)
    {
        try {
            $user = User::findOrFail($user_id);
            // if the user is client (user_type = 1) the balance will be decreases
            // if the user is provider (user_type = 2) the balance will be increase
            if ($user->user_type == 3) {
                $user->balance = $user->balance-$amount;
            } elseif ($user->user_type == 2) {
                $user->balance = $user->balance+$amount;
            } elseif ($user->user_type == 1) {
                $user->balance = $user->balance+$amount;
            }
            $user->save();
            return true;
        } catch (ModelNotFoundException $ex) {
            return false;
        }
    }

    /**
     * function to check if the user currantly have a call
     *
     * @param  int $user_id , $type
     *
     * @return booolean
     */
    public function userHasCall($user_id, $type)
    {
        if ($type == 1) {
            $call = Call::where('client_id', '=', $user_id)
                ->where('room_status', '=', Call::ROOM_JOIN_STATUS)
                ->first();
            if (sempty($call)) {
                return false;
            }
        } elseif ($type == 2) {
            $call = Call::where('service_provider_id', '=', $user_id)
                ->where('room_status', '=', Call::ROOM_JOIN_STATUS)
                ->first();
            if (!empty($call)) {
                return false;
            }
        }

        return true;
    }

    /**
     * function to get the user time zone
     *
     * @param int $user_id
     *
     * @return  string
     */
    public function getTimeZone($user_id)
    {
        $user = User::findOrFail($user_id);

        if ($user->time_zone != null) {
            return $user->time_zone;
        } else {
            return "UTC";
        }
    }

    /**
     * function to check if the SP have any Appointment or not
     *
     * @param  int $service_provider_id
     *
     * @return  boolean
     */
    public function checkAppointments($service_provider_id)
    {
        $current_time = Carbon::now();
        $appotments = Appointment::where('service_provider_id', '=', $service_provider_id)
            ->where('start_time', '>', $current_time)
            ->get();

        if (count($appotments) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * function to get service provider hour rate
     *
     * @param  int $service_provider_id
     *
     * @return  float
     */
    public function getProviderRate($service_provider_id)
    {
        $provider = ServiceProvider::findOrFail($service_provider_id);
        return $provider->price;
    }

    /**
     * function to get client time zone
     *
     * @param string $room_name
     *
     * @return  string
     */
    public function getClientTimaZone($room_name)
    {
        $call = Call::where('room_name', '=', $room_name)->first();
        if (empty($call)) {
            return "";
        }
        $client = Client::findOrFail($call->client_id);

        $user = User::findOrFail($client->user_id);
        return $user->time_zone;
    }

    /**
     * function to get the call price
     *
     * @param float $call_duration , int $provider_id
     *
     * @return  float
     */
    public function getCallPrice($call_duration, $second_duration, $provider_id)
    {
        $hour_rate = $this->getProviderRate($provider_id);
        $seconds_rate = $second_duration/3600;
        return $hour_rate*($call_duration/60+$seconds_rate);
    }

    /**
     * function to get the balance after the call
     *
     * @param int $user_type, int $call_id
     *
     * @return  float
     */
    public function getCallBalance($user_type, $call_id)
    {
        $transaction = Transaction::where('call_id', '=', $call_id)
            ->first();
        if (!empty($transaction)) {
            if ($user_type == 1) {
                return $transaction->clinet_balance;
            } elseif ($user_type == 2) {
                return $transaction->provider_balance;
            }
        } else {
            return 0;
        }
    }

    /**
     * function to get online users list
     *
     * @return array
     *
     */
    public function onlineProvidersList()
    {
        $providers = ServiceProvider::all();

        $online_list = [];
        $counter = 0;
        foreach ($providers as $provider) {
            ++$counter;
            if (Cache::has('provider-is-online-' . $provider->id)) {
                $online_list[$counter] = $provider->id;
            }
        }

        return $online_list;
    }

    /**
     * function to get online users list
     *
     * @return array
     *
     */
    public function offlineProvidersList()
    {
        $providers = ServiceProvider::all();

        $offline_list = [];
        $counter = 0;
        foreach ($providers as $provider) {
            ++$counter;
            if (!Cache::has('provider-is-online-' . $provider->id)) {
                $offline_list[$counter] = $provider->id;
            }
        }

        return $offline_list;
    }

    /**
     * function to get provider language name
     *
     * @param  int $provider_id
     *
     * @return  string
     */
    public function getProviderLanguageName($provider_id)
    {
        $language = ServiceProviderLanguage::where('service_provider_id', '=', $provider_id)->first();
        if (!empty($language)) {
            $language_info = Language::findOrFail($language->language_id);
            return $language_info->name;
        } else {
            return "";
        }
    }

    /**
     * function to get service provider rating
     *
     * @param int $provider_id
     *
     * @return  float
     */
    public function getProviderRateing($provider_id)
    {
        $provider = ServiceProvider::findOrFail($provider_id);
        return $provider->rating;
    }

    /**
     * function to get language name by id
     *
     * @param  int $language_id
     *
     * @return  string
     */
    public function getLanguageName($language_id)
    {
        if ($language_id == null) {
            return " ";
        } else {
            try {
                $language = Language::findOrFail($language_id);

                return $language->name;
            } catch (ModelNotFoundException $ex) {
                return response()
                    ->json([
                        'error' => __('messages.no_language_found_message')
                    ]);
            }
        }
    }

    /**
     * function to get topic name by id
     *
     * @param  int $topic_id
     *
     * @return  string
     */
    public function getTopicName($topic_id)
    {
        if ($topic_id == null) {
            return "";
        } else {
            $topic = Topic::findOrFail($topic_id);
            return $topic->topic_name;
        }
    }

    /**
     * function to count the number of unread notifications for the users
     *
     * @param int $user_id
     *
     * @return  int
     */
    public function countNotifications($user_id)
    {
        try {
            $user = User::find($user_id);
            $notifications = $user->notifications->where('read_at', '=', null)->where('is_seen', '=', 0);
            $data = "";
            $count_badge = 0;
            foreach ($notifications as $notification) {
                if ($notification->data['type'] == 4 || $notification->data['type'] == 2 ||
                    $notification->data['type'] == 3) {
                    $count_badge++;
                }
            }
            return $count_badge;
        } catch (ModelNotFoundException $ex) {
            return response()
                ->json([
                    'error' => 'there is no notification with this user id = '.$user_id
                ]);
        }
    }

    /**
     * function to check if the service provider has a next call
     *
     * @param int $service_provider_id, int $call_id
     *
     * @return  boolean
     */
    public function hasNextCall($service_provider_id, $call_id)
    {
        $call = Call::where('id', '=', $call_id)->first();
        $call_end_time = Carbon::parse($call->end_at);
        $call_end_time->addMinutes(15);
        $provider_next_calls = Call::where('service_provider_id', '=', $service_provider_id)
            ->where('start_at', '>', $call_end_time)
            ->get();
        if (count($provider_next_calls) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * function to end call by cron job
     *
     * @param  int $call_id
     *
     * @return  response
     */
    public function endCall($call_id, $user_id = null, $room_name = null)
    {
        $user = User::findOrFail($user_id);
        $is_join_room = 0;
        $call = Call::findOrFail($call_id);
        if (!empty($call)) {
            $platform = "";
            $device_token = "";
            $user_who_end_call = $user_id;
            $partner_id = "";
            if ($user->user_type == 1) {
                $service_provider = ServiceProvider::findOrFail($call->service_provider_id);
                $partner_id = $service_provider->user_id;
            } elseif ($user->user_type == 2) {
                $client = Client::findOrFail($call->client_id);
                $partner_id = $client->user_id;
            }
            $call->room_status = Call::ROOM_REJECTED_STATUS;
            $call->end_at = $call->start_at;
            $call->save();
            $this->sendEndCallPushNotification(
                $partner_id,
                $user_who_end_call,
                $is_join_room,
                $room_name,
                0,
                0,
                0,
                0,
                $call->id
            );
        }
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
        if ($call_duration == null) {
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
                'content_available' => false
            ];
        } else {
            $data = [
                'type' => 'call_ended',
                'user_type' => 2,
                'balance' => $balance,
                'end_call_user'=> $user_who_end_call,
                'is_join_room' => $is_join_room,
                'room_name' => $room_name,
                'content_available' => false
            ];
        }
        $users = User::where('id', '=', $partner_id)->orWhere('id', '=', $user_who_end_call)->get();
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user->platform == "ios") {
                    $this->sendToIos($data, $user->device_token);
                } elseif ($user->platform == "android") {
                    $this->sendToAndroid($data, $user->device_token);
                }
            }
        }
    }

    /**
     * function to update user fcm token
     *
     * @param  int $user_id , string $token
     *
     */
    public function updateUserToken($user_id, $token)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->device_token = $token;
            $user->save();
        } catch (ModelNotFoundException $ex) {
            echo 'there is no user with this id = '.$user_id;
        }
    }

    /**
     * function to update service provider call status (is in call or not)
     *
     * @param  int $provider_id
     */
    public function updateProviderCallStatus($provider_id, $status)
    {
        $service_provider = ServiceProvider::findOrFail($provider_id);
        $service_provider->is_in_call = $status;
        $service_provider->save();
    }

    /**
     * function to get the service provider info used to make call
     *
     * @param  int $service_provider_id
     *
     * @return  array
     */
    public function getPartnerInfo($service_provider_id)
    {
        $service_provider = ServiceProvider::findOrFail($service_provider_id);

        return [$service_provider->is_in_call, $service_provider->availability];
    }

    /**
     * function to check device token
     *
     * @param  string $token
     */
    public function checkDuplicateToken($token)
    {
        if (!empty($token)) {
            $user = User::where('device_token', '=', $token)->first();
            if (!empty($user)) {
                User::where('device_token', '=', $token)->update([
                    'device_token' => ''
                ]);
            }
        }
    }

    /**
     * function to format call duration
     *
     * @param  Model $call
     *
     * @return  string
     */
    public function formatCallDuration($call)
    {
        if (!empty($call)) {
            $seconds_duration = $call->getSecondsDuration();
            $minutes_duration = $call->getCallDuration();
            $houre_duration = intval($minutes_duration/60);
            $total_minutes = $minutes_duration%60;

            if ($houre_duration == 0) {
                $houre_duration = "00";
            }
            if ($total_minutes == 0) {
                $total_minutes = "00";
            }
            if ($seconds_duration == 0) {
                $seconds_duration = "00";
            }
            if ($seconds_duration < 10 && $seconds_duration != 0) {
                $seconds_duration = "0".$seconds_duration;
            }
            if ($total_minutes < 10 && $total_minutes != 0) {
                $total_minutes = "0".$total_minutes;
            }
            if ($houre_duration < 10 && $houre_duration != 0) {
                $houre_duration = "0".$houre_duration;
            }
            return $houre_duration.":".$total_minutes.":".$seconds_duration;
        }
    }

    /**
     * function to compute user balance in the call
     *
     * @param model $call
     *
     * @return bool
     */
    public function callBalance($call)
    {
        $provider_rate = $this->getProviderRate($call->service_provider_id);
        $clinet_balance = $this->getClientBalance($call->client_id);
        $client_time_zone = $this->getClientTimaZone($call->client_id);
        if (empty($client_time_zone)) {
            $client_time_zone = "UTC";
        }
        $current_time = Carbon::now();
        $start_time = $call->start_at;
        $start = Carbon::parse($start_time);
        $duration = $current_time->diffInMinutes($start);

        $call_price = $provider_rate*($duration/60);
        if ($call_price >= $clinet_balance) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * function to compute provider language rating
     *
     * @param int $provider_id
     *
     * @return  float
     */
    public function getLanguageRating($provider_id)
    {
        $language_rating = ServiceProvider::findOrFail($provider_id);
        $count = $language_rating->rate->count();
        $sum = $language_rating->rate->sum('call_rate');
        if ($count != 0) {
            return $sum/$count;
        }
        return 0;
    }

    /**
     * function to send notification to ios
     *
     * @param  array $data , string $device_token
     */
    public function sendToIos($data, $device_token)
    {
        if (!empty($device_token)) {
            $message = PushNotification::Message("", [
                'aps' => ['content_available' => 0],
                'custom' => array('data' => json_encode($data))
            ]);
            PushNotification::app('YALLATALK_IOS')
                ->to($device_token)
                ->send($message);
        }
    }

    /**
     * function to send notification to android
     *
     * @param  array $data , string $device_token
     */
    public function sendToAndroid($data, $device_token)
    {
        if (!empty($device_token)) {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notificationBuilder = new PayloadNotificationBuilder(__('messages.end_call_message'));
            $notificationBuilder
                ->setBody("The call has ended")
                ->setTitle("End call")
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($data);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            $downstreamResponse = FCM::sendTo($device_token, $option, $notification, $data);

            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }
}
