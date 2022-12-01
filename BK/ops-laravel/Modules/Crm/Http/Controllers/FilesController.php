<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\CrmDocument;
use Modules\Crm\Services\FilesService;
use App\Workshop;
use Validator;
use DB;
class FilesController extends Controller
{

    
    private $notesServices;
    public function __construct()
    {
        $this->document = app(\App\Http\Controllers\DocumentController::class);
        $this->filesServices = FilesService::getInstance();
    }
 
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $notes = CrmDocument::with('regularDocument')->latest()->get();
        return response()->json([
            'status' => TRUE,
            'data' => $notes
        ], 200);
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
                    'doc_file' => 'required',
                   'document_title' => 'required',
                    'field_id' => 'required',
                    'type' => 'required',
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                 $workshop = Workshop::where('code1', 'CRM')->first(['id', 'display']);
                //sharing Array of object
                $request->merge(['workshop_id'=>$workshop->id]);
                 $request->merge(['uncote' => 1]);
                $docResponse=$this->document->addFiles($request);
                $doc=$docResponse->getData();
                $request->merge(['regular_document_id'=>$doc->id]);
                return  $note = $this->filesServices->addFile($request->all());

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
    public function show($id, $type)
    {
        try {
            if (!empty($id) && !empty($type))
                $files = $this->filesServices->getFiles($id, $type);
            return response()->json([
                'status' => TRUE,
                'data' => $files
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('crm::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
    
    public function removeFile($fileID) {
        try {
            $validator = Validator::make(['fileID' => $fileID], [
                'fileID' => 'required|exists:tenant.crm_documents,id',
            ]);
            if ($validator->fails())
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
            
            DB::connection('tenant')->beginTransaction();
            
            $result = $this->filesServices->removeFile($fileID);
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => 'Internal server error'], 500);
        }
    }
}
