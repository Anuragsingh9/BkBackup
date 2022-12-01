<?php


namespace Modules\SuperAdmin\Services\DataServices\factory;


use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Modules\SuperAdmin\Exports\CollectionExport;
use Modules\SuperAdmin\Services\DataServices\IExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will export the user tags in excel
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class ExcelExportService
 * @package Modules\SuperAdmin\Services\DataServices\factory
 */
class ExcelExportService implements IExportService {

    /**
     * @inheritDoc
     */
    public function exportUserTags(Collection $tags, string $fileName): BinaryFileResponse {
        $langs = array_keys(config('superadmin.moduleLanguages'));
        $header = [];
        // preparing header with upper case lang names
        foreach ($langs as $lang) {
            $header[] = strtoupper($lang);
        }
        // adding langs header and predefined headers
        $header = [
            '', // Serial Number
            ...$header,
            ...array_values(config('superadmin.constants.tagExportHeader'))
        ];
        // data to export
        $data = [];
        foreach ($tags as $k => $tag) {
            $temp['SrNo'] = $k+1;
            // fetching tag value for each lang available
            foreach ($langs as $lang) {
                $temp[strtoupper($lang)] = ($value = $tag->locales->where('locale', $lang)->first()) ? $value->value : null;
            }
            $temp['created_at'] = $tag->created_at;
            $data[] = $temp;
        }
        return Excel::download(new CollectionExport(collect($data), $header), $fileName);
    }
}
