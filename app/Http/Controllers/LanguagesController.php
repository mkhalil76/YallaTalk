<?php

namespace YallaTalk\Http\Controllers;

use Illuminate\Http\Request;
use YallaTalk\Models\Language;
use Validator;
use DB;
use DataTables;

class LanguagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * function to get all languages
     *
     * @return  view
     */
    public function index()
    {
        return view('languages.index', compact('languages'));
    }

    /**
     * function to get languages for datatabel
     *
     * @param Request $request
     *
     * @return  response
     */
    public function languagesForDataTable()
    {
        $languages =      Language::query();
        
        return DataTables::of($languages)
            ->addColumn('actions', function ($languages) {
                $actions = '<a href="'.url('admin/languages/update/'.$languages->id).'"><button class="btn btn-primary btn-sm">Update</button></a>'.'-'.'<a href="'.url('admin/languages/delete/'.$languages->id).'"><button class="btn btn-danger btn-sm">Delete</button></a>';

                return  $actions;
            })->editColumn('created_at', function ($languages) {
                return date("Y-m-d", strtotime($languages->created_at));
            })->rawColumns(['actions'])->make(true);
    }

    /**
     * function to show create language page
     *
     * @return  view
     */
    public function create()
    {
        return view('languages.create');
    }

    /**
     * function to post create new language
     *
     * @param  Request $request
     *
     * @return  response
     */
    public function postCreate(Request $request)
    {
        $input = $request->only(
            'name'
        );

        $validator = Validator::make($input, [
            'name' => 'required|unique:languages'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        $language = new Language;
        $language->name = $request->name;

        if ($language->save()) {
            return redirect('admin/languages')->with([
                'success' => "language created Successfully"
            ]);
        }
    }

    /**
     * function to show update language page
     *
     * @param int $language_id
     *
     * @return view
     */
    public function update($language_id)
    {
        $language = Language::findOrFail($language_id);
        return view('languages.update', compact('language'));
    }
    /**
     * function to post update language
     *
     * @param int $language_id , Request $request
     *
     * @return  reponse
     */
    public function postUpdate($language_id, Request $request)
    {
        $input = $request->only(
            'name'
        );

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return redirect()->back()->with([
                'error' => $error
            ]);
        }

        $language = Language::findOrFail($language_id);
        $language->name = $request->name;

        if ($language->save()) {
            return redirect('admin/languages')->with([
                'success' => "language updated Successfully"
            ]);
        }
    }

    /**
     * function to delete language
     *
     * @param int $language_id
     *
     * @return  response
     */
    public function delete($language_id)
    {
        $language = Language::findOrFail($language_id);
        if ($language->delete()) {
            return redirect()->back()->with([
                'success' => "language deleted Successfully"
            ]);
        }
    }
}
