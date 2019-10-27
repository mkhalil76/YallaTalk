<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\Topic;
use Validator;
use Image;
use DataTables;

class TopicsController extends Controller
{
    /**
     * function to get all topics
     *
     * @param
     *
     * @return  view
     */
    public function index()
    {
        $topics = Topic::all();
        return view('topics.index', compact('topics'));
    }

    /**
     * function to get topics for datatabel
     *
     * @param  Request $request
     *
     * @return  DataTables;
     */
    public function getTopicsForDataTable()
    {
        $topics = Topic::query();
        return DataTables::of($topics)
            ->addColumn('actions', function ($topics) {
                $actions = '<a href="'.url('admin/topics/update/'.$topics->id).'"><button class="btn btn-primary btn-sm">update</button></a>'.'-'.'<a href="'.url('admin/topics/delete/'.$topics->id).'"><button class="btn btn-danger btn-sm">update</button></a>';

                return  $actions;
            })->editColumn('topic_icon', function ($topics) {
                $icon = '<img src="'.$topics->topic_icon.'"
                    width="120" height="120">';
                return $icon;
            })->editColumn('created_at', function ($topics) {
                return date("Y-m-d", strtotime($topics->created_at));
            })->rawColumns(['actions','topic_icon'])->make(true);
    }

    /**
     * function to delete topic
     *
     * @param int $topic_id
     *
     * @return  response
     *
     */
    public function delete($topic_id)
    {
        $topic = Topic::findOrFail($topic_id);
        if ($topic->delete()) {
            return redirect()->back()->with([
                'success' => 'topic deleted Successfully'
            ]);
        }
    }

    /**
     * function to update topic
     *
     * @param  int $topic_id
     *
     * @return  response
     *
     */
    public function update($topic_id)
    {
        $topic = Topic::findOrFail($topic_id);
        return view('topics.update', compact('topic'));
    }

    /**
     * function to post update topic
     *
     * @param  int $topic_id , Request $request
     *
     * @return  response
     */
    public function postUpdate($topic_id, Request $request)
    {
        $input = $request->only(
            'topic_icon',
            'topic_name'
        );

        $validator = Validator::make($input, [
            'topic_icon' => 'required',
            'topic_name' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        $filename = $request->topic_icon->store('photos');
        $filename = asset('/uploads/photos').'/'.$filename;
        $topic = Topic::findOrFail($topic_id);
        $topic->topic_name = $request->topic_name;
        $topic->icon = $filename;
        
        if ($topic->save()) {
            return redirect('admin/topics')->with([
                'success' => 'topic updated Successfully'
            ]);
        }
    }

    /**
     * function to show create new topic page
     *
     * @return view
     *
     */
    public function create()
    {
        return view('topics.create');
    }

    /**
     * function to post create new topic
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function postCreate(Request $request)
    {
        $input = $request->only(
            'topic_icon',
            'topic_name'
        );

        $validator = Validator::make($input, [
            'topic_icon' => 'required',
            'topic_name' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        $filename = $request->topic_icon->store('photos');
        $filename = asset('/uploads/photos').'/'.$filename;
        $topic = new Topic;
        $topic->topic_name = $input['topic_name'];

        $topic->icon = $filename;

        if ($topic->save()) {
            return redirect('admin/topics')->with([
                'success' => 'topic created Successfully'
            ]);
        }
    }
}
