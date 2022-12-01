<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Illuminate\Support\Facades\Auth;
    use Modules\Resilience\Entities\ConsultationAnswer;

    class ConsultationQuestionTransformer extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {
            return [
                "id"                       => $this->id,
                "question"                 => $this->question,
                "description"              => $this->description,
                "comment"                  => $this->comment,
                "question_type"            => $this->consultationQuestionType->question_type,
                "question_type_id"         => $this->consultationQuestionType->id,
                "allow_user_to_add_answer" => $this->allow_add_other_answers,
                "is_mandatory"             => $this->is_mandatory,
                "displayFriendRequest"     => $this->displayFriendRequest,
                "order"                    => $this->order,
                "options"                  => json_decode($this->options),
                "user_answer_id"           => $this->answerId(),
                "user_answer"              => $this->answered()
//            "consultation_question_type"    => new ConsultationQuestionTypeTransformer($this->consultationQuestionType),
            ];
        }

        public function answerId()
        {
            $answer = ConsultationAnswer::where('consultation_question_id', $this->id)->where('user_id', Auth::id())->first();
            if (!$answer) {
                return NULL;
            }
            return $answer->id;
        }

        public function answered()
        {
            $answer = ConsultationAnswer::where('consultation_question_id', $this->id)->where('user_id', Auth::id())->first();
            if (!$answer) {
                return NULL;
            }
            return [
                "answer"        => $this->isJson($answer->answer) ? json_decode($answer->answer) : $answer->answer,
                "manual_answer" => $this->isJson($answer->manual_answer) ? json_decode($answer->manual_answer) : $answer->manual_answer,
            ];
        }

        public function isJson($string)
        {
            return (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))) ? TRUE : FALSE;
        }
    }
