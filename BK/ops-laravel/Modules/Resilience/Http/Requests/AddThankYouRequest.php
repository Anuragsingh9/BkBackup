<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class AddThankYouRequest extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
//            $regex = '/^(?:https?:\/\/)?(?:www\.)?([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
            //|regex: . $regex .
             $regex = '/^(https?:\/\/)?((([a-z\\\\d]([a-z\\\\d-]*[a-z\\\\d])*)\\\\.)+[a-z]{2,}|((\\\\d{1,3}\\\\.){3}\\\\d{1,3}))(\\\\:\\\\d+)?(\\\\[-a-z\\\\d%_.~+]*)*(\\\\?[;&a-z\\\\d%_.~+=-]*)?(\\\\#[-a-z\\\\d_]*)?$/';
            return [
                'title'              => 'required|string|min:3|max:' . config('resilience.limit.title'),
                'title_text'         => 'required|string|min:3|max:' . config('resilience.limit.reinvent_footer'),
                'is_redirection'     => 'required|numeric|in:0,1',
                'step_type'          => 'required|numeric|in:6',
                'redirect_url'       => 'required_if:is_redirection,1|string|max:' . config('resilience.limit.url'),
                'redirect_url_label' => 'required_if:is_redirection,1|string|max:' . config('resilience.limit.title'),
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
    }
