<?php

namespace Modules\Newsletter\Services;
use DB;
use Modules\Newsletter\Entities\Newsletter;
use Modules\Newsletter\Entities\NewsletterBlock;


class NewsletterblockSingleton
{
    /**
     * Make instance of newsletterblock singleton class
     * @return NewsletterBlockSingleton|null
     */

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }
  

    /**
     * Add newsletter block
     * @param $newsletter_id
     * @param $template_block_id
     * @param $blocks
     * @param $imageUrl
     * @return mixed
     */
    public function addNewsletterBlock($insertData)
    {
        try {
            //add block to database
            DB::connection('tenant')->beginTransaction();
            $data = NewsletterBlock::create($insertData);
            DB::connection('tenant')->commit();
            return $data;
        }
        catch (\Exception $e)
        {
            DB::connection('mysql')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

     /**
     * Update modify block in storage
     * @param $newsletterId
     * @param $template_block_id
     * @param $blocks
     * @param $imageUrl
     * @return @response
     */

    public function updateNewsletterBlock($id,$updateData){
        //UPDATING THE DATA IN THE DATABSE THROUGH ID
            DB::connection('mysql')->beginTransaction();
        try {

            $newsletter_block= NewsletterBlock::whereId($id)->update($updateData);
            DB::connection('mysql')->commit();
            return response()->json(['status'=>true,'data'=>$newsletter_block,'msg'=> 'Newsletter Block Updated Successfully!','data'=>NewsletterBlock::find($id)],200);
        } catch (\Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
