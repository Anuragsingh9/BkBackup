<?php

namespace App\Http\Requests;

use App\Rules\FrEn;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkAccCreateRequest extends FormRequest {
    
    protected function validationData() {
        $data = parent::validationData();
        // this will modify the acc name with suffix but this will not remain in controller,
        // i.e. in controller its actual value will present
        if (isset($data['accName'])) {
            $data['accName'] = $this->input('accName') . '.' . env('HOST_SUFFIX');
        }
        return $data;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return session()->has('superadmin');
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'orgFname' => ['required', new FrEn],
            'orgLname' => ['required', new FrEn],
            'orgEmail' => 'required|email',
            'orgName'  => 'required|unique:organisation,name_org',
            // custom added key for the validation of unique account name
            'accName'  => 'required|string|unique:hostnames,fqdn',
        ];
    }
    
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode($validator->errors()->all(), ','),
            'errors' => $validator->errors(),
        ], 422));
    }
}
