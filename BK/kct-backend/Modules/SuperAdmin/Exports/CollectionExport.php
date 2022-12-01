<?php

namespace Modules\SuperAdmin\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionExport implements FromCollection, WithHeadings {
    private Collection $data;
    private ?array $header;

    public function __construct(Collection $data, array $header = null) {
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection {
        return $this->data;
    }

    public function headings(): array {
        return $this->header;
    }

}
