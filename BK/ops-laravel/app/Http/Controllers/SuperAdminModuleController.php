<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Resources\ModuleCollection;
    use App\Model\OpsModule;
    use App\Rules\FrenchName;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    
    /**
     * Class SuperAdminModuleController
     * @package App\Http\Controllers
     */
    class SuperAdminModuleController extends Controller
    {
        /**
         * SuperAdminModuleController constructor.
         */
        public function __construct()
        {
        }
        
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $modules = OpsModule::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'label_' . 'en', 'tooltip_' . 'en']);
            return view('super_admin.modulelist', compact('modules'));
        }
        
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            $modules = OpsModule::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'label_' . 'en', 'tooltip_' . 'en']);
            return view('super_admin.addModule', compact('modules'));
        }
        
        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            if (isset($request->id) && !empty($request->id)) {
                $rules = [
                    'label_en'   => ['required', 'min:3', 'max:255', new FrenchName],
                    'label_fr'   => ['required', 'min:3', 'max:255', new FrenchName],
                    'tooltip_en' => ['required', 'min:3', 'max:255', new FrenchName],
                    'tooltip_fr' => ['required', 'min:3', 'max:255', new FrenchName],
                ];
                $errors = $this->validate($request, $rules);
                $data = OpsModule::where('id', $request->id)->update([
                    'label_en'   => $request->label_en,
                    'label_fr'   => $request->label_fr,
                    'tooltip_en' => $request->tooltip_en,
                    'tooltip_fr' => $request->tooltip_fr,
                ]);
            } else {
                $rules = [
                    'label_en'   => ['required', 'min:3', 'max:255', new FrenchName],
                    'label_fr'   => ['required', 'min:3', 'max:255', new FrenchName],
                    'tooltip_en' => ['required', 'min:3', 'max:255', new FrenchName],
                    'tooltip_fr' => ['required', 'min:3', 'max:255', new FrenchName],
                ];
                $errors = $this->validate($request, $rules);
                $count = OpsModule::count() + 1;
                $data = OpsModule::create([
                    'label_en'   => $request->label_en,
                    'label_fr'   => $request->label_fr,
                    'tooltip_en' => $request->tooltip_en,
                    'tooltip_fr' => $request->tooltip_fr,
                    'sort_order' => $count,
                ]);
            }
            return redirect()->route('add-module-list');
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
            try {
                $user = OpsModule::findOrFail($id);
                $modules = OpsModule::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['id', 'label_' . 'en', 'tooltip_' . 'en']);
                return view('super_admin.addModule', compact('user','modules'));
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
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
            (OpsModule::where('id', $id)->delete());
            return redirect()->route('add-module-list');
        }
        
        /**
         * @return ModuleCollection
         */
        public function getModules()
        {
            $lang = App::getLocale();
            $data = OpsModule::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get(['label_' . $lang . ' as label', 'tooltip_' . $lang . ' as tooltip']);
            return new ModuleCollection($data);
        }
        
        
        /**
         * @param Request $request
         * @return array
         * this will update the sort order of modules
         */
        public function updateOrder(Request $request)
        {
            if ($request->has('ids')) {
                $arr = explode(',', $request->input('ids'));
                $arr = collect($arr)->filter()->toArray();
               
                foreach ($arr as $sortOrder => $id) {
                    $menu = OpsModule::find($id);
                    $menu->sort_order = $sortOrder;
                    $menu->save();
                }
                return ['success' => TRUE, 'message' => 'Updated'];
            }
        }
    }
