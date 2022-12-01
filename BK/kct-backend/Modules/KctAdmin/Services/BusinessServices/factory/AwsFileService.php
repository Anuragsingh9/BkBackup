<?php


namespace Modules\KctAdmin\Services\BusinessServices\factory;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\KctAdmin\Services\BusinessServices\IFileService;

class AwsFileService extends \Modules\SuperAdmin\Services\BusinessServices\factory\AwsFileService implements IFileService {

}
