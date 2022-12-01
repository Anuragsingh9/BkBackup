<?php


namespace Modules\UserManagement\Services\BusinessServices;


use Illuminate\Http\UploadedFile;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the file services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IFileService
 * @package Modules\UserManagement\Services\BusinessServices
 */
interface IFileService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To upload the user avatar
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UploadedFile|string $file
     * @param bool $resize
     * @return string|null
     */
    public function uploadUserAvatar(UploadedFile $file, bool $resize = false): ?string;

}
