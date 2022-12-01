<?php
/**
 * Created by PhpStorm.
 * User: Sourabh Pancharia
 * Date: 6/22/2019
 * Time: 2:14 PM
 */

namespace Modules\Crm\Services;


class DashBoard
{
    /**
     * SuperAdminSingleton constructor.
     */
    private $tenancy, $core, $import;

    public function __construct()
    {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->import = app(\App\Http\Controllers\import\ImportController::class);
    }

    /**
     * Make instance of SuperAdmin singleton class
     * @return DashBoard|null
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function getSearch($users, $contacts, $entities)
    {
        $company = $instance = $union = $union_internal = $union_external = $press = 0;
        $data = [];
        $users->map(function ($user) {
            $user['type'] = 'user';
            return $user;
        });
        $contacts->map(function ($contact) {
            $contact['type'] = 'contact';
            return $contact;
        });
        $entities->map(function ($entity) use (&$company, &$instance,&$union, &$union_internal, &$union_external, &$press) {
            if ($entity->entity_type_id == 1) {
                $entity['type'] = 'instance';
                $instance += 1;
            } elseif ($entity->entity_type_id == 2) {
                $entity['type'] = 'company';
                $company += 1;
            } elseif ($entity->entity_type_id == 3) {
                $entity['entity_sub_type'] = $entity->entity_sub_type;
                if($entity->entity_sub_type == 1) {
                    $entity['type'] = 'union_internal';
                    $union_internal += 1;
                } else {
                    $entity['type'] = 'union_external';
                    $union_external += 1;
                }
                $union += 1;
            } elseif ($entity->entity_type_id == 4) {
                $entity['type'] = 'press';
                $press += 1;
            }
            return $entity;
        });

        $collection = collect($users);
        $collection = $collection->merge($contacts);
        $collection = $collection->merge($entities);
        $collection = $collection->all();
        $data['data'] = $collection;
        $data['status'] = [
            'user' => count($users), 'contact' => count($contacts),
            'company' => $company,
            'instance' => $instance,
            'union' => $union,
            'union_internal' => $union_internal,
            'union_external' => $union_external,
            'press' => $press,
        ];
        return $data;
    }
}
