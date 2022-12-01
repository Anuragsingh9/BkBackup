<?php

namespace Modules\Newsletter\Http\Controllers;

use App\AccountSettings;
use App\Jobs\UploadImageToS3;
use App\Jobs\UploadToS3;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Modules\Newsletter\Entities\AdobePhotos;
use Modules\Newsletter\Entities\AdobePhotosTracking;

class AdobeStockController extends Controller {
    private $headers;
    
    public function __construct() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $settingValue = Setting::where('setting_key' , 'adobe_stock_api_setting')->first();
        $value = null;
        if($settingValue && $settingValue->setting_value) {
            $value = json_decode($settingValue->setting_value);
        }
        $this->headers = [
            'x-api-key: ' . ($value? $value->access_key:null),
            'x-product: ' . ($value?$value->app_name:null)
        ];
    }
    // INTERNAL USAGE FUNCTION
    public function get_image_url_by_id(int $id, $asset = 'media_id') {
        if (is_null($id)) {
            return '';
        }

        $url = 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US'; // This will always in url part
        $url .= '&search_parameters[' . $asset . ']=' . $id;
        $url .= '&search_parameters[limit]=1';
        $url .= '&result_columns[]=thumbnail_1000_url';
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $json_result = json_decode($server_output);
            return ($json_result && count($json_result->files)) > 0 ? $json_result->files[0]->thumbnail_1000_url : '';
        } catch (Exception $e) {
            return '';
        }
    }
    public function getImageByUrl($imageUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return Image::make($res);
    }
    public function checkImagePossilbeToUpload($imageId, $search) {
        $imageIsPossibleToUpload = false;
        $BOUGHT = 1;
        $USED = 2;
        $isAlreadyBought = AdobePhotosTracking::where([
            'account_id'     => $this->tenancy->hostname()['id'],
            'adobe_photo_id' => $imageId,
            'type'           => $BOUGHT
        ])
            ->count();
        $account_setting = AccountSettings::select('account_id', 'setting')
            ->where('account_id', $this->tenancy->hostname()['id'])
            ->first();
        if ($isAlreadyBought == 0) { // not bought
            $availableCredit = $account_setting['setting']['stock_setting']['available_credit'];
            if ($availableCredit > 0) { // check if credit available
                $imageIsPossibleToUpload = true;
                // todo actually bought will be done here now only logs generating and assuming world is free
                $this->generateLogs($account_setting, $imageId, $BOUGHT, $search);
                $this->generateLogs($account_setting, $imageId, $USED, $search);
                AccountSettings::where('account_id', $account_setting->account_id)
                    ->update(['setting->stock_setting->available_credit' => $availableCredit - 1]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg'    => 'Out of credit'
                ], 200);
            }
        } else { // bought - let account upload the image
            $imageIsPossibleToUpload = true;
            $this->generateLogs($account_setting, $imageId, $USED, $search);
        }
        return $imageIsPossibleToUpload;
    }
    public function executeCurlAndGetResult($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $json_result = json_decode($server_output);
            if ($json_result == '') {
                return '';
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => true,
                'data'   => ''
            ], 500);
        }
        return $json_result;
    }
    public function generateLogs($accountSetting, $imageId, $type, $search) {
        $BOUGHT = 1;
        // logs generate
        AdobePhotosTracking::create([
            'adobe_photo_id' => $imageId,
            'account_id'     => $accountSetting['account_id'],
            'user_id'        => '', // TODO Add USER ID
            'type'           => $type
        ]);
        // save search data so not to buy in future
        if ($type == $BOUGHT) {
            AdobePhotos::create([
                'adobe_photo_id' => $imageId,
                'search_tag'     => $search,
            ]);
        }
    }
    // END OF INTERNAL FUNCTIONS

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * TODO[DEVELOPER] add your env value in following format
     */
    public function imagesearch(Request $request) {
        //To halt the loops in case user sends numbers of data explicitly
        $MAX_SUBCATEGORIES = 3;
        $search_data = urlencode($request->get('search'));
        $current_page = LengthAwarePaginator::resolveCurrentPage();
        $per_page_items = 50;
        $offset = ($current_page - 1) * $per_page_items;

        $url = 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US&result_columns[]=nb_results&search_parameters[words]=' . $search_data;
        $adobeCountResult = $this->executeCurlAndGetResult($url);
        $adobeCount = ($adobeCountResult->nb_results > 10000) ? (int)($adobeCountResult->nb_results / 10000) * 10000 : $adobeCountResult->nb_results;
        try {
            if (!$request->order && !$request->orientation && !$request->color && !$request->assetSub) { // don't show the bought data on applying filter
                // No filter found and adding the bought data first
                $boughtData = AdobePhotos::where('search_tag', $search_data)
                    ->offset($offset)
                    ->limit($per_page_items)
                    ->get();
                $totalBought = AdobePhotos::where('search_tag', $search_data)
                    ->count();
                if ($boughtData->count() > 0) {
                    $ids = '';
                    foreach ($boughtData as $raw) {
                        $ids .= $raw->adobe_photo_id . '+';
                    }
                    $url = 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US'; // This will always in url part
                    $url .= '&result_columns[]=id' . '&result_columns[]=title' . '&result_columns[]=thumbnail_url' . '&result_columns[]=thumbnail_width' . '&result_columns[]=thumbnail_height';
                    $url .= '&search_parameters[words]=' . $ids;
                    $json_result = $this->executeCurlAndGetResult($url);
                }
                if ($boughtData->count() != $per_page_items) { // PARTIAL SEARCH PART
                    if ($boughtData->count()) {
                        $offset = 0;
                        $per_page_items = $per_page_items - $boughtData->count();
                    } else {
                        /* [EXPLANATION]
                         * Simple Offset                            : (current_page * per_page_item)
                         * To reduce extra one                      : (current_page * per_page_item) - per_page_item
                         * Simplify                                 : (current_page - 1) * per_page_item
                         * Remove page covered by purchase images   : (current_page - 1 - pages_covered_by_purchased) * per_page_item
                         * subtract the difference made by partial  : (current_page - 1 - pages_covered_by_purchased) * per_page_item - partial_item
                        */
                        $offset = ($current_page - 1 - (int)($totalBought / $per_page_items)) * $per_page_items - ($totalBought % $per_page_items);
                    }
                    // END OF PARTIAL SEARCH
                    $url = 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US&search_parameters[filters][premium]=false'; // This will always in url part
                    $url .= '&result_columns[]=id' . '&result_columns[]=title' . '&result_columns[]=thumbnail_url' . '&result_columns[]=thumbnail_width' . '&result_columns[]=thumbnail_height' . '&result_columns[]=media_type_id';
                    $url .= '&search_parameters[limit]=' . $per_page_items;
                    $url .= '&search_parameters[offset]=' . $offset;
                    $url .= '&search_parameters[words]=' . $search_data;
                    $result = (isset($json_result)) ? $json_result->files : [];
                    $json_result = $this->executeCurlAndGetResult($url);
                    $result = array_merge($result, $json_result->files);
                }
            } else { // Filter is there so prepare a new search with filters
                if ($request->color && (strlen($request->color) != 6)) {
                    return response()->json(['status' => false, 'msg' => 'Invalid Color'], 422);
                }
                $url = 'https://stock.adobe.io/Rest/Media/1/Search/Files?locale=en_US'; // This will always in url part
                $url .= '&result_columns[]=id' . '&result_columns[]=title' . '&result_columns[]=thumbnail_url' . '&result_columns[]=thumbnail_width' . '&result_columns[]=thumbnail_height' . '&result_columns[]=media_type_id' . '&result_columns[]=thumbnail_1000_url';
                $url .= '&search_parameters[filters][premium]=false'; // This will always in url part
                $url .= '&search_parameters[limit]=' . $per_page_items;
                $url .= '&search_parameters[offset]=' . $offset;
                //FILTERING
                $url .= '&search_parameters[order]=' . $request->order; // TYPE: Select, VALID INPUTS: [relevance, creation, popularity, nb_downloads, undiscovered]
                $url .= '&search_parameters[filters][orientation]=' . $request->orientation; // VALID INPUTS [horizontal, vertical, square, all (Default), Panoramic]
                $url .= '&search_parameters[filters][colors]=' . $request->color;
                if (isset($request->assetSub) && count($request->assetSub) <= $MAX_SUBCATEGORIES && $request->assetSub != '') { // TYPE: CHECKBOX, VALID INPUT ['photo', 'illustration', 'vector']
                    foreach ($request->assetSub as $subcategory) {
                        $url .= '&search_parameters[filters][content_type:' . $subcategory . ']=1';
                    }
                } else {
                    $url .= '&search_parameters[filters][content_type:photo]=1';
                    $url .= '&search_parameters[filters][content_type:illustration]=1';
                    $url .= '&search_parameters[filters][content_type:vector]=1';
                }
                // DONE FILTERING
                $url .= '&search_parameters[words]=' . $search_data;
                $result = (isset($json_result)) ? $json_result->files : [];
                $json_result = $this->executeCurlAndGetResult($url);
                if ($json_result == '') {
                    return response()->json(['status' => false, 'data' => 'Something went wrong internally'], 500);
                }
                $result = array_merge($result, $json_result->files);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => ''], 500);
        }
        // Pagination
        $paginatad_items = new LengthAwarePaginator($result, $adobeCount, $per_page_items, $current_page);
        $paginatad_items->setPath($request->getUri());
        return response()->json($paginatad_items, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     *
     * 1. validate
     * 2. Check image possible to upload
     *      - if image is already bought then let user upload and generate the upload logs
     *      - else if image is not bought then check for credit
     *          - if credit is available reduce the credit and generate the logs for used and bought and let user upload
     *          - else show no credit error -> [RETURN]
     * 3. Image Uploading
     *      a. Prepare the url - throw error if url is null
     *      b. [QUEUE]-Upload the original image to s3
     *      c. Create a Image,  Resize to width, Upload to S3
     * [RETURN] s3 url.
     */
    public function save_searched_image_to_amazon(Request $request) {
        $imageIsPossibleToUpload = false;
        // 1
        $validator = Validator::make($request->all(), [
            'imageId' => 'required',
            'width'   => 'required',
            'search' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg'    => 'Invalid Provided'
            ], 422);
        }
        // 2
        $imageIsPossibleToUpload = $this->checkImagePossilbeToUpload($request->imageId, $request->search);
        if ($imageIsPossibleToUpload === true) {
            $asset = $request->mediaTypeId <= 3 ? 'media_id' : 'model_id';
            $imageId = $request->imageId;
            $imageUrl = $this->get_image_url_by_id($imageId, $asset);
            if ($imageUrl == NULL || $imageUrl == '') {
                return response()->json([
                    'status' => true,
                    'data'   => 'no image url'
                ], 500);
            }
            // UPLOAD JOB
            $ext = pathinfo($imageUrl)['extension'];
            UploadToS3::dispatch($imageUrl, $imageId, $ext);
            $image = $this->getImageByUrl($imageUrl);
            if (!$image) {
                return response()->json([
                    'status' => true,
                    'data'   => 'no image processed'
                ], 500);
            }
            // RESIZE
            $image->resize($request->width, NULL, function ($constraint) {
                $constraint->aspectRatio();
            });
            // ENDING RESIZE
            $image->stream();
            $file_path = 'ooionline.com/'.$this->tenancy->hostname()->fqdn.'/AdobeStock/'.date('Y/m').'/'.$request->imageId . '.' . $ext;
            $s3 = Storage::disk('s3');
            $s3->put('/' . $file_path, $image->__toString(), 'public');
            return response()->json([
                'status' => true,
                'data'   => $s3->url($file_path)
            ], 200);
        } else {
            return $imageIsPossibleToUpload;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * input with request
     * 1. image id
     * 2. crop
     *     a. - start x
     *     b. - start y
     *     c. - width
     *     d. - height
     *  [ALGO]
     * 1. Validate
     * 2. Check image possible to upload
     *      - if image is already bought then let user upload and generate the upload logs
     *      - else if image is not bought then check for credit
     *          - if credit is available reduce the credit and generate the logs for used and bought and let user upload
     *          - else show no credit error -> [RETURN]
     *  3. Prepare image url
     *      - [QUEUE] - Upload original to s3
     *      - Create Image
     *      - Crop
     *      - Resize
     *      - Upload to S3 return S3 URL.
     */
    public function resize_image(Request $request) {
        $validator = Validator::make($request->all(), [
            'imageId' => 'required',
            'width'   => 'required',
            'search' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg'    => 'Invalid Provided'
            ], 422);
        }
        $isImagePossibleToCrop = $this->checkImagePossilbeToUpload($request->imageId, $request->search);
        if ($isImagePossibleToCrop === true) {
            $imageUrl = $this->get_image_url_by_id($request->imageId);
            if ($imageUrl == NULL || $imageUrl == '') {
                return response()->json([
                    'status' => true,
                    'data'   => ''
                ], 500);
            }
            $image = $this->getImageByUrl($imageUrl);
            if ($image == NULL) {
                return response()->json([
                    'status' => true,
                    'data'   => ''
                ], 500);
            }
            $ext = pathinfo($imageUrl)['extension'];
            UploadToS3::dispatch($imageUrl, $request->imageId, $ext);
            $w = (int)$request->w;
            $h = (int)$request->h;
            $x = (int)$request->x;
            $y = (int)$request->y;
            $image_name = $request->imageId . '_' . $x . 'x_' . $y . 'y_' . $w . 'w_' . $h . 'h.' . $ext;
            $image_s3_save_path ='ooionline.com/'.$this->tenancy->hostname()->fqdn.'/AdobeStock/'.date('Y/m').'/crop' . $image_name;
            $image->crop($w, $h, $x, $y);
            $image->resize($request->width, NULL, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->stream();
            Storage::disk('s3')
                ->put('/' . $image_s3_save_path, $image->__toString(), 'public');
            return response()->json([
                'status' => true,
                'data'   => Storage::disk('s3')->url($image_s3_save_path)
            ], 200);
        } else {
            return $isImagePossibleToCrop;
        }
    }
}