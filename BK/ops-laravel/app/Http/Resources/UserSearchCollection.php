<?php
    
    namespace App\Http\Resources;
    
    use Illuminate\Http\Resources\Json\ResourceCollection;
    use Illuminate\Support\Facades\App;
    
    class UserSearchCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        
        
        public function toArray($request)
        {
            // set locale for localization
            App::setLocale((strtolower(session()->get('lang'))) ? strtolower(session()->get('lang')) : 'fr');
            $role = '';
            $this->collection->map(function ($v, $i) use (&$role) {
//                dd($v);
                //this will get main role
                if (isset($v->getRelationValue('role')->eng_text)) {
                    $role = $v->getRelationValue('role')->eng_text.',';
                }
                //this will get crm or in permission field
                if (!empty($v->permissions)) {
                    collect($v->permissions)->map(function ($val, $key) use (&$role) {
                        if ($val) {
                            $role = $role . __('message.' . $key) . ',';
//                            $role = $role . $key.',';
                        }
                    });
                }
                //this will for workshops role
                if (!empty($v->userMeta)) {
                    ($v->userMeta)->unique('role')->sortBy('role')->map(function ($val1, $key1) use (&$role) {
                        if ($val1->role == 1) {
                            $role = $role . __('message.W_SEC') . ',';
                        }
                        if ($val1->role == 2) {
                            $role = $role . __('message.W_DEP') . ',';
                        }
                        if ($val1->role == 0) {
                            $role = $role . __('message.W_MEMBER') . ',';
                        }
                    });
                }
                $string = implode(',', array_unique(explode(',', rtrim($role, ','))));
                $v->newRoles = $string;
                unset($v->userMeta);
                unset($v->union);
                unset($v->permissions);
                unset($v->internal_id);
                unset($v->role);
                
            });
            
            return [
                'status' => TRUE,
                'data'   => $this->collection,
            ];
        }
    }
