<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\Alphanumeric;
use Validator;
use Illuminate\Routing\Controller;
use App\NewsletterList;
use Modules\Newsletter\Entities\Subscription;
use Modules\Newsletter\Services\SubscriptionServices;





use function GuzzleHttp\Promise\all;

class SubscriptionController extends Controller
{

    private $instance;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public  function __construct()
    {
        $this->instance = SubscriptionServices::getInstance();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $subscription = Subscription::with('list:id,name')->orderBy('id','desc')->get(['list_id', 'id', 'form_name', 'title', 'updated_at']);

            return response()->json(['status' => true, 'data' => $subscription]);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            /**
             * Validation for subscription_form
             */
            $validator = Validator::make($request->all(), [
                "form_name" => "required|min:3|max:100",
                "list_id" => "required",
                "success_url" => "required|min:3|max:100",
                "error_url"  => "required|min:3|max:100",
                "display_header_zone" => "required|in:0,1",
                "title" => "required|regex:/[a-zA-Z\s]+/|min:3|max:100",
                "seperator_line_color" => "required|max:191",
                "field_email"  => "required|in:1",
                "field_fname" => "required|in:0,1",
                "field_lname" => "required|in:0,1",
                "font_family" => "required|max:50",
                "font_size" => "required|max:100",
                "background_color" => "required|max:191",
                "button_color" =>    "required|max:191",
                "button_text_color" => "required|max:191",
                "rounded_button" => "required|max:20",
                "button_text" => "required|max:191",
                "html_code" => "required"
            ]);
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }

            return $this->instance->addSubscription([
                'form_name' => request('form_name'),
                'list_id' => request('list_id'),
                'success_url' =>  request('success_url'),
                'error_url' =>  request('error_url'),
                'display_header_zone' => request('display_header_zone'),
                'title' =>  request('title'),
                'seperator_line_color' =>  request('seperator_line_color'),
                'field_email'  => request('field_email'),
                'field_fname'  => request('field_fname'),
                'field_lname'  => request('field_lname'),
                'font_family' => request('font_family'),
                'font_size' => request('font_size'),
                'background_color' => request('background_color'),
                'button_color' => request('button_color'),
                'button_text_color' => request('button_text_color'),
                'rounded_button' => request('rounded_button'),
                'button_text' => request('button_text'),
                'html_code' =>request('html_code'),
                'html_form' =>request('html_form')
            ]);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Subscription $subscription)
    {
        try {

            $subscription = Subscription::find($id);
            return response()->json(['status' => true, 'data' => $subscription], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    { }

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
            /**
             * Validation for subscription_form
             */
            $validator = Validator::make($request->all(), [
                "form_name" => "required|min:3|max:100",
                "list_id" => "required",
                "success_url" => "required|min:3|max:100",
                "error_url"  => "required|min:3|max:100",
                "display_header_zone" => "required|in:0,1",
                "title" => "required|regex:/[a-zA-Z\s]+/|min:3|max:100",
                "seperator_line_color" => "required|max:191",
                "field_email"  => "required|in:1",
                "field_fname" => "required|in:0,1",
                "field_lname" => "required|in:0,1",
                "font_family" => "required|max:50",
                "font_size" => "required|max:100",
                "background_color" => "required|max:191",
                "button_color" =>    "required|max:191",
                "button_text_color" => "required|max:191",
                "rounded_button" => "required|max:20",
                "button_text" => "required|max:191",
                "html_code" => "required"
            ]);
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            return $this->instance->updateSubscription($id, [
                'form_name' => request('form_name'),
                'list_id' => request('list_id'),
                'success_url' =>  request('success_url'),
                'error_url' =>  request('error_url'),
                'display_header_zone' => request('display_header_zone'),
                'title' =>  request('title'),
                'seperator_line_color' =>  request('seperator_line_color'),
                'field_email'  => request('field_email'),
                'field_fname'  => request('field_fname'),
                'field_lname'  => request('field_lname'),
                'font_family' => request('font_family'),
                'font_size' => request('font_size'),
                'background_color' => request('background_color'),
                'button_color' => request('button_color'),
                'button_text_color' => request('button_text_color'),
                'rounded_button' => request('rounded_button'),
                'button_text' => request('button_text'),
                'html_code' =>request('html_code'),
                'html_form' =>request('html_form')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
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
        try {
            $subscription = Subscription::whereId($id)->delete();

            return response()->json(['status' => true, 'msg' => 'Deleted Successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function getdata(Request $request){

        if($request->has('id') && !empty($request->id)){

            $subscription = Subscription::where('id',$request->id)->first(['html_code']);
            if($subscription){
                echo "document.write(`$subscription->html_code`)";
            }
            else{
                echo "Subscription form not found";
            }

        }
        else{
            echo "Subscription form not found";
        }
        
    }

   
}
