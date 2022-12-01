<?php

namespace Modules\Qualification\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Qualification\Entities\VoteItem;
use Validator;
use DB;

class VoteItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            //feth the list of voteItems
            $voteItem = VoteItem::get(['id','option_name','short_name','description','option_color','option_tip_text','created_at']);
            return response()->json(['status' => true, 'data' => $voteItem], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('qualification::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'vote_id' => 'required',
                'option_name' => 'required',
                'short_name' => 'required',
                'option_color' => 'required',
                'option_tip_text' => 'required'
            ]);
            if($validator->fails()){
                return response()->json(['status'=>true, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            DB::beginTransaction();
            $voteItem = VoteItem::create([
                'vote_id' => $request->id,
                'option_name' => $request->option_name,
                'short_name' => $request->short_name,
                'option_color' => $request->option_color,
                'option_tip_text'=>$request->option_tip_text,
            ]);
            DB::commit();
            return response()->json(['status'=>true, 'msg' => 'Record Added Successfully','data'=>$voteItem]);
        } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('qualification::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        try{
            $voteItem = VoteItem::find($id);
            if(!$voteItem){
            return Response()->json(['status'=>false,'msg'=>'not found'],200);
            }
            return response()->json(['status'=>true , 'data'=>$voteItem]);
        } catch (\Exception $e) {
        return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        try{
            $validator = Validator::make($request->all(),[
                'vote_id' => 'required',
                'option_name' => 'required',
                'short_name' => 'required',
                'option_color' => 'required',
                'option_tip_text' => 'required'
            ]);
            if($validator->fails()){
                return response()->json(['status'=>true, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            DB::beginTransaction();
            $voteItem = VoteItem::whereId($id)->update([
                'vote_id' => $request->id,
                'option_name' => $request->option_name,
                'short_name' => $request->short_name,
                'description' => $request->description,
                'option_color' => $request->option_color,
                'option_tip_text'=>$request->option_tip_text,
            ]);
            DB::commit();
            return response()->json(['status'=>true, 'msg' => 'Record Updated Successfully','data'=>$voteItem]);
        } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
            }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
     
     DB::beginTransaction();
        try {
            VoteItem::find($id)->delete();
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Record Deleted Successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false,'msg' => $e->getMessage()], 500);
        }
    }
}
