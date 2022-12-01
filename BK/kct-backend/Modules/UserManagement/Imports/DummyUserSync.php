<?php

namespace Modules\UserManagement\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DummyUserSync implements ToCollection {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To return the dummy users collection
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $collection
     * @return Collection
     */
    public function collection(Collection $collection): Collection {
        return $collection;
    }
}
