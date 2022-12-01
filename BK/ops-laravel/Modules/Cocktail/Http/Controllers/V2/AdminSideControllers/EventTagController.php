<?php

namespace Modules\Cocktail\Http\Controllers\V2\AdminSideControllers;
use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Cocktail\Entities\EventTag;
use Modules\Cocktail\Http\Requests\V2\EventTagV2Request;
use Modules\Events\Service\ValidationService;


class EventTagController extends Controller
{
    /**
     * @var ValidationService|null
     */
    private $validationService;
    public function __construct() {

        $this->validationService = ValidationService::getInstance();
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        try {
            $result=EventTag::orderBy('name','asc')->get(['name','is_display','id']);
            return response()->json(['status' => true, 'data' => $result]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);

        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('cocktail::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(EventTagV2Request $request)
    {
        try {
            $checkEvent=EventTag::where(['name'=>$request->tag_name])->first();
            if($checkEvent){
                return response()->json(['status' => false, 'msg' =>"Tag already exist"],422);


            }
            $result=EventTag::create(['name'=>$request->tag_name,'created_by'=>\Auth::user()->id]);
            return response()->json(['status' => true, 'data' => $result],201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);

        }
     }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {

    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(EventTagV2Request $request,$id)
    {

        try {
            $tag=EventTag::find($id);
            if($tag){
                $tag->name=$request->tag_name;
                $tag->is_display=$request->is_display;
                $tag->save();
            }
            return response()->json(['status' =>($tag)? true:false, 'data' =>$tag]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $result=EventTag::where('id',$id)->delete();

            return response()->json(['status' =>($result)? true:false, 'data' =>$result]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);

        }
    }
}
