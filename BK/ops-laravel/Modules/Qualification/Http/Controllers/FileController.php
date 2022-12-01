<?php

namespace Modules\Qualification\Http\Controllers;

use App\Exports\ClientsExport;
use App\Exports\ProspectsExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Qualification\Entities\QualificationClients;
use Validator;
use Modules\Qualification\Entities\Vote;
use DB;
use Carbon\Carbon;
class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

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
     * @param  Request $request
     * @param type_of_votes
     * @param vote_name
     * @param vote_short_name
     * @param is_sync
     * @return Response
     */
    public function store(Request $request)
    {

    }


    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($wid=0, $case = 2)
    {
        try {
            $today = Carbon::today()->format('Ymd');
            if ($case == 2) {
                $fileName=(userLang()=='FR')?'Fichier-adherents':'Members-file';
                return (new ClientsExport)->forWid($wid)->download($fileName.$today.'.xlsx');
            } elseif ($case == 3 || $case == 4) {
                $fileName=(userLang()=='FR')?'Fichier-prospects':'Prospects-file';
                return (new ProspectsExport)->forWid($wid, $case)->download($fileName.$today.'.xlsx');
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param type_of_votes
     * @param vote_name
     * @param vote_short_name
     * @param is_sync
     * @return Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {

    }


}
