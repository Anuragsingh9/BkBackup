<?php

namespace App\Services;

use App\Exceptions\CustomException;
use Ramsey\Uuid\Uuid;

class DummyUsersService extends Service {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare and get the path
     * file will be checked and throw exception if file not found
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $filepath
     * @param $filename
     * @return string
     * @throws CustomException
     */
    public function prepareDummyImportPath($filepath, $filename) {
        $path = $filepath . '/' . $filename;
        if (!file_exists($path)) {
            throw new CustomException("File doesn't exists");
        }
        return $path;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the path for the files after extract from zip
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $filepath
     * @return string
     * @throws \Exception
     */
    public function prepareDummyImportExtractPath($filepath) {
        $uuid = Uuid::uuid1()->toString();
        // adding uuid to the extract folder name
        return "$filepath/$uuid";
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the file extension and validate its zip only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $filename
     * @return boolean
     * @throws CustomException
     */
    public function validateFileExtensionForDummyImport($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext !== 'zip') {
            throw new CustomException('Only zip file allowed');
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To extract the zip file
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     * @param $extractPath
     * @return bool
     * @throws CustomException
     */
    public function extractZipFile($path, $extractPath) {
        $zip = new \ZipArchive();
        $zipStatus = $zip->open($path);
        if ($zipStatus === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            return true;
        }
        throw new CustomException("Can not extract the provided zip");
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To scan the files to extract
     * this will return the files present in the directory
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $extractPath
     * @return array|false
     */
    public function scanFilesForImport($extractPath) {
        return scandir($extractPath);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate the extracted path contains the image folder or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $extractPath
     * @return bool
     * @throws CustomException
     */
    public function validateImageFolder($extractPath) {
        if (!is_dir("$extractPath/images")) {
            throw new CustomException("images folder missing");
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the excel file name
     * @warn this will return the only first excel file present in path
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $files
     * @return string|null
     */
    public function getExcelFileName($files) {
        $allowedExtensions = ['xls', 'xlsx'];
        // matching the files with extension and return if found
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, $allowedExtensions)) {
                return $file;
            }
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate if the extracted path location contains the excel files or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $filteredFiles
     * @return bool
     * @throws CustomException
     */
    public function validateExcelFile($filteredFiles) {
        if (!$filteredFiles) {
            throw new CustomException('No file found to process');
        }
        return true;
    }
}