<?php

    namespace Modules\Qualification\Http\Controllers;

    use App\Model\UserSkill;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;

    class QualificationController extends Controller
    {
        /**
         * Display a listing of the resource.
         * @return Response
         */
        public function index()
        {
            return view('qualification::index');
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
         * @return Response
         */
        public function store(Request $request)
        {
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
        public function edit()
        {
            return view('qualification::edit');
        }

        /**
         * Update the specified resource in storage.
         * @param Request $request
         * @return Response
         */
        public function update(Request $request)
        {
        }

        /**
         * Remove the specified resource from storage.
         * @return Response
         */
        public function destroy()
        {
        }

        public function getUserSkill($id)
        {
            return UserSkill::findOrFail($id);
        }
    }
