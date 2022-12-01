<?php

namespace App\Http\Controllers\improveMentFour;

use App\Http\Controllers\Controller;
use App\Model\TopicAdminNote;
use App\TopicNote;
use Illuminate\Http\Request;
use Validator;

class AdminNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = TopicAdminNote::with('user:id,fname,lname')->where(['topic_id'=> $request->topic_id,'is_archived'=>false])->get();
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()]);
        }
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'topic_notes_id' => 'required',
                'workshop_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $notesInfo = TopicNote::find($request->topic_notes_id);
            if ($notesInfo) {
                $res = TopicAdminNote::updateOrcreate(['user_id' => $notesInfo->user_id,
                    'topic_id' => $notesInfo->topic_id,
                    'meeting_id' => $notesInfo->meeting_id,
                    'workshop_id' => $request->workshop_id],
                    ['topic_note' => $notesInfo->topic_note,
                        'user_id' => $notesInfo->user_id,
                        'topic_id' => $notesInfo->topic_id,
                        'is_archived'=>false,
                        'meeting_id' => $notesInfo->meeting_id,
                        'workshop_id' => $request->workshop_id,
                        'notes_updated_at' => $notesInfo->updated_at,
                    ]);
                return response()->json(['status' => true, 'data' => $res], 200);
            }
            return response()->json(['status' => false, 'msg' => 'no data found'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_archived' => 'required',
                'topic_id' => 'required',
            ]);
            if (!empty($id) && $validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $data=TopicAdminNote::where('id',$id)->update(['is_archived' =>$request->is_archived]);
            if ($data) {
                $data = TopicAdminNote::with('user:id,fname,lname')->where(['topic_id'=> $request->topic_id,'is_archived'=>false])->get();
                return response()->json(['status' => true, 'data' => $data], 200);
            } else {
                return response()->json(['status' => false, 'data' => $data], 201);
            }
        } catch (\Exception $e) {
                       return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
