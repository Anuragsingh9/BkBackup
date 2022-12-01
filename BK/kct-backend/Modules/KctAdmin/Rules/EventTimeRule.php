<?php

namespace Modules\KctAdmin\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Modules\KctAdmin\Repositories\IEventRepository;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class EventTimeRule implements Rule {
    use ServicesAndRepo;

    private ?string $msg = null;
    private int $pastCheck = 0;
    private int $liveCheck = 0;
    private int $futureCheck = 0;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($past = 0, $live = 0, $future = 0) {
        $this->pastCheck = $past;
        $this->liveCheck = $live;
        $this->futureCheck = $future;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        $r = $this->adminServices()->validationService->eventTimeCheck($value,
            $this->pastCheck,
            $this->liveCheck,
            $this->futureCheck,
        );
        if (!$this->adminServices()->validationService->eventTimeCheck($value,
            $this->pastCheck,
            $this->liveCheck,
            $this->futureCheck,
        )) { // event time check has failed
            if ($this->futureCheck && !$this->pastCheck) {
                $this->msg = __("kctadmin::messages.event_must_future");
            }
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return $this->msg;
    }
}
