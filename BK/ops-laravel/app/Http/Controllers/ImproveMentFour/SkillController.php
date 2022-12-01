<?php

namespace App\Http\Controllers\ImproveMentFOur;

use App\Http\Controllers\Controller;
use App\Model\ConditionalSkill;
use App\Model\MandatoryCheckboxe;
use App\Model\SelectOption;
use App\Model\Skill;
use App\Model\SkillImage;
use App\Model\SkillMeta;
use DB;
use Illuminate\Http\Request;
use Modules\Qualification\Entities\Field;
use Validator;

class SkillController extends Controller
{

    private $core, $tenancy, $meeting;

    public function __construct()
    {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Skill::whereHas('skillTab', function ($q) use ($request) {
            $q->where('is_valid', 1)->where('tab_type', $request->id);
        })->with(['skillTab' => function ($q) {
            $q->where('is_valid', 1)->select('id', 'name');

        }])->with('skillFormat:id,name_en,name_fr', 'skillField')->withCount('userSkill')->where('is_valid', 1)/*->withCount('skillField')*/->withCount(['allUserSkills as skill_field_count' => function ($query) {
            $query->where('user_skills.type','candidate');
        }])->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get()/*->orderBy('id','desc')->first()*/
        ;
//dd($data);
        return response()->json(['status' => true, 'data' => $data], 200);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        var_export((($request->is_conditional_checkbox===true)) ? 1 : 0);exit;
        
        try {
            //start transaction for skip the wrong entry
            DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'skill_tab_id' => 'required',
                'short_name' => 'required',
                'is_mandatory' => 'required',
                'skill_format_id' => 'required',
                'is_unique' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
//var_export((isset($request->is_conditional_checkbox) && $request->is_conditional_checkbox));
            $skillCount = Skill::count() + 1;
          
            $skill = Skill::create(['skill_tab_id' => $request->skill_tab_id, 'name' => $request->name, 'short_name' => $request->short_name, 'description' => $request->description,
                'image' => '',
                //'is_valid' => $request->is_valid,
                'is_mandatory' => $request->is_mandatory,
                'skill_format_id' => $request->skill_format_id,
                'is_unique' => ($request->is_unique) ? 1 : 0,
                'comment' => '',
                'link_text' => '',
                'comment_link' => '',
                'sort_order' => $skillCount,
                'is_conditional' => (isset($request->is_conditional) && $request->is_conditional == 1) ? 1 : 0,
                 'is_qualifying' => (isset($request->is_qualifying)) ?$request->is_qualifying : 1,
                'tooltip_en' => $request->tooltip_en,
                'tooltip_fr' => $request->tooltip_fr
                //'comment_target_blank' => $request->comment_target_blank,

            ]);
            // var_dump($skill);die;
            if (isset($skill->id)) {
                //if type have select, image,mandetoryCheckBox
                $addSkillMeta = $this->addSkillMeta($skill, $request);
                if ($skill->is_conditional) {
                    $addConditionalFields = ConditionalSkill::create(['conditional_field_id' => $skill->id, 'conditional_checkbox_id' => $request->conditional_checkbox_id, 'is_checked' => (isset($request->is_conditional_checkbox) && ($request->is_conditional_checkbox=='true')) ? 1 : 0]);
                }
                if ($addSkillMeta) {
                    DB::connection('tenant')->commit();
                    $skill = Skill::with('skillTab:id,name', 'skillFormat:id,name_en,name_fr', 'skillCheckBox', 'skillImages', 'skillCheckBoxAcceptance')->find($skill->id);
                    return response()->json(['status' => true, 'data' => $skill], 200);
                } else {
                    DB::connection('tenant')->rollBack();
                    return response()->json(['status' => false, 'data' => 0], 201);
                }

            } else {
                return response()->json(['status' => false, 'data' => 0], 201);
            }
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id != 0) {

            $data = Skill::with('skillTab:id,name', 'skillFormat:id,name_en,name_fr')->withCount('userSkill')->where('skill_tab_id', $id)->where('is_valid', 1)->withCount(['allUserSkills as skill_field_count' => function ($query) {
                $query->where('user_skills.type','candidate');
            }])->with('skillField')->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
        } else {
            $data = Skill::with(['skillTab' => function ($q) {
                $q->where('is_valid', 1)->select('id', 'name');
            }])->with('skillFormat:id,name_en,name_fr')->withCount('userSkill')->withCount('skillField')->with('skillField')->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
        }
        return response(['status' => true, 'data' => $data], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $skill = Skill::with('conditionalSkill','skillMeta')->find($id);
            if ($skill) {
                $skillData = $this->getTabFormatData($skill);

                return response()->json(['status' => true, 'data' => $skillData], 200);
            }
            return response()->json(['status' => true, 'data' => $skill], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            //start transaction for skip the wrong entry
            DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'skill_tab_id' => 'required',
                'short_name' => 'required',
                'is_mandatory' => 'required',
                'skill_format_id' => 'required',
                'is_unique' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }

//            if ($request->hasFile('image')) {
            //                $domain = strtok($_SERVER['SERVER_NAME'], '.');
            //                $folder = $domain . '/uploads/skills';
            //                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
            //            } else {
            //                $filename = '';
            //            }
            //getting skill info
            $skill = Skill::find($id);
            $skillUpdate = Skill::where('id', $id)->update([
                /*'skill_tab_id' => $request->skill_tab_id,*/
                'name' => $request->name, 'short_name' => $request->short_name, 'description' => $request->description,
                'image' => '',
                //'image' => isset($filename) ? $filename : $skill->image,
                //'is_valid' => $request->is_valid,
                'is_mandatory' => $request->is_mandatory,

                'is_unique' => $request->is_unique,
                'comment' => '',
                'link_text' => '',
                'comment_link' => '',
                'is_conditional' => (isset($request->is_conditional) && $request->is_conditional == 1) ? 1 : 0,
                'is_qualifying' => (isset($request->is_qualifying)) ?$request->is_qualifying : 1,
				'tooltip_en' => $request->tooltip_en,
                'tooltip_fr' => $request->tooltip_fr
                //'comment_target_blank' => $request->comment_target_blank,

            ]);

            if (isset($skillUpdate)) {
                //if type have select, image,mandetoryCheckBox
                $addSkillMeta = $this->addSkillMeta($skill, $request, $id);
                if ($addSkillMeta) {
                    DB::connection('tenant')->commit();
                    return response()->json(['status' => true, 'data' => $skill], 200);
                } else {
                    DB::connection('tenant')->rollBack();
                    return response()->json(['status' => false, 'data' => 0], 201);
                }

            } else {
                return response()->json(['status' => false, 'data' => 0], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $res = Skill::where('id', $id)->update(['is_valid' => 0]);
            Field::where('field_id', $id)->delete();
//            Skill::find($id)->delete();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => 0], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    /**
     * addSkillMeta add the specified type data in Skill Meta related Table.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function addSkillMeta($skill, $request, $id = '')
    {
        if ($skill->skill_format_id == 13) {
            return $this->addSkillFile($skill, $request, $id);
        } elseif ($skill->skill_format_id == 8 || $skill->skill_format_id == 19) {
            return $this->addSkillSelect($skill, $request, ($id) ? 'edit' : 'add');
        } elseif ($skill->skill_format_id == 12 || $skill->skill_format_id == 17) {
            return $this->addSkillCheckBox($skill, $request, $id);
        } else {
            return $this->addSkillMetaData($skill, $request, $id);
        }

    }

    public function addSkillFile($skill, $request, $id)
    {
        if (empty($id)) {
            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                $skillImage = SkillImage::updateOrCreate(['skill_id' => $skill->id, 'url' => $filename], [
                    'url' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                ]);
            }
        } else {

            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                $skillImage = SkillImage::where('skill_id', $id)->update([
                    'url' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                ]);
            } else {

                $skillImage = SkillImage::where('skill_id', $id)->update([
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                ]);

            }
        }
        if (isset($skillImage)) {
            return true;
        } else {
            return false;
        }

    }

    //Delete option
    public function deleteSkillSelectOption(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $res = SelectOption::where('id', $request->id)->delete();
            return response()->json(['status' => ($res) ? true : false, 'msg' => $res], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    public function addSkillSelect($skill, $request, $type)
    {

        $options = json_decode($request->option_array);

        if (count($options) > 0) {
            if ($type == 'add') {
                $count = SelectOption::where('skill_id', $skill->id)->max('sort_order');
                foreach ($options as $k => $item) {
                    $skillSelect = SelectOption::Create([
                        'skill_id' => $skill->id,
                        'option_value' => $item->option_value,
                        'sort_order' => ++$count
                    ]);
                }
            } else {
                $count = SelectOption::where('skill_id', $skill->id)->max('sort_order');
                foreach ($options as $k => $item) {
                    $data = [
                        'skill_id' => $skill->id,
                        'option_value' => $item->option_value
                    ];
                    if ($item->id == 0) {
                        $data['sort_order'] = ++$count;
                    }
                    $skillSelect = SelectOption::updateOrCreate(['id' => $item->id], $data);
                }
                // return true;
            }
        }

        if (isset($skillSelect)) {
            return true;
        } else {
            return false;
        }
    }

    public function skillOptionDrag(Request $request)
    {

        # code...
        try {
            //code...
            $data = json_decode($request->data);
            if (count($data) > 0) {
                $ids = [];
                foreach ($data as $k => $val) {
                    array_push($ids, $val->id);
                    $result = SelectOption::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
                }
                $options = SelectOption::whereIn('id', $ids)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                return response()->json(['status' => true, 'data' => $options], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function addSkillCheckBox($skill, $request, $id)
    {
        if ($skill->skill_format_id == 12) {
            $skillCheckBox = $this->createCheckbox($skill, $request, $id);
        } else {
            $skillCheckBox = $this->createAcceptance($skill, $request, $id);
        }
        if ($skillCheckBox) {
            return true;
        } else {
            return false;
        }

    }

    public function addSkillMetaData($skill, $request, $id)
    {

//        $skillMeta = SkillMeta::where('skill_id' , $skill->id )->update(['value' => isset($request->value) ? $request->value : '']);
        if (empty($id)) {
            $skillMeta = SkillMeta::updateOrCreate(['skill_id' => $skill->id, 'value' => empty($request->value) ? '' : $request->value], [

                'value' => isset($request->value) ? $request->value : '',
                'skill_id' => $skill->id,
            ]);
        } else {
            $skillMeta = SkillMeta::where('skill_id', $skill->id)->update(['value' => isset($request->value) ? $request->value : '']);
        }
        if ($skillMeta) {
            return true;
        } else {
            return false;
        }

    }

    public function getTabFormatData($skill)
    {
        switch ($skill->skill_format_id) {
            case 13:
                return Skill::with('skillImages', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
                break;
            case 8:
                return Skill::with('skillSelect', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
                break;
            case 19:
                return Skill::with('skillSelect', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
                break;
            case 12:
                return Skill::with('skillCheckBox', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
                break;
            case 17:
                return Skill::with('skillCheckBoxAcceptance', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
                break;
            default:
                return Skill::with('skillMeta', 'conditionalSkill:id,conditional_field_id,conditional_checkbox_id')->find($skill->id);
        }
    }

    public function skillDrag(Request $request)
    {
        $data = json_decode($request->data);
        if (count($data) > 0) {
            foreach ($data as $k => $val) {
                $setting = Skill::where('id', $val->id)->update(['sort_order' => ($k + 1)]);
            }

            $skill = Skill::with(['skillTab' => function ($q) {
                $q->where('is_valid', 1)->select('id', 'name');
            }])->with('skillFormat:id,name_en,name_fr')->where('skill_tab_id', $request->id)->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();

            return response()->json(['status' => true, 'data' => $skill], 200);

        }

    }

    /*
     *this function is used to add Mandatory checkbox
     * */
    public function createCheckbox($skill, $request, $id)
    {
        if (empty($id)) {
            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                return $skillCheckBox = MandatoryCheckboxe::create([
                    'text_value' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                ]);
            }
        } else {
            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                $skillCheckBox = MandatoryCheckboxe::where('skill_id', $id)->update([
                    'text_value' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                ]);
            } else {
                return $skillCheckBox = MandatoryCheckboxe::where('skill_id', $id)->update([
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                ]);


            }
        }
    }

    /*
     *this function is used to add Mandatory acceptance
     * */
    public function createAcceptance($skill, $request, $id)
    {

        if (empty($id)) {
            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                return $skillCheckBox = MandatoryCheckboxe::create([
                    'text_value' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                    'title' => $request->title,
                    'type_of' => 1,
                ]);
            }
        } else {
            if ($request->hasFile('image')) {
                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                $folder = $domain . '/uploads/skills/files';
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                return $skillCheckBox = MandatoryCheckboxe::where('skill_id', $id)->update([
                    'text_value' => $filename,
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                    'title' => $request->title,
                    'type_of' => 1,
                ]);
            } else {
                return $skillCheckBox = MandatoryCheckboxe::where('skill_id', $id)->update([
                    'text_before_link' => $request->text_before_link,
                    'text_after_link' => $request->text_after_link,
                    'text_of_link' => $request->text_of_link,
                    'target_blank' => $request->target_blank,
                    'skill_id' => $skill->id,
                    'title' => $request->title,
                    'type_of' => 1,
                ]);
            }
        }
    }

    public function getConditionalCheckBox()
    {
        $conditional = Skill::where('skill_format_id', 18)->get(['id', 'name']);

        return response()->json(['status' => true, 'data' => $conditional], 200);
    }
}
