<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\GoogleCalendars;
use App\Models\Meeting;
use App\Models\Member;
use App\Services\Google;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.meeting.index');
    }

    public function meetingsDatatable(){
        $manager = Auth::user()->member()->first();
        $meetings = Meeting::where('manager_id', $manager->id);
        return DataTables::of($meetings)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                         ->editColumn('created_at', function($row) {
                             return $row->created_at->toFormattedDateString();
                         })
                         ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_id' => 'required',
        ]);

        $input = $request->all();
        $manager = Auth::user()->member()->first();
        $target = Member::find($input['target_id']);
        if ($manager)
            $meet = Meeting::create([
                'manager_id' => $manager->id,
                'target_id' => $input['target_id'],
                'startAt' => Carbon::now(),
                'title' => $manager->firstName.' x '.$target->firstName
            ]);
        else $meet=null;
        die(json_encode($meet->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        if ($this->authorize('update', $meeting)) {
            $teams = $meeting->target()->first()->teams()->pluck( 'teams.id' )->toArray();

            return view( 'client.meeting.edit' )->with( [
                'meeting' => $meeting,
                'teams'   => $teams
            ] );
        }else abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        $input = $request->all();
        $meeting->update($input);

        return redirect('client/1-1');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        //
    }

    public function schedule(Meeting $meeting){
        return view('client.meeting.schedule')->with([
            'event' => null,
            'meeting' => $meeting
        ]);
    }

    public function schedule_edit(Meeting $meeting){
        if ( ($meeting->google_event_id)&&(Auth::user()->google_calendar_id) )
            $event = GoogleCalendars::get_event(
                Auth::user()->google_calendar_id,
                $meeting->google_event_id
            );
        else return abort(404);

        return view('client.meeting.schedule')->with([
            'event' => $event,
            'meeting' => $meeting
        ]);
    }

    public function schedule_store(Request $request, Meeting $meeting){
        $validated = $request->validate([
            'title' => 'required',
            'start_date' => 'required'
        ]);

        $input = $request->all();
        $summary = $input['summary'];
        $summary .= '<br>1-on-1:'. route('client.meeting.edit', $meeting);
        try {
            $event = GoogleCalendars::create_event(
                Auth::user()->google_calendar_id,
                $input['title'],
                $input['start_date'],
                $summary,
                $meeting->target()->first()
            );
            if ($event)
                $meeting->update([
                    'google_event_id' => $event->id
                ]);
        }catch(\Exception $e) { }

        die(0);
    }
}
