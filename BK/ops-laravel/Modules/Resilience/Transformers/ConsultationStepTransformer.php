<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Modules\Resilience\Entities\ConsultationStepMeeting;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use phpDocumentor\Reflection\Types\Null_;

    class ConsultationStepTransformer extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {
            $meeting = NULL;
            if ($this->step_type == 3) {
                $meeting = ConsultationStepMeeting::with('meeting')->where('consultation_step_id', $this->id)->first();
            }
            if ($this->step_type == 5)
                $decode = $this->getPrivateUrl();
            else
                $decode = json_decode($this->extra_fields, TRUE);

            $stepAnswered = 0;
            if (isset($this->additional['showAnswerCountData']) && ($this->additional['showAnswerCountData']) && $this->step_type == 2) {
                $stepAnswered = ConsultationAnswer::whereIn('consultation_question_id', $this->consultationQuestion->pluck('id'))->count();
            }
            return [
                "id"                     => $this->id,
                "consultation_sprint_id" => $this->consultation_sprint_id,
                "title"                  => $this->title,
                "title_text"             => $this->title_text,
                "description"            => $this->description,
                "image"                  => $this->image,
                "step_type"              => $this->step_type,
                "active"                 => $this->active,
                "answerable"             => $this->answerable,
                "asset"                  => $decode,
                "answerCount"            => $stepAnswered,
                $this->mergeWhen((isset($this->additional['showStepData']) && ($this->additional['showStepData'])), [
                    "consultation_question" => ConsultationQuestionTransformer::collection($this->consultationQuestion),
                ]),
                "meeting"                => (!empty($meeting)) ? $meeting->meeting : NULL,

            ];
        }

        protected function getPrivateUrl()
        {
            if (!empty($this->extra_fields)) {
                $core = app(\App\Http\Controllers\CoreController::class);
                $decode = json_decode($this->extra_fields, TRUE);
                $decode['link'] = (isset($decode['link']) && !empty($decode['link'])) ? $core->getPrivateAsset($decode['link'], 60) : NULL;
                return $decode;
            } else {
                return NULL;
            }

        }

    }
