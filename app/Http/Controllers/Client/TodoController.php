<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;

class TodoController extends Controller
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

    public function todosDatatable(Meeting $meeting){
        $meeting_todos = Todo::where('meeting_id', $meeting->id);
        $old_todos = Todo::select('todos.*')->join("meetings","meetings.id","=","todos.meeting_id")
                                            ->where('manager_id',$meeting->manager_id)
                                            ->where('target_id', $meeting->target->id)
                                            ->where('done',0);
        $todos = $meeting_todos->union($old_todos);
        return DataTables::of($todos)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                         ->editColumn('due_date', function($row) {
                            if ($row->due_date) {
                                //$date = Carbon::parse($row->due_date);
                                return $row->due_date->toFormattedDateString();
                            }else return '';
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
    public function store(Request $request, Meeting $meeting)
    {
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][0];

        $validator = Validator::make($input,[ //$validated = $request->validate([
            'note' => 'required',
            'due_date' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $field=>$message){
                $errors[] = array( 'name' => $field, 'status' => $message[0] );
            }
            $data = array('fieldErrors'=> $errors);
        }else{

            $todo = Todo::create([
                'note' => $input['note'],
                'due_date' => $input['due_date'],
                'meeting_id' => $meeting->id
            ]);

            $data = array('data'=> array($todo));
        }

        die(json_encode($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting, Todo $todo)
    {
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][$todo->id];

        $validator = Validator::make($input,[ //$validated = $request->validate([
            'note' => 'required'
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $field=>$message){
                $errors[] = array( 'name' => $field, 'status' => $message[0] );
            }
            $data = array('fieldErrors'=> $errors);
        }else {
            $todo->update( $input );
            $data = array( 'data' => array( $todo ) );
        }

        die(json_encode($data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting, Todo $todo)
    {
        $todo->delete();

        die(json_encode(array()));
    }
}
