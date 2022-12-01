<?php

namespace Modules\Crm\Http\Controllers;

use App\Model\TaskComment;
use App\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Services\CommentService;
use Validator;
use DB;

class CommentController extends Controller
{
    private $commentsServices;

    public function __construct()
    {
        $this->commentsServices = CommentService::getInstance();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $comment = TaskComment::with('user')->latest()->paginate(5);
        return response()->json([
            'status' => TRUE,
            'data' => $comment
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
                    'comment' => 'required',
                    'field_id' => 'required',
                    'type' => 'required',
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $workshop = Workshop::where('code1', 'CRM')->first(['id', 'display']);

                if (isset($workshop->id)) {
                    //sharing Array of object
                    return $note = $this->commentsServices->addComment($request->all(), $workshop->id);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'data' => ''
                    ], 500);
                }

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
                $comments = $this->commentsServices->getComments($id, $type);
            return response()->json([
                'status' => TRUE,
                'data' => $comments
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
            $comments = TaskComment::findOrFail($id);
            return response()->json([
                'status' => TRUE,
                'data' => $comments
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
            $comment = DB::transaction(function () use ($request, $id) {
                $validator = Validator::make($request->all(), [
                    'comment' => 'required',
                ]);
                //validation false return errors
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //sharing Array of object
                return $comment = $this->commentsServices->editComment($request->all(), $id);

            });
            return response()->json([
                'status' => TRUE,
                'data' => $comment
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
            $comment = TaskComment::whereId($id)->delete();
            return response()->json([
                'status' => TRUE,
                'data' => 'Deleted sucessfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $e->getMessage()], 500);
        }
    }
}
