<?php
    
    namespace App\Http\Resources;
    
    use Illuminate\Http\Resources\Json\Resource;
    
    class MeetingResource extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            if ($this->isUpdate) {
                return [
                    'date'       => isset($this->d_date) ? $this->d_date : $this->date,
                    'start_time' => isset($this->d_start_time) ? $this->d_start_time : $this->start_time,
                    'end_time'   => isset($this->d_end_time) ? $this->d_end_time : $this->end_time,
                ];
            }
            return [
                'id'            => $this->id,
                'name'          => $this->name,
                'place'         => $this->place,
                'date'          => $this->date,
                'start_time'    => $this->start_time,
                'end_time'      => $this->end_time,
                'workshop_name' => ((isset($this->workshop->workshop_name)) ? $this->workshop->workshop_name : ''),
            ];
            
        }
    }
