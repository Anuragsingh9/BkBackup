<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\AssistanceReport;
use Modules\Crm\Entities\CrmNote;
use Modules\Crm\Services\AssistanceReportService;
use Validator;
use DB;

class AssistanceReportController extends Controller
{
    private $notesServices;

    public function __construct()
    {

        $this->notesServices = AssistanceReportService::getInstance();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
//        $notes = CrmNote::with('user')->latest()->get();
//        return response()->json([
//            'status' => TRUE,
//            'data' => $notes
//        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('crm::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
      
        try {
            //start transaction for skip the wrong entry
            $note = DB::transaction(function () use ($request) {
                $validator = Validator::make($request->all(), [
                    'reports' => 'required',
                    'crm_assistance_type_id' => 'required',
                    'field_id' => 'required',
                    'type' => 'required',
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //sharing Array of object
                return $note = $this->notesServices->addReport($request->all());

            });
            return response()->json([
                'status' => TRUE,
                'data' => $note
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id, $type,$assId=0)
    {
        try {
            if (!empty($id) && !empty($type))
                $reports = $this->notesServices->getReports($id, $type,$assId);
            return response()->json([
                'status' => TRUE,
                'data' => $reports
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        try {
            $report = AssistanceReport::findOrFail($id);
            return response()->json([
                'status' => TRUE,
                'data' => $report
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            //start transaction for skip the wrong entry
            $report = DB::transaction(function () use ($request, $id) {
                $validator = Validator::make($request->all(), [
                    'reports' => 'required',
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //sharing Array of object
                return $note = $this->notesServices->editReport($request->all(), $id);

            });
            return response()->json([
                'status' => TRUE,
                'data' => $report
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $note = AssistanceReport::whereId($id)->delete();
            return response()->json([
                'status' => TRUE,
                'data' => 'Deleted sucessfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }
}
