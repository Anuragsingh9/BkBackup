<?php

    namespace App\Http\Controllers\Project;

    use App\Model\ActivityType;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Validator;

    class ActivityTypeController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
        }

        public function index()
        {
            $activities = ActivityType::all(['id', 'fr_name', 'en_name', 'svg']);
            return response()->json([
                'status' => TRUE,
                'data'   => $activities,
            ], 200);
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create(Request $request)
        {
            //this function check that we have good svg file or not
            if (!$this->checkSvgFile($request)) {
                return response()->json(['status' => FALSE, 'msg' => __('message.svg')]);
            }

            $svg = '';
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/uploads/svg';
            $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
            $url = $this->core->getS3Parameter($filename);
//        $data = json_decode(file_get_contents('http://api.rest7.com/v1/raster_to_vector.php?url=' . $url . '&format=svg'));
            $svg = file_get_contents($url);
            $activity = ActivityType::insert(['fr_name' => $request->frname, 'en_name' => $request->enname, 'svg' => $svg]);
            if ($activity) {
                $activity_type = ActivityType::all(['id', 'fr_name', 'en_name', 'svg']);
                return response()->json([
                    'status' => TRUE,
                    'data'   => $activity_type,
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => $activity,
                ], 200);
            }
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {

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
        public function update(Request $request)
        {

            $svg = '';
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $folder = $domain . '/uploads/svg';
            if ($request->file('image') != NULL) {
                //this function check that we have good svg file or not
                if (!$this->checkSvgFile($request)) {
                    return response()->json(['status' => FALSE, 'msg' => __('message.svg')]);
                }
                $filename = $this->core->fileUploadByS3($request->file('image'), $folder, 'public');
                $url = $this->core->getS3Parameter($filename);
                /*
                 $data = json_decode(file_get_contents('http://api.rest7.com/v1/raster_to_vector.php?url=' . $url . '&format=svg'));
                 if (@$data->success !== 1)
                 {
                     die('Failed');
                 }*/
                $svg = file_get_contents($url);
                $updateData = ['fr_name' => $request->frname, 'en_name' => $request->enname, 'svg' => $svg];
            } else {
                $updateData = ['fr_name' => $request->frname, 'en_name' => $request->enname];
            }
            $activity = ActivityType::where('id', $request->id)->update($updateData);
            if ($activity) {
                $activity_type = ActivityType::all(['id', 'fr_name', 'en_name', 'svg']);
                return response()->json([
                    'status' => TRUE,
                    'data'   => $activity_type,
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => $activity,
                ], 200);
            }
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

        /**
         * @param $request
         * @return bool
         */
        protected function checkSvgFile($request)
        {
            //checking that we have file and its not empty
            //if not then checking that file have good mimeType and extension
            if ($request->hasFile('image') && !empty($request->file('image'))) {
                return (in_array($request->file('image')->getMimeType(),['image/svg+xml','image/svg']) && strtolower($request->file('image')->getClientOriginalExtension())=='svg');
            } else {
                return FALSE;
            }

        }

    }
