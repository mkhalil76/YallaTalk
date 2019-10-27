<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\User;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use YallaTalk\Models\ServiceProvider;
use YallaTalk\Models\Client;
use DataTables;
use UserHelper;
use Cache;
use YallaTalk\Models\Appointment;

class UsersController extends Controller
{
    /**
     * function to show the user profile
     *
     * @param int $user_id
     *
     * @return  view
     */
    public function show($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('users.show', compact('user'));
    }

    /**
     * function to update user info
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // validation rule
        $rules = [
            'email' => 'required|email',
        ];
        // grab input from the request
        $input = $request->only('email');

        $validator = Validator::make($input, $rules);

        //check for validation
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        try {
            $user = User::findOrFail($request->user_id);

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->country = $request->country;
            $user->address1 = $request->address1;
            $user->address2 = $request->address2;
            $user->email = $request->email;

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            if ($user->save()) {
                return redirect()->back()->with([
                    'success' => 'user updated Successfully'
                ]);
            }
        } catch (ModelNotFoundException $ex) {
            return redirect()->back()->with([
                'error' => 'user not found'
            ]);
        }
    }

    /**
     * function to get service provider list
     *
     * @return  Illuminate\view
     *
     */
    public function serviceProviders()
    {
        $providers = ServiceProvider::with('bankAccount')->with('user')->orderBy('created_at', 'DESC')->get();
        return view('users.service-providers.index', compact('providers'));
    }

    /**
     * function to show clients list
     *
     * @return  Illuminate\view
     *
     */
    public function client()
    {
        $clients = Client::all();
        return view('users.clients.index', compact('clients'));
    }

    /**
     * function to show the admins
     *
     * @return  Illuminate\view
     */
    public function admin()
    {
        $admins = User::where('user_type', '=', 3)->get();
        return view('users.admin', compact('admins'));
    }

    /**
     * function to get admins for datatable
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function adminsForDatatable()
    {
        $admins = User::where('user_type', '=', 3)->get();
        return DataTables::of($admins)->make(true);
    }

    /**
     * function to get clients for datatable
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function clientsForDatatable()
    {
        $clients = Client::query();

        return DataTables::of($clients)
            ->editColumn('user_name', function ($clients) {
                $user_info = UserHelper::getUserInfo($clients->user_id);
                return $user_info->first_name." ".$user_info->last_name;
            })->editColumn('avatar', function ($clients) {
                if ($clients->image != null) {
                    $avatar = '<img src="data:image/png;base64,'.$clients->image.'" width="60" height="60"/>';
                } else {
                    $avatar = '<img src="https://image.flaticon.com/icons/png/512/126/126486.png" width="60" height="60">';
                }

                return $avatar;
            })->editColumn('avaliabilty', function ($clients) {
                return UserHelper::getUserAvailability($clients->availability);
            })->editColumn('account_status', function ($clients) {
                return UserHelper::getAcountStatus($clients->account_status);
            })->editColumn('gender', function ($clients) {
                return UserHelper::getUserGender($clients->gender);
            })->rawColumns(['avatar','avaliabilty','account_status','gender'])->make('true');
    }

    /**
     * function to get service providers for datatables
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function providersForDatatable(Request $request)
    {
        $providers = ServiceProvider::query();

        return DataTables::of($providers)
            ->editColumn('user_name', function ($providers) {
                $user_info = UserHelper::getUserInfo($providers->user_id);
                return $user_info->first_name." ".$user_info->last_name;
            })->editColumn('avatar', function ($providers) {
                if ($providers->image != null) {
                    $avatar = '<img src="data:image/png;base64,'.$providers->image.'" width="60" height="60"/>';
                } else {
                    $avatar = '<img src="https://image.flaticon.com/icons/png/512/126/126486.png" width="60" height="60">';
                }

                return $avatar;
            })->editColumn('avaliabilty', function ($providers) {
                return UserHelper::getUserAvailability($providers->availability);
            })->editColumn('account_status', function ($providers) {
                return UserHelper::getAcountStatus($providers->account_status);
            })->editColumn('gender', function ($providers) {
                return UserHelper::getUserGender($providers->gender);
            })->editColumn('call_type', function ($providers) {
                return UserHelper::getCallType($providers->call_type);
            })->rawColumns([
                'avatar',
                'avaliabilty',
                'account_status',
                'gender',
                'call_type'
            ])->make('true');
    }


    /**
     * function to show the users appointments
     *
     * @return  view
     */
    public function appointment()
    {
        $status = [
            'PENDING' => 'orange',
            'REJECTED' => 'red',
            'APPROVED' => 'green',
        ];
        Cache::forever('status', $status);
        $appointments = Appointment::all();
        
        $format = [];
        foreach ($appointments as $appointment) {
            $format[] = [

                'title' => $appointment->status,
                'start' => date("Y-m-d", strtotime($appointment->start_time)),
                'start_time' => date("h:i A", strtotime($appointment->start_time)),
                'end' => $appointment->end_time,
                'end_time' => date("h:i A", strtotime($appointment->end_time)),
                'allDay' => false,
                'backgroundColor' => $status[$appointment->status],
                'borderColor' => $status[$appointment->status],
                'provider' => $appointment->service_provider_id,
                'client' => $appointment->client_id,
                'calender_id' => $appointment->id
            ];
        }

        return view('users.appointment', compact('appointments'))->with([
            'appointments' => json_encode($format)
        ]);
    }

    /**
     * function to update calender
     *
     * @param  Request $request
     *
     * @return  reponse
     *
     */
    public function updateAppointment(Request $request)
    {
        $start_time = date("Y-m-d", strtotime($request->start_date))." ".str_replace(["AM","PM"," "], "", $request->start_time).":00";

        $end_time = date("Y-m-d", strtotime($request->end_date))." ".
                str_replace(["AM","PM"," "], "", $request->end_time).":00";

        if ($request->calendar_id == null) {
            $appointment = new Appointment;
            $appointment->start_time = $start_time;
            $appointment->end_time = $end_time;
            $appointment->service_provider_id = $request->provider;
            $appointment->client_id = $request->clients;
            $appointment->call_type = $request->call_type;

            if ($appointment->save()) {
                return redirect()->back()->with([
                    'success' => 'Calender Updated Successfully'
                ]);
            }
        } else {
            $appointment = Appointment::findOrFail($request->calendar_id);
            if ($request->status == "CANCEL") {
                $appointment->delete();
                    return redirect()->back()->with([
                        'success' => __('messages.update_calender_message')
                    ]);
            } else {
                $appointment->status = $request->status;
                $appointment->start_time = $start_time;
                $appointment->end_time = $start_time;
                $appointment->service_provider_id = $request->provider;
                $appointment->client_id = $request->clients;

                if ($appointment->save()) {
                    return redirect()->back()->with([
                        'success' => __('messages.update_calender_message')
                    ]);
                }
            }
        }
    }

    /**
     * function to activate account again
     *
     * @param Request $request
     *
     * @return  response
     *
     */
    public function defreezAccount($provider_id)
    {
        $provider_info = ServiceProvider::findOrFail($provider_id);

        $provider_info->account_status = 1;

        if ($provider_info->save()) {
            return redirect()->back()->with([
                'success' => 'Account Activated Successfully'
            ]);
        }
    }

    /**
     * function to show in active accounts
     *
     *
     * @return  view
     */
    public function inactiveAccounts()
    {
        $providers = ServiceProvider::where('account_status', '!=', 1)->get();

        return view('users.service-providers.freez-account')
            ->with([
                'providers' => $providers
            ]);
    }

    /**
     * function to return yalltalk share invitation code
     * 
     * @param string $invitation_code
     * 
     * @return  string
     * 
     */
    public function getShareLink($code)
    {   
        echo __('messages.share_link_message').$code;
    } 
}
