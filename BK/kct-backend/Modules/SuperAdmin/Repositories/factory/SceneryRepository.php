<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\SuperAdmin\Entities\SceneryAsset;
use Modules\SuperAdmin\Entities\SceneryCategory;
use Modules\SuperAdmin\Repositories\ISceneryRepository;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the scenery related functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SceneryRepository
 * @package Modules\SuperAdmin\Repositories\factory
 */
class SceneryRepository implements ISceneryRepository {

    /**
     * @inheritDoc
     */
    public function fetchAllSceneryData(){
        return SceneryCategory::with('asset','sceneryLocale')->get();
    }

    /**
     * @inheritDoc
     */
    public function fetchEventSceneryData($assetId){
        return  SceneryAsset::whereId($assetId)->first();

    }

}
