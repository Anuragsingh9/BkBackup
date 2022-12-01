<?php


namespace Modules\KctAdmin\Services\BusinessServices;


use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Entities\Space;
use Modules\KctAdmin\Exceptions\ZoomGrantException;
use ZipStream\Exception;

interface ICoreService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a space for version 2 specific
     * this will add the hosts as well after space creation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @return Space
     */
    public function createSpace($param): Space;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default logo value for graphics setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     */
    public function setDefaultLogoUrl($path);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default logo for the kct if no logo is found
     * ------------------------------------------------------------------------------------------------------------------
     */
    public function setDefaultLogo();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the default color value to new color value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @param $value
     * @param null $setting
     * @return mixed
     */
    public function setKCTSettingValue($key, $value, $setting);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the setting of custom graphics
     * @note in case of setting not found it will add a new setting with default values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getCustomGraphicsSetting(): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the response for the graphics customization
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $setting
     * @return array
     */
    public function prepareCustomizationResource($setting): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the single value of the custom graphics
     * first get the setting
     * if setting not found set the default values
     *  fetch the setting again
     * decode previous value
     * update the previous value according to color or checkbox
     * store
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $field
     * @param $value
     */
    public function updateCustomGraphics($field, $value);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare all kinds of access link for event
     * -----------------------------------------------------------------------------------------------------------------
     * @param Event $event
     * @param bool $returnAllLinks
     * @return mixed
     * @throws ZoomGrantException|Exception
     */
    public function prepareAccessLinks(Event $event, bool $returnAllLinks = false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the links for the broadcast moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param false $returnAllLinks
     * @param array|null $links
     * @return array|null
     */
    public function prepareBroadcastingLinks(Event $event, bool $returnAllLinks = false, ?array $links=[]): ?array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare embedded url for moment
     * -----------------------------------------------------------------------------------------------------------------
     * @param Moment|null $moment
     * @return ?string
     */
    public function getMomentEmbeddedUrl(?Moment $moment): ?string;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the participant link for the user via join code
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @return string
     */
    public function prepareParticipantsLink(Event $event): string ;
}
