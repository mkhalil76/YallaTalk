<?php

namespace YallaTalk\Http\Modules\Api\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\Topic;
use Illuminate\Support\Facades\Cache;
use Yallatalk\Models\Client;
use YallaTalk\Models\ClientLanguage;
use Auth;
use YallaTalk\Models\ServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use UserHelper;
use YallaTalk\Models\ServiceProviderTopics;

class TopicController extends Controller
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * function to get all topics and cache them
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        Cache::get('topics', Topic::all(), 10);
        $topics = Cache::get('topics');
        return response()->json(['topics'=> $topics]);
    }

    /**
     * get the service providers for the topic
     *
     * @param int $topic_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serviceProvider($topic_id, $practing_language = null)
    {
        $user = Auth::user();
        $client_id = UserHelper::getClientID($user->id);
        $client = Client::findOrFail($client_id);
        $online_providers = UserHelper::onlineProvidersList();
        $client_languages = ClientLanguage::where('client_id', '=', $client_id)
            ->pluck('language_id')
            ->toArray();
        if ($topic_id == "all") {
            if (!isset($practing_language)) {
                $provider_list = ServiceProvider::with('user')
                    ->where('availability', '=', 1)
                    ->get();
            } else {
                $provider_list = ServiceProvider::with('user')
                    ->where('availability', '=', 1)
                    ->whereHas('language', function ($query) use ($practing_language) {
                        $query->where('id', '=', $practing_language);
                    })->get();
            }
            return response()->json([
                'service_provider' => $provider_list
            ]);
        }
        if ($user->is_updated == 0) {
            if (!isset($practing_language)) {
                $provider_list = ServiceProvider::with('user')
                    ->where('availability', '=', 1)
                    ->get();
            } else {
                $provider_list = ServiceProvider::with('user')
                    ->where('availability', '=', 1)
                    ->whereHas('language', function ($query) use ($practing_language) {
                        $query->where('id', '=', $practing_language);
                    })->get();
            }
            return response()->json([
                'service_provider' => $provider_list
            ]);
        } else {
            try {
                $topic = Topic::findOrFail($topic_id);
                if (!isset($practing_language)) {
                    $service_provider = ServiceProvider::with('user')
                        ->where('availability', '=', 1)
                        ->whereHas('topic', function ($query) use ($topic_id) {
                            $query->where('id', $topic_id);
                        })->get();
                } else {
                    $service_provider = ServiceProvider::with('user')
                        ->where('availability', '=', 1)
                        ->whereHas('language', function ($query) use ($practing_language) {
                            $query->where('id', '=', $practing_language);
                        })->whereHas('topic', function ($query) use ($topic_id) {
                            $query->where('id', $topic_id);
                        })->get();
                }
                return response()->json(['service_provider' => $service_provider]);
            } catch (ModelNotFoundException $ex) {
                return response()
                ->json([
                    'error' => 'there is no topic with this id = '.$topic_id
                ]);
            }
        }
    }

    /**
     * function to search for topics
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsSearch(Request $request)
    {
        $topic_name = $request->query('topic_name');
        
        if (!empty($topic_name)) {
            $topics = Topic::where('topic_name', 'like', '%'.$topic_name.'%')->get();
            return response()->json([
                'topics' => $topics
            ]);
        } else {
            $topics = Topic::all();
            return response()->json([
                'topics' => $topics
            ]);
        }
    }

    /**
     * function to update / add Service provider topics
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsUpdate(Request $request)
    {
        $user = Auth::user();

        $provider_id = UserHelper::getServiceProviderID($user->id);
        $provider = ServiceProvider::FindOrFail($provider_id);
        
        $topics = json_decode($request->topics);

        foreach ($topics as $topic) {
            $topics = ServiceProviderTopics::updateOrCreate([
                'service_provider_id' => $provider_id,
                'topic_id' => $topic
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => __('messages.provider_topic_update')
        ]);
    }

    /**
     * function to add new topic for the service provider
     *
     * @param Request $request
     *
     * @return  response
     */
    public function addTopic(Request $request)
    {
        $user = Auth::user();
        $topic_id = $request->topic_id;
        $provider_id = UserHelper::getServiceProviderID($user->id);

        $topic = ServiceProviderTopics::where('service_provider_id', '=', $provider_id)
            ->where('topic_id', '=', $topic_id)
            ->first();
        $topic_info = Topic::findOrFail($topic_id);
        if (empty($topic)) {
            $topic = new ServiceProviderTopics;
            $topic->service_provider_id = $provider_id;
            $topic->topic_id = $topic_id;

            if ($topic->save()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.provider_topic_update'),
                    'topic' => $topic_info
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.provider_topic_already_exist'),
                'topic' => $topic_info
            ]);
        }
    }

    /**
     * function to delete the topic
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function deleteTopic(Request $request)
    {
        $user = Auth::user();
        $topic_id = $request->topic_id;
        $provider_id = UserHelper::getServiceProviderID($user->id);

        $topic = ServiceProviderTopics::where('service_provider_id', '=', $provider_id)
            ->where('topic_id', '=', $topic_id)
            ->forceDelete();
        return response()->json([
            'success' => true,
            'message' => __('messages.provider_topic_deleted'),
        ]);
    }

    /**
     * function to get the provider topics
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function getProviderTopics(Request $request)
    {
        $user = Auth::user();
        $provider_id = UserHelper::getServiceProviderID($user->id);

        $provider = ServiceProvider::findOrFail($provider_id);

        return response()->json([
            'success' => true,
            'topics' => $provider->topic->toArray(),
        ]);
    }
}
