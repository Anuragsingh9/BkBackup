<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class AddStepRequest extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            $valid = ['required_if:step_type,4', 'regex:~
  ^(?:https?://)?                           # Optional protocol
   (?:www[.])?                              # Optional sub-domain
   (?:youtube[.]com/watch[?]v=|youtu[.]be/) # Mandatory domain name (w/ query string in .com)
   ([^&]{11})                               # Video id of 11 characters as capture group 1
    ~x'];
            $extension = (isset($this->step_type) && $this->step_type == 5) ? 'required_if:step_type,5|in:pdf,ppt,pptx,doc,docx,pdf,xls,xlsx,jpg,jpeg,png,gif' : '';

            return [
                'title'           => 'required|string|min:3|max:' . config('resilience.limit.title'),
                'description'     => 'sometimes|string|min:3|max:' . config('resilience.limit.description'),
                'step_type'       => 'required|numeric|in:2,3,4,5',
                'report_type'     => 'required_if:step_type,5|string|in:pdf,doc,xls,ppt,image',
                'active'          => 'required_if:step_type,2|numeric|in:0,1',
                'report_title'    => 'required_if:step_type,5|string|min:3|max:' . config('resilience.limit.title'),
                //'report_image'    => 'required_if:step_type,5|mimes:jpeg,png,jpg',
                'report_file'     => 'required_if:step_type,5',
                'extension'       => $extension,
//                'extension'     => ['required_if:step_type,5',Rule::in(['pdf','ppt','pptx','doc','docx','pdf','xls','xlsx'])],
                'video_title'     => 'required_if:step_type,4|string|min:3|max:' . config('resilience.limit.title'),
                'video_link'      => $valid,
                'instruction_pdf' => 'sometimes|mimes:pdf',
                'meeting_id'      => ['required_if:step_type,3', 'exists:tenant.meetings,id'],
            ];
        }

        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize()
        {
            return TRUE;
        }

        protected function failedValidation(Validator $validator)
        {
            throw new HttpResponseException(response()->json([
                'status' => FALSE,
                'msg'    => implode(',', $validator->errors()->all()),
            ], 422));
        }

        public function messages()
        {
            return [
                'meeting_id.required_if' => __('resilience::validation.meeting_id_required_if'),
            ];
        }

        /**
         * Get data to be validated from the request.
         *
         * @return array
         */
        public function validationData()
        {
            return array_merge(parent::validated(), [
                'extension' => isset($this->report_file) ? strtolower($this->report_file->getClientOriginalExtension()) : NULL,
            ]);
        }
    }
