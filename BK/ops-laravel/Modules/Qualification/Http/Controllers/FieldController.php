<?php
namespace Modules\Qualification\Http\Controllers;

use App\Model\SkillTabs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Qualification\Entities\Field;
use Validator;
use DB;
use App\Model\Skill;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            //feth the list of steps
                                  //,is_valid,is_mandatory'
            $field = Field::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->with('skill:id,name,short_name')->get(['id', 'step_id', 'field_id', 'sort_order']);
            if(!$field){
                return Response()->json(['status'=>false,'msg'=>'no data found','data'=> []],200);
            }
            return response()->json(['status' => true, 'data' => $field], 200);
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
     * @param step_id from steps table
     * @param field_id from skills table
     * @return Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'step_id' => 'required|exists:tenant.qualification_steps,id',
                'field_id' => 'required|exists:tenant.skills,id'
            ]);
            if($validator->fails()){
                return response()->json(['status'=>false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            DB::beginTransaction();
            $field = Field::create([
                'step_id' => $request->step_id,
                'field_id' => $request->field_id
            ]);
            DB::commit();
            return response()->json(['status'=> true,'msg'=>'Record Added Successfully','data'=>$field]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        try{                     
            $field = Field::find($id); 
            if(!$field){
                return response()->json(['status'=>false,'msg'=>'data not foubd','data'=> []],200);
            }
            return response()->json(['status'=>true , 'data'=>$field]);
       }
       catch (\Exception $e) {
           return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
       }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    // public function edit($id)
    // {
    //     try{
    //         $field = Field::find($id);
    //         if(!$field){
    //             return response()->json(['status'=>false,'msg'=>'data not foubd','data'=> []],200);
    //         }
    //         return response()->json(['status'=>true , 'data'=>$field]);
    //    }
    //    catch (\Exception $e) {
    //        return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
    //    }
    // }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
        try{
            /**
             * validation check that step_id exists in qualification_steps table
             * validation check that field_id exists in skills table
             */
            $validator = Validator::make($request->all(),[
                'step_id' => 'required|exists:tenant.qualification_steps,id',
                'field_id' => 'required|exists:tenant.skills,id'
            ]);
            if($validator->fails()){
                return response()->json(['status'=>false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            //tarnsaction start
            DB::beginTransaction();
            $field = Field::where('id',$id)->update([
                'step_id' => $request->step_id,
                'field_id' => $request->field_id
            ]);
            if(!$field){
                return Response()->json(['status'=>false,'msg'=>'no data for update','data'=> []],200);
            }
            //tarnsaction commit
            DB::commit();
            return response()->json(['status'=> true,'msg'=>'Record Added Successfully','data'=>$field]);
        }catch (\Exception $e) {
            //tarnsaction rollback
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
        //tarnsaction
        DB::beginTransaction();
        try {
           $field= Field::find($id);
            if(!$field){
                return Response()->json(['status'=>false,'msg'=>'no data for delete'],200);
            }
            $field->delete();
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Record Deleted Successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false,'msg' => $e->getMessage()], 500);
        }

    }

    public function getQualificationSkill($id, $keyword)
    {
        try {
            $keyword = ltrim($keyword);
            $keyword = rtrim($keyword);
            if (!empty($keyword) && strlen($keyword) >= 3) {
                $tabs = SkillTabs::where(['tab_type' => 5, 'is_valid' => 1])->get(['id']);
                $fields = Field::where('step_id', $id)->pluck('field_id');
                $skills = Skill::whereIn('skill_tab_id', $tabs->pluck('id'))->whereNotIn('id', $fields)->where(function ($query) use ($keyword) {
                    $query->orWhereRaw("LOWER(name) like  LOWER('%$keyword%')")->orWhereRaw("LOWER(short_name) like  LOWER('%$keyword%')");
                })->where('is_valid', 1)/*->where('is_qualifying', 1)COLLATE utf8mb4_bin COLLATE utf8mb4_bin*/
                ->get(['id', 'name', 'short_name']);

                return response()->json([
                    'status' => TRUE,
                    'data' => $skills
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => []
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function fieldDrag(Request $request)
    {
        $data = json_decode($request->data);
        if (count($data) > 0) {
            foreach ($data as $k => $val) {

                $setting = Field::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
            }
            $steps = Field::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->where('step_id', $request->step_id)->get();
            return response()->json(['status' => true, 'data' => $steps], 200);
        }
    }
}
