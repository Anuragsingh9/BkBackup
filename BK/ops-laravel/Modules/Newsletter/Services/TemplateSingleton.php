<?php

namespace Modules\Newsletter\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Newsletter\Entities\NewsletterBlock;
use Modules\Newsletter\Entities\Template;
use Auth;
use DB;
use Modules\Newsletter\Entities\TemplateBlock;
use Intervention\Image\ImageManagerStatic as Image;
use Modules\Newsletter\Entities\Newsletter;

class TemplateSingleton
{
    /**
     * TemplateSingleton constructor.
     */

    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * Make instance of template singleton class
     * @return TemplateSingleton|null
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
     * Get template list in from storage
     * @return mixed
     */
    public function getTemplates($size)
    {
        try {
            // Paginate template data
             $defaultSize = config('newsletter.TEMPLATE_LIST_PAGINATION_NUMBER');
            $sender = Template::withCount('newsletter')->orderBy('id','desc')->paginate(($size) ? $size : $defaultSize);
            return $sender;
        } catch (\Exception $e) {
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }
    /**
     * Get all template list in from storage
     * @return mixed
     */
    public function getAllTemplates()
    {
        try {
            // Paginate template data
            $sender = Template::get(['id','name']);
            return $sender;
        } catch (\Exception $e) {
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

    /**
     * Add a template in storage
     * @param $name
     * @param $createdBy
     * @param $description
     * @param null $textForBrowserView
     * @param null $headerHtmlCode
     * @param null $footerHtmlCode
     * @return mixed
     */
    public function addTemplate($name, $createdBy, $description, $textForBrowserView = null, $headerHtmlCode = null, $footerHtmlCode = null)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $insertData = [
                'name' => $name,
                'created_by' => $createdBy,
                'description' => $description,
                'text_for_browser_view' => $textForBrowserView,
                'header_html_code' => $headerHtmlCode,
                'footer_html_code' => $footerHtmlCode
            ];

            $result = Template::create($insertData);

            DB::connection('tenant')->commit();
            return $result;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

    /**
     * Get specified template data for modify
     * @param $id
     * @return mixed
     */
    public function modifyTemplate($id){
            $template = Template::find($id);
            return $template;
      
    }

    /**
     * Update specified template data in storage
     * @param $id
     * @param $name
     * @param $createdBy
     * @param null $description
     * @return mixed
     */
    public function updateTemplate($id, $name, $createdBy, $description = null)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            Template::where('id', $id)->update([
                'name' => $name,
                'created_by' => $createdBy,
                'description' => $description
            ]);
            $template = Template::find($id);
            DB::connection('tenant')->commit();
            return $template;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

    /** Update specified template header and footer in storage
     * @param $id
     * @param $textForBrowserView
     * @param $headerHtmlCode
     * @param $footerHtmlCode
     * @return mixed
     */
    public function modifyHeaderFooter($id, $textForBrowserView, $headerHtmlCode, $footerHtmlCode)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            if($headerHtmlCode){
                $headerHtmlCode= $this->Replacestr($headerHtmlCode);
            }
            if($footerHtmlCode){
                $footerHtmlCode= $this->Replacestr($footerHtmlCode);
            }
            Template::where('id', $id)->update([
                'text_for_browser_view' => $textForBrowserView,
                'header_html_code' => $headerHtmlCode,
                'footer_html_code' => $footerHtmlCode
            ]);
            $template = Template::find($id);
            DB::connection('tenant')->commit();
            return $template;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

    /**
     * Add a template block in storage
     * @param $request
     * @param $id
     * @return mixed
     */

    public function addTemplateBlock($request, $id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            // Upload image to S3 and get URL
            $folderName = 'templateBlocks';
            if($request->has('block_html_code')){
                $request->block_html_code= $this->Replacestr($request->block_html_code);
            }
            $imageUrl = $this->uploadFileToS3($request,null,null,$folderName);
            $blockCount = TemplateBlock::count() + 1;
            $insertData = [
                'template_id' => $id,
                'block_html_code' => $request->block_html_code,
                'image_url' => $imageUrl,
                'sort_order' => $blockCount
            ];

            $data = TemplateBlock::create($insertData);
             DB::connection('tenant')->commit();
            return $data;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

       /**
     * Get specified templateBlock data for modify
     * @param $id
     * @return mixed
     */
    public function updateTemplateblock($id){

        $templateBlock = TemplateBlock::find($id);

        return $templateBlock;
    }
    

    /**
     * Update specified template block in storage
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function modifyTemplateBlock($request, $tempId, $blockId)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            // Get record id using template_id
            //$update is array contain field which need update
            $update=[
                'template_id' => $tempId,
                'block_html_code' => $request->block_html_code,
            ];
            // checking image_url contain in request or not
            if($request->has('image_url')){
            // Upload image on S3 and get URL to store in DB
            $folderName = 'templateBlocks';
            $imageUrl = $this->uploadFileToS3($request, $tempId, $blockId, $folderName);
            // $blockCount = TemplateBlock::count() + 1;
            $update['image_url']= $imageUrl;
            }
            if($request->has('block_html_code')){
                $update["block_html_code"]= $this->Replacestr($request->block_html_code);
                // $update["block_html_code"]= $request->has('block_html_code');
            }
            TemplateBlock::with('template:id,name')
                ->where('id', $blockId)
                ->where('template_id', $tempId)
                ->update($update);
                
            $data = TemplateBlock::with('template:id,name')->find($blockId);

            DB::connection('tenant')->commit();
            return $data;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }

    /**Resize image 
     * @param $imageUrl
     *  @param $height
     * @param $width
     * @return mixed
     */
    public function imageResize($imageUrl,$height=90,$width=728)
    {
        if ($imageUrl)
        { 
            $filename = $imageUrl->getClientOriginalName();
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $image_resize = Image::make($imageUrl->getRealPath());
            $image_resize->resize($width, $height, function ($constraint) {
                $constraint->upsize();
            });
            
            $resource = $image_resize->stream()->detach();
    
        }
        return $resource;
    }

    public function uploadFileToS3($request, $tempId = null, $blockId = null, $folderName = null)
    {
        $filename = null;
        // Upload file to S3 and get URL
        if ($request->hasFile('image_url')) {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/newsletter/'.$folderName;
            

            // Get filename from database and delete it from S3
            if ($folderName == 'templateBlocks') {
                $file = TemplateBlock::with('template:id,name')
                    ->where('template_id', $tempId)
                    ->where('id', $blockId)
                    ->first(['image_url']);
            }
            if ($folderName == 'newsletterBlocks') {

                $file = NewsletterBlock::with('newsletter:id,name')
                    ->where('newsletter_id', $tempId)
                    ->where('id', $blockId)
                    ->first(['image_url']);
            }

            // check if image change then delete exiting file in s3
            if($blockId!=null && $request->has('image_url')){
                if (Storage::disk('s3')->exists($file->image_url)) {
                    Storage::disk('s3')->delete($file->image_url);
                }
            }
            // resize orginal image with appropriate size
            $resizeImg=$this->imageResize($request->image_url,410,728);
            //$filename is name of file which we save in s3
            $filename = $folder.'/'.time() . '.' . $request->file('image_url')->getClientOriginalExtension();
            // Upload file to S3
             $this->core->fileUploadToS3($filename, $resizeImg,'public');
           
            // $url = Storage::disk('s3')->url($filename);
        }
        return $filename;
    }

    /**
     * Remove specified template block from storage
     * @param $id
     * @return mixed
     */

    public function deleteTemplateBlock($tempId, $blockId)
    {
        try {
            // Delete template block
            $template = TemplateBlock::with('template:id,name')
                ->where('template_id', $tempId)
                ->where('id', $blockId)
                ->delete();
            return $template;
        } catch (\Exception $e) {
            $template['status'] = 500;
            $template['msg'] = $e->getMessage();
            return $template;
        }
    }
    public function Replacestr($string = null)
    {
        
        try {
          $pattern = '~(http(s?):)([/|.|\w|\s|-])*\.(?:jpg|gif|png)~';
            preg_match_all($pattern, $string, $match);
            // anuraj's call
            $newurl = $this->urlToImage($match[0]);
            // print_r($newurl);die;
            if(is_array($newurl)){
            $old_src = $match[0];
            $old_style = $match[0];
           
            $new_src = $newurl;
            $new_style = $newurl;
            


            array_walk($old_src, function (&$value, $key) {
                $value = 'src="' . $value . '"';
            });

            array_walk($old_style, function (&$value, $key) {
                $value = 'style="background:url(' . $value . ')"';
            });

           array_walk($new_src, function (&$value, $key) {
                $value = 'src="' . $value . '"';
            });
            array_walk($new_style, function (&$value, $key) {
                $value = 'style="background:url(' . $value . ')"';
            });
    
            $out = str_replace($old_src, $new_src, $string);
            $out = str_replace($old_style, $new_style,  $out);
            // echo $out;die;
            return $out;
        }
        else{
        	return $string;
        }

        } catch (\Exception $e) {
			return $string;
            // return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    //check url is have image or not
    function checkUrl($url){
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
            /* Handle 404 here. */
            return false;
        }

        curl_close($handle);
        return true;

    }
    function urlToImage($urls)
    {
        // dd($urls);
        // try {
            $newurl = []; 
            foreach ($urls as $url) {   
                $spliturl=explode(env('AWS_BUCKET'),$url);
                if(count($spliturl)>1 && Storage::disk('s3')->exists($spliturl[1])){ 
                    array_push($newurl, $url);
                }
                else{
                	// echo $this->checkUrl($url);die;
                    if($this->checkUrl($url)){                      
                        if (getimagesize($url) !== false){
                            $content = file_get_contents($url);
                            $name = time() . uniqid(rand()) . '.jpg';
                            $filepath="localhost/newsletter/templateBlocks/$name";
                            $this->core->fileUploadToS3($filepath, $content,'public');
                            array_push($newurl, Storage::disk('s3')->url($filepath));
                        }
                        else{
                        	array_push($newurl, $url);
                        }
                    }
                    else{
                        array_push($newurl, $url);
                    }
                }
            }
            return ($newurl);
        // } catch (\Exception $e) {
        //     // echo $e->getMessage();die;
        //     return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        // }
    }
}
