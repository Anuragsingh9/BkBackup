<?php
    
    namespace Modules\Crm\Http\Controllers;
    
    use App\Entity;
    use Modules\Crm\Services\CrmServices;
    use Modules\Crm\Services\DashBoard;
    use App\User;
    use DB;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Modules\Crm\Entities\Contact;
    
    class DashBoardController extends Controller
    {
        private $dashBoardService,$allowedEntities;
        
        public function __construct()
        {
            $this->dashBoardService = DashBoard::getInstance();
            $this->allowedEntities=CrmServices::allowedEntities();
        }
        
        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            return view('crm::index');
        }
        
        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('crm::create');
        }
        
        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @return Response
         */
        public function store(Request $request)
        {
            //
        }
        
        /**
         * Show the specified resource.
         * @param int $id
         * @return Response
         */
        public function show($id)
        {
            return view('crm::show');
        }
        
        /**
         * Show the form for editing the specified resource.
         * @param int $id
         * @return Response
         */
        public function edit($id)
        {
            return view('crm::edit');
        }
        
        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @param int $id
         * @return Response
         */
        public function update(Request $request, $id)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         * @param int $id
         * @return Response
         */
        public function destroy($id)
        {
            //
        }
        
        public function getSearch($keyword)
        {
            try {
                $result = [];
//            $keyword=strtolower($keyword);
                $keyword = ltrim($keyword);
                $keyword = rtrim($keyword);
                if (!empty($keyword) && strlen($keyword) >= 3) {
                    
                    //getting search results from Users table
                    DB::connection('tenant');
                    $users = User::where(function ($a) {
                        $a->where('sub_role', '!=', 'C1');
                        $a->orWhereNull('sub_role');
                    })->where(function ($query) use ($keyword) {
//$query->orWhere('fname', 'like', '%' . $keyword . '%')
//->orWhere('lname', 'like', '%' . $keyword . '%')
//->orWhereRaw("CONCAT(fname,' ',lname) like '%$keyword%'")
                        $query->orWhereRaw("LOWER(fname) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(lname) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(CONCAT(fname,' ',lname)) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(email) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci");
                    })->select(DB::raw("CONCAT(fname,' ',lname) AS name,email,id"))->get(['id', 'fname', 'lname', 'email']);
                    //getting search results from Contact table
                    $contacts = Contact::where(function ($query) use ($keyword) {
                        $query->orWhereRaw("LOWER(fname) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(lname) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(CONCAT(fname,' ',lname)) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(email) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci");
                    })->select(DB::raw("CONCAT(fname,' ',lname) AS name,email,id"))->get(['id', 'fname', 'lname', 'email']);
                    //getting search results from Entities table
                  
                    $entities = Entity::whereIn('entity_type_id', $this->allowedEntities)->where(function ($query) use ($keyword) {
                        $query->orWhereRaw("LOWER(long_name) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(short_name) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci")
                            ->orWhereRaw("LOWER(email) like  LOWER('%$keyword%') COLLATE utf8mb4_unicode_ci");
                    })->whereNotNull('long_name')->select(DB::raw("CONCAT(COALESCE(`long_name`,''),' ',COALESCE(`short_name`,'')) AS name,email,id ,entity_type_id,entity_sub_type "))->get();
                    
                    //getting combine result and adding type on results
                    $result = $this->dashBoardService->getSearch($users, $contacts, $entities);
                } else {
                    $data['data'] = [];
                    $data['status'] = [
                        'user'           => 0, 'contact' => 0,
                        'company'        => 0,
                        'instance'       => 0,
                        'union'          => 0,
                        'union_internal' => 0,
                        'union_external' => 0,
                        'press'          => 0,
                    ];
                    $result = $data;
                }
                
                return response()->json([
                    'status' => TRUE,
                    'data'   => $result,
                ], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'data' => $e->getMessage()], 500);
            }
        }
    }
