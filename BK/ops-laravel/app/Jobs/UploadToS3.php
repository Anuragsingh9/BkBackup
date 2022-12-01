<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class UploadToS3 implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $connection;
    public $tries = 5;
    public $filePath;
    public $imageUrl;

    public function __construct($imageUrl, $imageId, $ext) {
        $this->connection = 'sqs';
        $this->filePath = 'stock/' . $imageId . '.' . $ext;
        $this->imageUrl = $imageUrl;
    }

    public function handle() {
        $s3 = Storage::disk('s3');
        $s3->put('/' . $this->filePath, file_get_contents($this->imageUrl), 'public');
    }
}
