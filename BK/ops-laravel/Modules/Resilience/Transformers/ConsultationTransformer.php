<?php

    namespace Modules\Resilience\Transformers;

    use App\Setting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Modules\Resilience\Entities\ConsultationSprint;


    class ConsultationTransformer extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {
            $consultationSprint = [];
            if (isset($this->additional['showStep']) && $this->additional['showStep']) {
                $consultationSprint = $this->consultationSprint->transform(function (ConsultationSprint $consultationSprint) {
                    return (new ConsultationSprintTransformer($consultationSprint))->additional($this->additional);
                });
            }

            if (isset($this->additional['dateFormat']) && $this->additional['dateFormat']) {
                $this->start_date = date('d/m/Y', strtotime(str_replace('-', '/', $this->start_date)));
                $this->end_date = date('d/m/Y', strtotime(str_replace('-', '/', $this->end_date)));
                $this->display_results_until = date('d/m/Y', strtotime(str_replace('-', '/', $this->display_results_until)));
                $originalStart = $this->start_date;
                $originalEnd = $this->end_date;
                $originalDisplay = $this->display_results_until;
            } else {
                $originalStart = $this->start_date;
                $originalEnd = $this->end_date;
                $originalDisplay = $this->display_results_until;
                $this->start_date = getConsultationFormatAttribute($this->start_date);
                $this->created_at = getConsultationFormatAttribute($this->created_at);
                $this->end_date = getConsultationFormatAttribute($this->end_date);
                $this->display_results_until = getConsultationFormatAttribute($this->display_results_until);
            }


            return [
                "uuid"                           => $this->uuid,
//                "user_id"               => $this->user_id,
                "workshop_id"                    => $this->workshop_id,
                "name"                           => $this->name,
                "internal_name"                  => $this->internal_name,
                "long_name"                      => $this->long_name,
                "created_at"                     => $this->created_at,
                "start_date"                     => $this->start_date,
                "original_start_date"            => $originalStart,
                "original_end_date"              => $originalEnd,
                "original_display_results_until" => $originalDisplay,
                "end_date"                       => $this->end_date,
                "display_results_until"          => $this->display_results_until,
                "is_reinvent"                    => $this->is_reinvent,
                "public_reinvent"                => $this->public_reinvent,
                "allow_to_go_back"               => $this->allow_to_go_back,
                "member_count"                   => collect($this->workshop->meta)->whereNotIn('role', [3, 4])->unique('user_id')->count(),
                "answer_count"                   => $this->consultationAnsweredUser->count(),
                "consultation_sprint"            => $consultationSprint,
                $this->mergeWhen((isset($this->additional['showReinvent']) && ($this->additional['showReinvent'])), [
                    "reinvent_page" => $this->is_reinvent ? $this->reinventPage() : NULL,

                ]),
            ];
        }

        public function reinventPage()
        {

            $setting = Setting::where('setting_key', "reinvent_page")->first();
            if (!$setting) {
                return NULL;
            }
            return json_decode($setting->setting_value);
        }
    }
