<?php
    
    namespace App\Http\Controllers\Universal;
    
    use App\Imports\ContactsImport;
    use App\Model\Skill;
    use App\Model\SkillTabs;
    use App\Services\ImportServices;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Validation\Rule;
    use Maatwebsite\Excel\Facades\Excel;
    use Maatwebsite\Excel\HeadingRowImport;
    use Modules\Newsletter\Entities\Contact;
    use Validator;
    use App\Exports\ListsExport;
    
    class ListImportController extends Controller
    {
        
        /**
         * SuperAdminSingleton constructor.
         */
        private $importService;
        
        public function __construct()
        {
            $this->importService = ImportServices::getInstance();
        }
        
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            //
        }
        
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
        
        }
        
        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $validator = Validator::make([
                'file'      => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
                'type'      => $request->type,
            
            ],
                [
                    'file'      => 'required|file|max:1024',
                    'extension' => ['required', Rule::in(['xlsx', 'xls', 'csv'])],
                    'type'      => 'required|between:0,4',
//                'list_id' => 'sometimes|required|exists:lists,id',
                ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $file = $request->file('file');
            $fileData = $this->addFileAsType($file, $request->type);

//        $headings = (new HeadingRowImport)->toArray(public_path() . 'public/temp_uploads/1563431650Sample file of user (1).xls');

//        $data['fields'] = isset($fileData[0][0][0]) ? $fileData[0][0][0] : $fileData[0];
            $data['fields'] = isset($fileData[0][0][0]) ? collect($fileData[0][0][0])->filter()->toArray() : $fileData[0];
            $data['static'] = $this->importService->getFillable($request->type);
            $data['file_name'] = $fileData[1];
            return response()->json(['status' => TRUE, 'data' => $data]);
        }
        
        public function addFileAsType($file, $type)
        {
            switch ($type) {
                case 0:
                    $folderName = 'user';
                    break;
                case 1:
                    $folderName = 'contact';
                    break;
                case 2:
                    $folderName = 'company';
                    break;
                case 3:
                    $folderName = 'instance';
                    break;
                case 4:
                    $folderName = 'union';
                    break;
                case 5:
                    $folderName = 'press';
                    break;
                default:
                    $folderName = 'contact';
                    break;
            }
            
            $fileName = time() . $file->getClientOriginalName();
            $path = public_path() . 'public/temp_uploads/' . Auth::user()->id . "/$folderName/";
//        $this->importService->removeExcel($path);
            
            $file->move($path, $fileName);
            $headings = (new HeadingRowImport)->toArray(public_path() . 'public/temp_uploads/' . Auth::user()->id . "/$folderName/" . $fileName);
            return [$headings, $fileName];
        }
        
        /**
         * Display the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function show($id)
        {
            //
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function edit($id)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroy($id)
        {
            //
        }
        
        public function importStepTwo(Request $request)
        {
            try {
                //VALIDATION GOES HERE
                $validator = Validator::make($request->all(), [
                    'file_name' => 'required',
                    'type'      => 'required',
                    'key'       => 'required',
//                'listId' => 'sometimes|required|exists:lists,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $finalImport = $this->importService->addAsType(['file_name' => $request->file_name, 'type' => $request->type, 'key' => $request->key, 'listId' => (isset($request->listId) ? $request->listId : 0), 'step' => 2]);
                return response()->json(['status' => TRUE, 'data' => $finalImport->getData()], 200);
                
            } catch (\Exception $e) {
                
                $failures = $e;
                $error = [];
                $this->importService->addErros($failures, $error, $request->type);
//dd($e);
                return response()->json(['status' => FALSE, 'msg' => (!empty($error) ? $error : $e->getMessage())], (!empty($error) ? 200 : 500));
            }
        }
        
        public function importStepThree(Request $request)
        {
            try {
                //VALIDATION GOES HERE
                $validator = Validator::make($request->all(), [
                    'file_name' => 'required',
                    'type'      => 'required',
                    'key'       => 'required',
//                'listId' => 'sometimes|required|exists:lists,id',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                
                $finalImport = $this->importService->addAsType(['file_name' => $request->file_name, 'type' => $request->type, 'key' => $request->key, 'listId' => (isset($request->listId) ? $request->listId : 0), 'step' => 3]);
                return response()->json(['status' => TRUE, 'data' => ['created' => $finalImport->getCreated(), 'updated' => $finalImport->getUpdated()]], 200);
            } catch (\Exception $e) {
                
                $failures = $e;
                $error = [];
                $this->importService->addErros($failures, $error, $request->type);
                
                return response()->json(['status' => FALSE, 'msg' => (!empty($error) ? $error : $e->getMessage())], (!empty($error) ? 200 : 500));
            }
            
            
        }
        
        /**
         * @param array $models
         * @param string $key
         * @return array
         */
        public function getCustomFillable($keyword, $type = 0)
        {
            if (!empty($keyword) && strlen($keyword) >= 3) {
                $keyword = ltrim($keyword);
                $keyword = rtrim($keyword);
                $skillsTabs = SkillTabs::where('tab_type', $type)->pluck('id');
                $tabsArray = Skill::whereIn('skill_tab_id', $skillsTabs)->where(function ($query) use ($keyword) {
                    $query->orWhere(\DB::raw("LOWER(short_name)"), 'like', '%' . $keyword . '%')
                        ->orWhere(\DB::raw("LOWER(name)"), 'like', '%' . $keyword . '%');
                })->get(['id', 'short_name', 'name', 'skill_format_id']);
                return response()->json(['status' => TRUE, 'data' => $tabsArray], 200);
            }
        }
        
        public function export(Request $request)
        {
            // $newsletter_list = ListModel::with('newsletter_contacts', 'users')->find($request->listId,['id', 'name', 'description', 'type', 'typology_id']);
            // dd($newsletter_list);
            return Excel::download(new ListsExport($request->listId), 'List.xls');
        }
        
        public function getTemplateDownload()
        {
            try {
                $lang = App::getLocale();
                if ($lang == 'fr')
                    $filename = public_path('public/templates/Importation-de-liste-FR.xlsx');
                else
                    $filename = public_path('public/templates/Importation-de-liste-EN.xlsx');
                
                return response()->download($filename);
            } catch (\Exception $e) {
                return abort(404);
            }
        }
    }
