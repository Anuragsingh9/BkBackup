<?php

namespace App\Imports;

use App\DummyUsers;
use App\Exceptions\CustomException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Cocktail\Services\KctService;


class DummyUserImport implements ToCollection {
    
    /**
     * @var string
     */
    private $path;
    
    public function __construct($path) {
        $this->path = $path;
    }
    
    public function collection(Collection $collection) {
        $dataToInsert = [];
        if (($c = count($collection)) > 0) {
            for ($i = 1; $i < $c; $i++) {
                $data = $this->validateRow($collection[$i], $i);
                $dataToInsert[] = $data;
            }
        }
        DummyUsers::insert($dataToInsert);
    }
    
    /**
     * @param $row
     * @param $rowNumber
     * @return array
     * @throws CustomException
     */
    public function validateRow($row, $rowNumber) {
        $data = [
            'fname'            => isset($row[0]) ? $row[0] : null,
            'lname'            => isset($row[1]) ? $row[1] : null,
            'company'          => isset($row[2]) ? $row[2] : null,
            'company_position' => isset($row[3]) ? $row[3] : null,
            'union'            => isset($row[4]) ? $row[4] : null,
            'union_position'   => isset($row[5]) ? $row[5] : null,
            'video_url'        => isset($row[6]) ? "$row[6]" : null,
            'avatar'           => isset($row[7]) ? $row[7] : null,
            'type'             => 1, // regular
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now(),
        ];
        $validator = Validator::make($data, [
            'fname'            => 'required|string|max:100',
            'lname'            => 'required|string|max:100',
            'company'          => 'required|string|max:500',
            'company_position' => 'required|string|max:500',
            'union'            => 'required|string|max:500',
            'union_position'   => 'required|string|max:500',
            'avatar'           => 'required',
            'video_url'        => 'required',
        ]);
        if ($validator->fails()) {
            throw new CustomException("Error in row $rowNumber: " . implode(',', $validator->errors()->all()));
        }
        $data['avatar'] = $this->validateMediaExists($data['avatar'], $data);
        
        $data['video_url'] = "{$data['video_url']}.mp4";
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if the image present or not in directory
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $fileName
     * @return bool
     * @throws CustomException
     */
    public function validateMediaExists($fileName, $debug = null) {
        $imagePath = "$this->path/images/$fileName.jpg";
        if (!file_exists($imagePath)) {
            dd($fileName, $debug);
            throw new CustomException("File $fileName not present in images folder after extract");
        }
        // opening file to upload
        $inputStream = fopen($imagePath, "r+");
        
        // preparing hostname to append in respective bucket
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        $hostname = $tenancy->hostname();
        $imageUploadPath = "$hostname->fqdn/dummy_users/$fileName.jpg";
        KctService::getInstance()->getCore()->fileUploadToS3($imageUploadPath, $inputStream, 'public');
        return $imageUploadPath;
    }
}
