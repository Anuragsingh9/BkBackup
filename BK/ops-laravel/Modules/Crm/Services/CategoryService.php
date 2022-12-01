<?php

/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 6/22/2019
 * Time: 2:14 PM
 */

namespace Modules\Crm\Services;

use App\Entity;
use App\EntityUser;
use App\Model\Contact;
use App\Model\EntityDependency;
use App\Model\SkillTabs;
use App\User;
use Validator;
use DB;
use Auth;

class CategoryService {
    /**
     * SuperAdminSingleton constructor.
     */
        private $contactServices, $allowedEntities;
    
    public function __construct() {
        
            $this->allowedEntities = CrmServices::allowedEntities();
        // $this->contactServices = ContactServices::getInstance();
    }
    
    /**
     * Make instance of SuperAdmin singleton class
     * @return CategoryService|null
     */
    public static function getInstance() {
        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    
    public function addUser() {
    }
    
    public function addContact($data) {
        $validator = Validator::make($data, [
            'email' => 'required|unique:tenant.newsletter_contacts,email',
            'lname' => 'required',
            'fname' => 'required'
        ]);
        //validation false return errors
        if ($validator->fails()) {
            return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
        }
        // $contact = Contact::create(['fname' => $data->fname, 'lname' => $data->lname, 'email' => $data->email]);
        
        $contact = Contact::create(['fname' => $data['fname'], 'lname' => $data['lname'], 'email' => $data['email']]);
        
        return $contact;
    }
    
    public function getResponse($type, $id) {
        switch ($type) {
            case 'contact':
                return $this->getContactResponse($id, $type);
                break;
            case 'company':
                return $this->getEntityResponse($id, $type);
                break;
            case 'union':
                return $this->getEntityResponse($id, $type);
                break;
            case 'instance':
                return $this->getEntityResponse($id, $type);
                break;
            case 'press':
                return $this->getEntityResponse($id, $type);
            default:
                return $this->getUserResponse($id, $type);
        }
    }
    
    public function getUserResponse($id, $type) {
        try {
            $user = User::find($id, ['id', 'fname', 'lname', 'email', 'role', 'permissions', 'role_wiki', 'role_commision', 'phone', 'address', 'postal', 'city', 'country', 'mobile']);
            $custom = $this->generateUserResponse($user, $type);
            $where = ['user_id' => $user->id];
            $this->getPersonBelongsResponse($custom, $where);
            return $this->getCustomResponse($custom, 0);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getContactResponse($id, $type) {
        $contact = Contact::findOrFail($id);
        $custom = $this->generateUserResponse($contact, $type);
        $where = ['contact_id' => $contact->id];
        $this->getPersonBelongsResponse($custom, $where);
        return $this->getCustomResponse($custom, 1);
    }
    
    public function getEntityResponse($id, $type) {
        $entity = Entity::whereNotNull('long_name')->findOrFail($id);
        $custom = $this->generateEntityResponse($entity, $type, 'message.professional_tab');
        if ($type == 'company') {
            $type = 2;
            $this->getCompanyDependencyResponse($custom, $entity, 'message.professional_tab');
        } elseif ($type == 'instance') {
            $type = 3;
            $this->getCompanyDependencyResponse($custom, $entity, 'message.professional_tab', 'instance');
        } elseif ($type == 'union') {
            $type = 4;
            $this->getCompanyDependencyResponse($custom, $entity, 'message.professional_tab', 'union');
        } elseif ($type == 'press') {
            $type = 7;
            $this->getCompanyDependencyResponse($custom, $entity, 'message.professional_tab', 'press');
        }
        
        $custom['personBelongsCount'] = EntityUser::where(function ($q) use ($id) {
            $q->where('entity_id', $id);
        })->where(function ($query) {
            $query->whereHas('user')->orWhereHas('contact');
        })->count();
        
        return $this->getCustomResponse($custom, $type, 'message.professional_tab');
    }
    
    public function generateUserResponse($data, $type, $tabType = 'message.personal_tab') {
        if ($type == 'contact') {
            $response = [
                'id'               => $data->id,
                'type'             => $type,
                'entityHeaderData' => ['name' => $data->fname . ' ' . $data->lname],
                'entityTabList'    => [
                    [
                        'type'     => __($tabType),
                        'dataType' => 'person-info',
                        'data'     => [
                            [__('message.fname') => ['field' => 'fname', 'value' => $data->fname]],
                            [__('message.lname') => ['field' => 'lname', 'value' => $data->lname]],
                            [__('message.email') => ['field' => 'email', 'value' => $data->email]],
                            [__('message.phone') => ['field' => 'phone', 'value' => $data->phone]],
                            [__('message.address') => ['field' => 'address1', 'value' => $data->address]],
                            [__('message.postal') => ['field' => 'postal', 'value' => $data->postal]],
                            [__('message.city') => ['field' => 'city', 'value' => $data->city]],
                            [__('message.country') => ['field' => 'country', 'value' => $data->country]],
                            [__('message.mobile') => ['field' => 'mobile', 'value' => $data->mobile]]
                        ],
                        'custom'   => FALSE
                    ],
                ]
            ];
        } else {
            $response = [
                'id'               => $data->id,
                'type'             => $type,
                'entityHeaderData' => ['name' => $data->fname . ' ' . $data->lname],
                'entityTabList'    => [
                    [
                        'type'     => __($tabType),
                        'dataType' => 'person-info',
                        'data'     => [
                            [__('message.fname') => ['field' => 'fname', 'value' => $data->fname]],
                            [__('message.lname') => ['field' => 'lname', 'value' => $data->lname]],
                            [__('message.email') => ['field' => 'email', 'value' => $data->email]],
                            [__('message.phone') => ['field' => 'phone', 'value' => $data->phone]],
                            [__('message.address') => ['field' => 'address1', 'value' => $data->address]],
                            [__('message.postal') => ['field' => 'postal', 'value' => $data->postal]],
                            [__('message.city') => ['field' => 'city', 'value' => $data->city]],
                            [__('message.country') => ['field' => 'country', 'value' => $data->country]],
                            [__('message.mobile') => ['field' => 'mobile', 'value' => $data->mobile]],
                        ],
                        'custom'   => FALSE
                    ],
                    [
                        'type'     => __('message.roles'),
                        'dataType' => 'roles',
                        'data'     => ['permission' => (isset($data->permissions) ? $data->permissions : NULL), 'role' => $data->role, 'roleWiki' => $data->role_wiki, 'roleCommision' => $data->role_commision],
                        'custom'   => FALSE
                    ],
                    [
                        'type'      => __('message.diligence'),
                        'dataType'  => 'Diligence',
                        'data'      => [],
                        'custom'    => FALSE,
                        'diligence' => TRUE,
                    ]
                ]
            ];
        }
        
        return $response;
    }
    
    public function generateEntityResponse($data, $type, $tabType = 'message.personal_tab') {
        
        $response = [
            'id'                 => $data->id,
            'type'               => $type,
            'entity_sub_type'    => (isset($data->entity_sub_type) ? $data->entity_sub_type : NULL),
            'entityHeaderData'   => ['name' => $data->long_name, 'short_name' => $data->short_name],
            'entityTabList'      => [
                [
                    'type'     => __($tabType),
                    'dataType' => 'company-info',
                    'data'     => [
                        [__('message.' . strtolower($type)) . ' Name' => ['field' => 'long_name', 'value' => $data->long_name]],
                        [__('message.short_name') => ['field' => 'short_name', 'value' => $data->short_name]],
                        [__('message.entity_website') => ['field' => 'entity_website', 'value' => $data->entity_website]],
                        [__('message.address') => ['field' => 'address1', 'value' => $data->address1]],
                        [__('message.city') => ['field' => 'city', 'value' => $data->city]],
                        [__('message.country') => ['field' => 'country', 'value' => $data->country]],
                        [__('message.industry_id') => ['field' => 'industry_id', 'value' => $data->industry_id, 'meta' => $data->industry]],
                        [__('message.phone') => ['field' => 'phone', 'value' => $data->phone]],
                        [__('message.zip_code') => ['field' => 'zip_code', 'value' => $data->zip_code]],
                        [__('message.logo') => ['field' => 'entity_logo', 'value' => $data->entity_logo]],
                        [__('message.fax') => ['field' => 'fax', 'value' => ($data->fax != 'null' || $data->fax != NULL) ? $data->fax : '']],
                        [__('message.email') => ['field' => 'email', 'value' => $data->email]]
                    
                    ],
                    
                    'custom' => FALSE
                ],
            ],
            'personBelongsCount' => 0
        ];
        return $response;
    }
    
    public function getCustomResponse(&$array, $type) {
        try {
            $permissions = \Auth::user()->permissions;
            $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ? TRUE : 0;
            $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ? TRUE : 0;
            $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ? TRUE : 0;
            $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ? TRUE : 0;
            
            
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            $superPermissiom = DB::connection('mysql')
                ->table('account_settings')
                ->where('account_id', $hostname->id)
                ->first(['custom_profile_enable']);
            
            if ($superPermissiom->custom_profile_enable) {
                
                $tabs = SkillTabs::where(['tab_type' => $type, 'is_valid' => 1])
                    ->where(function ($q) use ($crmAdmin, $crmEditor, $crmAssistance, $crmRecruitment) {
                        if (!$crmAdmin) {
                            
                            if ($crmEditor)
                                $q->orWhere('visible->crmEditor', 1);
                            if ($crmAssistance)
                                $q->orWhere('visible->crmAssistance', 1);
                            if ($crmRecruitment)
                                $q->orWhere('visible->crmRecruitment', 1);
//                        if (((!$crmEditor) && (!$crmAssistance) && (!$crmRecruitment)) && Auth::user()->role == 'M2')
//                            $q->orWhere('visible->user', 1);
                        }
                    })
                    ->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')
                    ->get(['id', 'name', 'visible']);
                
                foreach ($tabs as $tab) {
                    //this commented response was wrong as per vijay so changed it as previouss
//                    $array['customTabList'][] = [
                    $array['entityTabList'][] = [
                        'type'   => $tab->name,
                        'id'     => $tab->id,
                        'data'   => [],
                        'custom' => TRUE
                    ];
                    //                }
                    
                }
            }
            
            return $array;
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getPersonBelongsResponse(&$array, $where) {
        //$where
            $entityUsers = EntityUser::with(['entity' => function ($q) {
                $q->whereIn('entity_type_id', $this->allowedEntities);
            }])->where($where)->get();
        $array['personBelongsTo'] = [
            'company'  => [],
            'union'    => [],
            'instance' => [],
            'press'    => [],
        ];
        foreach ($entityUsers as $entity) {
            if (isset($entity->entity->entity_type_id) && $entity->entity->entity_type_id == 2) {
                $array['personBelongsTo']['company'][] = ['id' => $entity->id, 'long_name' => $entity->entity->long_name, 'data_id' => $entity->entity_id, 'position' => $entity->entity_label];
            } elseif (isset($entity->entity->entity_type_id) && $entity->entity->entity_type_id == 3) {
                $array['personBelongsTo']['union'][] = ['id' => $entity->id, 'long_name' => $entity->entity->long_name, 'data_id' => $entity->entity_id, 'position' => $entity->entity_label, 'membership_type' => (isset($entity->membership_type) ? (($entity->membership_type == 0) ? 'Member' : 'Staff') : NULL)];
            } elseif (isset($entity->entity->entity_type_id) && $entity->entity->entity_type_id == 1) {
                $array['personBelongsTo']['instance'][] = ['id' => $entity->id, 'long_name' => $entity->entity->long_name, 'data_id' => $entity->entity_id, 'position' => $entity->entity_label];
            } elseif (isset($entity->entity->entity_type_id) && $entity->entity->entity_type_id == 4) {
                $array['personBelongsTo']['press'][] = ['id' => $entity->id, 'long_name' => $entity->entity->long_name, 'data_id' => $entity->entity_id, 'position' => $entity->entity_label];
            }
        }
        
        return $array;
    }
    
    public function getCompanyDependencyResponse(&$array, $entity, $a = '', $type = 'company') {
        $field = strtolower($type) . 'Dependency';
        $entityDep = EntityDependency::with('entity')->where('entity_id', $entity->id)->first();
        
        if (isset($entityDep->id)) {
            $array[$field] = ['id' => $entityDep->entity->id, 'long_name' => $entityDep->entity->long_name];
        } else {
            $array[$field] = [];
        }
        return $array;
    }
}
