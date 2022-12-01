<?php


namespace Modules\KctAdmin\Services\BusinessServices;


use Illuminate\Database\Eloquent\Builder;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will contain all methods related to the analytics feature
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IAnalyticsService
 *
 * @package Modules\KctAdmin\Services\DataServices
 */
interface IAnalyticsService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing query builder for fetching analytics data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupIds
     * @param $startDate
     * @param $endDate
     * @return Builder
     */
    public function fetchAnalyticsData($groupIds, $startDate, $endDate): Builder;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event type number by matching the key with defined types for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @return mixed
     */
    public function getEventTypeBySearchKey($key);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To apply the sorting on query based on the order key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Builder $builder
     * @param $orderBy
     * @param $order
     * @param $keysAlise
     * @return mixed
     */
    public function addSortingToAnalytics(Builder $builder, $orderBy, $order, $keysAlise);
}
