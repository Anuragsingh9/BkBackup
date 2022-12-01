<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Newsletter\Entities\Template;
use Modules\Newsletter\Entities\TemplateBlock;
use Modules\Newsletter\Services\SenderSingleton;
use Modules\Newsletter\Services\TemplateSingleton;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use Modules\Newsletter\Entities\Newsletter;

class TemplateController extends Controller
{
    /**
     * TemplateController constructor.
     * @param TemplateSingleton $template
     */

    public function __construct(TemplateSingleton $template)
    {
        $this->template = $template;
    }
    /**
     * Display a listing of the all template.
     * @return Response
     */
    public function getAllTemplates(){
        try {
            // Get all template list
            $template = $this->template->getAllTemplates();
            // if ($template['status'] == 500) {
            //     return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            // }
            return response()->json(['status' => true, 'template' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    /**
     * Display a listing of the template.
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            // Get template list
            $size = ($request->has('size') && !empty($request->size)) ? $request->size : null;
            $template = $this->template->getTemplates($size);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }
            return response()->json(['status' => true, 'template' => $template], 200);
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
        return view('newsletter::create');
    }

    /**
     * Store a newly created template in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:3|max:100',
            ]);
                
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $name = $request->name;
            $createdBy = $request->created_by;
            $description = $request->description;
            $textForBrowserView = $request->text_for_browser_view;
            $headerHtmlCode = $request->header_html_code;
            $footerHtmlCode =  $request->footer_html_code;
            // Add a new template
            $template = $this->template->addTemplate($name, $createdBy, $description, $textForBrowserView, $headerHtmlCode, $footerHtmlCode);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }
            return response()->json(['status' => true, 'template' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    { }

    /**
     * Show the form for editing the specified template.
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            // Get data for specific template to edit
            $template = $this->template->modifyTemplate($id);
            if ($template) {
                return response()->json(['status' => true, 'data' =>  $template], 200);
            } else {
                return response()->json(['status' => false, 'msg' =>  'Record not found'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified template in storage.
     * @param  Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:3|max:100',
            ]);
                
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $name = $request->name;
            $createdBy = $request->created_by;
            $description = $request->description;
            // Update template data of name, created by and description only
            $template = $this->template->updateTemplate($id, $name, $createdBy, $description);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }

            return response()->json(['status' => true, 'template' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified template from storage.
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            // Soft delete template
            $model = Template::find($id);
            $model->delete();

            //TemplateBlock::where('template_id',$id)->delete();
            return response()->json(['status' => true, 'msg' => 'Template Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => $model, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified header and footer for template in storage.
     * @param Request $request
     * @param $id
     * @return  response
     */

    public function updateHeaderFooter(Request $request, $id)
    {
        try {

            $textForBrowserView = $request->text_for_browser_view;
            $headerHtmlCode = $request->header_html_code;
            $footerHtmlCode =  $request->footer_html_code;
            // Update header and footer of a template
            $template = $this->template->modifyHeaderFooter($id, $textForBrowserView, $headerHtmlCode, $footerHtmlCode);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }

            return response()->json(['status' => true, 'template' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Add template block in storage
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function addTemplateBlock(Request $request, $id)
    {
        try {
            //Validation 
            $validator = Validator::make($request->all(), [
                'template_id' => 'required',
                'block_html_code' => 'required',
                'image_url' => 'required|image',
                // 'short_order' => 'required',
            ]);
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            // Add block of a template
            $template = $this->template->addTemplateBlock($request, $id);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }
            return response()->json(['status' => true, 'msg' => 'Block Added Successfully', 'TemplateBlocks' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    /**
     * Show the form for editing the specified templateBlock.
     * @param $id
     * @return Response
     */
    public function editTemplateBlock($id)
    {
        try {
            // Get data for specific template to edit
            $template = $this->template->updateTemplateblock($id);
            if ($template) {
                return response()->json(['status' => true, 'data' =>  $template], 200);
            } else {
                return response()->json(['status' => false, 'msg' =>  'Record not found'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified template block in storage.
     * @param Request $request
     * @param $id
     * @return response
     */
    public function updateTemplateBlock(Request $request, $tempId, $blockId)
    {
        try {
            //Validation 
            $validator = Validator::make($request->all(), [
                'template_id' => 'required',
                'block_html_code' => 'required',
                'image_url' => 'image',
                // 'short_order' => 'required',
            ]);
            /**
             * check validator and if validation fail send response with error message
             */
            if ($validator->fails()) {

                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            // Update blocks of a template
            $template = $this->template->modifyTemplateBlock($request, $tempId, $blockId);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }
            return response()->json(['status' => true, 'msg' => 'Templateblock Updated Successfully', 'template' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove specified template block in storage.
     * @param $id
     * @return response
     */
    public function deleteTemplateBlock($tempId, $blockId)
    {
        try {
            // Delete block of a template
            $template = $this->template->deleteTemplateBlock($tempId, $blockId);
            if ($template['status'] == 500) {
                return response()->json(['status' => false, 'msg' => $template['msg']], 500);
            }
            return response()->json(['status' => true, 'msg' => 'Templateblock Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update template_id in newsletter
     * fetch TemplateBlock data 
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplateBlock($id, $newsletter_id = null)
    {
        try {

            if (!empty($newsletter_id)) {
                Newsletter::where('id', $newsletter_id)->update(['template_id' => $id]);
            }
            // $block = TemplateBlock::where('template_id', $id)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'template_id', 'block_html_code', 'image_url','sort_order']);
            // return response()->json(['status' => true, 'data' => $block], 200);
            $block = TemplateBlock::where('template_id', $id)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'template_id', 'block_html_code', 'image_url','sort_order']);
           $template = Template::find($id,['id','header_html_code','footer_html_code','text_for_browser_view','name']);
           if(!empty($newsletter_id)){
            $data=$block; 
           }
           else{
                $data=['block'=>$block,'template'=>$template]; 
            }
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    public function shortOrder(Request $request)
    {
        try {
            $data = json_decode($request->data);
            $ids=[];
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $val) {
                    $setting = TemplateBlock::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                    $ids[]=$val->id;
                }
                if($setting){
                    $TemplateBlock=TemplateBlock::whereIn('id', $ids)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id','template_id','block_html_code','image_url','sort_order']);
                    return response()->json(['status' => true, 'data' => $TemplateBlock], 200);
                }
                else{
                    return response()->json(['status' => true, 'data' => $data], 200);
                }
            }
            
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
