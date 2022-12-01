<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class UpdateAssetRequest extends FormRequest
    {

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            if ($this->hasFile('link')) {
                $valid = 'mimes:pdf';
            } else {
                $valid = ['regex:~
  ^(?:https?://)?                           # Optional protocol
   (?:www[.])?                              # Optional sub-domain
   (?:youtube[.]com/watch[?]v=|youtu[.]be/) # Mandatory domain name (w/ query string in .com)
   ([^&]{11})                               # Video id of 11 characters as capture group 1
    ~x'];
            }
            return [
                'step_type'            => 'required|',
                'consultation_step_id' => 'required|exists:tenant.consultation_steps,id',
                'title'                => 'required|string|max:100',
                'image'                => 'sometimes|image|mimes:jpeg,png,jpg,',
                'link'                 => $valid,
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
    }
