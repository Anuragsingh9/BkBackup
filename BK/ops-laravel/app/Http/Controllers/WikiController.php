<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use Auth;
use App\Wiki;
use App\WikiEditor;
use App\WikiCategory;
use App\WorkshopMeta;
use App\User;

class WikiController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    public function addWiki(Request $request)
    {
        $wiki = Wiki::insert($request->except(['']));
        return response()->json($wiki);
    }
    public function getWikiList(Request $request)
    {
        $data = Wiki::all();
        return response()->json($data);
    }
    public function getWikiAdmin(Request $request)
    {
        $data = User::with('wiki')->where('role_wiki', 1)->get();
        return response()->json($data);
    }
    public function getWiki()
    {   
        $data['wiki']=[];
  
        $wikiData=Wiki::where('added_by', Auth::user()->id)->orderBy('created_at','DESC');
        $wikiEditorData=WikiEditor::with('wiki')->where('editor_id', Auth::user()->id)->get();
       if ($wikiData->count() >0) {
            $data['wiki']=$wikiData->get()->toArray();
        }

        if (count($wikiEditorData)>0) {
            foreach ($wikiEditorData as $val) {
                
                $data['wiki'][]=[
                    'id'=>$val->wiki->id,
                    'wiki_name'=>$val->wiki->wiki_name,
                    'created_at'=>dateConvert($val->wiki->created_at, 'Y-m-d H:i:s'),
                    'status'=>$val->wiki->status,
                    'type'=>'Wiki Admin',
                    ];
            }
        }
        return response()->json($data);
    }

    public function editWiki(Request $request, $id)
    {
        return response()->json(Wiki::where('id', $request->id)->update($request->except(['_method'])));
    }

    public function updateStatus(Request $request)
    {
        $data  = Wiki::where('id', $request->id)->update(array('status'=>$request->status));
        return response()->json($data);
    }

    public function inviteEditor(Request $request)
    {
        $data=[];
        if ($request->commission_id) {
            $members = WorkshopMeta::where('workshop_id', $request->commission_id);
            if ($members->count() > 0) {
                $memList = $members->get();
                foreach ($memList as $val) {
                    WikiEditor::firstOrCreate(['editor_id'=>$val->user_id,'added_by'=>$request->added_by,'wiki_id'=>$request->wiki_id]);
                }
                $data=WikiEditor::with('user', 'wiki')->where('wiki_id', $request->wiki_id)->get();
            }
        } else {
            WikiEditor::updateOrCreate(['wiki_id'=>$request->wiki_id,'editor_id'=>$request->editor_id], ['editor_id'=>$request->editor_id,'added_by'=>$request->added_by,'wiki_id'=>$request->wiki_id]);
            $data=WikiEditor::with('user', 'wiki')->where('wiki_id', $request->wiki_id)->get();
        }
        return response()->json($data);
    }
    public function addWikiCategory(Request $request)
    {
        $newRec = WikiCategory::updateOrCreate(['id'=>$request->id], ['category_name'=>$request->category_name,'category_desc'=>$request->category_desc]);
        return response()->json($newRec);
    }
    
    public function getWikiCategory(Request $request)
    {   
        if($request->id>0){
            $data= DB::connection('tenant')->select('SELECT w.* ,(SELECT count(*) FROM wikis WHERE find_in_set(w.id,wiki_category_id) AND id='.$request->id.') as is_checked FROM wiki_categories as w');
        } else {
            $data= DB::connection('tenant')->select('SELECT w.* ,(SELECT count(*) FROM wikis WHERE find_in_set(w.id,wiki_category_id)) as count FROM wiki_categories as w');
        }
        
        return response()->json($data);
    }
    public function DeleteWikiCategory($id)
    {
        $res=0;
        if (WikiCategory::where('id', $id)->delete()) {
            $res = 1;
        }
        return response()->json($res);
    }
    public function getWikiEditor(Request $request)
    {
        $data=WikiEditor::with('user', 'wiki')->where('wiki_id', $request->wiki_id)->get();
        return response()->json($data);
    }
    public function DeleteEditor($id)
    {
        $res = WikiEditor::where('id', $id)->delete();
        return response()->json($res);
    }
    public function DeleteWiki($id)
    {
        $res = Wiki::where('id', $id)->delete();
        return response()->json($res);
    }
    public function getWikiById($id)
    {
        $data  = Wiki::where('id', $id)->first();
        return response()->json($data);
    }
    public function getWikiPrivilege($wid)
    {

        $wikiStatus = Wiki::where('status', 1)->where('id', $wid)->count();
         if( Wiki::where('added_by', Auth::user()->id)->where('status', 1)->where('id', $wid)->count()>0 && $wikiStatus>0 ) {
            $data = 1;
         } else if( WikiEditor::where('editor_id', Auth::user()->id)->where('wiki_id', $wid)->count()>0 && $wikiStatus>0 ){
            $data = 2;
         } else {
            $data = 0;
         }
        return response()->json($data);
    }
}
