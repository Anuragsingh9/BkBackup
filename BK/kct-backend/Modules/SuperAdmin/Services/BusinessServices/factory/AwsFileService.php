<?php


namespace Modules\SuperAdmin\Services\BusinessServices\factory;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\SuperAdmin\Services\BusinessServices\IFileService;
use Modules\SuperAdmin\Traits\ServicesAndRepo;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will handle the AWS file services
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class AwsFileService
 * @package Modules\SuperAdmin\Services\BusinessServices\factory
 */
class AwsFileService implements IFileService {
    use ServicesAndRepo;

    private array $tenantExcludedDirectory = ['general', 'assets'];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method add the tenant into the path
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     * @param $tenant
     * @return mixed|string
     */
    private function addTenantToPath($path, $tenant) {
        if ($tenant && !Str::startsWith($path, $this->tenantExcludedDirectory)) {
            // if tenant is allowed then check if tenant is available or not
            $hostname = $this->suServices()->tenantService->getHostname();
            // checking if path already contain fqdn or not
            if ($hostname && !Str::startsWith($path, $hostname->fqdn)) {
                $path = "$hostname->fqdn/$path";
            }
        }
        return $path;
    }

    /**
     * @inheritDoc
     */
    public function getFileUrl($path, bool $tenant = true) {
        if (!$path) {
            return null;
        }
        return Storage::disk('s3')->url($this->addTenantToPath($path, $tenant));
    }

    /**
     * @inheritDoc
     */
    public function storeFile($file, string $path, $tenant = true, string $visibility = 'public'): string {
        $uploadUrl = Storage::disk('s3')->put(
            $this->addTenantToPath(
                $path,
                $tenant
            ),
            $file,
            $visibility
        );
        // checking if storage return file path or boolean
        if ($uploadUrl === true) {
            return $path;
        } else {
            return $uploadUrl;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $path): bool {
        return Storage::disk('s3')->delete($path);
    }

    /**
     * @inheritDoc
     */
    public function copyFile($currentLocation, $newLocation): ?string {
        return Storage::disk('s3')->copy($currentLocation, $newLocation);
    }

    /**
     * @inheritDoc
     */
    public function uploadImageByUrl($url, $path, bool $encodeToJpg = false): ?string {
        $image = Image::make($url)->stream();
        if ($encodeToJpg) {
            $image->encode('jpg');
        }
        return $this->storeFile($image->__toString(), $path);
    }

    /**
     * @inheritDoc
     */
    public function isFileExists($path): bool {
        return Storage::disk('s3')->exists($path);
    }

    /**
     * @inheritDoc
     */
    public function moveFile($from, $to) {
        return Storage::disk('s3')->move($from, $to);
    }
}
