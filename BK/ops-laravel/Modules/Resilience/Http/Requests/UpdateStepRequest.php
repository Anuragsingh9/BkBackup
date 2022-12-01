<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class UpdateStepRequest extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            $valid = ['sometimes', 'regex:~
  ^(?:https?://)?                           # Optional protocol
   (?:www[.])?                              # Optional sub-domain
   (?:youtube[.]com/watch[?]v=|youtu[.]be/) # Mandatory domain name (w/ query string in .com)
   ([^&]{11})                               # Video id of 11 characters as capture group 1
    ~x'];
            $extension = (isset($this->report_type)) ? 'sometimes|in:pdf,ppt,pptx,doc,docx,pdf,xls,xlsx,jpg,jpeg,png,gif' : '';

            return [
                'title'           => 'sometimes|string|min:3|max:' . config('resilience.limit.title'),
                'description'     => 'sometimes|string|min:3|max:' . config('resilience.limit.description'),
                'step_type'       => 'required|numeric|in:2,3,4,5',
                'report_type'     => 'sometimes|string|in:pdf,doc,xls,ppt,image',
                'active'          => 'sometimes|numeric|in:0,1',
                'answerable'      => 'sometimes|numeric|in:0,1',
                'report_title'    => 'sometimes|string|max:' . config('resilience.limit.title'),
                //'report_image'    => 'sometimes|mimes:jpeg,png,jpg',
                'report_file'     => 'sometimes',
                'extension'       => $extension,
                'video_title'     => 'sometimes|string|max:' . config('resilience.limit.title'),
                'video_link'      => $valid,
                'instruction_pdf' => 'sometimes|mimes:pdf',
                'meeting_id'      => ['sometimes', 'exists:tenant.meetings,id'],
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
