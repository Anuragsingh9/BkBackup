<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\DocumentType,App\RegularDocument;


class DocumentTypeController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function addDocType(Request $request)
    {
        $newRec = DocumentType::updateOrCreate(['id'=>$request->id],['document_name'=>$request->document_name,'document_code'=>$request->document_code]);
        return response()->json($newRec);
    }
    public function editDocType(Request $request){
        return response()->json(DocumentType::where('id',$request->id)->get());
    }
    public function getDocTypes()
    {
        //$data=DocumentType::all();
        $data=DB::connection('tenant')->select('SELECT d_type.* ,(SELECT count(*) FROM regular_documents WHERE d_type.id=document_type_id And is_active=1) as counts FROM document_types as d_type ');
        return response()->json($data);
    }
    public function DeleteDocTypes($id)
    {
        $res=0;
        if(DocumentType::where('id',$id)->delete())
            $res = 1;
        return response()->json($res);
    }
}
