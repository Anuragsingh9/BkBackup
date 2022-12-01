<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EventMemberExport implements FromCollection, WithHeadings, ShouldAutoSize {
    protected $userData, $headerRow;
    
    public function __construct($userData, $headerRow = null) {
        $this->userData = $userData;
        $this->headerRow = $headerRow;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return $this->userData;
    }
    
    
    public function headings(): array {
        return $this->headerRow;
    }
}
