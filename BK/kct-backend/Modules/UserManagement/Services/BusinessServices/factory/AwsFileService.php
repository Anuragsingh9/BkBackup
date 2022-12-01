<?php


namespace Modules\UserManagement\Services\BusinessServices\factory;


use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Modules\KctUser\Traits\Services;
use Modules\UserManagement\Services\BusinessServices\IFileService;
use Modules\UserManagement\Traits\ServicesAndRepo;
use Ramsey\Uuid\Uuid;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the aws file services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * class AwsFileService
 * @package Modules\UserManagement\Services\BusinessServices\factory
 */
class AwsFileService extends \Modules\SuperAdmin\Services\BusinessServices\factory\AwsFileService implements IFileService {
    use ServicesAndRepo;
    use Services;

    /**
     * @inheritDoc
     */
    public function uploadUserAvatar(UploadedFile $file, bool $resize = false): ?string {
        // adding account name before path
        $path = config('usermanagement.constants.s3.userAvatar');
        if ($resize) {
            $ext = $file->getClientOriginalExtension();
            $file = $this->userServices()->userService->prepareAvatar(Image::make($file))->__toString();
            $filename = Uuid::uuid4()->toString() . ".$ext";
            $path = $path . "/$filename";
        }
        return $this->storeFile($file, $path);
    }

}
