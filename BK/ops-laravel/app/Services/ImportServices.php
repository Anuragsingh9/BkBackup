<?php
    
    namespace App\Services;
    
    use App\EntityUser;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use App\Model\Skill;
    use App\Model\SkillTabFormat;
    use App\Model\UserSkill;
use Maatwebsite\Excel\Facades\Excel;
use DB, File;
    use App\Entity;
    use App\Imports\ContactsImport;
    use App\Imports\EntityImport;
    use App\Imports\UsersImport;
    use App\User;
    use Illuminate\Support\Str;
    use Illuminate\Validation\Rule;
    use Batch;
    use Modules\Newsletter\Entities\Contact;
    use Modules\Newsletter\Rules\ValidDate;
    use Modules\Newsletter\Rules\ValidSelectOption;
    use Illuminate\Support\Arr;
    use App\Rules\CompanyValidation;
    
    /**
     * Class ImportServices
     * @package App\Services
     */
    class ImportServices
    {
        /**
         * @var
         */
        protected static $instance;
        
        //DEFINING SINGLETON CLASS
        
        /**
         * @return mixed
         */
        public static function getInstance()
        {
            if (is_null(ImportServices::$instance)) {
                ImportServices::$instance = new ImportServices();
            }
            return ImportServices::$instance;
        }
        
        /**
         * @param array $value
         * @return ContactsImport|\Illuminate\Http\JsonResponse
         */
        public function addAsType(array $value)
        {
            if (isset($value['file_name']) && isset($value['type'])) {
                
                switch ($value['type']) {
                    case 0:
                        return $this->importUser($value);
                        break;
                    case 1:
                        return $this->importContact($value);
                        break;
                    default:
                        return $this->importEntity($value);
                }
            }
        }
        
        /**
         * @param $value
         * @return ContactsImport
         */
        public function importContact($value)
        {
            $path = public_path() . 'public/temp_uploads/' . Auth::user()->id . '/contact/';
            $file = $path . $value['file_name'];
            $import = new ContactsImport();
            $import->setStep($value['step']);
            $import->setEmails(Contact::all(['email', 'id']));
            $this->adjustArray(json_decode($value['key'], TRUE), $import);
            $import->setFinalData(json_decode($value['key'], TRUE));
            $import->setListID($value['listId']);
            Excel::import($import, $file);
            if ($import->getStep() == 3)
                $this->removeExcel($path, $file);
            return $import;
            
        }
        
        /**
         * @param $value
         * @return \Illuminate\Http\JsonResponse
         */
        public function importUser($value)
        {
            $path = public_path() . 'public/temp_uploads/' . Auth::user()->id . '/user/';
            $file = $path . $value['file_name'];
            $import = new UsersImport();
            
            $import->setStep($value['step']);
            $import->setEmails(User::all(['email', 'id']));
            $this->adjustArray(json_decode($value['key'], TRUE), $import);
            $import->setFinalData(json_decode($value['key'], TRUE));
            $import->setListID($value['listId']);
            
            Excel::import($import, $file);
            if ($import->getStep() == 3)
                $this->removeExcel($path, $file);
            
            return $import;
        }
        
        /**
         * @param $value
         * @return \Illuminate\Http\JsonResponse
         */
        public function importEntity($value)
        {
            switch ($value['type']) {
                case 2:
                    $folderName = 'company';
                    $entity_id = 2;
                    break;
                case 3:
                    $folderName = 'instance';
                    $entity_id = 1;
                    break;
                case 4:
                    $folderName = 'union';
                    $entity_id = 3;
                    break;
                case 5:
                    $folderName = 'press';
                    $entity_id = 4;
                    break;
                
            }
            $path = public_path() . 'public/temp_uploads/' . Auth::user()->id . "/$folderName/";
            $file = $path . $value['file_name'];
            $import = new EntityImport();
            $import->setStep($value['step']);
        $import->setEntityType($entity_id);
            $import->setEntityTypeStr($folderName);
            $this->adjustArray(json_decode($value['key'], TRUE), $import);
            $import->setFinalData(json_decode($value['key'], TRUE));
            Excel::import($import, $file);
            
            if ($import->getStep() == 3)
                $this->removeExcel($path, $file);
            return $import;
        }
        
        /**
         * @param $type
         * @return array
         */
        public function getFillable($type)
        {
            
            switch ($type) {
                case 0:
                    $fillable = new User();
                    return $this->renderFillable($fillable, $type, 8, 2);
                    break;
                case 1:
                    $fillable = new Contact();
                    return $this->renderFillable($fillable, $type, 8, 2);
                    break;
                default:
                    $fillable = new Entity();
                    if ($type == 4) {
                        $fieldCount = 12;
                    } else {
                        $fieldCount = 11;
                    }
                    return $this->renderFillable($fillable, $type, $fieldCount, 0);
            }
        }
        
        public function renderFillable($fillable, $type, $dropCount = 2, $requiredCount = 2)
        {
            $fields = $fillable->getFillable();

            $exitCount = 0;
            $allFillables = [];

            foreach ($fields as $k => $field) {
                ++$exitCount;
                $allFillables['personal_tab'][] = ['label' => __('message.' . $field), 'db_name' => $field, 'required' => ($k <= $requiredCount) ?? FALSE];

                if (($exitCount) > $dropCount) {
                    break;
                }
            }
            //internal Id
            $allFillables['personal_tab'][] = ['label' => __('message.' . 'internal_id'), 'db_name' => 'internal_id', 'required' =>  FALSE];


            if ($type == 4 && config('constants.Press')) {
                $allFillables['personal_tab'][] = ['label' => __('message.' . 'membership_type'), 'db_name' => 'membership_type', 'required' => FALSE];
                $allFillables['personal_tab'][12] = ['label' => __('message.' . 'entity_sub_type'), 'db_name' => 'entity_sub_type', 'required' => TRUE];
            }
            if ($type == 0 || $type == 1) {
                //checking that CRM is enabled or not and add fields accordingly
                if (config('constants.CRM')) {
                    $allFillables['professional_tab'][] = ['label' => __('message.company_name'), 'db_name' => 'company_name', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.company_position'), 'db_name' => 'company_position', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.union_name'), 'db_name' => 'union_name', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.union_position'), 'db_name' => 'union_position', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.membership_type'), 'db_name' => 'membership_type', 'required' => FALSE];
                }
                //checking that Instance is enabled or not and add fields accordingly
                if (config('constants.Instance')) {
                    $allFillables['professional_tab'][] = ['label' => __('message.instance_name'), 'db_name' => 'instance_name', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.instance_position'), 'db_name' => 'instance_position', 'required' => FALSE];
                }
                //checking that Press is enabled or not and add fields accordingly
                if (config('constants.Press')) {
                    $allFillables['professional_tab'][] = ['label' => __('message.press_name'), 'db_name' => 'press_name', 'required' => FALSE];
                    $allFillables['professional_tab'][] = ['label' => __('message.press_position'), 'db_name' => 'press_position', 'required' => FALSE];
                }
                
            }
            
            return $allFillables;
        }
        
        /**
         * @param array $key
         * @param $import
         * @return mixed
         */
        public function adjustArray(array $key, $import)
        {
            $filterArray = [];
            $isCustom = [];
            $entityArray = [];
            
            if (is_array($key)) {
                foreach ($key as $item) {
                    
                    if (isset($item['map']) && isset($item['is_custom'])) {
                        $arrKey = array_keys($item['map']);
                        if ($item['tab'] == 'personal_tab') {
                            $filterArray[$item['map'][$arrKey[0]]] = $arrKey[0];
                        }
                        if ($item['tab'] == 'professional_tab') {
                            $filterArray[$item['map'][$arrKey[0]]] = $arrKey[0];
                            $entityArray[$item['map'][$arrKey[0]]] = $arrKey[0];
                        }
                        if ($item['is_custom']) {
                            $filterArray[isset($item['map'][$arrKey[0]]['label']) ? $item['map'][$arrKey[0]]['label'] : 0] = $arrKey[0];
                            $isCustom[$arrKey[0]] = isset($item['map'][$arrKey[0]]['value']) ? $item['map'][$arrKey[0]]['value'] : 0;
                        }
                    }
                }
                if (count($isCustom) > 0) {
                    $rulesArray = $this->addCustomValidation($isCustom);
                    $rulesArray = $this->formatKey($rulesArray);
                    foreach ($rulesArray as $k => $item) {
                        $import->setAllRules($k, $item);
                    }
                }

                if (count($entityArray) > 0) {
                    $entities = Entity::whereNotNull('long_name')->get(['id', 'long_name', 'entity_type_id'])->filter();
                    $collection = $entities;
                    $entities1 = $entities;
                    $entities1->map(function ($name, $k) {
                        $entities1[$k]['long_name'] = strtolower($name->long_name);
                        $entities1[$k]['entity_type_id'] = ($name->entity_type_id);
                    });
                    App::setLocale(strtolower(session()->get('lang')));
                    $entityRulesArray=[];
                    foreach ($entityArray as $k => $item) {

                        if ($k == 'company_name' || $k == 'union_name' || $k == 'instance_name' || $k == 'press_name') {
                            if ($k == 'company_name') {
                                $type = __('message.companyVal');
                                $entity_type_id = 2;
                                $entity = collect($entities1)->where('entity_type_id', 2)->pluck('long_name')->filter();
                                $entity1 = collect($collection)->where('entity_type_id', 2)->pluck('long_name')->filter();
                            } elseif ($k == 'press_name') {
                                $type = __('message.press');
                                $entity_type_id = 4;
                                $entity = collect($entities1)->where('entity_type_id', 4)->pluck('long_name')->filter();
                                $entity1 = collect($collection)->where('entity_type_id', 4)->pluck('long_name')->filter();
                            } elseif ($k == 'union_name') {
                                $type = __('message.unionVal');
                                $entity_type_id = 3;
                                $entity = collect($entities1)->where('entity_type_id', 3)->pluck('long_name')->filter();
                                $entity1 = collect($collection)->where('entity_type_id', 3)->pluck('long_name')->filter();
                            } else {
                                $entity = collect($entities1)->where('entity_type_id', 1)->pluck('long_name')->filter();
                                $entity_type_id = 1;
                                $entity1 = collect($collection)->where('entity_type_id', 1)->pluck('long_name')->filter();
                                $type = __('message.instanceVal');
                            }
                            
                            $entityRulesArray['*.' . $item] = [
                                'nullable',
//                                function ($attribute, $value, $fail) use ($entity, $entity1, $type) {
//
//                                    if (!in_array($value, array_merge($entity->toArray(), $entity1->toArray()))) {
//                                        $fail($type . ' ' . $value . '  ' . __('message.entity_message'));
//                                    }
//                                },
//                            new CompanyValidation($k,array_merge($entity->toArray(), $entity1->toArray())),
//                            Rule::in(array_merge($entity->toArray(), $entity1->toArray()), ''),
                                 'regex:/^[A-Za-z. -]+$/', Rule::unique('tenant.entities','long_name')->where(function ($query)  use($entity_type_id){
                                    return $query->where('entity_type_id', $entity_type_id);
                                })
                            ];
                            if ($k == 'membership_type') {
                                $entityRulesArray['*.' . $item] = [
                                    'numeric',
                                    Rule::in([0, 1]),
                                ];
                            }
                            
                        }
                    }
//                dd($entityRulesArray);
                    foreach ($entityRulesArray as $k => $item) {
                        $import->setAllRules($k, $item);
                    }
                }
                
            }
            
            return $import->setRuleArray($filterArray);
        }
        
        /**
         * @param $isCustom
         * @return array
         */
        public function addCustomValidation($isCustom)
        {
            $rulesArray = [];
            
            $skillFormats = SkillTabFormat::whereNotIn('id', [7, 12, 13, 16, 17])->get(['id']);
            $skills = Skill::whereIn('id', array_values($isCustom))->get(['id', 'skill_tab_id', 'skill_format_id', 'short_name']);
            foreach ($skills as $skill) {
                
                switch ($skill->skill_format_id) {
                    case 1:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'sometimes',
//                            'required',
                                Rule::in(['true', 'TRUE', 'false', 'FALSE', '0', '1']),
                            ];
                        }
                        break;
                    case 2:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                Rule::in(['yes', 'YES', 'no', 'NO', '0', '1']),
                            ];
                        }
                        break;
                    case 3:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                'between:0,10',
                            ];
                        }
                        break;
                    case 4:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                'between:1,10',
                            ];
                        }
                        break;
                    case 5:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                'between:1,5',
                            ];
                        }
                        break;
                    case 6:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required'
                            ];
                        }
                        break;
                    case 8:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
                                'required',
                                new ValidSelectOption($skill->id),
                            ];
                        }
                        break;
                    case 9:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                'numeric',
                            ];
                        }
                        break;
                    case 10:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required'
                            ];
                        }
                        break;
                    case 11:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required',
                                new ValidDate,
                            ];
                        }
                        break;
                    case 14:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required'
                            ];
                        }
                        break;
                    case 15:
                        if (array_search($skill->id, $isCustom) != FALSE) {
                            $rulesArray['*.' . array_search($skill->id, $isCustom)] = [
//                            'required'
                            ];
                        }
                        break;
                    default:
                        return $rulesArray;
                }
            }
            return $rulesArray;
        }
        
        /**
         * @param array $elements
         * @return array
         */
        private function formatKey(array $elements): array
        {
            return collect($elements)->mapWithKeys(function ($rule, $attribute) {
                $attribute = Str::startsWith($attribute, '*.') ? $attribute : '*.' . $attribute;
                
                return [$attribute => $this->formatRule($rule)];
            })->all();
        }
        
        /**
         * @param string|object|callable|array $rules
         *
         * @return string|array
         */
        private function formatRule($rules)
        {
            if (is_array($rules)) {
                foreach ($rules as $rule) {
                    $formatted[] = $this->formatRule($rule);
                }
                
                return $formatted ?? [];
            }
            
            if (is_object($rules) || is_callable($rules)) {
                return $rules;
            }
            
            if (Str::contains($rules, 'required_if') && preg_match('/(.*):(.*),(.*)/', $rules, $matches)) {
                $column = Str::startsWith($matches[2], '*.') ? $matches[2] : '*.' . $matches[2];
                
                return $matches[1] . ':' . $column . ',' . $matches[3];
            }
            
            return $rules;
        }
        
        /**
         * @param $failures
         * @param $error
         * @return mixed
         */
        public function addErros($failures, &$error)
        {
            if (isset($failures->response)) {
                $nameArray = $failures->response;
            }
            foreach ($failures as $failure) {
                if (is_array($failure)) {
                    foreach ($failure as $k => $item) {
                        if (is_array($item)) {
                            foreach (Arr::wrap($item) as $key => $message) {
//                            if (Str::after($k, '.') == 10) {
//                                @$exp = explode(' ', $message);
//                                @$message = (str_replace($exp[2], $nameArray['*.' . Str::after($k, '.')], $message));
//                            }
                                $error[][Str::before($k, '.') + 2] = $message;
                            }
                        }
                    }
                }
            }
            
            return $error;
        }
        
        /**
         * @param $format
         * @param $collect
         * @return array
         */
        public function decideToAdd($format, $collect)
        {
            if (isset($collect['type']) && ($collect['type'] == 'company' || $collect['type'] == 'union' || $collect['type'] == 'instance' || $collect['type'] == 'contact' || $collect['type'] == 'press')) {
                switch ($format) {
                    case 8:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'select_input' => $collect['value']];
                        }
                        break;
                    case 14:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'comment_text_input' => $collect['value']];
                        }
                        break;
                    case 15:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'address_text_input' => $collect['value']];
                        }
                        break;
                    case 11:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'date_input' => $collect['value']];
                        }
                        break;
                    case 10:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'long_text_input' => $collect['value']];
                        }
                        break;
                    case 9:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'numerical_input' => $collect['value']];
                        }
                        break;
                    case 6:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'text_input' => $collect['value']];
                        }
                        break;
                    case 5:
                        return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'scale_5_input' => $collect['value']];
                        break;
                    case 4:
                        if (!empty($collect['value'])) {
                            return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'scale_10_input' => $collect['value']];
                        }
                        break;
                    case 3:
                        return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'percentage_input' => $collect['value']];
                        break;
                    case 2:
                        return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'yes_no_input' => (!empty($collect['value']) ? $collect['value'] : 0)];
                        break;
                    case 1:
                        return ['field_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'type' => $collect['type'], 'skill_id' => $collect['skill_id'], 'yes_no_input' => (!empty($collect['value']) ? $collect['value'] : 0)];
                        break;
                }
            } else {
                
                switch ($format) {
                    
                    case 8:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'select_input' => $collect['value']];
                        }
                        break;
                    case 14:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'comment_text_input' => $collect['value']];
                        }
                        break;
                    case 15:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'address_text_input' => $collect['value']];
                        }
                        break;
                    case 11:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'date_input' => $collect['value']];
                        }
                        break;
                    case 10:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'long_text_input' => $collect['value']];
                        }
                        break;
                    case 9:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'numerical_input' => $collect['value']];
                        }
                        break;
                    case 6:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'text_input' => $collect['value']];
                        }
                        break;
                    case 5:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'scale_5_input' => $collect['value']];
                        }
                        break;
                    case 4:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'scale_10_input' => $collect['value']];
                        }
                        break;
                    case 3:
                        if (!empty($collect['value'])) {
                            return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'percentage_input' => $collect['value']];
                        }
                        break;
                    case 2:
                        return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'yes_no_input' => (!empty($collect['value']) ? $collect['value'] : 0)];
                        break;
                    case 1:
                        return ['user_id' => (isset($collect['user_id']) ? $collect['user_id'] : Auth::user()->id), 'created_by' => Auth::user()->id, 'skill_id' => $collect['skill_id'], 'checkbox_input' => (!empty($collect['value']) ? $collect['value'] : 0)];
                        break;
                    
                }
            }
        }
        
        /**
         * this function is for getting last auto incremented id for db as last recored can provide wrong count
         * @param $item
         * @return mixed
         */
        public function getLastId($item)
        {
            $table = $item->getTable();
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            $db = $hostname->website->uuid;
            $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table'";
            $lastId = DB::select($sql);

            return $lastIdBeforeInsertion = $lastId[0]->AUTO_INCREMENT;
        }
        
        /**
         * this function is used for add custom fields to specific user
         * @param $insertedIds
         * @param $cusIns
         */
        public function addUserSkill($insertedIds, $cusIns, $type, $cusContainsIns)
        {
            $userSkill = $userSkillUp = [];
            foreach ($insertedIds as $k => $insertedId) {
                if (isset($cusIns[$k])) {
                    foreach ($cusIns[$k] as $sId => $item) {
                        $userSkill[] = $this->decideToAdd($item['skill_format_id'], ['user_id' => ($insertedId - 1), 'skill_id' => $sId, 'value' => $item['value'], 'type' => $type]);
                    }
                }
            }
            foreach ($cusContainsIns as $sId => $item) {
                foreach ($cusContainsIns[$sId] as $k => $item1) {
                    $userSkills = $this->decideToAdd($item1['skill_format_id'], ['user_id' => $item['id'], 'skill_id' => $k, 'value' => $item1['value'], 'type' => $type, 'id' => $k]);
                    if ($userSkills) {
                        $userSkills['id'] = $k;
                    }
                    $userSkillUp[] = $userSkills;
                }
            }
            
            if (count($userSkill) > 0) {
                UserSkill::insert($userSkill);
            }
            $userSkillUp = collect($userSkillUp)->filter()->toArray();
            
            if (count($userSkillUp) > 0) {
                $userInstance = new UserSkill;
                $index = 'id';
                Batch::update($userInstance, $userSkillUp, $index);
            }
            
        }
        
        /**
         * this function is used for add person Entity Relationships
         * @param $insertedIds
         * @param $eniIns
         */
        public function addPersonEntity($insertedIds, $eniIns, $type = 0, $entityPosIns, $eniContainsIns, $entityPosContainsIns, $entityPosTypeIns, $entityPosTypeContainsIns, $entityPosType)
        {
            //checking type
            if ($type == 0) {
                $userId = 'user_id';
            } else {
                $userId = 'contact_id';
            }
            //init the variables
            $entityUser = $entityUserUpIns = $entityUserUpCom = $entityUserUpPre = $entityUserUn = [];
            //if we have entity insertation
            // $eniIns = collect($eniIns)->unique()->toArray();
            if (count($eniIns) > 0) {
                $company = collect($eniIns)->unique('company_name')->pluck('company_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $union = collect($eniIns)->unique('union_name')->pluck('union_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $instance = collect($eniIns)->unique('instance_name')->pluck('instance_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $press = collect($eniIns)->unique('press_name')->pluck('press_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                
                $companyPos = collect($entityPosIns)->pluck('company_position');
                $unionPos = collect($entityPosIns)->pluck('union_position');
                $unionPosType = collect($entityPosTypeIns)->pluck('membership_type');
                $instancePos = collect($entityPosIns)->pluck('instance_position');
                $pressPos = collect($entityPosIns)->pluck('press_position');
                
                $instances = Entity::whereIn(DB::raw("LOWER(long_name)"), $instance)->where('entity_type_id', 1)->get(['id', 'long_name']);
                $companies = Entity::whereIn(DB::raw("LOWER(long_name)"), $company)->where('entity_type_id', 2)->get(['id', 'long_name']);
                $unions = Entity::whereIn(DB::raw("LOWER(long_name)"), $union)->where('entity_type_id', 3)->get(['id', 'long_name']);
                $presses = Entity::whereIn(DB::raw("LOWER(long_name)"), $press)->where('entity_type_id', 4)->get(['id', 'long_name']);
                
            }
            
            //if we have already created entities
            // $eniContainsIns = collect($eniContainsIns)->unique()->toArray();
            if (count($eniContainsIns) > 0) {
                $company = collect($eniContainsIns)->unique('company_name')->pluck('company_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $union = collect($eniContainsIns)->unique('union_name')->pluck('union_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $instance = collect($eniContainsIns)->unique('instance_name')->pluck('instance_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                $press = collect($eniContainsIns)->unique('press_name')->pluck('press_name')->filter()->map(function ($name) {
                    return strtolower($name);
                });
                
                $companyPos = collect($entityPosContainsIns)->pluck('company_position');
                $unionPos = collect($entityPosContainsIns)->pluck('union_position');
                $unionPosType = collect($entityPosTypeContainsIns)->pluck('membership_type');
                $instancePos = collect($entityPosContainsIns)->pluck('instance_position');
                $pressPos = collect($entityPosContainsIns)->pluck('press_position');
                
                $instances = Entity::whereIn(DB::raw("LOWER(long_name)"), $instance)->where('entity_type_id', 1)->get(['id', 'long_name']);
                $companies = Entity::whereIn(DB::raw("LOWER(long_name)"), $company)->where('entity_type_id', 2)->get(['id', 'long_name']);
                $unions = Entity::whereIn(DB::raw("LOWER(long_name)"), $union)->where('entity_type_id', 3)->get(['id', 'long_name']);
                $presses = Entity::whereIn(DB::raw("LOWER(long_name)"), $press)->where('entity_type_id', 4)->get(['id', 'long_name']);
            }
            
            foreach ($insertedIds as $k => $insertedId) {
                
                foreach ($eniIns[$k] as $sId => $item) {
                    
                    if ($sId == 'company_name') {
                        
                        $company = $companies->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        
                        if ($company !== FALSE) {
                            $entityUser[] = [$userId => ($insertedId - 1), 'entity_id' => $companies[$company]->id, 'entity_label' => $companyPos[$k], 'created_by' => Auth::user()->id, 'membership_type' => NULL];
                        }
                    } elseif ($sId == 'instance_name') {
                        $instance = $instances->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($instance !== FALSE) {
                            $entityUser[] = [$userId => ($insertedId - 1), 'entity_id' => $instances[$instance]->id, 'entity_label' => $instancePos[$k], 'created_by' => Auth::user()->id, 'membership_type' => NULL];
                        }
                    } elseif ($sId == 'press_name') {
                        $press = $presses->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($press !== FALSE) {
                            $entityUser[] = [$userId => ($insertedId - 1), 'entity_id' => $presses[$press]->id, 'entity_label' => $pressPos[$k], 'created_by' => Auth::user()->id, 'membership_type' => NULL];
                        }
                    } else {
                        $union = $unions->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($union !== FALSE) {
                            $entityUser[] = [$userId => ($insertedId - 1), 'entity_id' => $unions[$union]->id, 'entity_label' => $unionPos[$k], 'created_by' => Auth::user()->id, 'membership_type' => (isset($unionPosType[$k]['membership_type']) ? $unionPosType[$k]['membership_type'] : 0)];
                        }
                    }
                    
                }
            }
            
            foreach ($eniContainsIns as $k => $item1) {
                
                foreach ($eniContainsIns[$k] as $sId => $item) {
                    
                    if ($sId == 'company_name') {
                        $company = $companies->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        
                        if ($company !== FALSE) {
                            $entityUserUpCom[] = [$userId => $item1['user_id'], 'entity_id' => $companies[$company]->id, 'entity_label' => (isset($entityPosContainsIns[$k]['company_position']) ? $entityPosContainsIns[$k]['company_position'] : ''), 'created_by' => Auth::user()->id];
                        }
                    } elseif ($sId == 'instance_name') {
                        $instance = $instances->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($instance !== FALSE) {
                            $entityUserUpIns[] = [$userId => $item1['user_id'], 'entity_id' => $instances[$instance]->id, 'entity_label' => (isset($entityPosContainsIns[$k]['instance_position']) ? $entityPosContainsIns[$k]['instance_position'] : ''), 'created_by' => Auth::user()->id];
                        }
                    } elseif ($sId == 'press_name') {
                        $press = $presses->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($press !== FALSE) {
                            $entityUserUpPre[] = [$userId => $item1['user_id'], 'entity_id' => $presses[$press]->id, 'entity_label' => (isset($entityPosContainsIns[$k]['press_position']) ? $entityPosContainsIns[$k]['press_position'] : ''), 'created_by' => Auth::user()->id];
                        }
                    } elseif ($sId == 'union_name') {
                        
                        $union = $unions->search(function ($value, $key) use ($item) {
                            return strtolower($value->long_name) == strtolower($item);
                        });
                        if ($union !== FALSE) {
                            $entityUserUn[] = [$userId => $item1['user_id'], 'entity_id' => $instances[$union]->id, 'entity_label' => (isset($entityPosContainsIns[$k]['union_position']) ? $entityPosContainsIns[$k]['union_position'] : ''), 'created_by' => Auth::user()->id, 'membership_type' => (isset($entityPosTypeContainsIns[$k]['membership_type']) ? $entityPosTypeContainsIns[$k]['membership_type'] : 0)];
                        }
                    }
                }
            }
            
            //union add
            if (count($entityUserUn) > 0) {
                EntityUser::insert($entityUserUn);
            }
            $existingEntity = EntityUser::whereIn($userId, array_column($entityUserUpIns, $userId))->get(['entity_id']);
            $entity = Entity::whereIn('id', $existingEntity->pluck('entity_id'))->get(['long_name', 'entity_type_id', 'id']);
            
            //instanse add
            if (count($entityUserUpIns) > 0) {
                EntityUser::whereIn($userId, array_column($entityUserUpCom, $userId))->whereIn('entity_id', $entity->where('entity_type_id', 1)->pluck('id'))->delete();
                foreach ($entityUserUpIns as $entityUserUpIn) {
                    EntityUser::create($entityUserUpIn);
                }
            }
            //press add
            if (count($entityUserUpPre) > 0) {
                EntityUser::whereIn($userId, array_column($entityUserUpCom, $userId))->whereIn('entity_id', $entity->where('entity_type_id', 4)->pluck('id'))->delete();
                foreach ($entityUserUpPre as $entityUserUpPr) {
                    EntityUser::create($entityUserUpPr);
                }
            }
            //company add
            if (count($entityUserUpCom) > 0) {
                EntityUser::whereIn($userId, array_column($entityUserUpCom, $userId))->whereIn('entity_id', $entity->where('entity_type_id', 2)->pluck('id'))->delete();
                foreach ($entityUserUpCom as $entityUserUpCo) {
                    EntityUser::create($entityUserUpCo);
                }
            }
            
            if (count($entityUser) > 0) {
                EntityUser::insert($entityUser);
            }
            
            return ['created' => (count($entityUser) > 0) ? count($entityUser) : 0, 'updated' => (count($entityUserUn) + count($entityUserUpIns) + count($entityUserUpCom) + count($entityUserUpPre))];
        }
        
        /**
         * this function is used to remove saved excel directory
         * @param $path
         */
        public function removeExcel($path, $file = NULL)
        {
            if (empty($file))
                return File::deleteDirectory($path);
            else
                return File::delete($file);
        }
    }