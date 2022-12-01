<?php
    
    namespace Modules\Qualification\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Validator;
    use Modules\Qualification\Entities\Vote;
    use Modules\Qualification\Entities\VoteOption;
    use DB;
    
    class VoteController extends Controller
    {
        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            try {
                //feth the list of votes
                $vote = Vote::get(['id', 'type_of_votes', 'vote_name', 'vote_short_name', 'is_sync', 'vote_description']);
                if (!$vote) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $vote], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Show the form for creating a new resource.
         * @return Response
         */
        public function create()
        {
            return view('qualification::create');
        }
        
        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @param type_of_votes
         * @param vote_name
         * @param vote_short_name
         * @param is_sync
         * @return Response
         */
        public function store(Request $request)
        {
            try {
                
                $validator = Validator::make($request->all(), [
                    'type_of_votes'   => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'vote_name'       => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'vote_short_name' => 'required|alpha_num|min:3|max:25',
                    'is_sync'         => 'required|in:0,1',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //Transaction start
                DB::beginTransaction();
                $vote = Vote::create([
                    'type_of_votes'    => 'Qualification',
                    'vote_name'        => $request->vote_name,
                    'vote_short_name'  => $request->vote_short_name,
                    'vote_description' => $request->vote_description,
                    'is_sync'          => $request->is_sync,
                ]);
                //Transaction  commit
                DB::commit();
                // return response
                return response()->json(['status' => TRUE, 'msg' => 'Record Inserted Successfully.', 'data' => $vote], 200);
            } catch (\Exception $e) {
                //Transaction rollback
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        
        /**
         * Show the specified resource.
         * @return Response
         */
        public function show()
        {
            return view('qualification::show');
        }
        
        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function edit($id)
        {
            try {
                //fetch single record for edit
                $vote = Vote::find($id);
                //check if data is empty
                if (!$vote) {
                    return response()->json(['status' => FALSE, 'msg' => 'not found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $vote], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @param type_of_votes
         * @param vote_name
         * @param vote_short_name
         * @param is_sync
         * @return Response
         */
        public function update(Request $request, $id)
        {
            try {
                
                $validator = Validator::make($request->all(), [
                    'type_of_votes'   => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'vote_name'       => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'vote_short_name' => 'required|alpha_num|min:3|max:25',
                    'is_sync'         => 'required|in:0,1',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                //Transaction start
                DB::beginTransaction();
                $vote = Vote::whereId($id)->update([
                    'type_of_votes'    => 'Qualification',
                    'vote_name'        => $request->vote_name,
                    'vote_short_name'  => $request->vote_short_name,
                    'vote_description' => $request->vote_description,
                    'is_sync'          => $request->is_sync,
                ]);
                if (!$vote) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data for update', 'data' => []], 200);
                }
                //Transaction commit
                DB::commit();
                // return response
                return response()->json(['status' => TRUE, 'msg' => 'Record Inserted Successfully.', 'data' => $vote], 200);
            } catch (\Exception $e) {
                //Transaction rollback
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroy($id)
        {
            //Transaction start
            DB::beginTransaction();
            try {
                $vote = Vote::find($id);
                //check if id exists or not
                if (!$vote) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data for delete'], 200);
                }
                //delet if exists
                $vote->delete();
                //Transaction commit
                DB::commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Deleted Successfully'], 200);
            } catch (\Exception $e) {
                //Transaction rollback
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
            
        }
        
        
        // VoteOption Api ADD UPDATE DELETE GET
        
        /**
         * Display a listing of the votes options
         * @return Response
         */
        
        public function getOption()
        {
            try {
                $voteOption = VoteOption::get(['id', 'vote_id', 'option_name', 'short_name', 'option_color', 'option_tip_text', 'short_order']);
                if (!$voteOption) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $voteOption], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getVoteOptions($id)
        {
            $voteOption = VoteOption::where('vote_id', $id)->get(['id', 'vote_id', 'option_name', 'option_color', 'short_name', 'short_order']);
            if ($voteOption->count()) {
                return response()->json(['status' => TRUE, 'data' => $voteOption]);
            }
            return response()->json(['status' => TRUE, 'msg' => 'data not found', 'data' => []], 200);
            
        }
        
        /**
         * Store a newly created resource in storage.
         * @param Request $request
         * @param vote_id  from votes table
         * @param short_name
         * @param option_color
         * @param option_tip_text
         * @return Response
         */
        public function addVoteOption(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'vote_id'         => 'required|exists:votes,id',
                    'option_name'     => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'short_name'      => 'required|alpha_num|min:3|max:25',
                    'option_color'    => 'required',
                    'option_tip_text' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => TRUE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                $blockCount = VoteOption::count() + 1;
                DB::beginTransaction();
                $VoteOption = VoteOption::create([
                    'vote_id'         => $request->vote_id,
                    'option_name'     => $request->option_name,
                    'short_name'      => $request->short_name,
                    'description'     => $request->description,
                    'option_color'    => $request->option_color,
                    'option_tip_text' => $request->option_tip_text,
                    'short_order'     => $blockCount,
                ]);
                DB::commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Added Successfully', 'data' => $VoteOption]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Show the form for editing the specified resource.
         * @return Response
         */
        public function editVoteOption($id)
        {
            try {
                $VoteOption = VoteOption::find($id);
                if (!$VoteOption) {
                    return Response()->json(['status' => FALSE, 'msg' => 'not found', 'data' => []], 200);
                }
                return response()->json(['status' => TRUE, 'data' => $VoteOption]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @param vote_id  from votes table
         * @param short_name
         * @param option_color
         * @param option_tip_text
         * @return Response
         */
        public function updateVoteOption(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'vote_id'         => 'required|exists:votes,id',
                    'option_name'     => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                    'short_name'      => 'required|alpha_num|min:3|max:25',
                    'option_color'    => 'required',
                    'option_tip_text' => 'required|regex:/[a-zA-Z0-9\s]+/|min:3|max:191',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => TRUE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
                DB::beginTransaction();
                $VoteOption = VoteOption::where('id', $id)->update([
                    'vote_id'         => $request->vote_id,
                    'option_name'     => $request->option_name,
                    'short_name'      => $request->short_name,
                    'description'     => $request->description,
                    'option_color'    => $request->option_color,
                    'option_tip_text' => $request->option_tip_text,
                ]);
                if (!$VoteOption) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data for update', 'data' => []], 200);
                }
                DB::commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Updated Successfully', 'data' => $VoteOption]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroyVoteOption($id)
        {
            
            DB::beginTransaction();
            try {
                $voteOption = VoteOption::find($id);
                if (!$voteOption) {
                    return Response()->json(['status' => FALSE, 'msg' => 'no data for delete'], 200);
                }
                $voteOption->delete();
                DB::commit();
                return response()->json(['status' => TRUE, 'msg' => 'Record Deleted Successfully'], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function testEmail($res = NULL)
        {
            $core = app(\App\Http\Controllers\CoreController::class);
            $mailData['mail']['email'] = 'opbissa@sharabh.com';
            $mailData['mail']['msg'] = 'Testing From OOi';
            $mailData['mail']['subject'] = 'Testing From OOi';
            $mail = $core->SendEmail($mailData);
            if (!$mail) {
                
                throw new \Exception('Email Server Not Working OOi');
            }
            if ($res)
                return $mail;
            else
                dd($mail);
            
        }
    }
