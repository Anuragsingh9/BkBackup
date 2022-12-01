<?php

namespace Modules\Qualification\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Qualification\Entities\QualificationTemplate;
use Illuminate\Support\Facades\Storage;
use Validator;
use DB;

class QualificationTempController extends Controller
{
    private $core;

    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            //feth the list of votes
            $temp = QualificationTemplate::get(['id', 'title', 'language', 'file']);
            return response()->json(['status' => true, 'data' => $temp], 200);
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
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
                'language' => 'required|alpha',
                'file' => 'required|mimes:doc,pdf,docx,xlsx,xls|max:5000'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            // transaction start
            DB::beginTransaction();
            // if request has file
            if ($request->hasFile('file')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();;
                $directory = $domain . "/upload/qualification/template/" . $filename;
                /**
                 * file upload to s3
                 * path for s3
                 *  https://s3-eu-west-2.amazonaws.com/opsimplify.com/{$imageUrlS3}
                 * @param $directory =  $domain . "/upload/qualification/template/" . $filename;
                 * @param $file
                 */
                $imageUrlS3 = $this->core->fileUploadToS3($directory, $file, 'public');
                // insert data into database if file uploaded succesfully in s3
                // name,language,file
                //$imageUrlS3 = store the path of s3 in database
                if ($imageUrlS3) {
                    $temp = QualificationTemplate::create([
                        'title' => $request->title,
                        'language' => $request->language,
                        'file' => $imageUrlS3
                    ]);
                }
            }
            //transaction commit
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Block Added Successfully', 'data' => $temp], 200);
        } catch (\Exception $e) {
            //transaction rollback
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
        try {
            $temp = QualificationTemplate::find($id);
            if (!$temp) {
                return Response()->json(['status' => false, 'msg' => 'not found', 'data' => []], 200);
            }
            return response()->json(['status' => true, 'data' => $temp]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
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
            $validator = Validator::make($request->all(), [
//                'title' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:255',
//                'language' => 'required|alpha',
                'file' => 'required|mimes:doc,pdf,docx,xlsx,xls|max:5000'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            // transaction start
            DB::beginTransaction();
            // if request has file
            if ($request->hasFile('file')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();;
                $directory = $domain . "/upload/qualification/template/" . $filename;
                /**
                 * file upload to s3
                 * path for s3
                 *  https://s3-eu-west-2.amazonaws.com/opsimplify.com/($imageUrlS3)
                 * @param $directory =  $domain . "/upload/qualification/template/" . $filename;
                 * @param $file
                 */
                $imageUrlS3 = $this->core->fileUploadToS3($directory, $file, 'public');

                // update data into database if file uploaded succesfully in s3
                // name,language,file
                //$imageUrlS3 = store the path of s3 in database
                if ($imageUrlS3) {
                    $temp = QualificationTemplate::where('id', $id)->update([
//                        'name' => $request->name,
//                        'language' => $request->language,
                        'file' => $imageUrlS3
                    ]);
                }

            }
            if (!$temp) {
                return Response()->json(['status' => false, 'msg' => 'no data found', 'data' => []], 200);
            }
            //transaction commit
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Data Updated Successfully', 'data' => $temp], 200);
        } catch (\Exception $e) {
            //transaction rollback
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
        //transaction start
        DB::beginTransaction();
        try {
            $temp = QualificationTemplate::find($id);
            // check if the id is exist or not
            if (!$temp) {
                return Response()->json(['status' => false, 'msg' => 'no data for delete'], 200);
            }
            // delete if id exist
            $this->core->fileDeleteBys3($temp->file);
            $temp->update(['file' => '']);
            //transaction commit
            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Record Deleted Successfully'], 200);
        } catch (\Exception $e) {
            //transaction rollback
            DB::rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
