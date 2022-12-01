<?php
    
    namespace App\Rules;
    
    use App\DoodleDates;
    use Illuminate\Contracts\Validation\Rule;
    
    class MeetingDatesLimit implements Rule
    {
        /**
         * Create a new rule instance.
         *
         * @return void
         */
        private $inputData;
        private $messageKey = 'meetingDates';
        
        public function __construct($data = [])
        {
            $this->inputData = $data;
        }
        
        /**
         * Determine if the validation rule passes.
         *
         * @param string $attribute
         * @param mixed $value
         * @return bool
         */
        public function passes($attribute, $value)
        {
            if (!empty($this->inputData)) {
                if (in_array($this->inputData['meeting_type'], [2, 3]) && $this->inputData['meeting_date_type']==0) {
                    $count = json_decode($value);
                    if ($this->checkDatesvalid($count))
                        return TRUE;
                } else {
                    return TRUE;
                }
                
            } else {
                return TRUE;
            }
            
        }
        
        /**
         * Get the validation error message.
         *
         * @return string
         */
        public function message()
        {
            return trans('validation.' . $this->messageKey);
        }
        
        public function checkDatesvalid($dates)
        {
            if ((count($dates) <= config('constants.meeting_dates_limit')) === TRUE) {
                $dates = array_column($dates, 'date');
                $datesCount = DoodleDates::whereIn(\DB::raw("(DATE_FORMAT(date,'%Y/%m/%d'))"), $dates)->count();
                return TRUE;
                if ($datesCount > 0) {
                    $this->messageKey = 'meetingDatesExists';
                    return FALSE;
                }
            } else {
                $this->messageKey = 'meetingDates';
                return FALSE;
            }
        }
    }
