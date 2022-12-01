<?php

namespace Modules\Newsletter\Services;
use Modules\Newsletter\Entities\Subscription;

class SubscriptionServices
{
    protected static $instance;
    
    public static function getInstance()
    {
        if(is_null(SubscriptionServices::$instance)) 
        {
            SubscriptionServices::$instance = new SubscriptionServices();
        }

        return  SubscriptionServices::$instance;
    }

 /**
     * store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param form_name
     * @param list_id
     * @param success_url
     * @param error_url
     * @param display_header_zone
     * @param title
     * @param seperator_line_color
     * @param field_email
     * @param field_fname
     * @param field_lname
     * @param font_family
     * @param font_size
     * @param background_color
    *  @param button_color
    *  @param button_text_color
    *  @param rounded_button
     * @param button_text
     * 
     * @return \Illuminate\Http\Response
     */
 
    public function addSubscription($subscription=[]){

       $subscription = Subscription::create([
           
            'form_name' =>  $subscription['form_name'],
            'list_id' => $subscription['list_id'],
            'success_url' => $subscription['success_url'],
            'error_url' =>  $subscription['error_url'],
            'display_header_zone' => $subscription['display_header_zone'],
            'title' =>  $subscription['title'],
            'seperator_line_color' => $subscription['seperator_line_color'],
            'field_email'  => $subscription['field_email'],
            'field_fname'  =>$subscription['field_fname'],
            'field_lname'  => $subscription['field_lname'],
            'font_family' => $subscription['font_family'],
            'font_size' => $subscription['font_size'],
            'background_color' => $subscription['background_color'],
            'button_color' => $subscription['button_color'],
            'button_text_color' => $subscription['button_text_color'],
            'rounded_button' =>$subscription['rounded_button'],
            'button_text' => $subscription['button_text'],
            'html_code' => $subscription['html_code'],
            'html_form' => $subscription['html_form']
        ]);
         /**
         * if validation successfull,store subscription data with success message
         */

        return response()->json(['status' => true, 'msg'=>'Subscribed Successfully' ,'data'=>$subscription],200);


    }
 /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param form_name
     * @param list_id
     * @param success_url
     * @param error_url
     * @param display_header_zone
     * @param title
     * @param seperator_line_color
     * @param field_email
     * @param field_fname
     * @param field_lname
     * @param font_family
     * @param font_size
     * @param background_color
    *  @param button_color
    *  @param button_text_color
    *  @param rounded_button
     * @param button_text
     * 
     * @return \Illuminate\Http\Response
     */

    public function updateSubscription($id,$subscription=[]){

         /**
         * if validation successfull,update subscription data with success message
         */
       
        $subscription =  Subscription::whereId($id)->update([
       
            'form_name' =>  $subscription['form_name'],
            'list_id' => $subscription['list_id'],
            'success_url' =>  $subscription['success_url'],
            'error_url' =>  $subscription['error_url'],
            'display_header_zone' => $subscription['display_header_zone'],
            'title' =>  $subscription['title'],
            'seperator_line_color' =>  $subscription['seperator_line_color'],
            'field_email'  => $subscription['field_email'],
            'field_fname'  => $subscription['field_fname'],
            'field_lname'  => $subscription['field_lname'],
            'font_family' => $subscription['font_family'],
            'font_size' => $subscription['font_size'],
            'background_color' => $subscription['background_color'],
            'button_color' => $subscription['button_color'],
            'button_text_color' => $subscription['button_text_color'],
            'rounded_button' => $subscription['rounded_button'],
            'button_text' => $subscription['button_text'],
            'html_code' => $subscription['html_code'],
            'html_form' => $subscription['html_form']
        ]);

        return response()->json(['status' => true,'msg'=>'Updated Successfully' ,'data'=>$subscription],200);
    }


}

                