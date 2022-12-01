<?php


namespace Modules\KctAdmin\Repositories\factory;


use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\Label;
use Modules\KctAdmin\Entities\LabelLocale;
use Modules\KctAdmin\Repositories\ILabelRepository;

class LabelRepository implements ILabelRepository {

    /**
     * @inheritDoc
     */
    public function getAll($groupId = null): Collection {
        return Label::with(['locales' => function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
        }])->get();
    }

    /**
     * @inheritdoc
     */
    public function getLabelsByName($names, $groupId = null): Collection {
        return Label::with(['locales' => function ($q) use ($groupId) {
            $q->where('group_id', $groupId);
        }])->whereIn('name', $names)->get();
    }

    /**
     * @inheritDoc
     */
    public function getLocaleByName(?string $name, ?string $locale, $groupId = null): LabelLocale {
        return LabelLocale::whereHas('label', function ($q) use ($name) {
            $q->where('name', $name);
        })->where('locale', $locale)->where('group_id', $groupId)->first();
    }
}
