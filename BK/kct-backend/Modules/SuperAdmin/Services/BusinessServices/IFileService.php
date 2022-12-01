<?php


namespace Modules\SuperAdmin\Services\BusinessServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will handle the file management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IFileService
 * @package Modules\SuperAdmin\Services\BusinessServices
 */
interface IFileService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the file url
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     * @param bool $tenant
     * @return mixed
     */
    public function getFileUrl($path, bool $tenant = true);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To upload the file and return the relative path for that
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $file
     * @param string $path
     * @param bool $tenant
     * @param string $visibility
     * @return string
     */
    public function storeFile($file, string $path, bool $tenant = true, string $visibility = 'public'): string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To delete the file from database
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To duplicate the file
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $currentLocation
     * @param $newLocation
     * @return string|null
     */
    public function copyFile($currentLocation, $newLocation): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To upload the image by the another url to s3
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $url
     * @param $path
     * @return string|null
     */
    public function uploadImageByUrl($url, $path): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if the s3 file exists or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     * @return bool
     */
    public function isFileExists($path): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To move a file from one location to another location
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public function moveFile(string $from, string $to);
}
