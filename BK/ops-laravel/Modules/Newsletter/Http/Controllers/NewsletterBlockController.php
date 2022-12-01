<?php

namespace  Modules\Newsletter\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\NewsletterBlock;
use Modules\Newsletter\Entities\Newsletter;
use Modules\Newsletter\Services\NewsletterblockSingleton;
use Modules\Newsletter\Services\TemplateSingleton;
use Validator;
use function Aws\recursive_dir_iterator;
use Intervention\Image\ImageManagerStatic as Image;


class NewsletterBlockController extends Controller

{
  /**
     * NewsletterBlockController constructor.
     * @param NewsletterBlockSingleton $newsblock
     * @param TemplateSingleton $templateSingleton
     * @param app(\App\Http\Controllers\CoreController::class)
     */

    public function __construct(NewsletterBLOCKSingleton $newsletter,TemplateSingleton $templateSingleton)
    {
        $this->newsletter = $newsletter;
        $this->templateSingleton = $templateSingleton;
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newsletter_block = NewsletterBlock::paginate(10);
        return response()->json(['status' => true, 'data' => $newsletter_block],200);
    }
     /**
     * Add newsletter block in storage
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'newsletter_id' => 'required',
                'template_block_id' => 'required',
                'blocks' => 'required',
                'image_url' => 'required',
            ]);
            // $validator->after(function($validator) use($request)
            // {
            //                 if ($this->CheckUniqueId($request->newsletter_id)>0)
            //                 {
            //                     $validator->errors()->add('id', 'Id already exists!');
            //                 }
            //             });
              /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }


         $blockCount = NewsletterBlock::count();
                    $insertData=[
                        'newsletter_id'=>$request->newsletter_id,
                        'template_block_id' => $request->template_block_id,
                        'blocks' => $request->blocks,
                        'image_url' => $request->image_url,
                        'sort_order' =>  $blockCount+1
                    ];
                    //add block to database
                    $newsletter = $this->newsletter->addNewsletterBlock($insertData);

                    $block = NewsletterBlock::where('newsletter_id',$request->newsletter_id)->get(['id','newsletter_id','blocks','image_url']);
                    if($block->count()){

                     return response()->json(['status' => true, 'msg' => 'Block Added Successfully' ,'data' => $block], 200);

                       }
                       return response()->json(['status' => false, 'msg' => 'not found'], 200);




        }
        catch(\Exception $e)
        {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    // public function CheckUniqueId($id){
    //     return NewsletterBlock::where('newsletter_id',$id)->count();
    // }
//
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $newsletter_block = NewsletterBlock::findOrfail($id);
            if (!$newsletter_block) {
                return response()->json(['status' => false, 'msg' => 'not found'], 200);
            }
            return response()->json(['data'=>$newsletter_block],200);
        }catch (\Exception $e){
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified newsletter block in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param $newsletterId
     * @param $blockId
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request,$id)

    // {


    //     //! Validation
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             // 'newsletter_id' => 'required',
    //             // 'template_block_id' => 'required',
    //             'blocks' => 'required',
    //             // 'image_url' => 'sometimes|required|image',
    //         ]);
    //           /**
    //            *
    //          * check validator and if validation fail send response with error message
    //          */
    //         if ($validator->fails()) {

    //             return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
    //         }
    //         if($request->has('image_url')){
    //             $imageUrl = $this->templateSingleton->imageResize($request->file('image_url'),300,300);
    //         }


    //         if (isset($imageUrl) && !empty($imageUrl))
    //         {
    //             $domain = strtok($_SERVER['SERVER_NAME'], '.');
    //             $filename =  time().'.'.$request->file('image_url')->getClientOriginalExtension();
    //             $directory = $domain."/upload/".$filename;
    //             //upload file to s3
    //             $imageUrlS3 = $this->core->fileUploadToS3($directory,$imageUrl, 'public');
    //            if($imageUrlS3)
    //            {
    //                 $updateData=[
    //                     'newsletter_id'=>$request->newsletter_id,
    //                     'template_block_id' => $request->template_block_id,
    //                     'blocks' => $request->blocks,
    //                     'image_url' => $directory,
    //                 ];

    //                 $newsletter = $this->newsletter->updateNewsletterBlock($id,$updateData);

    //            }

    //         }
    //         $updateData=[
    //             'newsletter_id'=>$request->newsletter_id,
    //             'template_block_id' => $request->template_block_id,
    //             'blocks' => $request->blocks,
    //         ];

    //         $newsletter = $this->newsletter->updateNewsletterBlock($id,$updateData);
    //        return $newsletter;
    //     }
    //     catch (\Exception $e) {
    //         return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
    //     }
    // }
        public function update(Request $request, $id)
        {
         //! Validation
        try {
            $validator = Validator::make($request->all(), [
                'blocks' => 'required',
            ]);
            /**
             *
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
           
            $updateData = [
                'blocks' => $request->blocks,
            ];
            $newsletter = $this->newsletter->updateNewsletterBlock($id, $updateData);
            return $newsletter;
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    /**
     *
     * Remove the specified Contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $newsletter_block = NewsletterBlock::find($id);
           $newsletter_id = $newsletter_block->newsletter_id;
            if ($newsletter_block->delete()) {
                $data = NewsletterBlock::where('newsletter_id', $newsletter_id)->get();
                return response()->json(['status' => true, 'msg' => 'Newsletter Deleted Successfully', 'data' => $data], 200);
            }
        }
        catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
       //fetch NewsletterBlock data
       public function getNewsletterBlock($id)
       {
           try {

               $block = NewsletterBlock::where('newsletter_id',$id)->get(['newsletter_id','template_block_id','blocks','image_url']);

               if($block->count()){

                return response()->json(['status' => true, 'data' => $block], 200);

               }
               return response()->json(['status' => false, 'msg' => 'not found'], 200);

           }catch (\Exception $e) {

               return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
           }

       }
    public function shortOrder(Request $request)
    {

        try {
            $data = json_decode($request->data);
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $val) {
                    $setting = NewsletterBlock::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                }

                if($setting){

                    return response()->json(['status' => true, 'data' => $data], 200);
                }

            }

        }catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }

    }
    public function blockImageUpload(Request $request) {
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'width' => 'required|integer',
                'file' => 'required|image',
                // 'file' => 'required|image|dimensions:min_width=600',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false,
                                         'msg'    => implode(',', $validator->errors()
                                             ->all())
                ], 400);
            }
//            if ($request->has('file')) {
//                $imageUrl = $this->templateSingleton->imageResize($request->file('file'), 300, 300);
//            }
            //            if (isset($imageUrl) && !empty($imageUrl)) {
            if ($request->has('file')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $filename = time() . '.' . $request->file('file')->getClientOriginalExtension();

                $directory = $domain . "/upload/newsletter/" . $filename;
                $image = Image::make(file_get_contents($request->file));
               $image->resize( ( ($request->width<100) ?600:$request->width), NULL, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->stream();
                //upload file to s3
                $imageUrlS3 = Storage::disk('s3')->put($directory, $image->__toString(), 'public');
                $url = Storage::disk('s3')->url($directory);
                if ($imageUrlS3) {
                    // return response()->json( ['url'=>env('AWS_PATH') . $directory],200);
                    return response()->json(['data'=>$url,'status'=>true], 200, [], JSON_UNESCAPED_SLASHES);
                } else {
                    return response()->json(['msg'=>'file not upload','status'=>false], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function updateOrder(Request $request){
        try{
            $data = json_decode($request->data);
            $ids=[];
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $val) {
                    $setting = NewsletterBlock::where('id', $val->id)->update(['short_order' => ($k + 1)]);
                    $ids[]=$val->id;
                }
            $list=NewsletterBlock::whereIn('id', $ids)->orderByRaw('CAST(short_order AS UNSIGNED) ASC')->get();
            if($setting){
                return response()->json(['status' => true, 'data' => $list], 200);
            }
            else{
                return response()->json(['status' => false, 'data' => $list], 200);
            }
        }
        }
        catch(\Exception $e){
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);          
        }
    }
}
