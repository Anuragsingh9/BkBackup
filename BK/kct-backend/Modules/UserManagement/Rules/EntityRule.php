<?php

namespace Modules\UserManagement\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\UserManagement\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;

class EntityRule implements Rule
{
    use ServicesAndRepo;
    use UmHelper;

    /**
     * @var mixed|null
     */
    private ?string  $attribute;
    /**
     * @var mixed
     */
    private $value;

    private string $error = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute = null)
    {
        $this->attribute = $attribute;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        if (!$this->value) {
            return true;
        }
        if (!$this->attribute) {
            $exp = $this->setAttribute($attribute);
            $this->attribute = $attribute = $exp['attribute'];
        } else {
            $attribute = $this->attribute;
        }

        switch ($attribute) {
            case "company_name":
            case "union_name":
                return $this->entityNameValidation();
            case "c_position":
            case "u_position":
                return $this->entityPositionValidation();
            case "company_id":
            case "union_id":
                return $this->entityIdValidation();
            default:
                return true;
        }
    }

    public function entityNameValidation(): bool {
        $atr = ['attribute' => $this->attribute];
        if (!preg_match("/^[a-zàâäáçéèêëìîïíôóòûùüúÿñæœÀÂÁÇÉÈÊËÎÏÌÍÔÛÙÜÚŸÑÆŒ¿0-9 ̛̔̕'’!@#$&^*%-]*$/i", $this->value)) {
            $this->error = __("validation.alpha", $atr);
            return false;
        }
        return true;
    }

    public function entityPositionValidation(): bool {
        $atr = ['attribute' => $this->attribute];
        if (!preg_match("/^[a-zàâäáçéèêëìîïíôóòûùüúÿñæœÀÂÁÇÉÈÊËÎÏÌÍÔÛÙÜÚŸÑÆŒ¿0-9 ̛̔̕'’!@#$&^*%-]*$/i", $this->value)) {
            $this->error = __("validation.alpha", $atr);
            return false;
        }
        return true;
    }

    public function entityIdValidation(): bool {
        $atr = ['attribute' => $this->attribute];
        if (!is_numeric($this->value)){
            $this->error = __("validation.integer",$atr);
            return false;
        }
        $entityExist = $this->umRepo()->entityRepository->findById($this->value);
        if (!$entityExist){
            $this->error = __("validation.exists", $atr);
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->error;
    }
}
