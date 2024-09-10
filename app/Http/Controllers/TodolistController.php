<?php

namespace App\Http\Controllers;

use App\Models\Todolist;
use Illuminate\Http\Request;


class TodolistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('todolist');
    }


    /**
     * Display the specified resource by ajax request.
     *
     * @param  \App\Todolist  $Todolist
     * @return \Illuminate\Http\Response
     */
    public function getLists(Request $request) {
        $order_by = array();
        $length = $request->length;
        $start = $request->start;
        $columnData = array(
            'id',
            'task',
            'taskStatus',
            'created_at',
            'action'
        );
        $sortData = $request->order;
        $order_by[0] = $columnData[$sortData[0]['column']];
        $order_by[1] = $sortData[0]['dir'];
        $searchData = $request->searchBox;

        $totalData = Todolist::count();

        $totalFiltered = $totalData;
        $jsonArray = array(
            'draw' => $request->draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalData,
            'data' => array(),
        );

        $listQuery = Todolist::select('id', 'task', 'taskStatus', 'created_at');
        if (!empty($request->task)) {
            $task = $request->task;
            $posted_array['task'] = $request->task;
            $posted_array['taskDescription'] = 'NA';
            if (!Todolist::where('task', '=', $request->task)->exists()) {
               Todolist::create($posted_array);
            } 
        }

        if (!empty($request->showall)) {

        }else{
            $listQuery->where('taskStatus', '=', 0);
        }
        $totalFiltered = $listQuery->count();
        $tasklists = $listQuery->offset($start)
                ->limit($length)
                ->orderBy($order_by[0], $order_by[1])
                ->get();

        $data = array();

        $statusArray = array(
            0 => 'Non Completed',
            1 => 'Completed',
            2 => 'In-progress'
        );

        if (!empty($tasklists)) {
            $btn = '';
            foreach ($tasklists as $key => $task) {

                $btn = '<a href="javascript:void(0);" rel="'.$task->id.'" data-token="'.csrf_token().'" class="btn btn-success btn-sm changeStatus" title="Status"><i class="fa fa-check changeStatus" rel="'.$task->id.'" title="Status"></i></a>';

                $btn = $btn.' <a href="javascript:void(0);" rel="'.$task->id.'" data-token="'.csrf_token().'" class="btn btn-danger btn-sm deleteRecord" title="Detele"><i class="fa fa-trash-o deleteRecord" rel="'.$task->id.'" title="Delete"></i></a>';

                $nestedData['sr_no'] = $start + $key + 1;
                $nestedData['task'] = $task->task;
                $nestedData['taskStatus'] = isset($statusArray[$task->taskStatus])?$statusArray[$task->taskStatus]:'Not Completed';
                $nestedData['created_at'] = date('j M Y h:i a', strtotime($task->created_at));
                $nestedData['action'] = $btn;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todolist  $todolist
     * @return \Illuminate\Http\Response
     */
    public function show(Todolist $todolist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Todolist  $todolist
     * @return \Illuminate\Http\Response
     */
    public function edit(Todolist $todolist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todolist  $todolist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todolist $todolist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todolist  $todolist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todolist $todolist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todolist  $Todolist
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request) {
        $sID = $request->sID;
        $jsonArray = array('flag' => false);
        if (Todolist::destroy($sID)) {
            $jsonArray['flag'] = true;
        }
        echo json_encode($jsonArray);
        exit;
    }

    /**
     * Change the status of specified resource from storage.
     *
     * @param  \App\Models\Todolist  $todolist
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request) {
        $sID = $request->sID;
        $jsonArray = array('flag' => false);
        if (Todolist::where('id', '=', $sID)->update(['taskStatus' => '1'])) {
            $jsonArray['flag'] = true;
        }
        echo json_encode($jsonArray);
        exit;
    }
}
