<?php

namespace Modules\Resilience\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ConsultationStepAssetTransformer extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            "id"                    => $this->id,
            "consultation_step_id"  => $this->consultation_step_id,
            "title"                 => $this->title,
            "info_type"             => $this->info_type,
            "media_link"            => $this->media_link,
            "pdf"                   => $this->pdf,
            "allow_download_pdf"    => $this->allow_download_pdf
        ];
    }

}
