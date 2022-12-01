<?php

namespace Modules\Events\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

/**
 * Class ImageAspectRatioCheck
 * @package Modules\Events\Rules
 */
class ImageAspectRatioCheck implements Rule {
    /**
     * - the max ratio allowed for width / height
     *
     * @var int
     */
    private $widthByHeightMaxRatio;
    
    /**
     * - the max ratio allowed for height/width
     *
     * @var int
     */
    private $heightByWidthMaxRatio;
    /**
     * @var string|null
     */
    private $msg;
    
    /**
     * Create a new rule instance.
     *
     * @param int $widthByHeightMaxRatio
     * @param int $heightByWidthMaxRatio
     */
    public function __construct($widthByHeightMaxRatio, $heightByWidthMaxRatio) {
        $this->widthByHeightMaxRatio = $widthByHeightMaxRatio;
        $this->heightByWidthMaxRatio = $heightByWidthMaxRatio;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param UploadedFile $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($value) {
            if (($size = getimagesize($value)) !== false && count($size) >= 2) {
                $w = $size[0];
                $h = $size[1];
                if ($h == 0 || $w / $h > $this->widthByHeightMaxRatio) {
                    $this->msg = __('events::message.image_width_long');
                    return false;
                } else if ($w == 0 || $h / $w > $this->heightByWidthMaxRatio) {
                    $this->msg = __('events::message.image_height_long');
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msg;
    }
}
