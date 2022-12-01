<?php

namespace App\Http\Controllers\ImproveMentFour;

use App\Http\Controllers\Controller;
use App\Model\LabelCustomization;
use Illuminate\Http\Request;
use Validator;

class LabelCustomizationController extends Controller
{

    public function index()
    {
        $labels = LabelCustomization::all();
        return response()->json([
            'status' => true,
            'data' => $labels
        ], 200);
    }

    public function updateLabel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'custom_en' => 'required',
                'custom_fr' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);//validation false return errors
            }

            $data = LabelCustomization::where('id', $request->id)->update(['id' => $request->id, 'custom_en' => $request->custom_en, 'custom_fr' => $request->custom_fr, 'on_off' => $request->on_off]);

            if ($data) {
                $labels = LabelCustomization::all();
                return response()->json(['status' => true, 'data' => $labels], 200);
            } else {
                return response()->json(['status' => false, 'data' => $data], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }
}
