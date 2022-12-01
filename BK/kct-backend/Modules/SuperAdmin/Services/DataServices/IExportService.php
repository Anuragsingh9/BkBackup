<?php


namespace Modules\SuperAdmin\Services\DataServices;


use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will use for export the user tags
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IExportService
 * @package Modules\SuperAdmin\Services\DataServices
 */
interface IExportService {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To export the user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $tags
     * @param string $fileName
     * @return BinaryFileResponse
     */
    public function exportUserTags(Collection $tags, string $fileName): BinaryFileResponse;
}
