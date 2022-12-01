<?php
    
    namespace App\Http\Requests;
    
    use App\Rules\MeetingDatesLimit;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Contracts\Validation\Validator;
    
    class MeetingStoreRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize()
        {
            return TRUE;
        }
        
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                'name'              => 'required|string|max:255',
                'description'       => 'nullable|string|max:1000',
                'place'             => 'required|string|max:255',
                'image'             => 'nullable|image',
                'date'              => 'required|date|after:yesterday',
                'start_time'        => 'required',
                'end_time'          => 'required',   // todo after start
                'meeting_date_type' => 'nullable|integer|in:0,1',
                'meeting_type'      => 'required|integer|in:1,2,3',
                'workshop_id'       => 'required|',//exists:tenant.workshops,id
                'user_id'           => 'required|exists:tenant.users,id',
                'visibility'        => 'nullable|integer',
                'status'            => 'nullable|in:0,1',
                'is_offline'        => 'nullable|integer',
                'meetingDates'      => ['required_if:meeting_date_type,0', new MeetingDatesLimit($this->toArray())],
            ];
        }
        
        
    }
