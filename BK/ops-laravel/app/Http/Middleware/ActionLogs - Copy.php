<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;
use App\ActionLog;

class ActionLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $action_type = $request->route()->methods[0];
        $curr_actions = [
            'method' => $action_type,
            'function' => explode('@', $request->route()->action['uses'])[1],
            'params' => ($action_type == 'POST') ? $request->all() : $request->route()->parameters,
            'log_text' => ''
        ];

        $this->saveActionLogs($curr_actions);
        return $next($request);
    }

    public function saveActionLogs($curr_actions)
    {


        //both action updateOrCreate
        $params_check = ['addResources', 'addResourcesCategory', 'addIndustry', 'addUnion', 'updateWorkshop', 'addGroup', 'addIssuer', 'addDocType', 'addWikiCategory'];

        //all actions 
        $list_actions = [
            [
                'method' => 'POST',
                'function' => 'updateOrgSetting',
                'match_key' => '',
                'log_text' => 'Save Org Setting'
            ],
            [
                'method' => 'POST',
                'function' => 'addResources',
                'match_key' => 'id',
                'log_text' => '##ACTION## Add Resources'
            ],
            [
                'method' => 'POST',
                'function' => 'addResourcesCategory',
                'match_key' => 'resources_id',
                'log_text' => '##ACTION## Resources Category'
            ],
            [
                'method' => 'GET',
                'function' => 'DeleteResourcesCategory',
                'match_key' => '',
                'log_text' => 'Delete Resources Category'
            ],
            [
                'method' => 'POST',
                'function' => 'addIndustry',
                'match_key' => 'id',
                'log_text' => '##ACTION## Industry'
            ],
            [
                'method' => 'GET',
                'function' => 'DeleteIndustry',
                'match_key' => '',
                'log_text' => 'Delete Industry'
            ],
            [
                'method' => 'POST',
                'function' => 'addUnion',
                'match_key' => 'id',
                'log_text' => '##ACTION## Union'
            ],
            [
                'method' => 'GET',
                'function' => 'deleteUnion',
                'match_key' => '',
                'log_text' => 'Delete Union'
            ],
            [
                'method' => 'POST',
                'function' => 'updateWorkshop',
                'match_key' => 'id',
                'log_text' => '##ACTION## Workshop'
            ],
            [
                'method' => 'POST',
                'function' => 'addGroup',
                'match_key' => 'id',
                'log_text' => '##ACTION## Group'
            ],
            [
                'method' => 'GET',
                'function' => 'DeleteGroup',
                'match_key' => '',
                'log_text' => 'Delete Group'
            ],
            [
                'method' => 'POST',
                'function' => 'addDocument',
                'match_key' => '',
                'log_text' => 'Add Document'
            ],
            [
                'method' => 'POST',
                'function' => 'addIssuer',
                'match_key' => 'id',
                'log_text' => '##ACTION## Issuer'
            ],
            [
                'method' => 'POST',
                'function' => 'addDocType',
                'match_key' => 'id',
                'log_text' => '##ACTION## Doc Type'
            ],
            [
                'method' => 'GET',
                'function' => 'DeleteWiki',
                'match_key' => '',
                'log_text' => 'Delete Wiki'
            ],
            [
                'method' => 'PUT',
                'function' => 'editWiki',
                'match_key' => '',
                'log_text' => 'Edit Wiki'
            ],
            [
                'method' => 'POST',
                'function' => 'addWiki',
                'match_key' => '',
                'log_text' => 'Add Wiki'
            ],
            [
                'method' => 'POST',
                'function' => 'addWikiCategory',
                'match_key' => 'id',
                'log_text' => '##ACTION## Wiki Category'
            ],
            [
                'method' => 'POST',
                'function' => 'addUser',
                'match_key' => '',
                'log_text' => 'Add User'
            ],
            [
                'method' => 'POST',
                'function' => 'editUser',
                'match_key' => '',
                'log_text' => 'Edit User'
            ],
            [
                'method' => 'POST',
                'function' => 'updatePdfGraphic',
                'match_key' => '',
                'log_text' => 'Update PDF Graphic'
            ],
            [   // Push Notifi Graphics , Graphics Plateform
                'method' => 'POST',
                'function' => 'updateGraphicSetting',
                'match_key' => 'setting_key',
                'log_text' => 'Update Graphic Setting'
            ],
            [
                'method' => 'POST',
                'function' => 'updateEmailGraphic',
                'match_key' => '',
                'log_text' => 'Update Email Graphic'
            ],
            [
                'method' => 'POST',
                'function' => 'updateSetting',
                'match_key' => '',
                'log_text' => 'Update Setting'
            ],
            [
                'method' => 'POST',
                'function' => 'updateEmailGraphic',
                'match_key' => '',
                'log_text' => 'Update Email Graphic'
            ],
            [
                //For Email Settings & Notification Push Settings - All Pages
                'method' => 'POST',
                'function' => 'updateSetting',
                'match_key' => 'setting_key',
                'log_text' => 'Update Setting'
            ],

        ];


        $key = array_search($curr_actions['function'], array_column($list_actions, 'function'));
        if ($key !== false) {
            if (in_array($curr_actions['function'], $params_check)) {
                $match_key = $list_actions[$key]['match_key'];
                if ($curr_actions['params'][$match_key] > 0) {
                    $dataArray['action'] = str_replace(['##ACTION##'], ['Update'], $list_actions[$key]['log_text']);
                } else {
                    $dataArray['action'] = str_replace(['##ACTION##'], ['Add'], $list_actions[$key]['log_text']);
                }
            } else {
                $dataArray['action'] = $list_actions[$key]['log_text'];
            }
            $dataArray['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $dataArray['user_id'] = Auth::user()->id;
            ActionLog::create($dataArray);
        }
    }
}
