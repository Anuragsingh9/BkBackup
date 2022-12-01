<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;

class ColorRGBA implements Rule {
    
    protected $errorMessage;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $this->errorMessage = null;
        
        $color = json_decode($value, 1);
        if (!isset($color['transparency'])) {
            $this->errorMessage = "$attribute must have colors value";
            return false;
        }
        $color = $color['transparency'];
        if (count($color) != 4) {
            $this->errorMessage = "$attribute must have all rgba values";
            return false;
        }
        foreach ($color as $colorCode => $colorValue) {
            if (!in_array($colorCode, ['r', 'g', 'b', 'a'])) {
                $this->errorMessage = 'Only rgba values are allowed, not[' . $colorCode . ']';
            } else if ($colorCode != 'a') { // RGB Validations
                if (!is_numeric($colorValue))
                    $this->errorMessage = "Color $colorCode must have integer value";
                else if ($colorValue < 0 || $colorValue > 255)
                    $this->errorMessage = "Color $colorCode must be between 0 to 255";
            } else if ($colorCode == 'a') {// Alpha validations
                if (!is_double($colorValue) && !is_float($colorValue) && !is_numeric($colorValue)) {
                    $this->errorMessage = "Color $colorCode must be a valid number between 0-1";
                } else if ($colorValue < 0 || $colorValue > 1) {
                    $this->errorMessage = "Color $colorCode must be between 0-1";
                }
            }
        }
        if ($this->errorMessage === null)
            return true;
        return false;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->errorMessage;
    }
}
