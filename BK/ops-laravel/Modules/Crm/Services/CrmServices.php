<?php

    namespace Modules\Crm\Services;

    use App\Entity;
    use App\EntityUser;
    use App\Model\Contact;
    use App\Model\Skill;
    use App\Model\SkillTabs;
    use App\Model\UserSkill;
    use App\User;
    use App\WorkshopMeta;
    use function Couchbase\defaultDecoder;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use Modules\Crm\Entities\CrmFilter;
    use Modules\Crm\Entities\CrmFilterCondition;
    use Modules\Crm\Entities\CrmFilterField;
    use Modules\Crm\Entities\CrmFilterRule;
    use Modules\Crm\Entities\CrmFilterType;
    use Modules\Crm\Entities\ViewSkillUser;
    use Modules\Crm\Entities\ViewSkillContact;
    use Modules\Crm\Entities\ViewSkillCompany;
    use Modules\Crm\Entities\ViewSkillPress;
    use Modules\Crm\Entities\ViewSkillUnion;
    use Modules\Crm\Entities\ViewSkillInstance;
    use Modules\Crm\Rules\Alphanumeric;
    use Modules\Crm\Rules\CrmFilterComponentExist;
    use Modules\Crm\Rules\CrmFilterConditionExist;
    use Modules\Crm\Rules\CrmFilterFieldNameExist;
    use Modules\Crm\Rules\CrmFilterNameRequire;
    use Modules\Crm\Rules\CrmFilterNameUnique;
    use Modules\Crm\Rules\CrmFilterTypeExist;
    use Modules\Crm\Rules\CrmFilterSkillExist;
    use Validator;
    use Illuminate\Support\Facades\DB;
    use Batch;

    class CrmServices
    {
        protected static $instance;
        public $request;
        public $validation;
        protected $filter;
        protected $filterData;
        protected $save_selected_fields = FALSE;
        protected $unionSuType = FALSE;
        protected $selectedFilterType = [];
        protected $selectedFilterConditions = [];
        protected $tableNames = [];
        protected $selectedFilterFields = [
            'entity'              => [
                ['short_name' => 'long_name', 'name' => 'Long Name'],
                ['short_name' => 'short_name', 'name' => 'Short Name'],
                ['short_name' => 'email', 'name' => 'Email'],
                ['short_name' => 'address1', 'name' => 'Address'],
                ['short_name' => 'entity_website', 'name' => 'Website'],
                ['short_name' => 'city', 'name' => 'City'],
                ['short_name' => 'country', 'name' => 'Country'],
                ['short_name' => 'zip_code', 'name' => 'Zip'],
                ['short_name' => 'fax', 'name' => 'Fax'],
                ['short_name' => 'phone', 'name' => 'Phone'],
                ['short_name' => 'industry_id', 'name' => 'Industry'],

            ],
            'user'                => [
                ['short_name' => 'fname', 'name' => 'First name'],
                ['short_name' => 'lname', 'name' => 'Last name'],
                ['short_name' => 'email', 'name' => 'Email'],
                ['short_name' => 'phone', 'name' => 'Phone'],
                ['short_name' => 'mobile', 'name' => 'Mobile'],
                ['short_name' => 'address', 'name' => 'Address'],
                ['short_name' => 'postal', 'name' => 'Postal'],
                ['short_name' => 'city', 'name' => 'City'],
                ['short_name' => 'country', 'name' => 'Country'],
            ],
            'newsletter_contacts' => [
                ['short_name' => 'fname', 'name' => 'First name'],
                ['short_name' => 'lname', 'name' => 'Last name'],
                ['short_name' => 'email', 'name' => 'Email'],
                ['short_name' => 'phone', 'name' => 'Phone'],
                ['short_name' => 'mobile', 'name' => 'Mobile'],
                ['short_name' => 'address', 'name' => 'Address'],
                ['short_name' => 'postal', 'name' => 'Postal'],
                ['short_name' => 'city', 'name' => 'City'],
                ['short_name' => 'country', 'name' => 'City'],
            ],
        ];

        protected $skipCustomFields = [
            'comment_text_input',
            'file_input',
            //'select_input',
            'mandatory_acceptance_input',
            'mandatory_checkbox_input',
            'mandatory_file_input',
            'blank_line',
            'conditional_checkbox_input',
            'referrer_input',
            'long_text_input',
            'address_text_input',
            // 'radio_input',
        ];
        protected $ENTITIES = [
            'instance' => 1,
            'company'  => 2,
            'union'    => 3,
            'press'    => 4,
        ];

        //DEFINING SINGLETON CLASS
        public static function getInstance()
        {
            if (is_null(CrmServices::$instance)) {
                CrmServices::$instance = new CrmServices();
            }
            return CrmServices::$instance;
        }

        /**
         * set filter object from database
         *
         * @param $id
         */
        public function setFiler($id)
        {
            $this->filter = CrmFilter::find($id);

//        dd(boolval($this->filter->save_selected_fields));
            if ($this->filter)
                $this->save_selected_fields = boolval($this->filter->save_selected_fields);
        }

        /**
         * @param null $from
         * @param null $to
         * @param bool $all
         * @return mixed
         */
        public function listFilters($filterTypeId = 1, $to = NULL, $all = FALSE)
        {
            if (empty($filterTypeId)) {
                return collect([]);
            }
            $permissions = \Auth::user()->permissions;
            $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?? 0;
            $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?? 0;
            $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?? 0;
            $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?? 0;

            $filters = CrmFilter::where('filter_type_id', $filterTypeId)->where(function ($n) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment) {
                $n->orderBy('created_at', 'desc');
                if ((!(in_array(Auth::user()->role, ['M1', 'M0'])))) {
                    if ((!$crmAdmin)) {
                        if ((!$crmEditor) || $crmAssistance || $crmRecruitment) {
                            $n->where('created_by', Auth::user()->id);
                        }
                    }
                }
            })->get(['id', 'name']);

            return $filters;
        }

        /**
         * check filter variable
         *
         * @return bool
         */
        public function hasFilter()
        {
            return ($this->filter) ? TRUE : FALSE;
        }

        /**
         * get filter object
         *
         * @return mixed
         */
        public function getFiler()
        {
            return $this->filter;
        }

        /**
         * @return array
         */
        public function previewResult()
        {
            if ($this->checkFilterData() || !empty($this->selectedFilterConditions)) {
                if ($this->selectedFilterType['identifier'] == 'user') {
                    $data = $this->getFilterDataByUser();
                } else {
                    $data = $this->getFilterDataByEntity();
                }
                if ($this->filter) {
                    $value = json_decode($this->filter->getFilterField->value, TRUE);
                    $tabs = [];
                    //dd($value['custom'], 'dd');
                    if (isset($value['custom'])) {
                        $tabsArray = Skill::whereIn('id', $value['custom'])->with(['skillTab:id,name,tab_type', 'skillSelect'])->get();

                        if (!empty($tabsArray)) {
                            foreach ($tabsArray as $tab) {
                                $key = collect($value['custom'])->search($tab->id);
                                if ($key !== FALSE) {
                                    $tabs[$key] = [
                                        'id'            => $tab->id,
                                        'name'          => $tab->name,
                                        'short_name'    => $tab->short_name,
                                        'field_type_id' => $tab->skill_format_id,
                                        'skill_tab_id'  => $tab->skill_tab_id,
                                        'skill_tab'     => $tab->skillTab,
                                        'tooltip_en'    => $tab->tooltip_en,
                                        'tooltip_fr'    => $tab->tooltip_fr,
                                        'skill_option'  => $tab->skillSelect,

                                    ];
                                } else {
                                    $tabs[] = [
                                        'id'            => $tab->id,
                                        'name'          => $tab->name,
                                        'short_name'    => $tab->short_name,
                                        'field_type_id' => $tab->skill_format_id,
                                        'skill_tab_id'  => $tab->skill_tab_id,
                                        'skill_tab'     => $tab->skillTab,
                                        'skill_option'  => $tab->skillSelect,
                                        'tooltip_en'    => $tab->tooltip_en,
                                        'tooltip_fr'    => $tab->tooltip_fr,
                                    ];
                                }

                            }
                        }
                        ksort($tabs);
                    }

                    if ($this->filter->filter_type_id != 1) {
                        $value[$this->selectedFilterType['identifier']][] = ['short_name' => 'family_id', 'name' => 'Family'];
                    }

                    $fields = [
                        'default' => $this->getLabel($value[$this->filter->getFilterType->identifier]),
                        'custom'  => $tabs,
                    ];
                } else {
                    $tabs = [];
                    $fieldsIds = array_column($this->selectedFilterConditions, 'field_id');

                    if (isset($this->selectedFilterFields['custom']) || count($fieldsIds) > 0) {
                        if (count($this->selectedFilterFields['custom']) > 0) {
                            $fIds = $this->selectedFilterFields['custom'];
                        } elseif (count($fieldsIds) > 0) {
                            $fIds = $fieldsIds;
                        } else {
                            $fIds = [];
                        }
                        if ($this->selectedFilterType['identifier'] == 'user') {
                            $tabsArray = Skill::whereHas('skillTab', function ($q) {
                                $q->whereIn('tab_type', [0, 1]);
                            })->whereIn('id', $fIds)->with(['skillTab:id,name,tab_type', 'skillSelect'])->get();
                        } elseif ($this->selectedFilterType['identifier'] == 'entity') {
                            $tabsArray = Skill::whereHas('skillTab', function ($q) {
                                $q->whereNotIn('tab_type', [0, 1]);
                            })->whereIn('id', $fIds)->with(['skillTab:id,name,tab_type', 'skillSelect'])->get();
                        } else {
                            $tabsArray = Skill::whereIn('id', $fIds)->with(['skillTab:id,name', 'skillSelect'])->get();
                        }
                        if (!empty($tabsArray)) {
                            foreach ($tabsArray as $tab) {
                                $tabs[] = [
                                    'id'            => $tab->id,
                                    'name'          => $tab->name,
                                    'short_name'    => $tab->short_name,
                                    'skill_tab_id'  => $tab->skill_tab_id,
                                    'field_type_id' => $tab->skill_format_id,
                                    'skill_tab'     => $tab->skillTab,
                                    'skill_option'  => $tab->skillSelect,
                                    'tooltip_en'    => $tab->tooltip_en,
                                    'tooltip_fr'    => $tab->tooltip_fr,
                                ];
                            }
                        }
                    }


                    if ($this->request->filter_type_id != 1) {
                        $this->selectedFilterFields[$this->selectedFilterType['identifier']][] = ['short_name' => 'family_id', 'name' => 'Family'];
                    }
                    $fields = [
                        'default' => $this->getLabel($this->selectedFilterFields[$this->selectedFilterType['identifier']]),
                        'custom'  => $tabs,
                    ];
                }
                if ($this->filter) {
                    $typeId = $this->filter->filter_type_id;
                    $name = $this->filter->getFilterType->name;
                    $defaultFilter = $this->filter->is_default;
                } else {
                    $filterType = CrmFilterType::find($this->request->filter_type_id);
                    $typeId = $filterType->id;
                    $name = $filterType->name;
                    $defaultFilter = 0;
                }
                //we are using array value to sent response as a array
                $response = [
                    'response_data'          => array_values($data[0]),
                    'filter_type_id'         => $typeId,
                    'default_filter'         => $defaultFilter,
                    'filter_type'            => $name,
                    'filter_name'            => ($this->filter) ? $this->filter->name : $this->request->filter_name,
                    'selected_fields'        => $fields,
                    'conditions'             => $this->selectedFilterConditions,
                    'query'                  => $data[1],
                    'total_pages'            => isset($data['total_page']) ? $data['total_page'] : 0,
                    'current_page'           => isset($data['current_page']) ? $data['current_page'] : 0,
                    'no_of_records_per_page' => isset($data['no_of_records_per_page']) ? $data['no_of_records_per_page'] : 0,
                    'total_records'          => isset($data['total_records']) ? $data['total_records'] : 0,
                ];
                return $response;
            }
            return [];
        }

        protected function getFilterDataByUser()
        {

            $skills = [];
            $data = [];
            $select = [];
            $userAnd = [];
            $userOr = [];
            $contactAnd = [];
            $contactOr = [];

            $contactSelect = [];
            $is_default = FALSE;
            $contactInCondition = collect($this->selectedFilterConditions)->where('component', 'contact')->where('is_default', FALSE)->count();
            $userInCondition = collect($this->selectedFilterConditions)->where('component', 'user')->where('is_default', FALSE)->count();
            $personInCondition = collect($this->selectedFilterConditions)->where('component', 'persons')->where('is_default', TRUE)->count();
            $entityInCondition = collect($this->selectedFilterConditions)->whereIn('component', ['company', 'union', 'press', 'instance'])->where('is_default', TRUE)->count();
            $userDefCondition = collect($this->selectedFilterConditions)->where('component', 'user')->where('is_default', TRUE)->count();
            foreach ($this->selectedFilterConditions as $condition) {

                $user = new User();
                $contact = new \App\Contact();

                if ($condition['condition'] == 'all' && isset($this->filter->is_default)) {

                    $is_default = TRUE;
                    $getQuery['user']['unionDef'][] = $user->select('id')->toSql();
                    $getQuery['user']['unionBindDef'][] = $user->getBindings();
                    $getQuery['contact']['unionDef'][] = $contact->select('id')->toSql();
                    $getQuery['contact']['unionBindDef'][] = $contact->getBindings();
                    break;
                }
                if ($condition['is_default']) {
                    $is_default = TRUE;
                    if ($condition['component'] == 'company') {
                        $user = $this->userEntityQueryBuilder($user, $condition, 2);
                        $contact = $this->userEntityQueryBuilder($contact, $condition, 2);
                    } else if ($condition['component'] == 'union') {
                        $user = $this->userEntityQueryBuilder($user, $condition, 3);
                        $contact = $this->userEntityQueryBuilder($contact, $condition, 3);
                    } else if ($condition['component'] == 'instance') {
                        $user = $this->userEntityQueryBuilder($user, $condition, 1);
                        $contact = $this->userEntityQueryBuilder($contact, $condition, 1);
                    } else if ($condition['component'] == 'press') {
                        $user = $this->userEntityQueryBuilder($user, $condition, 4);
                        $contact = $this->userEntityQueryBuilder($contact, $condition, 4);
                    } else if ($condition['component'] == 'user' && (($contactInCondition == 0 && $userInCondition > 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0))) {
                        $operator = $this->getConditionOperator($condition['condition']);
                        $condition['value'] = str_replace(':value', $condition['value'], $operator->value);

                        if (empty($condition['value']))
                            $condition['value'] = NULL;
                        $condition['operator'] = $operator->operator;
                        if ($condition['condition_type'] == 'and') {
                            $user = $user->where($condition['field_name'], $condition['operator'], $condition['value']);
                        } else {
                            $user = $user->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                        }
                        if ($operator->short_name == "is_empty") {
                            $user = $user->whereNull($condition['field_name']);
                        } else if ($operator->short_name == "is_not_empty") {
                            $user = $user->whereNotNull($condition['field_name']);
                        }
                    } else if ($condition['component'] == 'contact' && (($contactInCondition > 0 && $userInCondition == 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0))) {
                        $operator = $this->getConditionOperator($condition['condition']);
                        $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                        if (empty($condition['value']))
                            $condition['value'] = NULL;
                        $condition['operator'] = $operator->operator;
                        $field = $condition['field_name'];
                        if ($condition['condition_type'] == 'and') {
                            $contact = $contact->where($condition['field_name'], $condition['operator'], $condition['value']);
                        } else {
                            $contact = $contact->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                        }
                        if ($operator->short_name == "is_empty") {
                            $contact = $contact->whereNull($condition['field_name']);
                        } else if ($operator->short_name == "is_not_empty") {
                            $contact = $contact->whereNotNull($condition['field_name']);
                        }

                        $contactSelect = DB::raw("newsletter_contacts.$field as $field");
                    } else if ($condition['component'] == 'persons') {
                        if (($contactInCondition > 0 && $userInCondition == 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0)) {
                            $operator = $this->getConditionOperator($condition['condition']);
                            $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                            if (empty($condition['value']))
                                $condition['value'] = NULL;

                            $condition['operator'] = $operator->operator;
                            $field = $condition['field_name'];
                            if ($condition['condition_type'] == 'and') {
                                $contact = $contact->where($condition['field_name'], $condition['operator'], $condition['value']);
                            } else {
                                $contact = $contact->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                            }
                            if ($operator->short_name == "is_empty") {
                                $contact = $contact->whereNull($condition['field_name']);
                            } else if ($operator->short_name == "is_not_empty") {
                                $contact = $contact->whereNotNull($condition['field_name']);
                            }
                            $contactSelect[] = DB::raw("newsletter_contacts.$field as $field");
                        }

                        if (($contactInCondition == 0 && $userInCondition > 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0)) {
                            $operator = $this->getConditionOperator($condition['condition']);
                            $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                            if (empty($condition['value']))
                                $condition['value'] = NULL;
                            $condition['operator'] = $operator->operator;
                            if ($condition['condition_type'] == 'and') {
                                $user = $user->where($condition['field_name'], $condition['operator'], $condition['value']);
                            } else {
                                $user = $user->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                            }
                            if ($operator->short_name == "is_empty") {
                                $user = $user->whereNull($condition['field_name']);
                            } else if ($operator->short_name == "is_not_empty") {
                                $user = $user->whereNotNull($condition['field_name']);
                            }

                            if ($this->save_selected_fields) {
                                $field = $condition['field_name'];
                                $select[] = DB::raw("users.$field as $field");
                                //                    $contactSelect[] = DB::raw("newsletter_contacts.$field as $field");
                            }
                        }
                    } else if ($condition['component'] == 'user' && (($contactInCondition == 0 && $userInCondition == 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0) || $userDefCondition > 0)) {
                        $operator = $this->getConditionOperator($condition['condition']);
                        $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                        $condition['operator'] = $operator->operator;

                        if ($condition['value'] == 'crmAdmin' || $condition['value'] == 'crmEditor' || $condition['value'] == 'crmAssistance' || $condition['value'] == 'crmRecruitment' && $condition['condition_type'] == 'and') {
                            $val = ($condition['operator'] == '=') ? 1 : 0;
                            $user = $user->where('permissions->' . $condition['value'], $condition['operator'], $val);
                        } elseif ($condition['value'] == 'W0' || $condition['value'] == 'W1' && $condition['condition_type'] == 'and') {
                            $_role = ($condition['field_name'] == 'W0') ? 1 : 2;
                            $meta = WorkshopMeta::where('role', $condition['operator'], $_role)->groupBy('user_id')->pluck('user_id')->toArray();
                            $user = $user->whereIn('id', $meta);
                        } elseif ($condition['value'] == 'crmAdmin' || $condition['value'] == 'crmEditor' || $condition['value'] == 'crmAssistance' || $condition['value'] == 'crmRecruitment' && $condition['condition_type'] == 'or') {
                            $val = ($condition['operator'] == '=') ? 1 : 0;
                            $user = $user->OrWhere('permissions->' . $condition['field_name'], $condition['operator'], $val);
                        } elseif ($condition['value'] == 'W0' || $condition['value'] == 'W1' && $condition['condition_type'] == 'or') {
                            $_role = ($condition['field_name'] == 'W0') ? 1 : 2;
                            $meta = WorkshopMeta::where('role', $condition['operator'], $_role)->groupBy('user_id')->pluck('user_id')->toArray();
                            $user = $user->OrWhereIn('id', $meta);
                        } elseif ($condition['condition_type'] == 'or') {
                            $user = $user->OrWhere('role', $condition['operator'], $condition['value']);
                        } else {
                            $user = $user->where('role', $condition['operator'], $condition['value']);
                        }


                        /* if (empty($condition['value']))
                            $condition['value'] = NULL;

                        if ($condition['condition_type'] == 'and') {
                            $user = $user->where($condition['field_name'], $condition['operator'], $condition['value']);
                        } else {
                            $user = $user->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                        }switch ($condition['field_name']) {
                            case 'C1':
                                $user = $user->whereRaw('JSON_CONTAINS(permissions, \'{"crmAdmin": ' . $val . '}\')');
                                break;
                            case 'C2':
                                $user = $user->whereRaw('JSON_CONTAINS(permissions, \'{"crmEditor": ' . $val . '}\')');
                                break;
                            case 'C3':
                                $user = $user->whereRaw('JSON_CONTAINS(permissions, \'{"crmFinance": ' . $val . '}\')');
                                break;
                            case 'C4':
                                $user = $user->whereRaw('JSON_CONTAINS(permissions, \'{"crmRecruitment": ' . $val . '}\')');
                                break;
                            case 'C5':
                                $user = $user->whereRaw('JSON_CONTAINS(permissions, \'{"crmAssistance": ' . $val . '}\')');
                                break;
                            case 'W0':
                                $meta = WorkshopMeta::where('role', 1)->groupBy('user_id')->pluck('user_id')->toArray();
                                // echo implode(',',$meta);die;
                                $user = $user->whereIn('id', $meta);
                                break;

                            case 'W1':
                                $meta = WorkshopMeta::where('role', 2)->groupBy('user_id')->pluck('user_id');
                                $user = $user->whereIn('id', $meta);
                                break;

                            default:
                                $user = $user->where('role', $condition['field_name']);
                                break;
                        }*/
                    }

                    if ($condition['condition_type'] == 'and') {
                        if (($contactInCondition == 0 && $userInCondition > 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0) || ($contactInCondition == 0 && $userInCondition == 0 && $entityInCondition > 0) || $userDefCondition > 0) {
                            $getQuery['user']['intersectDef'][] = $user->select('id')->toSql();
                            $getQuery['user']['intersectBindDef'][] = $user->getBindings();
                        }
                        if (($contactInCondition > 0 && $userInCondition == 0) || ($contactInCondition == 0 && $userInCondition == 0 && $personInCondition > 0) || ($contactInCondition == 0 && $userInCondition == 0 && $entityInCondition > 0)) {
                            $getQuery['contact']['intersectDef'][] = $contact->select('id')->toSql();
                            $getQuery['contact']['intersectBindDef'][] = $contact->getBindings();
                        }
                    } else {
                        $getQuery['user']['unionDef'][] = $user->select('id')->toSql();
                        $getQuery['user']['unionBindDef'][] = $user->getBindings();
                        $getQuery['contact']['unionDef'][] = $contact->select('id')->toSql();
                        $getQuery['contact']['unionBindDef'][] = $contact->getBindings();
                    }

                } else {

                    if ($this->save_selected_fields) {
                        $field = $condition['field_name'];
                        $select[] = DB::raw("null as $field");
                        //                    $contactSelect[] = DB::raw("null as $field");
                    }
                    $skills[] = $condition;
                }
            }
            foreach ($this->selectedFilterFields['user'] as $key => $field) {
                $shortName = $field['short_name'];
                $name = $field['name'];
                $select[] = DB::raw("users.$shortName as $shortName");
            }

            foreach ($this->selectedFilterFields['newsletter_contacts'] as $key => $field) {
                $shortName = $field['short_name'];
                $name = $field['name'];
                $contactSelect[] = DB::raw("newsletter_contacts.$shortName as $shortName");
            }
            $select[] = DB::raw("users.id as user_id");
            $select[] = DB::raw("users.id as id");
            //        $select[] = DB::raw("entities.long_name as long_name");
            $select[] = DB::raw("'users' as table_name");

            $contactSelect[] = DB::raw("newsletter_contacts.id as user_id");
            $contactSelect[] = DB::raw("newsletter_contacts.id as id");
            $contactSelect[] = DB::raw("'newsletter_contacts' as table_name");
            $dataC = [];

            if ($is_default) {
                //  $user->with(['entity']);
                if (isset($getQuery['user']['intersectDef'])) {
                    $userAnd = $this->getIntersectQueryData($getQuery['user']['intersectDef'], $getQuery['user']['intersectBindDef']);
                    if (count($userAnd) <= 0) {
                        $intersectDefU = TRUE;
                    }
                }
                if (isset($getQuery['user']['unionBindDef'])) {
                    $userOr = $this->getUnionQueryData($getQuery['user']['unionDef'], $getQuery['user']['unionBindDef']);
                    if (count($userOr) <= 0) {
                        $unionDefU = TRUE;
                    }
                }
//                dd($userAnd,$userOr);
                if (isset($getQuery['contact']['intersectDef'])) {
                    $contactAnd = $this->getIntersectQueryData($getQuery['contact']['intersectDef'], $getQuery['contact']['intersectBindDef']);
                    if (count($contactAnd) <= 0) {
                        $intersectDefC = TRUE;
                    }
                }
                if (isset($getQuery['contact']['unionBindDef'])) {
                    $contactOr = $this->getUnionQueryData($getQuery['contact']['unionDef'], $getQuery['contact']['unionBindDef']);
                    if (count($contactOr) <= 0) {
                        $unionDefC = TRUE;
                    }
                }


            }


            $ids = [];
            $contactIds = [];
            $skillIds = [];
            $selected = [];
            $contains = [];
            $contactSkills = [];
            $userCustomAnd = [];
            $contactCustomAnd = [];
            $entityCustomAnd = [];
            $userCustomOr = [];
            $contactCustomOr = [];
            $entityCustomOr = [];


            if (!empty($skills)) {
                foreach ($skills as $skill) {
                    if ($skill['component'] != 'user') {
                        $contactSkills[] = $skill;
                        continue;
                    }

                    $userSkill = new ViewSkillUser();
                    $skillIds[] = $skill['field_id'];
                    $skillObject = Skill::find($skill['field_id']);
                    if ($skillObject && (!in_array($skillObject->skillFormat->short_name, $this->skipCustomFields))) {

                        $userSkill = $userSkill->where("skill_id", $skillObject->id);
                        $skillObject->skillFormat;
                        $operator = $this->getConditionOperator($skill['condition']);
                        $skill['value'] = str_replace(':value', $skill['value'], $operator->value);

                        if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                            if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } elseif ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('original_value');
                                $skill['value'] = '0';
//                                $skill['value'] = 'is not null';
                            } elseif ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } else {
                                $skill['value'] = '0';
                            }
                        }

                        if ($skillObject->skillFormat->short_name == 'date_input') {
                            $sField = 'whereDate';
                            /*  if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                  $userSkill = $userSkill->whereNotNull('original_value');
                              }*/

                        } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                            $skill['value'] = floatval($skill['value']);
                            $sField = 'where';
                        } else {
                            $sField = 'where';
                        }
                        $skill['operator'] = $operator->operator;
                        if ($skill['condition_type'] == 'and') {
                            $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], 'persons');
                            $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*->where('short_name',$skillObject->skillFormat->short_name)*/
                            ;
                        } else {
//                            $userSkill = $this->addDefaultCondition($userSkill,$skill['condition_type']);
                            $userSkill = $userSkill->where(function ($a) use ($skill, $skillObject, $sField) {
                                $a->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']);
                            });

//                            $userSkill = $userSkill->orWhere('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*orWhere($skillObject->skillFormat->short_name, $skill['operator'], $skill['value'])*/

                        }
                        /*if ($operator->short_name == "is_empty") {
                            $userSkill = $userSkill->whereNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                        } else if ($operator->short_name == "is_not_empty") {
                            $userSkill = $userSkill->whereNotNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                        }*/
                        $field = $skillObject->skillFormat->short_name;
                        $selected[] = DB::raw("user_skills.$field as $field");
//                    var_export($userSkill->toSql());
//                        $getQuery[] = $userSkill->toSql();
//                        $getQuery[]['bind'] = $userSkill->getBindings();
                        if ($skill['condition_type'] == 'and') {
                            $getQuery['intersect'][] = $userSkill->select('user_id')->toSql();
                            $getQuery['intersectBind'][] = $userSkill->getBindings();
                        } else {
                            $getQuery['union'][] = $userSkill->select('user_id')->toSql();
                            $getQuery['unionBind'][] = $userSkill->getBindings();
                        }
                    }
                }
                //=====================testing code==================//
                if (isset($getQuery['intersect'])) {
                    $userCustomAnd = $this->getIntersectQueryData($getQuery['intersect'], $getQuery['intersectBind']);
                    if (count($userCustomAnd) <= 0) {
                        $intersectElseU = TRUE;
                    }
                }
                if (isset($getQuery['union'])) {
                    $userCustomOr = $this->getUnionQueryData($getQuery['union'], $getQuery['unionBind']);
                    if (count($userCustomOr) <= 0) {
                        $unionElseU = TRUE;
                    }
                }
            }

            $entityUserSkills = [];
            $entityIds = [];

            if (!empty($contactSkills)) {
                //$userSkill = $userSkill->where('type', 'contact');
                foreach ($contactSkills as $skill) {
                    if ($skill['component'] != 'contact') {
                        $entityUserSkills[] = $skill;
                        continue;
                    }
                    $userSkill = new ViewSkillContact();
                    $skillIds[] = $skill['field_id'];
                    $skillObject = Skill::find($skill['field_id']);
                    if (isset($skillObject->skillFormat)) {
                        if ($skillObject && (!in_array($skillObject->skillFormat->short_name, $this->skipCustomFields))) {
                            //$userSkill = $this->addDefaultCondition($userSkill);
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);

                            $operator = $this->getConditionOperator($skill['condition']);
                            $skill['value'] = str_replace(':value', $skill['value'], $operator->value);
                            if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                                if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                    $userSkill = $userSkill->whereNull('original_value');
                                    $skill['value'] = '0';
                                } elseif ($operator->short_name == "is_not_empty") {
                                    $userSkill = $userSkill->whereNotNull('original_value');
                                    $skill['value'] = '0';
//                                    $skill['value'] = 'is not null';
                                } elseif ($operator->short_name == "is_empty") {
                                    $userSkill = $userSkill->whereNull('original_value');
                                    $skill['value'] = '0';
                                } else {
                                    $skill['value'] = '0';
                                }
                            }
                            $skill['operator'] = $operator->operator;
                            if ($skillObject->skillFormat->short_name == 'date_input') {
                                $sField = 'whereDate';
                                /* if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                     $userSkill = $userSkill->whereNotNull('original_value');
                                 }*/

                            } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                                $skill['value'] = floatval($skill['value']);
                                $sField = 'where';
                            } else {
                                $sField = 'where';
                            }
                            if ($skill['condition_type'] == 'and') {
                                $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], 'persons');
                                $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*->where('short_name',$skillObject->skillFormat->short_name)*/
                                ;
                            } else {
                                $userSkill = $userSkill->where(function ($a) use ($skill, $skillObject, $sField) {
                                    $a->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']);
                                });/*orWhere($skillObject->skillFormat->short_name, $skill['operator'], $skill['value'])*/;
                            }
                            /*if ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                            } else if ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                            }*/
                            $field = $skillObject->skillFormat->short_name;
                            $selected[] = DB::raw("user_skills.$field as $field");
                            if ($skill['condition_type'] == 'and') {
                                $getQuery['conCus']['intersect'][] = $userSkill->select('user_id')->toSql();
                                $getQuery['conCus']['intersectBind'][] = $userSkill->getBindings();
                            } else {
                                $getQuery['conCus']['union'][] = $userSkill->select('user_id')->toSql();
                                $getQuery['conCus']['unionBind'][] = $userSkill->getBindings();
                            }
//                            $getQuery[] = $userSkill->toSql();
//                            $getQuery[]['bind'] = $userSkill->getBindings();
//                            $contactIds = array_merge($contactIds, array_values(array_filter($userSkill->pluck('user_id')->toArray())));
                        }
                    }
                }
                //=====================testing code==================//
                if (isset($getQuery['conCus']['intersect'])) {
                    $contactCustomAnd = $this->getIntersectQueryData($getQuery['conCus']['intersect'], $getQuery['conCus']['intersectBind']);
                    if (count($contactCustomAnd) <= 0) {
                        $intersectElseC = TRUE;
                    }
                }
                if (isset($getQuery['conCus']['union'])) {
                    $contactCustomOr = $this->getUnionQueryData($getQuery['conCus']['union'], $getQuery['conCus']['unionBind']);
                    if (count($contactCustomOr) <= 0) {
                        $unionElseC = TRUE;
                    }
                }
            }

            if (!empty($entityUserSkills)) {

//                $userSkill = new ViewSkillCompany();
                //            $userSkill = $userSkill->where('type', 'contact');
                foreach ($entityUserSkills as $skill) {
                    $skillIds[] = $skill['field_id'];
                    $skillObject = Skill::find($skill['field_id']);

                    if ($skillObject) {
                        if ($skill['component'] == 'company' /*&& !isset($contains['skill_company_view'])*/) {
                            $userSkill = new ViewSkillCompany();
                            $contains[$userSkill->getTable()] = 0;
                        } else if ($skill['component'] == 'union' /*&& !isset($contains['skill_union_view'])*/) {
                            $userSkill = new ViewSkillUnion();
                            $contains[$userSkill->getTable()] = 0;
//                        $userSkill = $userSkill->where('type', 'union');
                        } else if ($skill['component'] == 'instance' /*&& !isset($contains['skill_instance_view'])*/) {
                            $userSkill = new ViewSkillInstance();
                            $contains[$userSkill->getTable()] = 0;
//                        $userSkill = $userSkill->where('type', 'instance');
                        } else if ($skill['component'] == 'press' /*&& !isset($contains['skill_press_view'])*/) {
//                            if (!isset($contains['skill_press_view'])) {
                            $userSkill = new ViewSkillPress();
                            $contains[$userSkill->getTable()] = 0;
//                            }

//                        $userSkill = $userSkill->where('type', 'instance');
                        }

                        $skillObject->skillFormat;
                        $operator = $this->getConditionOperator($skill['condition']);
                        $skill['value'] = str_replace(':value', $skill['value'], $operator->value);
                        if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                            if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } elseif ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('original_value');
                                $skill['value'] = '0';
//                                $skill['value'] = 'is not null';
                            } elseif ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } else {
                                $skill['value'] = '0';
                            }
                        }
                        $skill['operator'] = $operator->operator;
                        if ($skillObject->skillFormat->short_name == 'date_input') {
                            $sField = 'whereDate';
                            /* if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                 $userSkill = $userSkill->whereNotNull('original_value');
                             }*/


                        } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                            $skill['value'] = floatval($skill['value']);
                            $sField = 'where';
                        } else {
                            $sField = 'where';
                        }
                        if ($skill['condition_type'] == 'and') {
                            $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], $skill['component']);
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);
                            $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*->where('short_name',$skillObject->skillFormat->short_name)*/
                            ;
                        } else {
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);
                            $userSkill = $userSkill->where(function ($a) use ($skill, $skillObject, $sField) {
                                $a->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']);
                            });/* $userSkill = $userSkill->orWhere('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])orWhere($skillObject->skillFormat->short_name, $skill['operator'], $skill['value'])*/;

                        }
                        /*if ($operator->short_name == "is_empty") {
                            $userSkill = $userSkill->whereNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                        } else if ($operator->short_name == "is_not_empty") {
                            $userSkill = $userSkill->whereNotNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                        }*/
                        $field = $skillObject->skillFormat->short_name;
                        $selected[] = DB::raw("user_skills.$field as $field");
                        if ($skill['condition_type'] == 'and') {
                            $getQuery['entCus']['intersect'][] = $userSkill->select('entity_id')->toSql();
                            $getQuery['entCus']['intersectBind'][] = $userSkill->getBindings();
                        } else {
                            $getQuery['entCus']['union'][] = $userSkill->select('entity_id')->toSql();
                            $getQuery['entCus']['unionBind'][] = $userSkill->getBindings();
                        }

                    }
                }

                //=====================testing code==================//
                if (isset($getQuery['entCus']['intersect'])) {
                    $entityCustomAnd = $this->getIntersectQueryData($getQuery['entCus']['intersect'], $getQuery['entCus']['intersectBind']);
                    if (count($entityCustomAnd) <= 0) {
                        $intersectElseE = TRUE;
                    }
                }
                if (isset($getQuery['entCus']['union'])) {
                    $entityCustomOr = $this->getUnionQueryData($getQuery['entCus']['union'], $getQuery['entCus']['unionBind']);
                    if (count($entityCustomOr) <= 0) {
                        $unionElseE = TRUE;
                    }
                }
            }

            $userAnd = (!empty($userAnd)) ? array_column($userAnd, 'id') : [];
            $userOr = (!empty($userOr)) ? array_column($userOr, 'id') : [];
            $userCustomAnd = (!empty($userCustomAnd)) ? array_column($userCustomAnd, 'user_id') : [];
            $userCustomOr = (!empty($userCustomOr)) ? array_column($userCustomOr, 'user_id') : [];
            $contactAnd = (!empty($contactAnd)) ? array_column($contactAnd, 'id') : [];
            $contactOr = (!empty($contactOr)) ? array_column($contactOr, 'id') : [];
            $contactCustomAnd = (!empty($contactCustomAnd)) ? array_column($contactCustomAnd, 'user_id') : [];
            $contactCustomOr = (!empty($contactCustomOr)) ? array_column($contactCustomOr, 'user_id') : [];

            $entityCustomAnd = (!empty($entityCustomAnd)) ? array_column($entityCustomAnd, 'entity_id') : [];
            $entityCustomOr = (!empty($entityCustomOr)) ? array_column($entityCustomOr, 'entity_id') : [];

            //here we have data for result of querys data is null or not
            $intersectElseU = (isset($intersectElseU) ? 1 : 0);
            $intersectElseC = (isset($intersectElseC) ? 1 : 0);
            $intersectElseE = (isset($intersectElseE) ? 1 : 0);
            $unionElseE = (isset($unionElseE) ? 1 : 0);
            $unionElseC = (isset($unionElseC) ? 1 : 0);
            $unionElseU = (isset($unionElseU) ? 1 : 0);
            $intersectDefC = (isset($intersectDefC) ? 1 : 0);
            $intersectDefU = (isset($intersectDefU) ? 1 : 0);
            $unionDefC = (isset($unionDefC) ? 1 : 0);
            $unionDefU = (isset($unionDefU) ? 1 : 0);
            //===================//
            //manipulation for ids of contact and user based on their skill search and def
            $params = [];
            $params ['userCanMerge'] = 0;
            $ids = $this->getIntersectIds($userCustomAnd, $userAnd, $ids, $intersectElseU, $intersectDefU, $params);

            $ids = array_merge($ids, $userOr);
            $ids = array_merge($ids, $userCustomOr);
            //$ids = $this->getIntersectIds($userCustomOr, $userOr, $ids, (isset($unionElseU) ? 1 : 0));

            $params['contactCanMerge'] = 0;
            if ($intersectElseC == 0 && count($contactCustomAnd) > 0) {
                $contactIds = $this->getIntersectIds($contactCustomAnd, $contactAnd, $contactIds, $intersectElseC, $intersectDefC, $params);
            } elseif ($intersectElseC == 0 && count($contactCustomAnd) == 0 && (count($contactAnd) > 0) &&
                ((count($userCustomAnd) == 0 && $intersectElseU == 0) && (count($userCustomOr) == 0 && $unionElseU == 0))
            ) {
                $contactIds = array_merge($contactIds, $contactAnd);
            }
            //

            if ((count($userCustomAnd) == 0 && $intersectElseU == 0) && count($contactOr) > 0)
                $contactIds = array_merge($contactIds, $contactOr);
            if ((count($userCustomAnd) == 0 && $intersectElseU == 0) && count($contactCustomOr) > 0)
                $contactIds = array_merge($contactIds, $contactCustomOr);
//            $contactIds = $this->getIntersectIds($contactCustomOr, $contactOr, $contactIds, (isset($unionElseC) ? 1 : 0));
            //get userIds Based on their Custom fields
            //we need to add or check here that condition dont have any custom only default
            // @todo  , (isset($intersectElseU) ? 1 : 0) add this
            //$params = [];
            //this is condition when we have entityCustom Fields

            if (count($entityCustomAnd) > 0 && $intersectElseE == 0) {
                $this->adjsutParam($userCustomAnd, $intersectElseU, 'userCust', $params);
                $this->adjsutParam($contactCustomAnd, $intersectElseC, 'contactCust', $params);
                $this->adjsutParam($userAnd, $intersectDefU, 'userDef', $params);
                $this->adjsutParam($contactAnd, $intersectDefC, 'contactDef', $params);
                $this->adjsutParam($entityCustomAnd, $intersectElseE, 'entityCust', $params);
            } elseif (count($entityCustomAnd) == 0 && $intersectElseE == 1) {
                $params['blankIds'] = 1;
                $this->adjsutParam($entityCustomAnd, $intersectElseE, 'entityCust', $params);
                // count($entityCustomOr) > 0
            }

            $this->getPersonIdsOnEntity($entityCustomAnd, $entityCustomOr, $entityIds, $ids, $contactIds, $params);

            if (count($select) != count($contactSelect)) {
                $min = min(count($select), count($contactSelect));
                if (count($select) == $min) {
                    for ($i = 0; $i < (count($contactSelect) - count($select)); $i++) {
                        $select[] = DB::raw("Null as col" . $i);
                    }
                } elseif (count($contactSelect) == $min) {
                    for ($i = 0; $i < (count($select) - count($contactSelect)); $i++) {
                        $contactSelect[] = DB::raw("Null as col" . $i);
                    }
                }
            }

            $userSkillData = User::whereIn('id', $ids)->select($select)->where(function ($a) {
                $a->where('sub_role', '!=', 'C1');
                $a->orWhereNull('sub_role');
            })->with('entity');

            $contactSkillData = Contact::whereIn('id', $contactIds)/*->with('entity')*/ ->select($contactSelect);
            $total_rowsC = $contactSkillData->count();
            //user count
            $total_rowsU = $userSkillData->count();
            if (isset($this->request->pageno) && $this->request->pageno != 0) {
                $pageno = $this->request->pageno;
            } else {
                $pageno = 1;
            }
            $no_of_records_per_page = (isset($this->request->perPage) ? ($this->request->perPage) : 10);
            $offset = ($pageno - 1) * $no_of_records_per_page;

            //pagination
            if (isset($this->request->search_filter_type) && $this->request->search_filter_type == 'user') {
                $data = array_unique(array_merge($userSkillData->offset($offset)
                    ->limit($no_of_records_per_page)->get()->toArray(), $data), SORT_REGULAR);
                $userData = $data;
                $totPage = ceil($total_rowsU / $no_of_records_per_page);
                $totRecords = ($total_rowsU);
            } elseif (isset($this->request->search_filter_type) && $this->request->search_filter_type == 'contact') {
                $dataC = array_unique(array_merge($contactSkillData->offset($offset)
                    ->limit($no_of_records_per_page)->get()->toArray(), $dataC), SORT_REGULAR);
                $userData = $dataC;
                $totPage = ceil($total_rowsC / $no_of_records_per_page);
                $totRecords = ($total_rowsC);
            } else {

                $getQ[0] = $userSkillData->toSql();
                $getQ[1] = $contactSkillData->toSql();
                $getB[0] = $userSkillData->getBindings();
                $getB[1] = $contactSkillData->getBindings();
                $userData = $this->getUnionQueryData($getQ, $getB, $offset, $no_of_records_per_page);
                $totPage = ceil(($total_rowsU + $total_rowsC) / $no_of_records_per_page);
                $totRecords = ($total_rowsU + $total_rowsC);
            }

            $fieldsIds = array_column($this->selectedFilterConditions, 'field_id');

            if ((isset($this->selectedFilterFields['custom']) && !empty($this->selectedFilterFields['custom'])) || count($fieldsIds) > 0) {
                $skillIds = (empty($this->selectedFilterFields['custom'])) ? $fieldsIds : $this->selectedFilterFields['custom'];
                $userIds = array_column($data, 'user_id');
                $skills = Skill::whereHas('skillTab', function ($q) {
                    $q->whereIn('tab_type', [0, 1]);
                })->whereIn('id', $skillIds)->with(['skillFormat', 'allUserSkills' => function ($query) use ($userIds, $dataC) {
                    $query->whereIn('user_id', $userIds)->orWhereIn('field_id', array_column($dataC, 'user_id'))->orderBy('id', 'desc');
                }])->get()->toArray();

                foreach ($skills as $skill) {
                    $fieldName = $skill['short_name'];
                    $field = ($skill['skill_format']['short_name'] == "radio_input") ? 'select_input' : $skill['skill_format']['short_name'];
                    foreach ($data as $user) {
                        $fieldValue = NULL;
                        $userId = $user['user_id'];
                        if (!empty($skill['all_user_skills'])) {
                            $key = array_search($userId, array_column($skill['all_user_skills'], 'user_id'));
                            if (is_numeric($key)) {
                                $fieldValue = $skill['all_user_skills'][$key][$field];
                                $user[$fieldName] = $fieldValue;
                            }
                        }
                        $key = array_search($userId, array_column($data, 'user_id'));
                        $userData[$key][$fieldName] = $fieldValue;
                    }
                    foreach ($dataC as $contact) {
                        $fieldValue = NULL;
                        $userId = $contact['user_id'];
                        if (!empty($skill['all_user_skills'])) {
                            $key = array_search($userId, array_column($skill['all_user_skills'], 'field_id'));
                            if (is_numeric($key)) {

                                $fieldValue = $skill['all_user_skills'][$key][$field];
                                $contact[$fieldName] = $fieldValue;
                            }
                        }

                        $key = array_search($userId, array_column($dataC, 'user_id'));
                        $dataC[$key][$fieldName] = $fieldValue;
                    }
                }
            }

            return [$userData, isset($getQuery) ? $getQuery : '', 'total_page' => $totPage, 'current_page' => $pageno, 'no_of_records_per_page' => ($no_of_records_per_page), 'total_records' => $totRecords];
            return [$userData, isset($getQuery) ? $getQuery : '', 'total_page' => ((isset($_no_of_records_per_page) ? floor(($total_pagesU + $total_pagesC) / 2) : ($total_pagesU + $total_pagesC))), 'current_page' => $pageno, 'no_of_records_per_page' => (isset($_no_of_records_per_page) ? ($no_of_records_per_page * 2) : $no_of_records_per_page), 'total_records' => ($total_rowsU + $total_rowsC)];
            return $userData;
        }

        protected function getFilterDataByEntity()
        {
            $skills = [];
            $data = [];
            $select = [];
            $entityAnd = [];
            $entityOr = [];
            $entity = new Entity();
            $is_default = FALSE;
            $filterType = $this->selectedFilterType;
            $component = json_decode($filterType['component']);
            $entityInCondition = collect($this->selectedFilterConditions)->whereIn('component', ['company', 'union', 'press', 'instance'])->where('is_default', TRUE)->count();
            $contactInCondition = collect($this->selectedFilterConditions)->where('component', 'contact')->where('is_default', FALSE)->count();
            $userInCondition = collect($this->selectedFilterConditions)->where('component', 'user')->where('is_default', FALSE)->count();
            $personInCondition = collect($this->selectedFilterConditions)->where('component', 'persons')->where('is_default', TRUE)->count();

            $userDefCondition = collect($this->selectedFilterConditions)->where('component', 'user')->where('is_default', TRUE)->count();

            $type = 2;

            if (property_exists($component, 'instance')) {
                $type = 1;
            } else if (property_exists($component, 'union')) {
                $type = 3;
            } else if (property_exists($component, 'press')) {
                $type = 4;
            }


            //        dd($entity->toSql());

            $and = FALSE;
            //this loop is for iterating conditions
            foreach ($this->selectedFilterConditions as $condition) {
//checking here that condition is for default field
                if ($condition['condition'] == 'all' && isset($this->filter->is_default)) {

                    $is_default = TRUE;
                    $getQuery['entity']['unionDef'][] = $entity->select('id')->toSql();
                    $getQuery['entity']['unionBindDef'][] = $entity->getBindings();
                    break;
                }
                $entity = new Entity();
                if ($condition['is_default']) {

                    $is_default = TRUE;
                    $operator = $this->getConditionOperator($condition['condition']);
                    $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                    if (empty($condition['value']))
                        $condition['value'] = NULL;

                    $condition['operator'] = $operator->operator;
                    $condition['operator_name'] = $operator->short_name;
                    if ($condition['component'] == 'company' || $condition['component'] == 'union' || $condition['component'] == 'instance' || $condition['component'] == 'press') {
                        if ($condition['field_name'] == 'phone') {

                            if (strpos($condition['value'], '%') == FALSE) {
                                $condition['value'] = floatval($condition['value']);
                            }
                        }
                        if ($condition['field_name'] == 'entity_sub_type') {
                            $condition['value'] = ($condition['value'] == 'internal') ? 1 : 2;
                        }

                        //                    $type = 2;
                        if ($condition['condition_type'] == 'and') {
                            if ($condition['field_name'] == 'entity_website' && ($condition['operator_name'] == 'is')) {
                                $entity = $entity->where(function ($a) use ($condition) {
                                    $a->orWhere($condition['field_name'], $condition['operator'], !empty($condition['value']) ? ('https://' . str_replace('https://', '', $condition['value'])) : "");
                                    $a->orWhere($condition['field_name'], $condition['operator'], !empty($condition['value']) ? ('http://' . str_replace('http://', '', $condition['value'])) : "");
                                });
                            } else {
                                $entity = $entity->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                            }

                        } else {
                            //need to check with himanshu
//                        if (!$and) {
//                            $and = true;
//                            $entity = $entity->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
//                        } else {
                            if ($condition['field_name'] == 'entity_website' && ($condition['operator_name'] == 'is')) {
                                $entity = $entity->where(function ($a) use ($condition) {
                                    $a->orWhere($condition['field_name'], $condition['operator'], !empty($condition['value']) ? ('https://' . str_replace('https://', '', $condition['value'])) : "");
                                    $a->orWhere($condition['field_name'], $condition['operator'], !empty($condition['value']) ? ('http://' . str_replace('http://', '', $condition['value'])) : "");
                                });

                            } else {
                                $entity = $entity->orWhere($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                            }
                        }

                        if ($operator->short_name == "is_empty") {
                            $entity = $entity->orWhereNull($condition['field_name']);
                        } else if ($operator->short_name == "is_not_empty") {
                            $entity = $entity->whereNotNull($condition['field_name']);
                        }
                    } else if ($condition['component'] == 'persons') {

                        if ($condition['condition_type'] == 'and') {
                            if ($condition['field_name'] == 'phone') {
                                if (strpos($condition['value'], '%') == FALSE) {
                                    $condition['value'] = floatval($condition['value']);
                                }
                            }
                            // $skill['value'] = floatval($skill['value']);
                            if (($userInCondition > 0 && $contactInCondition == 0) || ($personInCondition > 0 && $contactInCondition == 0)) {

                                $entity = $entity->whereHas('user', function ($query) use ($condition) {
                                    if ($condition['operator_name'] == "is_empty") {
                                        $query/*->orWhereNull($condition['field_name'])*/
                                        ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else if ($condition['operator_name'] == "is_not_empty") {
                                        $query/*->whereNotNull($condition['field_name'])*/
                                        ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else {
                                        $query->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                                    }
                                });
                            }
                            //contact
                            if (($userInCondition == 0 && $contactInCondition > 0) || ($personInCondition > 0 && $userInCondition == 0)) {
                                $entity = $entity->orWhereHas('contact', function ($query) use ($condition) {
                                    if ($condition['operator_name'] == "is_empty") {
                                        $query/*->orWhereNull($condition['field_name'])*/
                                        ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else if ($condition['operator_name'] == "is_not_empty") {
                                        $query/*->whereNotNull($condition['field_name'])*/
                                        ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else {
                                        $query->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                                    }
                                });
                            }
                        } else {
                            if (($userInCondition > 0 && $contactInCondition == 0) || ($personInCondition > 0 && $contactInCondition == 0)) {
                                $entity = $entity->orWhereHas('user', function ($query) use ($condition) {
                                    if ($condition['field_name'] == 'phone') {
                                        if (strpos($condition['value'], '%') == FALSE) {
                                            $condition['value'] = floatval($condition['value']);
                                        }
                                    }
                                    if ($condition['operator_name'] == "is_empty") {
                                        $query->orWhereNull($condition['field_name'])
                                            ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else if ($condition['operator_name'] == "is_not_empty") {
                                        $query->whereNotNull($condition['field_name'])
                                            ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else {
                                        $query->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                                    }
                                });
                            }
                            //contact
                            if (($userInCondition == 0 && $contactInCondition > 0) || ($personInCondition > 0 && $userInCondition == 0)) {
                                $entity = $entity->orWhereHas('contact', function ($query) use ($condition) {
                                    if ($condition['field_name'] == 'phone') {
                                        if (strpos($condition['value'], '%') == FALSE) {
                                            $condition['value'] = floatval($condition['value']);
                                        }
                                    }
                                    if ($condition['operator_name'] == "is_empty") {
                                        $query->orWhereNull($condition['field_name'])
                                            ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else if ($condition['operator_name'] == "is_not_empty") {
                                        $query->whereNotNull($condition['field_name'])
                                            ->where($condition['field_name'], $condition['operator'], $condition['value']);
                                    } else {
                                        $query->where($condition['field_name'], $condition['operator'], !empty($condition['value']) ? $condition['value'] : "");
                                    }
                                });
                            }
                        }
                    }
                    else if ($condition['component'] == 'user' && $userDefCondition > 0) {
                        $operator = $this->getConditionOperator($condition['condition']);
                        $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                        $condition['operator'] = $operator->operator;

                        if ($condition['condition_type'] == 'and') {
                            $entity = $entity->whereHas('user', function ($query) use ($condition) {
                                if ($condition['value'] == 'crmAdmin' || $condition['value'] == 'crmEditor' || $condition['value'] == 'crmAssistance' || $condition['value'] == 'crmRecruitment' && $condition['condition_type'] == 'and') {
                                    $val = ($condition['operator'] == '=') ? 1 : 0;
                                    $query = $query->whereRaw('JSON_CONTAINS(permissions, \'{"'.$condition['value'].'": ' . $val . '}\')');
                                } elseif ($condition['value'] == 'W0' || $condition['value'] == 'W1' && $condition['condition_type'] == 'and') {
                                    $_role = ($condition['field_name'] == 'W0') ? 1 : 2;
                                    $meta = WorkshopMeta::where('role', $condition['operator'], $_role)->groupBy('user_id')->pluck('user_id')->toArray();
                                    $query = $query->whereIn('id', $meta);
                                } else {
                                    $query = $query->where('role', $condition['operator'], $condition['value']);
                                }
                            });
                        } else {
                            $entity = $entity->orWhereHas('user', function ($query) use ($condition) {
                                if ($condition['value'] == 'crmAdmin' || $condition['value'] == 'crmEditor' || $condition['value'] == 'crmAssistance' || $condition['value'] == 'crmRecruitment' && $condition['condition_type'] == 'or') {
                                    $val = ($condition['operator'] == '=') ? 1 : 0;
                                    $query = $query->whereRaw('JSON_CONTAINS(permissions, \'{"'.$condition['value'].'": ' . $val . '}\')');
                                } elseif
                                ($condition['value'] == 'W0' || $condition['value'] == 'W1' && $condition['condition_type'] == 'or') {
                                    $_role = ($condition['field_name'] == 'W0') ? 1 : 2;
                                    $meta = WorkshopMeta::where('role', $condition['operator'], $_role)->groupBy('user_id')->pluck('user_id')->toArray();
                                    $query = $query->whereIn('id', $meta);
                                } else {
                                    $query = $query->where('role', $condition['operator'], $condition['value']);
                                }
                            });
                    }
                    }
                    if ($this->save_selected_fields) {
                        $field = $condition['field_name'];
                        $select[] = DB::raw("entities.$field as $field");
                    }
                    //code for defQuery
                    if ($condition['condition_type'] == 'and') {
                        $getQuery['entity']['intersectDef'][] = $entity->select('id')->toSql();
                        $getQuery['entity']['intersectBindDef'][] = $entity->getBindings();

                    } else {
                        $getQuery['entity']['unionDef'][] = $entity->select('id')->toSql();
                        $getQuery['entity']['unionBindDef'][] = $entity->getBindings();
                    }

                } else {
                    if ($this->save_selected_fields) {
                        $field = $condition['field_name'];
                        $select[] = DB::raw("null as $field");
                    }
                    $skills[] = $condition;
                }
            }
            //this loop is for adding select fields
            foreach ($this->selectedFilterFields['entity'] as $key => $field) {
                $shortName = $field['short_name'];
                $name = $field['name'];
                $select[] = DB::raw("entities.$shortName as $shortName");
            }
            $select[] = DB::raw("entities.id as company_id,entities.entity_type_id as entity_type_id");

            $entity = $entity->where('entity_type_id', $type);

            if ($is_default) {
                if (isset($getQuery['entity']['intersectDef'])) {
                    $entityAnd = $this->getIntersectQueryData($getQuery['entity']['intersectDef'], $getQuery['entity']['intersectBindDef']);
                    if (count($entityAnd) <= 0) {
                        $intersectDef = TRUE;
                    }
                }
                if (isset($getQuery['entity']['unionBindDef'])) {
                    $entityOr = $this->getUnionQueryData($getQuery['entity']['unionDef'], $getQuery['entity']['unionBindDef']);
                    if (count($entityOr) <= 0) {
                        $unionDef = TRUE;
                    }
                }
                //dd($select);
                //                        dd($entity->select($select)->toSql());
//                $getQuery[] = $entity->select('*')->toSql();
//                $getQuery[]['bind'] = $entity->getBindings();
//                $data = $entity->select($select)->with(['industry' => function ($q) {
//                    $q->with('parent');
//                }])->get()->filter(function ($value, $key) use ($type) {
//                    return $value->entity_type_id == $type;
//                })->toArray();
            }

            $ids = [];
            $contains = [];
            $entityCustomAnd = [];
            $entityCustomOr = [];
            $skillIds = [];
            $selected = [];
            $entityUser = [];

            if (!empty($skills)) {
                foreach ($skills as $skill) {
                    if ($skill['component'] == 'user' || $skill['component'] == 'contact') {
                        $entityUser[] = $skill;
                        continue;
                    }
                    $skillIds[] = $skill['field_id'];
                    $skillObject = Skill::find($skill['field_id']);

                    if ($skillObject) {
                        if ($skill['component'] == 'company' /*&& !isset($contains['skill_company_view'])*/) {
                            $userSkill = new ViewSkillCompany();
                            $contains[$userSkill->getTable()] = 0;
                        } else if ($skill['component'] == 'union' /*&& !isset($contains['skill_union_view'])*/) {
                            $userSkill = new ViewSkillUnion();
                            $contains[$userSkill->getTable()] = 0;
//                        $userSkill = $userSkill->where('type', 'union');
                        } else if ($skill['component'] == 'instance' /*&& !isset($contains['skill_instance_view'])*/) {
                            $userSkill = new ViewSkillInstance();
                            $contains[$userSkill->getTable()] = 0;
//                        $userSkill = $userSkill->where('type', 'instance');
                        } else if ($skill['component'] == 'press' /*&& !isset($contains['skill_press_view'])*/) {
//                            if (!isset($contains['skill_press_view'])) {
                            $userSkill = new ViewSkillPress();
                            $contains[$userSkill->getTable()] = 0;
//                            }

//                        $userSkill = $userSkill->where('type', 'instance');
                        }

                        $skillObject->skillFormat;
                        $operator = $this->getConditionOperator($skill['condition']);
                        $skill['value'] = str_replace(':value', $skill['value'], $operator->value);
                        if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                            if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } elseif ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('original_value');
                                $skill['value'] = '0';
//                                $skill['value'] = 'is not null';
                            } elseif ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } else {
                                $skill['value'] = '0';
                            }
                        }
                        $skill['operator'] = $operator->operator;
                        if ($skillObject->skillFormat->short_name == 'date_input') {
                            $sField = 'whereDate';
                            /* if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                 $userSkill = $userSkill->whereNotNull('original_value');
                             }*/
                        } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                            $skill['value'] = floatval($skill['value']);
                            $sField = 'where';
                        } else {
                            $sField = 'where';
                        }
                        if ($skill['condition_type'] == 'and') {
                            $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], $skill['component']);
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);
                            $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*->where('short_name',$skillObject->skillFormat->short_name)*/
                            ;
                        } else {
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);
                            $userSkill = $userSkill->where(function ($a) use ($skill, $skillObject, $sField) {
                                $a->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']);
                            });

                        }

                        $field = $skillObject->skillFormat->short_name;
                        $selected[] = DB::raw("user_skills.$field as $field");
                        if ($skill['condition_type'] == 'and') {
                            $getQuery['entCus']['intersect'][] = $userSkill->select('entity_id')->toSql();
                            $getQuery['entCus']['intersectBind'][] = $userSkill->getBindings();
                        } else {
                            $getQuery['entCus']['union'][] = $userSkill->select('entity_id')->toSql();
                            $getQuery['entCus']['unionBind'][] = $userSkill->getBindings();
                        }

                    }
                }

                //=====================testing code==================//
                if (isset($getQuery['entCus']['intersect'])) {
                    $entityCustomAnd = $this->getIntersectQueryData($getQuery['entCus']['intersect'], $getQuery['entCus']['intersectBind']);
                    if (count($entityCustomAnd) <= 0) {
                        $intersectElseE = TRUE;
                    }
                }
                if (isset($getQuery['entCus']['union'])) {
                    $entityCustomOr = $this->getUnionQueryData($getQuery['entCus']['union'], $getQuery['entCus']['unionBind']);
                    if (count($entityCustomOr) <= 0) {
                        $unionElseE = TRUE;
                    }
                }

            }

            $userIds = [];
            $entityContact = [];

            if (!empty($entityUser)) {

                foreach ($entityUser as $skill) {
                    $userSkill = new ViewSkillUser();
                    if ($skill['component'] == 'contact') {
                        $entityContact[] = $skill;
                        continue;
                    }
                    $skillObject = Skill::find($skill['field_id']);

                    if ($skillObject && (!in_array($skillObject->skillFormat->short_name, $this->skipCustomFields))) {

                        $userSkill = $userSkill->where('skill_id', $skillObject->id);
                        $skillObject->skillFormat;
                        $operator = $this->getConditionOperator($skill['condition']);
                        $skill['value'] = str_replace(':value', $skill['value'], $operator->value);
                        if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                            if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } elseif ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('original_value');
//                                $skill['value'] = 'is not null';
                                $skill['value'] = '0';
                            } elseif ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('original_value');
                                $skill['value'] = '0';
                            } else {
                                $skill['value'] = '0';
                            }
                        }

                        $skill['operator'] = $operator->operator;
                        if ($skillObject->skillFormat->short_name == 'date_input') {
                            $sField = 'whereDate';
                            /* if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                 $userSkill = $userSkill->whereNotNull('original_value');
                             }*/

                        } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                            $skill['value'] = floatval($skill['value']);
                            $sField = 'where';
                        } else {
                            $sField = 'where';
                        }
                        if ($skill['condition_type'] == 'and') {
                            $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], 'persons');
                            $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']) /*->where('short_name',$skillObject->skillFormat->short_name)*/
                            ;
                        } else {
                            $userSkill = $userSkill->orWhere('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']) /*orWhere($skillObject->skillFormat->short_name, $skill['operator'], $skill['value']  )*/
                            ;
                        }
                        $field = $skillObject->skillFormat->short_name;
                        $selected[] = DB::raw("user_skills.$field as $field");
//                        $getQuery[] = $userSkill->toSql();
//                        $getQuery[]['bind'] = $userSkill->getBindings();
//                        $userIds[] = $userSkill->pluck('user_id')->toArray();
                        if ($skill['condition_type'] == 'and') {
                            $getQuery['userIntersect'][] = $userSkill->select('user_id')->toSql();
                            $getQuery['userIntersectBind'][] = $userSkill->getBindings();
                        } else {
                            $getQuery['userUnion'][] = $userSkill->select('user_id')->toSql();
                            $getQuery['userUnionBind'][] = $userSkill->getBindings();
                        }

                    }
                }
                //=====================testing code==================//
                if (isset($getQuery['userIntersect'])) {
                    $userCustomAnd = $this->getIntersectQueryData($getQuery['userIntersect'], $getQuery['userIntersectBind']);

                    if (count($userCustomAnd) <= 0) {
                        $intersectElseU = TRUE;
                    }
                }
                if (isset($getQuery['userUnion'])) {
                    $userCustomOr = $this->getUnionQueryData($getQuery['userUnion'], $getQuery['userUnionBind']);
                    if (count($userCustomOr) <= 0) {
                        $unionElseU = TRUE;
                    }
                }
//                $this->getIdsOfCustom($userCustomAnd, (isset($intersectElseU) ? 1 : 0), $userCustomOr, (isset($unionElseU) ? 1 : 0));
            }

            if (!empty($entityContact)) {
                foreach ($entityContact as $skill) {
                    if ($skill['component'] != 'contact') {
                        $entityUserSkills[] = $skill;
                        continue;
                    }
                    $userSkill = new ViewSkillContact();
                    $skillIds[] = $skill['field_id'];
                    $skillObject = Skill::find($skill['field_id']);
                    if (isset($skillObject->skillFormat)) {
                        if ($skillObject && (!in_array($skillObject->skillFormat->short_name, $this->skipCustomFields))) {
                            //$userSkill = $this->addDefaultCondition($userSkill);
                            $userSkill = $userSkill->where("skill_id", $skillObject->id);

                            $operator = $this->getConditionOperator($skill['condition']);
                            $skill['value'] = str_replace(':value', $skill['value'], $operator->value);
                            if (empty($skill['value']) || $skill['condition'] == 'is_empty' || $skill['condition'] == 'is_not_empty') {
                                if (in_array($skillObject->skillFormat->short_name, ['text_input', 'long_text_input', 'address_text_input']) && $operator->short_name == "is_empty") {
                                    $userSkill = $userSkill->whereNull('original_value');
                                    $skill['value'] = '0';
                                } elseif ($operator->short_name == "is_not_empty") {
                                    $userSkill = $userSkill->whereNotNull('original_value');
                                    $skill['value'] = '0';
//                                    $skill['value'] = 'is not null';
                                } elseif ($operator->short_name == "is_empty") {
                                    $userSkill = $userSkill->whereNull('original_value');
                                    $skill['value'] = '0';
                                } else {
                                    $skill['value'] = '0';
                                }
                            }

                            $skill['operator'] = $operator->operator;
                            if ($skillObject->skillFormat->short_name == 'date_input') {
                                $sField = 'whereDate';
                                /* if (!in_array($skillObject->skillFormat->short_name, ["is_empty", "is_not_empty"])) {
                                     $userSkill = $userSkill->whereNotNull('original_value');
                                 }*/

                            } elseif (in_array($skillObject->skillFormat->short_name, ["numerical_input", "percentage_input", 'scale_10_input', 'scale_5_input'])) {
                                $skill['value'] = floatval($skill['value']);
                                $sField = 'where';
                            } else {
                                $sField = 'where';
                            }
                            if ($skill['condition_type'] == 'and') {
                                $userSkill = $this->addDefaultCondition($userSkill, $skill['condition_type'], 'persons');
                                $userSkill = $userSkill->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value'])/*->where('short_name',$skillObject->skillFormat->short_name)*/
                                ;
                            } else {
                                $userSkill = $userSkill->where(function ($a) use ($skill, $skillObject, $sField) {
                                    $a->where('short_name', $skillObject->skillFormat->short_name)->$sField('value', $skill['operator'], $skill['value']);
                                });/*orWhere($skillObject->skillFormat->short_name, $skill['operator'], $skill['value'])*/;
                            }
                            /*if ($operator->short_name == "is_empty") {
                                $userSkill = $userSkill->whereNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                            } else if ($operator->short_name == "is_not_empty") {
                                $userSkill = $userSkill->whereNotNull('short_name')->where('short_name',$skillObject->skillFormat->short_name);
                            }*/
                            $field = $skillObject->skillFormat->short_name;
                            $selected[] = DB::raw("user_skills.$field as $field");
                            if ($skill['condition_type'] == 'and') {
                                $getQuery['conCus']['intersect'][] = $userSkill->select('user_id')->toSql();
                                $getQuery['conCus']['intersectBind'][] = $userSkill->getBindings();
                            } else {
                                $getQuery['conCus']['union'][] = $userSkill->select('user_id')->toSql();
                                $getQuery['conCus']['unionBind'][] = $userSkill->getBindings();
                            }
//                            $getQuery[] = $userSkill->toSql();
//                            $getQuery[]['bind'] = $userSkill->getBindings();
//                            $contactIds = array_merge($contactIds, array_values(array_filter($userSkill->pluck('user_id')->toArray())));
                        }
                    }
                }
                //=====================testing code==================//
                if (isset($getQuery['conCus']['intersect'])) {
                    $contactCustomAnd = $this->getIntersectQueryData($getQuery['conCus']['intersect'], $getQuery['conCus']['intersectBind']);
                    if (count($contactCustomAnd) <= 0) {
                        $intersectElseC = TRUE;
                    }
                }
                if (isset($getQuery['conCus']['union'])) {
                    $contactCustomOr = $this->getUnionQueryData($getQuery['conCus']['union'], $getQuery['conCus']['unionBind']);
                    if (count($contactCustomOr) <= 0) {
                        $unionElseC = TRUE;
                    }
                }
            }

            $entityAnd = (!empty($entityAnd)) ? array_column($entityAnd, 'id') : [];
            $entityOr = (!empty($entityOr)) ? array_column($entityOr, 'id') : [];
            $entityCustomAnd = (!empty($entityCustomAnd)) ? array_column($entityCustomAnd, 'entity_id') : [];
            $entityCustomOr = (!empty($entityCustomOr)) ? array_column($entityCustomOr, 'entity_id') : [];

            $userCustomAnd = (!empty($userCustomAnd)) ? array_column($userCustomAnd, 'user_id') : [];
            $userCustomOr = (!empty($userCustomOr)) ? array_column($userCustomOr, 'user_id') : [];
            $contactCustomAnd = (!empty($contactCustomAnd)) ? array_column($contactCustomAnd, 'user_id') : [];
            $contactCustomOr = (!empty($contactCustomOr)) ? array_column($contactCustomOr, 'user_id') : [];

            //here we have data for result of querys data is null or not
            $intersectElseU = (isset($intersectElseU) ? 1 : 0);
            $intersectElseC = (isset($intersectElseC) ? 1 : 0);
            $intersectElseE = (isset($intersectElseE) ? 1 : 0);
            $unionElseE = (isset($unionElseE) ? 1 : 0);
            $unionElseC = (isset($unionElseC) ? 1 : 0);
            $unionElseU = (isset($unionElseU) ? 1 : 0);
            $intersectDef = (isset($intersectDef) ? 1 : 0);
            $unionDef = (isset($unionDef) ? 1 : 0);

            //===================//

            $params = [];
            $params['entityCanMerge'] = 0;
            //manipulation for ids of contact and user based on their skill search and def
            $ids = $this->getIntersectIds($entityCustomAnd, $entityAnd, $ids, $intersectElseE, $intersectDef, $params);
            $ids = array_merge($ids, $entityOr);
            $ids = array_merge($ids, $entityCustomOr);


            //this is condition when we have entityCustom Fields
            if (count($userCustomAnd) > 0 && $intersectElseU == 0) {
                $this->adjsutParam($userCustomAnd, $intersectElseU, 'userCust', $params);
            } else if (count($contactCustomAnd) > 0 && $intersectElseC == 0) {
                $this->adjsutParam($contactCustomAnd, $intersectElseC, 'contactCust', $params);
            } elseif (count($userCustomAnd) == 0 && $intersectElseU == 1) {
                $params['blankIds'] = 1;
                $params['userCust'] = $intersectElseU;
                $this->adjsutParam($entityCustomAnd, $intersectElseE, 'entityCust', $params);
                // count($entityCustomOr) > 0
            } elseif (count($contactCustomAnd) == 0 && $intersectElseC == 1) {
                $params['blankIds'] = 1;
                $params['contactCust'] = $intersectElseC;
                $this->adjsutParam($entityCustomAnd, $intersectElseE, 'entityCust', $params);
                // count($entityCustomOr) > 0
            }

            $this->getEntityIdsOnPerson($entityCustomAnd, $entityCustomOr, $ids, $params);


            $select[] = DB::raw("entities.id as entity_id");
            $entitySkillData = Entity::whereIn('id', $ids)->where('entity_type_id', $type)->select($select)->with(['industry' => function ($q) {
                $q->with('parent');
            }]);

            $pageno = (isset($this->request->pageno) ? $this->request->pageno : 1);
            $no_of_records_per_page = (isset($this->request->perPage) ? $this->request->perPage : 10);
            $offset = ($pageno - 1) * $no_of_records_per_page;
            $total_rowsE = $entitySkillData->count();
            $total_pagesE = ceil($total_rowsE / $no_of_records_per_page);
            //dd($offset,$no_of_records_per_page,$total_rowsE,$total_pagesE);
            $data = array_unique(array_merge($entitySkillData->offset($offset)
                ->limit($no_of_records_per_page)->get()->toArray(), $data), SORT_REGULAR);
            $fieldsIds = array_column($this->selectedFilterConditions, 'field_id');
            $entityData = $data;
            if ((isset($this->selectedFilterFields['custom']) && !empty($this->selectedFilterFields['custom'])) || count($fieldsIds) > 0) {

                $skillIds = (empty($this->selectedFilterFields['custom'])) ? $fieldsIds : $this->selectedFilterFields['custom'];
                //$skillIds = $this->selectedFilterFields['custom'];
                $entityIds = array_column($data, 'company_id');

                $skills = Skill::whereIn('id', $skillIds)->with(['skillFormat', 'allUserSkills' => function ($query) use ($entityIds) {
                    $query->whereIn('field_id', $entityIds)->orderBy('id', 'desc');
                }])->get()->toArray();

                foreach ($skills as $skill) {
                    $fieldName = $skill['short_name'];
                    $field = ($skill['skill_format']['short_name'] == "radio_input") ? 'select_input' : $skill['skill_format']['short_name'];
                    foreach ($data as $entity) {
                        $fieldValue = NULL;
                        $entityId = $entity['company_id'];
                        if (!empty($skill['all_user_skills'])) {

                            $key = array_search($entityId, array_column($skill['all_user_skills'], 'field_id'));

                            if (is_numeric($key)) {
                                $fieldValue = $skill['all_user_skills'][$key][$field];
                                $entity[$fieldName] = $fieldValue;
                            }
                        }
                        $key = array_search($entityId, array_column($data, 'company_id'));
                        $entityData[$key][$fieldName] = $fieldValue;
                    }
                }
            }

            return [$entityData, isset($getQuery) ? $getQuery : '', 'total_page' => ($total_pagesE), 'current_page' => $pageno, 'no_of_records_per_page' => $no_of_records_per_page, 'total_records' => $total_rowsE];
        }

        protected
        function userEntityQueryBuilder($object, $data, $type)
        {
            $membershipType = 0;
            if ($type == 3) {
                $membershipType = collect($this->selectedFilterConditions)->where('component', 'union')->where('is_default', TRUE)->where('field_name', 'membership_type')->count();
            }

            $operator = $this->getConditionOperator($data['condition']);
            $data['value'] = str_replace(':value', $data['value'], $operator->value);
            if (empty($data['value']))
                $data['value'] = NULL;
            $data['operator'] = $operator->operator;
            $data['operator_name'] = $operator->short_name;
            if ($data['field_name'] == 'entity_sub_type') {
                $data['value'] = ($data['value'] == 'internal') ? 1 : 2;
            }
            if ($data['field_name'] == 'membership_type') {
                $data['value'] = ($data['value'] == 'is_staff') ? 1 : 0;
            }
            //var_export($data);exit;
            if ($data['condition_type'] == 'and') {
                return $object->whereHas('entity', function ($query) use ($data, $type, $membershipType) {

                    if ($data['operator_name'] == "is_empty") {
                        $query->where('entity_type_id', $type)
                            ->where($data['field_name'], $data['operator'], $data['value']);
//                            ->orWhereNull($data['field_name'])

                    } elseif ($data['operator_name'] == "is_not_empty") {
                        $query->where('entity_type_id', $type)
                            ->where($data['field_name'], $data['operator'], $data['value']);
//                            ->orWhereNotNull($data['field_name'])
                    } else {
                        if ($data['field_name'] == 'entity_website' && ($data['operator_name'] == 'is')) {
                            $query->where(function ($a) use ($data, $type) {
                                $a->orWhere($data['field_name'], $data['operator'], !empty($data['value']) ? ('https://' . str_replace('https://', '', $data['value'])) : "");
                                $a->orWhere($data['field_name'], $data['operator'], !empty($data['value']) ? ('http://' . str_replace('http://', '', $data['value'])) : "");
                            });
                        } else {
                            $query->where('entity_type_id', $type)
                                ->where($data['field_name'], $data['operator'], $data['value']);
                        }
                    }

                    if ($membershipType > 0) {
                        $this->addDefaultUnionCondition($query, $data['condition_type']);
                        //dd('ruk');
                    }
                });
            }


            return $object->orWhereHas('entity', function ($query) use ($data, $type, $membershipType) {

                if ($data['field_name'] == 'entity_website' && ($data['operator_name'] == 'is')) {
                    $query->where(function ($a) use ($data, $type) {
                        $a->orWhere($data['field_name'], $data['operator'], !empty($data['value']) ? ('https://' . str_replace('https://', '', $data['value'])) : "");
                        $a->orWhere($data['field_name'], $data['operator'], !empty($data['value']) ? ('http://' . str_replace('http://', '', $data['value'])) : "");
                    });
                } else {
                    $query->where('entity_type_id', $type)
                        ->where($data['field_name'], $data['operator'], $data['value']);
                }
                if ($membershipType > 0) {
                    $this->addDefaultUnionCondition($query, $data['condition_type']);
                    //dd('ruk');
                }

            });
            //        dd($object->toSql());
        }


        protected
        function entityUserQueryBuilder($object, $data, $type)
        {
            $operator = $this->getConditionOperator($data['condition']);
            $data['value'] = str_replace(':value', $data['value'], $operator->value);
            if (empty($data['value']))
                $data['value'] = NULL;
            $data['operator'] = $operator->operator;

            if ($data['condition_type'] == 'and') {
                return $object->whereHas('user', function ($query) use ($data, $type) {
                    //                $query->where('entity_type_id', $type);
                    $query->where($data['field_name'], $data['operator'], $data['value']);
                });
            }

            return $object->orWhereHas('user', function ($query) use ($data, $type) {
                //            $query->where('entity_type_id', $type);
                $query->where($data['field_name'], $data['operator'], $data['value']);
            });
            //        dd($object->toSql());
        }


        protected
        function getFieldNameByFormatId($formatId)
        {
            switch ($formatId) {
                case 7:
                    return 'file_input';
                    break;
                case 8:
                    return 'select_input';
                    break;
                case 12:
                    return 'mandatory_checkbox_input';
                    break;
                case 13:
                    return 'mandatory_file_input';
                    break;
                case 14:
                    return 'comment_text_input';
                    break;
                case 15:
                    return 'address_text_input';
                    break;
                case 11:
                    return 'date_input';
                    break;
                case 10:
                    return 'long_text_input';
                    break;
                case 9:
                    return 'numerical_input';
                    break;
                case 6:
                    return 'text_input';
                    break;
                case 5:
                    return 'scale_5_input';
                    break;
                case 4:
                    return 'scale_10_input';
                    break;
                case 3:
                    return 'percentage_input';
                    break;
                case 2:
                    return 'yes_no_input';
                    break;
                case 17:
                    return 'mandatory_acceptance_input';
                    break;
                default:
                    return 'checkbox_input';
            }
        }

        public
        function getFilerData()
        {
            if ($this->checkFilterData()) {
                $request = $request = new \Modules\Crm\Http\Requests\UserOpenFilterRequest;
                $request->merge(['filter_type_id' => $this->filter->filter_type_id, 'filter_id' => $this->filter->id]);
                $userOpenFilter = app(\Modules\Crm\Http\Controllers\UserOpenFilterController::class);
                $userOpenFilter->store($request);
                $data = [
                    'name'                 => $this->filter->name,
                    'save_selected_fields' => boolval($this->filter->save_selected_fields),
                    'filter_type_id'       => $this->filter->filter_type_id,
                    'filter_type'          => $this->filter->getFilterType->name,
                    'condition'            => $this->selectedFilterConditions,
                    'fields'               => $this->selectedFilterFields,
                ];
                return $data;
            }
        }

        /**
         * @param $model
         * @param $conditions
         * @param $select
         * @return mixed
         */
        protected
        function getModelCondition($model, $conditions, $select)
        {
            foreach ($conditions as $condition) {
                $operator = $this->getConditionOperator($condition['condition']);
                $value = str_replace(':value', $condition['value'], $operator->value);
                $field = $condition['field_default'];
                if ($condition['condition_type'] == 'and') {
                    $model = $model->where($field, $operator->operator, $value);
                } else {
                    $model = $model->orWhere($field, $operator->operator, $value);
                }
            }
            $model = $model->select($select);
            return $model;
        }

        /**
         * @param $conditions
         * @param $object
         * @param $is_default
         * @return array
         */
        protected
        function getModelSelectedFields($conditions, $object, $is_default = TRUE)
        {
            $select = [];
            $fieldName = [];

            if ($is_default) {
                $tableName = $object->getTableName();
                $fields = $object->getFillables();
            }

            foreach ($this->selectedFilterFields as $table => $conditionsField) {
                foreach ($conditionsField as $key => $field) {
                    if (!in_array($key, $fieldName)) {
                        if (in_array($key, $fields) && $tableName == $table) {
                            $fieldName[] = $field;
                            $select[] = $tableName . '.' . $key . ' as ' . $field;
                        } else {
                            $fieldName[] = $field;
                            $select[] = DB::raw("null as $field");
                        }
                    }
                }
            }

            $select[] = DB::raw("'$tableName' as table_name");
            return $select;
        }

        /**
         * @param $conditions
         * @param $tableName
         * @return $this
         */
        protected
        function mergeSelectedFieldsWithCondition($conditions, $tableName)
        {
            $array = array_column($conditions, 'field_default');
            $array = array_combine($array, $array);
            $this->selectedFilterFields[$tableName] = array_merge($array, $this->selectedFilterFields[$tableName]);
            return $this;
        }

        /**
         * @param $object
         * @param $preTable
         * @param $table
         * @param $data
         * @return mixed
         */
        protected
        function filterQueryBuilder($object, $preTable, $table, $data)
        {
            if ($table == $preTable) {
                if ($data['condition_type'] == 'and') {
                    return $object->where($data['field'], $data['operator'], $data['value']);
                }
                return $object->orWhere($data['field'], $data['operator'], $data['value']);
            }

            if ($data['condition_type'] == 'and') {
                return $object->whereHas($table, function ($query) use ($data) {
                    return $query->where($data['field'], $data['operator'], $data['value']);
                });
            }
            //        dd($table, $data);
            return $object->orWhereHas($table, function ($query) use ($data) {
                return $query->where($data['field'], $data['operator'], $data['value']);
            });
        }

        /**
         * @param $shortName
         * @return mixed
         */
        protected
        function getConditionOperator($shortName)
        {
            $operator = CrmFilterRule::where('short_name', $shortName)->select('operator', 'value', 'short_name')->first();
            return $operator;
        }

        /**
         * @return bool
         */
        protected
        function checkFilterData()
        {


            if ($this->hasFilter()) {

                if (!empty($this->selectedFilterType) && !empty($this->selectedFilterConditions)) {
                    return TRUE;
                }
                if ($this->filter->getFilterType && $this->filter->getFilterConditions) {
                    $this->selectedFilterType = $this->filter->getFilterType->toArray();
                    $this->selectedFilterConditions = $this->filter->getFilterConditions->toArray();
                    $this->selectedFilterFields = json_decode($this->filter->getFilterField->value, TRUE);
                    $this->setTableNames();
                    return TRUE;
                }
                return FALSE;
            }
            return FALSE;
        }

        /**
         *
         */
        protected
        function setTableNames()
        {
            $component = json_decode($this->selectedFilterType['component']);
            foreach ($this->selectedFilterConditions as $condition) {
                $componentName = $condition['component'];
                $defaultModels = $component->$componentName->default;

                foreach ($defaultModels as $defaultModel) {
                    //                    $model = reset($defaultModels);
                    $model = $defaultModel;
                    $object = new $model;
                    $tableName = $object->getTableName();
                    if (!in_array($tableName, $this->tableNames))
                        $this->tableNames[] = $tableName;
                }
            }
        }

        /**
         * get filter data from request and save it in database
         *
         * @return bool
         */
        public
        function saveFiler()
        {

            if ($this->request && !empty($this->filterData)) {
                $crmFilter = CrmFilter::create($this->filterData);
                if ($crmFilter) {
                    $this->setFilterCondition($crmFilter);

                    CrmFilterField::insert([
                        'filter_id' => $crmFilter->id,
                        'value'     => json_encode($this->selectedFilterFields),
                    ]);

                    foreach ($this->selectedFilterConditions as $condition) {
                        CrmFilterCondition::create($condition);
                    }

                    return $crmFilter->id;
                }
                return FALSE;
            }
            return FALSE;
        }

        /**
         * get filter data from request and save it in database
         *
         * @return bool
         */
        public
        function updateFilter()
        {
            if ($this->request && !empty($this->filterData)) {

                unset($this->filterData['filter_type_id']);
                $crmFilter = CrmFilter::where('id', $this->filter->id)->update($this->filterData);
                if ($crmFilter) {
                    $this->setFilterCondition($this->filter);

                    /*$component = json_decode($this->selectedFilterType['component']);
                    foreach ($this->selectedFilterConditions as $condition){
                        $componentName = $condition['component'];
                        $defaultModels = $component->$componentName->default;

                        foreach ($defaultModels as $defaultModel){
                            $model = $defaultModel;
                            $object = new $model;
                            $tableName = $object->getTableName();
                            $this->mergeSelectedFieldsWithCondition($this->selectedFilterConditions, $tableName);
                        }
                    }*/
                    $previousData = CrmFilterField::where('filter_id', $this->filter->id)->first(['value']);
                    if (isset($previousData->value)) {
                        $custom = json_decode($previousData->value);
                        $this->selectedFilterFields['custom'] = $custom->custom;
                        array_unique($this->selectedFilterFields['custom']);
                    }

                    CrmFilterField::where('filter_id', $this->filter->id)->update([
                        'filter_id' => $this->filter->id,
                        'value'     => json_encode($this->selectedFilterFields),
                    ]);

                    CrmFilterCondition::where('filter_id', $this->filter->id)->delete();
                    foreach ($this->selectedFilterConditions as $condition) {
                        CrmFilterCondition::create($condition);
                    }
                    return TRUE;
                }
                return FALSE;
            }
            return FALSE;
        }

        /**
         * @return $this|bool
         */
        public
        function previewBeforeSave()
        {
            $this->filterData = [
                'name'           => $this->request->filter_name,
                'is_default'     => isset($this->request->is_default) ? $this->request->is_default : 0,
                'filter_type_id' => $this->request->filter_type_id,
                'created_by'     => (isset(Auth::user()->id) ? Auth::user()->id : 1), // TODO: implement auth user
            ];

            $filterType = CrmFilterType::find($this->request->filter_type_id, ['name', 'component', 'id', 'identifier']);
            if ($filterType) {
                $this->selectedFilterType = $filterType->toArray();
            } else {
                return FALSE;
            }

            if ($this->request->selected_fields && isset($this->request->selected_fields['custom'])) {
                $this->selectedFilterFields['custom'] = $this->request->selected_fields['custom'];
            }

            if ($this->request->selected_fields && isset($this->request->selected_fields['default'])) {
                foreach ($this->request->selected_fields['default'] as $field) {
                    $key = array_search($field, array_column($this->selectedFilterFields[$this->selectedFilterType['identifier']], 'short_name'));
                    if (is_numeric($key)) {
                        continue;
                    }
                    if ($this->selectedFilterType['identifier'] == 'user') {
                        $user = new User();
                        $userFillable = $user->getFillables();
                        if (in_array($field, $userFillable)) {
                            $this->selectedFilterFields['user'][] = [
                                'short_name' => $field,
                                'name'       => ucwords(str_replace('_', ' ', $field)),
                            ];
                            continue;
                        }
                        $contact = new \App\Contact();
                        $contactFillable = $contact->getFillables();
                        if (in_array($field, $contactFillable)) {
                            $this->selectedFilterFields['newsletter_contacts'][] = [
                                'short_name' => $field,
                                'name'       => ucwords(str_replace('_', ' ', $field)),
                            ];
                            continue;
                        }
                    } else {
                        $this->selectedFilterFields[$this->selectedFilterType['identifier']][] = [
                            'short_name' => $field,
                            'name'       => ucwords(str_replace('_', ' ', $field)),
                        ];
                    }
                }
            }

            /*$crmFilter = CrmFilter::create($filterData);
            if($crmFilter){

            }*/

            return $this->setFilterCondition(NULL);
        }

        /**
         * list of filter type for api
         *
         * @return array of filter type
         */
        public
        function getFilterType($id = 1)
        {
            $data = [
                'entity' => $this->getFilterEntity($id),
                'rules'  => $this->getfilterRules(),
            ];
            return $data;
        }

        /**
         * get filter rules from database
         *
         * @return mixed
         */
        protected
        function getFilterRules()
        {

            if (isset(Auth::user()->setting)) {
                $json = json_decode(Auth::user()->setting);
                $lang = isset($json->lang) ? strtolower($json->lang) : 'fr';
                session()->put('lang', $lang);
            } elseif (session()->has('lang')) {
                $lang = strtolower(session()->get('lang'));
            } else {
                $lang = 'fr';
            }
            $return = [];
            $crmFilterRules = CrmFilterRule::get(['id', 'name', 'short_name', 'available_formats', 'fr_name']);
            foreach ($crmFilterRules as $crmFilterRule) {
                $return[] = [
                    'id'                => $crmFilterRule->id,
                    'name'              => $crmFilterRule->name,
                    'name_label'        => (($lang == 'fr') ? $crmFilterRule->fr_name : $crmFilterRule->name),
                    'short_name'        => $crmFilterRule->short_name,
                    'available_formats' => json_decode($crmFilterRule->available_formats),
                ];
            }

            return $return;
        }

        /**
         * get filter entity from database
         *
         * @return array
         */
        protected
        function getFilterEntity($id = 1)
        {
            $return = [];
            if (isset(Auth::user()->setting)) {
                $json = json_decode(Auth::user()->setting);
                $lang = isset($json->lang) ? strtolower($json->lang) : 'fr';
                session()->put('lang', $lang);
            } elseif (session()->has('lang')) {
                $lang = strtolower(session()->get('lang'));
            } else {
                $lang = 'fr';
            }
            $filters = CrmFilterType::where('id', $id)->get(['id', 'name', 'component', 'fr_name']);
            foreach ($filters as $filter) {
                $components = json_decode($filter->component);

                $fields = [];
                foreach ($components as $key => $component) {
//                    if (($key == 'instance' || $key == 'press') && (config('constants.' . ucfirst($key)) == 0)) {
//                        continue;
//                    }
                    if ($filter->name != 'Person' && (isset($component->default[0]) && $component->default[0] == "App\Entity")) {
                        $default = (isset($component->default) && is_array($component->default)) ? $this->getModelFillable($component->default, $key, ['entity_label']) : [];
                    } else {
                        $default = (isset($component->default) && is_array($component->default)) ? $this->getModelFillable($component->default, $key) : [];
                    }
                    $custom = (isset($component->custom)) ? $this->getCustomFillable($component->custom, $key) : [];
                    $fields[] = [
                        'name'       => $key,
                        'name_label' => trans('message.' . $key),
                        'fields'     => [
                            'default' => collect($default)->unique('name')->toArray(),
                            'custom'  => $custom,
                        ],
                    ];
                }

                $data = [
                    'id'         => $filter->id,
                    'name'       => $filter->name,
                    'name_label' => (($lang == 'fr') ? $filter->fr_name : $filter->name),
                    'component'  => $fields,
                ];
                $return[] = $data;
            }

            return $return;
        }

        /**
         * get fillable fields from database table
         *
         * @param array $models
         * @return array of model fields
         */
        protected
        function getModelFillable(array $models, $key = '', $exclude = [])
        {
            $fields = [];

            foreach ($models as $model) {
                $object = new $model;

                if ($key == 'user' || $key == 'contact') {
                    $field = $object->getFillablesPerson();
                } else {
                    $field = $object->getFillables();
                }
                if ($key == 'user') {
                    $fields = array_merge($fields, ['role']);
                }
                if ($key == 'persons' && !empty($fields)) {
                    $fields = array_intersect($fields, $field);
                    $fields = array_values($fields);
                    //                dd($field, $fields);
                } else {
                    if (!empty($exclude)) {
                        $field = array_diff($field, $exclude);
                    }

                    $fields = array_merge($fields, $field);
                }
            }
            $return = [];
            foreach ($fields as $field) {
                //  dump($key);
                $return[] = [
                    'name'       => str_replace('_', ' ', $field),
                    'name_label' => __('message.' . $field),
                    'short_name' => $field,

                ];
            }
            return $return;
        }

        /**
         * @param array $models
         * @param string $key
         * @return array
         */
        protected
        function getCustomFillable(array $models, $key = '')
        {
            $type = [
                'user'     => [
                    'index' => 0,
                    'name'  => 'User',
                ],
                'contact'  => [
                    'index' => 1,
                    'name'  => 'Contact',
                ],
                'company'  => [
                    'index' => 2,
                    'name'  => 'Company',
                ],
                'instance' => [
                    'index' => 3,
                    'name'  => 'Instance',
                ],
                'union'    => [
                    'index' => 4,
                    'name'  => 'Union',
                ],
                'press'    => [
                    'index' => 7,
                    'name'  => 'Press',
                ],
            ];

            if ($key == 'union_external' || $key == 'union_internal') {
                $key = 'union';
            }
            $tabs = [];
            if (isset($type[$key])) {
                $skillsTabs = SkillTabs::where('is_valid', 1)->where('tab_type', $type[$key]['index'])->pluck('id');
                $tabsArray = Skill::where('is_valid', 1)->whereIn('skill_tab_id', $skillsTabs)->with('skillFormat', 'skillSelect')->get(['id', 'is_valid', 'skill_tab_id', 'skill_format_id', 'sort_order', 'is_conditional', 'is_qualifying', 'name', 'short_name']);

                if (!empty($tabsArray)) {
                    foreach ($tabsArray as $tab) {
                        if (!empty($tab->skillFormat)) {

                            if (!in_array($tab->skillFormat->short_name, $this->skipCustomFields)) {
                                $tabs[] = [
                                    'id'            => $tab->id,
                                    'name'          => $tab->name,
                                    'short_name'    => $tab->short_name,
                                    'field_type'    => $tab->skillFormat->field_type,
                                    'field_type_id' => $tab->skill_format_id,
                                    'select_option' => $tab->skillSelect,
                                ];
                            }

                        }
                    }
                }
            }
            return $tabs;
        }

        /**
         * validate filter data from api
         *
         * @param Request $request
         * @param null $id
         * @param string $type
         * @return $this
         */
        public
        function validateFilterData(Request $request, $id = NULL, $type = 'save')
        {
            $this->request = $request;

            $requestAll = $request->all();
            if ($type == 'update') {
                $requestAll['filter_type_id'] = $this->filter->filter_type_id;
                $this->request->filter_type_id = $this->filter->filter_type_id;
            }
            // TODO: implement validation for filter data
            $rules = $this->filterValidationRules($id, $type);
            $this->validation = validator::make($requestAll, $rules);
            return $this;
        }

        public
        function validateFieldUpdateData(Request $request)
        {
            $this->request = $request;
            $requestAll = $request->all();

            $rules = $this->filterValidationUpdateRules();
            $this->validation = validator::make($requestAll, $rules);

            return $this;
        }

        public
        function validateFieldMainData(Request $request)
        {
            $this->request = $request;
            $requestAll = $request->all();

            $rules = $this->filterValidationMainRecordRules();
            $this->validation = validator::make($requestAll, $rules);
            return $this;
        }

        /**
         * validation rules for filter data
         *
         * @param null $id
         * @return array
         */
        protected
        function filterValidationRules($id = NULL, $type)
        {
            if ($id) {
                $rules = [
                    //                'filter_type_id' => ['required', new CrmFilterTypeExist],
                    'filter_name'                 => ['required', new Alphanumeric, new CrmFilterNameUnique($id)], //
                    'conditions'                  => ['required'],
                    'conditions.*.condition'      => ['required', new CrmFilterConditionExist],
                    'conditions.*.field_name'     => [new CrmFilterNameRequire($this->request), new CrmFilterFieldNameExist($this->request)],
                    'conditions.*.component'      => ['required', new CrmFilterComponentExist],
                    'conditions.*.condition_type' => ['required', 'in:and,or'],
                    'conditions.*.is_default'     => ['required', 'boolean'],

                ];
            } else {
                $rules = [
                    'filter_type_id' => ['required', new CrmFilterTypeExist],

                    'conditions'                  => ['required'],
                    'save_selected_fields'        => ['required'],
                    'selected_fields'             => ['required'],
                    //                'selected_fields.default' => ['required'],
                    //                'selected_fields.custom' => ['required'],
                    'conditions.*.condition'      => ['required', new CrmFilterConditionExist],
                    'conditions.*.field_name'     => [new CrmFilterNameRequire($this->request), new CrmFilterFieldNameExist($this->request)],
                    'conditions.*.component'      => ['required', new CrmFilterComponentExist],
                    'conditions.*.condition_type' => ['required', 'in:and,or'],
                    'conditions.*.is_default'     => ['required', 'boolean'],

                ];
            }
            if ($type != 'preview') {
                $rules['filter_name'] = ['required', new Alphanumeric, new CrmFilterNameUnique($id)];
            }
            return $rules;
        }

        protected
        function filterValidationUpdateRules()
        {
            $rules = [
                'fields.*.field_type' => ['required', 'in:custom,default'],
                'fields.*.field_id'   => ['sometimes', 'required', new CrmFilterSkillExist],
                'fields.*.value'      => ['nullable'],
                'entity.*.type'       => ['required', 'in:user,contact,entity,instance,press,company,union'],
            ];
            return $rules;
        }

        protected
        function filterValidationMainRecordRules()
        {
            $rules = [
                'fields' => ['required'],
                'entity' => ['required'],
            ];
            return $rules;
        }

        /**
         * @param null $crmFilter
         * @return $this
         */
        protected
        function setFilterCondition($crmFilter = NULL)
        {
            $conditions = [];
            foreach ($this->request->conditions as $condition) {
                $data = [
                    'component'      => $condition['component'],
                    'condition'      => $condition['condition'],
                    'condition_type' => $condition['condition_type'],
                    'value'          => isset($condition['value']) ? $condition['value'] : '',
                    'filter_type_id' => $this->request->filter_type_id,
                    'filter_id'      => $crmFilter ? $crmFilter->id : NULL,
                ];
                $data['field_default'] = isset($condition['field_name']) ? $condition['field_name'] : '';
                $data['field_name'] = isset($condition['field_name']) ? $condition['field_name'] : '';
                $data['is_default'] = $condition['is_default'];
                if (!$condition['is_default']) {
                    $data['field_id'] = isset($condition['field_id']) ? $condition['field_id'] : '';
                }

                $conditions[] = $data;
            }
            $this->selectedFilterConditions = $conditions;
            $this->setTableNames();
            return $this;
        }

        /**
         * @return bool
         */
        public
        function deleteFilter()
        {
            $id = $this->filter->id;

            CrmFilterField::where('filter_id', $id)->delete();
            CrmFilterCondition::where('filter_id', $id)->delete();
            $this->filter->delete();
            return TRUE;
        }

        /**
         * get filter entity from database
         *
         * @return array
         */
        public
        function getCustomFieldList($filterType)
        {
            $default = [];
            $custom = [];
            if ($filterType->identifier == 'user') {
                $user = new User();
                $fields = $user->getFillablesPerson();
                $custom = $this->getCustomFillable([], 'user');
                $custom = array_merge($custom, $this->getCustomFillable([], 'contact'));
            } else if ($filterType->identifier == 'entity') {
                $entity = new Entity();
                $fields = $entity->getFillables();
                $key = strtolower($filterType->name);
                $custom = $this->getCustomFillable([], $key);
            } else {
                //            return response()->json(['status' => false, 'msg' => 'Filter type not identified', 400]);
            }
            foreach ($fields as $field) {
                $default[] = [
                    'name'       => str_replace('_', ' ', $field),
                    'short_name' => $field,
                ];
            }
            return [
                'default'        => $default,
                'custom'         => $custom,
                'filter_type_id' => $filterType->id,
                'filter_type'    => $filterType->name,
            ];
        }

        /**
         * @return bool
         */
        public
        function addFilterField($request)
        {
            $id = $this->filter->id;
            $fields = CrmFilterField::where('filter_id', $id)->first();

            if ($fields && $request->custom && !empty($request->custom)) {
                $value = json_decode($fields->value, TRUE);

                $custom = isset($value['custom']) ? $value['custom'] : [];

                //            $value['custom'] = array_merge($custom, $request->custom);
                $value['custom'] = array_unique(array_merge($custom, $request->custom), SORT_REGULAR);
                $fields->value = json_encode($value);
                $fields->save();
            }

            return TRUE;
        }

        /**
         * @return bool
         */
        public
        function deleteFilterField($request)
        {
            $id = $this->filter->id;
            $fields = CrmFilterField::where('filter_id', $id)->first();

            if ($fields && $request->custom_id) {
                $value = json_decode($fields->value, TRUE);
                $custom = isset($value['custom']) ? $value['custom'] : [];

                if (($key = array_search($request->custom_id, $custom)) !== FALSE) {
                    unset($custom[$key]);
                }
                $value['custom'] = $custom;
                $fields->value = json_encode($value);
                $fields->save();
            }
            return TRUE;
        }

        /**
         * @return mixed
         */
        public
        function updateCustomFieldsValue()
        {
            $request = $this->request;
            $fields = $request->fields;
            $entities = $request->entity;
            $fieldsData = [];
            foreach ($fields as $field) {

                if ($field['field_type_id'] == '0') {
                    $this->updateDefaultFieldsValue($field, $entities);
                } elseif ($field['field_type_id'] == 'entity_relation') {
                    $this->updateEntityRelation($field, $entities);
                } else {
                    $skill = Skill::find($field['field_id']);
                    if ($skill) {
                        $fieldName = ($skill->skillFormat->short_name == "radio_input") ? 'select_input' : $skill->skillFormat->short_name;
                        $fieldsData[] = [
                            'field_name' => $fieldName,
                            'skill_id'   => $skill->id,
                            'value'      => $field['value'],
                        ];
                    }
                }
            }

            foreach ($entities as $entity) {
                $this->updateUserSkillsTable($fieldsData, $entity);
            }

            return TRUE;
        }

        protected
        function updateUserSkillsTable($fields, $entity)
        {
            switch ($entity['type']) {
                case 'user':
                    $tabType = 0;
                    break;
                case 'contact':
                    $tabType = 1;
                    break;
                case 'company':
                    $tabType = 2;
                    break;
                case 'instance':
                    $tabType = 3;
                    break;
                case 'union':
                    $tabType = 4;
                    break;
                case 'press':
                    $tabType = 7;
                    break;
                default:
                    $tabType = 2;
            }

            $skills = Skill::whereHas('skillTab', function ($q) use ($tabType) {
                $q->where('tab_type', $tabType);
            })->whereIn('id', array_column($fields, 'skill_id'))->get(['id', 'skill_tab_id']);

            foreach ($fields as $field) {
                if (in_array($field['skill_id'], $skills->pluck('id')->toArray())) {
                    if ($field['field_name'] == 'select_input' && empty($field['value'])) {
                        $field['value'] = 0;
                    }

                    if ($entity['type'] == 'user') {
                        UserSkill::updateOrCreate(
                            ['user_id' => $entity['entity_id'], 'skill_id' => $field['skill_id']],
                            [$field['field_name'] => $field['value']]
                        );
                    } else if ($entity['type'] == 'contact') {
                        UserSkill::updateOrCreate(
                            ['field_id' => $entity['entity_id'], 'type' => 'contact', 'skill_id' => $field['skill_id']],
                            [$field['field_name'] => $field['value']]
                        );
                    } else {
                        UserSkill::updateOrCreate(
                            ['field_id' => $entity['entity_id'], 'skill_id' => $field['skill_id'], 'type' => $entity['type']],
                            [$field['field_name'] => $field['value'], 'type' => $entity['type']]
                        );
                    }
                }
            }
        }

        protected
        function updateDefaultFieldsValue($field, $entities)
        {
            foreach ($entities as $entity) {

                if ($entity['type'] == 'user') {
                    $fieldsData[] = [
                        'industry_id' => $field['value'],
                        'id'          => $entity['entity_id'],
                    ];
                } elseif ($entity['type'] == 'entity' || $entity['type'] == 'company' || $entity['type'] == 'union' || $entity['type'] == 'instance' || $entity['type'] == 'press') {
                    $fieldsDataEntity[] = [
                        'industry_id' => $field['value'],
                        'id'          => $entity['entity_id'],
                    ];
                }
            }
            if (isset($fieldsData)) {
                $userInstance = new User;
                $index = 'id';
                Batch::update($userInstance, $fieldsData, $index);
            } elseif (isset($fieldsDataEntity)) {
                $entityInstance = new Entity;
                $index = 'id';
                Batch::update($entityInstance, $fieldsDataEntity, $index);
            }

        }

        protected
        function updateEntityRelation($field, $entities)
        {

            $fieldsData = $fieldsDataC = [];
            foreach ($entities as $entity) {
                $type = $entity['type'] == 'contact' ? 'contact_id' : 'user_id';
                if ($field['editType'] == 'ADD') {
                    if ($entity['type'] == 'user') {
                        throw_if(!isset($field['value']['value']), \Exception::class, 'Value Should Not Empty');
                        $this->adjustEntityRelationData($field, $entity);

                    } elseif ($entity['type'] == 'contact') {
                        throw_if(!isset($field['value']['value']), \Exception::class, 'Value Should Not Empty');
                        $this->adjustEntityRelationData($field, $entity);
                    }
                } elseif ($field['editType'] == 'DELETE') {
                    throw_if(empty($field['field_name']), \Exception::class, 'Field Name Should Not Empty');
                    // Check duplicate entry
                    $entity_type = $field['field_name'];
                    $entity_user_relation = EntityUser::whereHas('entity', function ($q) use ($entity_type) {
                        $q->where('entity_type_id', $this->ENTITIES[$entity_type]);
                    })->where($type, $entity['entity_id']);
                    if ($entity_user_relation->count() > 0) {
                        EntityUser::whereIn('entity_id', $entity_user_relation->pluck('entity_id')->toArray())->where($type, $entity['entity_id'])->delete();
                    }
                }
            }
            // dump($fieldsData, $fieldsDataC);

//            if (isset($fieldsData)) {
//                $userInstance = new User;
//                $index = 'id';
//                Batch::update($userInstance, $fieldsData, $index);
//            } elseif (isset($fieldsDataEntity)) {
//                $entityInstance = new Entity;
//                $index = 'id';
//                Batch::update($entityInstance, $fieldsDataEntity, $index);
//            }
        }

        protected
        function adjustEntityRelationData($field, $entity)
        {
            $type = $entity['type'] == 'contact' ? 'contact_id' : 'user_id';
            $pos = (App::getLocale() == 'fr') ? 'Fonction à préciser' : 'Position to be precised';

            $create_columns = [
                $type          => $entity['entity_id'],
                'entity_id'    => $field['value']['value'],
                'entity_label' => isset($field['value']['position']) ? $field['value']['position'] : $pos,
            ];
            $update_columns = [
                'entity_id'    => $field['value']['value'],
                'entity_label' => isset($field['value']['position']) ? $field['value']['position'] : $pos,
            ];

            $entity_type = Entity::find($field['value']['value'])->entity_type_id;
            // check if entity id provided is actually have the same type
            throw_if((!($entity_type && $entity_type == $this->ENTITIES[$field['value']['type']])), \Exception::class, 'Invalid Entity');

            if ($field['value']['type'] == 'union' && config('constants.CRM')) {
                $create_columns['membership_type'] = isset($request->member_type) ? $field['value']['membership_type'] : '';
                $update_columns['membership_type'] = isset($request->member_type) ? $field['value']['membership_type'] : '';
            }

            // Check already belongs to some company instance or press
            $entity_type = $field['value']['type'];
            if ($field['value']['type'] != 'union' && in_array($this->ENTITIES[$entity_type], self::allowedEntities())) {

                $entity_user_count = EntityUser::whereHas('entity', function ($q) use ($entity_type) {
                    $q->where('entity_type_id', $this->ENTITIES[$entity_type]);
                })->with('user:id,fname,setting')->where($type, $entity['entity_id']);
                $lang = $entity_user_count->first();
                if ($entity_user_count->count()) {
                    if (isset($lang->user->setting)) {
                        $_lang = json_decode($lang->user->setting);
                        $update_columns['entity_label'] = (isset($_lang->lang) && $_lang->lang == 'EN') ? 'Position to be precised' : 'Fonction à préciser';
                    }

                    throw_if((!$entity_user_count->update($update_columns)), \Exception::class, 'Not Updated');
                } else {

                    if (isset($lang->user->setting)) {
                        $_lang = json_decode($lang->user->setting);
                        $create_columns['entity_label'] = (isset($_lang->lang) && $_lang->lang == 'EN') ? 'Position to be precised' : 'Fonction à préciser';
                    } else {
                        if ($entity['type'] == 'user') {
                            $user = User::where('id', $entity['entity_id'])->first(['id', 'setting']);
                            if (isset($user->setting)) {
                                $_lang = json_decode($user->setting);
                                $create_columns['entity_label'] = (isset($_lang->lang) && $_lang->lang == 'EN') ? 'Position to be precised' : 'Fonction à préciser';
                            }
                        }
                    }
                    // DONE
                    $entityUser = EntityUser::create($create_columns);
                    throw_if(!isset($entityUser->id), \Exception::class, 'person not added to entity');
                }

                throw_if((!$entity_user_count->count()), \Exception::class, 'No Previous Relation Found');
                /* throw_if($entity_user_count->count(), \Exception::class, ucfirst($field['value']['type']) . ' already belongs to ' . ucfirst($entity['type']) . $entity['entity_id']);*/
            } elseif (in_array($this->ENTITIES[$entity_type], self::allowedEntities())) {

                $entity_user_count = EntityUser::whereHas('entity', function ($q) use ($entity_type, $field) {
                    $q->where('entity_type_id', $this->ENTITIES[$entity_type]);
                    $q->where('id', $field['value']['value']);
                })->with('user:id,fname,setting')->where($type, $entity['entity_id']);
                $lang = $entity_user_count->first();
                // DONE
                if (!$entity_user_count->count()) {
                    $entity_user_count1 = EntityUser::whereHas('entity', function ($q) use ($entity_type, $field) {
                        $q->where('entity_type_id', $this->ENTITIES[$entity_type]);
                    })->with('user:id,fname,setting')->where($type, $entity['entity_id']);
                    $lang1 = $entity_user_count1->first();
                    if (isset($lang1->user->setting)) {
                        $_lang = json_decode($lang1->user->setting);
                        $create_columns['entity_label'] = (isset($_lang->lang) && $_lang->lang == 'EN') ? 'Position to be precised' : 'Fonction à préciser';
                    } else {
                        if ($entity['type'] == 'user') {
                            $user = User::where('id', $entity['entity_id'])->first(['id', 'setting']);
                            if (isset($user->setting)) {
                                $_lang = json_decode($user->setting);
                                $create_columns['entity_label'] = (isset($_lang->lang) && $_lang->lang == 'EN') ? 'Position to be precised' : 'Fonction à préciser';
                            }
                        }
                    }

                    $entityUser = EntityUser::create($create_columns);
                    throw_if(!isset($entityUser->id), \Exception::class, 'person not added to entity');
                }
            }
        }

        public
        function getLabel(array &$data)
        {
            foreach ($data as $k => $datum) {
                $data[$k]['name'] = __('message.' . $datum['short_name']);
            }
            return array_values($data);
        }

        public
        function getIntersectQueryData($getQuery, $getQueryBind)
        {
            $query = '';
            foreach ($getQuery as $k => $val) {
                if ($k > 0) {
                    $query .= ' intersect ';
                }
                $query .= $val;
            }
            return $interseDef = (DB::connection('tenant')->select($query, call_user_func_array("array_merge", $getQueryBind)));
        }

        public
        function getUnionQueryData($getQuery, $getQueryBind, $offset = NULL, $no_of_records_per_page = NULL)
        {
            $query = '';
            foreach ($getQuery as $k1 => $val1) {
                if (!empty($query)) {
                    $query .= ' UNION ';
                } elseif (empty($query) && ($k1 > 0)) {
                    $query .= ' UNION ';
                }

                $query .= $val1;
            }
            if ((!empty($offset) || $offset == 0) && !empty($no_of_records_per_page)) {
                return $combine = (DB::connection('tenant')->select($query . " LIMIT $offset, $no_of_records_per_page", call_user_func_array("array_merge", $getQueryBind)));
            }
            return $combine = (DB::connection('tenant')->select($query, call_user_func_array("array_merge", $getQueryBind)));

        }

        public
        function addDefaultCondition($object, $type, $component)
        {
            $collection = collect($this->selectedFilterConditions)->where('component', $component)->where('is_default', TRUE)->where('condition_type', $type)->toArray();

            if (count($collection) > 0) {
                foreach ($collection as $condition) {
                    $operator = $this->getConditionOperator($condition['condition']);
                    $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                    if (empty($condition['value']))
                        $condition['value'] = NULL;
                    $condition['operator'] = $operator->operator;

                    if ($condition['field_name'] == 'entity_sub_type') {
                        $condition['value'] = ($condition['value'] == 'internal') ? 1 : 2;
                    }
                    if ($condition['field_name'] == 'membership_type') {
                        $condition['value'] = ($condition['value'] == 'is_staff') ? 1 : 0;
                    }

                    if ($condition['condition_type'] == 'and') {
                        $object = $object->where($condition['field_name'], $condition['operator'], $condition['value']);
                    } else {
                        $object = $object->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                    }
                    if ($operator->short_name == "is_empty") {
                        $object = $object->whereNull($condition['field_name']);
                    } else if ($operator->short_name == "is_not_empty") {
                        $object = $object->whereNotNull($condition['field_name']);
                    }
                }
            }
            return $object;
        }

        public
        function getIntersectIds($custom, $object, &$ids, $else = 0, $elseDef = 0, &$param)
        {

            if ($else) {
                $ids = array_merge($ids, array_intersect($object, $custom));
            } elseif (!empty($custom) && !empty($object)) {
                $ids = array_merge($ids, array_intersect($object, $custom));
            } elseif (empty($custom) && !empty($object)) {
                $ids = array_merge($ids, $object);
            } elseif (!empty($custom) && (empty($object) && $elseDef == 0)) {
                $ids = array_merge($ids, $custom);
            } elseif (!empty($custom) && (empty($object) && $elseDef)) {

                $ids = array_merge($ids, array_intersect($object, $custom));
            } else {
                if (isset($param['entityCanMerge'])) {
                    $param['entityCanMerge'] = 1;
                } elseif (isset($param['contactCanMerge'])) {
                    $param['contactCanMerge'] = 1;
                } elseif (isset($param['userCanMerge'])) {
                    $param['contactCanMerge'] = 1;
                }
            }

            return $ids;
        }

        public
        function getPersonIdsOnEntity($entityCustomAnd, $entityCustomOr, &$entityIds, &$ids, &$contactIds, $param)
        {

            //$entityCustomAnd, $entityCustomOr,$userCustomAnd,$contactCustomAnd
            if (isset($param['userDef']) && (empty($param['userDef']) && $param['userDefResFalse'])) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $userIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('user_id')->pluck('user_id')->toArray();
                $userIds = array_filter($userIds);
                $ids = array_intersect($ids, $userIds);
//                $ids = array_merge($ids, array_intersect($ids, $userIds));
            } elseif (isset($param['userDef']) && (!empty($param['userDef']) && $param['userDefResFalse'] == 0)) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $userIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('user_id')->pluck('user_id')->toArray();
                $userIds = array_filter($userIds);
                $ids = array_intersect($userIds, $ids);
//                $ids = array_merge($ids, array_intersect($userIds, $ids));dd($ids);
            } else if (isset($param['entityCust']) && (empty($param['entityCust']) && $param['entityCustResFalse'])) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $userIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('user_id')->pluck('user_id')->toArray();
                $userIds = array_filter($userIds);
                $ids = array_merge($ids, array_intersect($ids, $userIds));
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                }
            } elseif (isset($param['entityCust']) && (!empty($param['entityCust']) && $param['entityCustResFalse'] == 0)) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $userIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('user_id')->pluck('user_id')->toArray();
                $userIds = array_filter($userIds);
                // $ids = array_merge($ids, array_intersect($ids, $userIds));
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                } else {
                    $ids = array_merge($ids, $userIds);
                }
            }

            //contact condtion for same as user
            if (isset($param['contactDef']) && (empty($param['contactDef']) && $param['contactDefResFalse'])) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $cIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('contact_id')->pluck('contact_id')->toArray();
                $cIds = array_filter($cIds);
                $contactIds = array_intersect($cIds, $contactIds);
//                $contactIds = array_merge($contactIds, array_intersect($contactIds, $cIds));
            } elseif (isset($param['contactDef']) && (!empty($param['contactDef']) && $param['contactDefResFalse'] == 0)) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $cIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('contact_id')->pluck('contact_id')->toArray();
                $cIds = array_filter($cIds);
                $contactIds = array_intersect($cIds, $contactIds);
//                $contactIds = array_merge($contactIds, array_intersect($contactIds, $cIds));
            } elseif (isset($param['entityCust']) && (empty($param['entityCust']) && $param['entityCustResFalse'])) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $cIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('contact_id')->pluck('contact_id')->toArray();
                $cIds = array_filter($cIds);
                $contactIds = array_merge($contactIds, array_intersect($contactIds, $cIds));
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $contactIds = [];
                }
            } elseif (isset($param['entityCust']) && (!empty($param['entityCust']) && $param['entityCustResFalse'] == 0)) {
                $entityIds = array_merge($entityIds, $entityCustomAnd);
                $cIds = EntityUser::whereIn('entity_id', $entityIds)->whereNotNull('contact_id')->pluck('contact_id')->toArray();
                $cIds = array_filter($cIds);
//                $contactIds = array_merge($contactIds, array_intersect($contactIds, $cIds));
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $contactIds = [];
                } else {
                    $contactIds = array_merge($contactIds, $cIds);
                }
            }
            return [$ids, $contactIds];
        }

        public
        function getEntityIdsOnPerson($entityCustomAnd, $entityCustomOr, &$ids, $param)
        {

            //$entityCustomAnd, $entityCustomOr,$userCustomAnd,$contactCustomAnd
            if (isset($param['userCust']) && (empty($param['userCust']) && (isset($param['userCustResFalse']) && $param['userCustResFalse']))) {
                $ids = [];
            } elseif (isset($param['userCust']) && (!empty($param['userCust']) && (isset($param['userCustResFalse']) && $param['userCustResFalse'] == 0))) {
                $entityIds = EntityUser::whereIn('user_id', $param['userCust'])->pluck('entity_id')->toArray();
                $entityIds = array_filter($entityIds);
                if (isset($param['entityCanMerge']) && $param['entityCanMerge'])
                    $ids = array_merge($ids, $entityIds);
                else
                    $ids = array_intersect($ids, $entityIds);
            } elseif (isset($param['entityCust']) && (empty($param['entityCust']) && $param['entityCustResFalse'])) {
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                }
            } elseif (isset($param['entityCust']) && (!empty($param['entityCust']) && $param['entityCustResFalse'] == 0)) {
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                }
            } elseif ((isset($param['blankIds']) && $param['blankIds']) && (isset($param['userCust']) && $param['userCust'] == 1)) {
                $ids = [];
            }
            //contact condtion for same as user
            if (isset($param['contactCust']) && (empty($param['contactCust']) && $param['contactCustResFalse'])) {
                $ids = [];
            } elseif (isset($param['contactCust']) && (!empty($param['contactCust']) && $param['contactCustResFalse'] == 0)) {
                $entityIds = EntityUser::whereIn('contact_id', $param['contactCust'])->pluck('entity_id')->toArray();
                $entityIds = array_filter($entityIds);
                if (isset($param['entityCanMerge']) && $param['entityCanMerge'])
                    $ids = array_merge($ids, $entityIds);
                else
                    $ids = array_intersect($ids, $entityIds);
//                $ids = array_merge($ids, array_intersect($ids, $entityIds));
            } elseif (isset($param['entityCust']) && (empty($param['entityCust']) && $param['entityCustResFalse'])) {
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                }
            } elseif (isset($param['entityCust']) && (!empty($param['entityCust']) && $param['entityCustResFalse'] == 0)) {
                if (isset($param['blankIds']) && $param['blankIds']) {
                    $ids = [];
                }
            } elseif ((isset($param['blankIds']) && $param['blankIds']) && (isset($param['contactCust']) && $param['contactCust'] == 1)) {
                $ids = [];
            }

            return $ids;
        }

        public
        function getIdsOfCustom($objectCustomAnd, $intersectElse, $objectCustomOr, $unionElse)
        {
//            if (isset($userIds[0]) && is_array($userIds[0])) {
//                $userIds = array_filter($userIds[0]);
//            }
//            $userEntityIds = EntityUser::whereIn('user_id', $userIds)->pluck('entity_id')->toArray();
//            $ids = array_merge($ids, $userEntityIds);
        }

        protected
        function adjsutParam($objectCustomAnd, $intersectElse, $type, &$params)
        {
            if (count($objectCustomAnd) > 0) {
                $params[$type] = $objectCustomAnd;
                $params[$type . 'ResFalse'] = $intersectElse;
                $params['intersect'] = TRUE;
            } elseif (count($objectCustomAnd) == 0 && $intersectElse == 1) {
                $params[$type] = $objectCustomAnd;
                $params[$type . 'ResFalse'] = $intersectElse;
                $params['intersect'] = TRUE;
            }/* elseif (count($objectCustomAnd) == 0 && $intersectElse == 0) {
                $params[$type] = $objectCustomAnd;
                $params[$type . 'ResFalse'] = $intersectElse;
                $params['intersect'] = FALSE;
            }*/
            return $params;
        }

        public
        function addDefaultUnionCondition($query, $type = 'and')
        {
            $collection = collect($this->selectedFilterConditions)->where('component', 'union')->where('is_default', TRUE)->where('condition_type', $type)->where('field_name', '!=', 'membership_type')->toArray();

            if (count($collection) > 0) {
                foreach ($collection as $condition) {
                    $operator = $this->getConditionOperator($condition['condition']);
                    $condition['value'] = str_replace(':value', $condition['value'], $operator->value);
                    if (empty($condition['value']))
                        $condition['value'] = NULL;

                    if ($condition['field_name'] == 'entity_sub_type') {
                        $condition['value'] = ($condition['value'] == 'internal') ? 1 : 2;
                    }

                    $condition['operator'] = $operator->operator;
                    if ($condition['condition_type'] == 'and') {
                        $object = $query->where($condition['field_name'], $condition['operator'], $condition['value']);
                    } else {
                        $object = $query->where(function ($a) use ($condition) {
                            $a->where($condition['field_name'], $condition['operator'], $condition['value']);
                        });
//                        $object = $query->orWhere($condition['field_name'], $condition['operator'], $condition['value']);
                    }
                    if ($operator->short_name == "is_empty") {
                        $object = $query->whereNull($condition['field_name']);
                    } else if ($operator->short_name == "is_not_empty") {
                        $object = $query->whereNotNull($condition['field_name']);
                    }
                }
            }
            return $query;
        }

        public
        static function allowedEntities()
        {
            $array = [
                'instance' => 1,
                'company'  => 2,
                'union'    => 3,
                'press'    => 4,
            ];;
            if (!config('constants.CRM')) {
                unset($array['company']);
                unset($array['union']);
            }
            if (!config('constants.Press')) {
                unset($array['press']);
            }
            if (!config('constants.Instance')) {
                unset($array['instance']);
            }
            return $array;
        }
    }
    
