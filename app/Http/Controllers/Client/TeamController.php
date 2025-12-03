<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function manage_teams()
    {
        return view('client.team.manage');
    }

    public function get_teams(){
        $teams = Team::where('organization_id', Auth::user()->organization()->id)->pluck('name','id');
        die(json_encode($teams));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('client.team.create');
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
            'name' => 'required|max:255',
        ]);

        $input = $request->all();

        Team::create([
            'name' => $input['name'],
            'organization_id' => Auth::user()->organization()->id
        ]);

        die(0);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        return view('client.team.edit')->with([
            'team'=>$team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);

        $input = $request->all();

        $team->update($input);

        die(0);
    }

    public function add_members(Request $request, Team $team)
    {
        $input = $request->all();

        if ( isset( $input['members']) ){
            $members = explode(',', $input['members']);
            $team->members()->withTimestamps()->syncWithoutDetaching($members);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();
        die(0);
    }

    public function delete_member(Request $request, Team $team)
    {
        $input = $request->all();

        if ( isset( $input['member']) ){
            $team->members()->detach($input['member']);
        }
    }

    /*
    public function goals(Request $request, Team $team){
        $goals = $team->goals()->get()->toJson(JSON_PRETTY_PRINT);
        die( $goals );
    }
    */
}
