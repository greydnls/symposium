<?php namespace Symposium\Http\Controllers;

use Auth;
use Input;
use Log;
use Redirect;
use Session;
use Talk;
use TalkRevision;
use Validator;
use View;

class TalksController extends BaseController
{
    protected $rules = [
        'title' => 'required',
        'type' => 'required',
        'level' => 'required',
        'length' => 'required|integer|min:0',
    ];

    protected $sorting_talks = [
        'date' => '',
        'alpha' => '',
    ];

    public function __construct()
    {
        $this->beforeFilter(
            'auth'
        );
    }

    /**
     * Display all of the current user's talks
     *
     * @return Response
     */
    public function index()
    {
        $bold_style = 'style="font-weight: bold;"';

        $sorting_talk = $this->sorting_talks;

        switch (Input::get('sort')) {
            case 'date':
                $sorting_talk['date'] = $bold_style;
                $talks = Auth::user()->talks->sortByDesc('created_at');
                break;
            case 'alpha':
                // Pass through
            default:
                $sorting_talk['alpha'] = $bold_style;
                $talks = Auth::user()->talks->sortBy(function ($talk) {
                    return strtolower($talk->current()->title);
                });
                break;
        }

        return View::make('talks.index')
            ->with('talks', $talks)
            ->with('sorting_talk', $sorting_talk);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('talks.create')
            ->with('current', new TalkRevision);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $validator = Validator::make(Input::all(), $this->rules);

        if ($validator->passes()) {
            // Save
            $talk = new Talk;
            $talk->author_id = Auth::user()->id;
            $talk->save();

            $revision = new TalkRevision;
            $revision->title = Input::get('title');
            $revision->type = Input::get('type');
            $revision->length = Input::get('length');
            $revision->level = Input::get('level');
            $revision->description = Input::get('description');
            $revision->organizer_notes = Input::get('organizer_notes');
            $revision->talk_id = $talk->id;
            $revision->save();

            Session::flash('message', 'Successfully created new talk.');

            return Redirect::to('/talks/' . $talk->id);
        }

        return Redirect::to('talks/create')
            ->withErrors($validator)
            ->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  str  $talkId
     * @return Response
     */
    public function edit($talkId)
    {
        try {
            $talk = Auth::user()->talks()->findOrFail($talkId);
        } catch (Exception $e) {
            Session::flash('error-message', 'Sorry, but that isn\'t a valid URL.');
            Log::error($e);
            return Redirect::to('/');
        }

        return View::make('talks.edit')
            ->with('current', $talk->current());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  str  $talkId
     * @return Response
     */
    public function update($talkId)
    {
        $validator = Validator::make(Input::all(), $this->rules);

        if ($validator->passes()) {
            $talk = Auth::user()->talks()->findOrFail($talkId);

            $revision = new TalkRevision;
            $revision->title = Input::get('title');
            $revision->type = Input::get('type');
            $revision->length = Input::get('length');
            $revision->level = Input::get('level');
            $revision->description = Input::get('description');
            $revision->organizer_notes = Input::get('organizer_notes');
            $revision->talk_id = $talk->id;
            $revision->save();

            Session::flash('message', 'Successfully edited talk.');

            return Redirect::to('talks/' . $talk->id);
        }

        return Redirect::to('talks/' . $talkId . '/edit')
            ->withErrors($validator)
            ->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return Response
     */
    public function show($id)
    {
        $talk = Auth::user()->talks()->findOrFail($id);
        $current = Input::has('revision') ? $talk->revisions()->findOrFail(Input::get('revision')) : $talk->current();

        return View::make('talks.show')
            ->with('talk', $talk)
            ->with('showingRevision', Input::has('revision'))
            ->with('current', $current);
    }

    /**
     * Show the confirmation for deleting the specified resource
     *
     * @param  int  $id
     * @return Resource
     */
    public function delete($id)
    {
        dd('t');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Auth::user()->talks()->findOrFail($id)->delete();

        Session::flash('message', 'Successfully deleted talk.');

        return Redirect::to('talks');
    }
}
