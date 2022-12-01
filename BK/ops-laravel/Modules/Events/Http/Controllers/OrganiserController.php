<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Events\Entities\Eventable;
use Modules\Events\Entities\Organiser;
use Modules\Events\Http\Requests\CreateOrganiserRequest;
use Modules\Events\Http\Requests\UpdateOrganiserRequest;
use Modules\Events\Transformers\OrganiserCollection;
use Validator;
use DB;

class OrganiserController extends Controller {
    public function __construct() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }
    
    /**
     * Display a listing of the resource.
     * @return OrganiserCollection
     */
    public function index() {
        return new OrganiserCollection(Organiser::paginate(2));
    }
    
    /**
     * Store a newly created resource in storage.
     * @param CreateOrganiserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOrganiserRequest $request) {
        
        try {
            DB::connection('tenant')->beginTransaction();
            $imageUrl = $this->uploadImageGetUrl($request->image);
            $organiser = Organiser::create([
                'fname'   => $request->fname,
                'lname'   => $request->lname,
                'company' => $request->company,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'website' => (!empty($request->website) ? $request->website : ''),
                'image'   => $imageUrl,
            ]);
            DB::connection('tenant')->commit();
            return response()->json(['status' => TRUE, 'msg' => 'Record Added Successfully', 'data' => $organiser]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Show the specified resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $organiser = Organiser::find($id);
        return response()->json(['status' => TRUE, 'data' => $organiser]);
    }
    
    /**
     * Update the specified resource in storage.
     * @param UpdateOrganiserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOrganiserRequest $request, $id) {
        try {
            $organiser = Organiser::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => "Record Not Found"], 404);
        }
        try {
            DB::connection('tenant')->beginTransaction();
            $updates = [
                'fname'   => $request->fname,
                'lname'   => $request->lname,
                'company' => $request->company,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'website' => $request->website,
            ];
            if ($request->has('image') && $request->image) {
                $imageUrl = $this->uploadImageGetUrl($request->image);
                $updates['image'] = $imageUrl;
            }
            $organiser->update($updates);
            DB::connection('tenant')->commit();
            return response()->json(['status' => TRUE, 'msg' => 'Record Updated Successfully', 'data' => $organiser]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $organiser = Organiser::find($id);
        if (!$organiser) {
            return response()->json(['status' => FALSE, 'msg' => 'Record Not Found'], 404);
        }
        $eventables = Eventable::where(['eventable_type' => 'Modules\Events\Entities\Organiser', 'eventable_id' => $id, 'deleted_at' => NULL])
            ->count();
        if ($eventables) {
            return response()->json(['status' => FALSE, 'msg' => 'Organiser Already Assigned to Some Event'], 422);
        }
        if ($organiser->delete()) {
            return response()->json(['status' => TRUE, 'msg' => 'Record Deleted Successfully']);
        } else {
            return response()->json(['status' => FALSE], 400);
        }
    }
    
    public function getOrganiserList(Request $request, $itemPerPage = 10) {
        $orderable = ['lname', 'company', 'email'];
        $order = ((isset($request->order) && ($request->order == 'desc' || $request->order == 'asc')) ? $request->order : 'asc');
        if ($request->has('field') && in_array($request->field, $orderable)) {
            $organisers = Organiser::orderBy($request->field, $order)->orderBy('fname', $order);
        } elseif ($request->field == 'fname') {
            $organisers = Organiser::orderBy('fname', $order)->orderBy('lname', $order);
        } else {
            $organisers = Organiser::orderBy('id', 'desc');
        }
        return (new OrganiserCollection($organisers->paginate($itemPerPage)));
    }
    
    public function uploadImageGetUrl($image) {
        $imageUrl = '';
        if ($image) {
            $s3 = Storage::disk('s3');
            $hostname = $this->tenancy->hostname()['fqdn'];
            
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'ooionline.com/' . $hostname . '/events/organiser/' . $fileName;
            $s3->put('/' . $filePath, file_get_contents($image), 'public');
            $imageUrl = $s3->url($filePath);
        }
        return $imageUrl;
    }
    
    public function searchOrganiserList($key) {
        try {
            if (strlen($key) >= 3) {
                $key = ltrim($key);
                $key = rtrim($key);
                $organisers = Organiser::where(function ($query) use ($key) {
                    $query
                        ->orWhere('fname', 'like', "%" . $key . "%")
                        ->orWhere('lname', 'like', "%" . $key . "%")
                        ->orWhere('email', 'like', "%" . $key . "%")
                        ->orWhere('company', 'like', "%" . $key . "%")
                        ->orWhere(DB::raw("CONCAT(fname,' ',lname)"), 'like', "%" . $key . "%");
                })
                    ->get();
            }
            return new OrganiserCollection($organisers);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'data' => 'Internal server error in getting org admin list' . $e->getMessage()], 500);
        }
    }
}
