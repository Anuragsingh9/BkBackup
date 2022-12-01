<?php

namespace Modules\Resilience\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Modules\Resilience\Exports\Sheets\ExcelPerQuestionSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnswerExport implements WithMultipleSheets {

    use Exportable;
    protected $questions;
    public function __construct($questions) {
        $this->questions = $questions;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->questions as $question) {
            $sheets[] = new ExcelPerQuestionSheet($question);
        }
        return $sheets;
    }
}
