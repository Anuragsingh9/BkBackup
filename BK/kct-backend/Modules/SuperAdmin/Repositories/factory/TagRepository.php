<?php


namespace Modules\SuperAdmin\Repositories\factory;


use Illuminate\Database\Eloquent\Collection;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\SuperAdmin\Entities\UserTagLocale;
use Modules\SuperAdmin\Exceptions\SuCustomException;
use Modules\SuperAdmin\Repositories\ITagRepository;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This repository is responsible for getting data from same application database directly
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class TagRepository
 * @package Modules\SuperAdmin\Repositories\factory
 */
class TagRepository implements ITagRepository {

    /**
     * @inheritDoc
     */
    public function getUnModeratedTagByType(int $tagType, int $paginate = null) {
        $builder = UserTag::with('locales')->where('tag_type', $tagType)
            ->where('status', config('superadmin.models.userTag.status_Pending'));
        return $paginate ? $builder->paginate($paginate) : $builder->get();
    }

    /**
     * @inheritDoc
     */
    public function updateTagLocaleValue(int $tagId, string $tagValue, string $locale): UserTagLocale {
        $model = UserTagLocale::updateOrCreate([
            'tag_id' => $tagId,
            'locale' => $locale,
        ], [
            'value' => $tagValue
        ]);
        if (!$model) {
            throw new SuCustomException('ise', null, null, 500);
        }
        return $model;
    }

    /**
     * @inheritDoc
     */
    public function getModeratedTagsByType(int $tagType, string $orderBy = null): Collection {
        // if fr have order by request then return the  tag type, status, lang with the user tags
        if ($orderBy) {
            $tags = UserTag::with('locales')
                ->select('user_tags.*')
                ->where('tag_type', $tagType)
                ->where('status', config('superadmin.models.userTag.status_Accepted'))
                // currently ordering with en lang only change if required
                ->where('locale', 'en')
                ->join('user_tag_locales', 'user_tag_locales.tag_id', '=', 'user_tags.id')
                ->orderBy($orderBy)
                ->get();
        } else {// else return the tag type, status only
            $tags = UserTag::with('locales')
                ->where('tag_type', $tagType)
                ->where('status', config('superadmin.models.userTag.status_Accepted'))
                ->get();
        }

        return $tags;
    }

    /**
     * @inheritDoc
     */
    public function updateTagStatus(int $tagId, int $status): int {
        return UserTag::where('id', $tagId)->update([
            'status' => $status,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function searchTag(string $key, string $tagType): Collection {
        return UserTag::whereHas('locales', function ($q) use ($key) {
            $q->where('value', 'like', "%$key%");
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name, string $tagType, ?string $locale = null): ?UserTag {
        return UserTag::whereHas('locales', function ($q) use ($name, $locale) {
            $q->where("value", $name);
            $q->where('locale', $locale);
        })->where("tag_type", $tagType)->first();
    }

    /**
     * @inheritDoc
     */
    public function findById(?string $id): ?UserTag {
        return UserTag::find($id);
    }

    /**
     * @inheritDoc
     */
    public function getTagByKey(?string $key, ?string $locale, ?string $tagType): Collection {
        return UserTag::with(['locales' => function ($q) use ($key, $locale) {
            $q->where('locale', $locale);
            $q->where("value", 'like', "%$key%");
        }])
            ->whereHas('locales', function ($q) use ($key, $locale) {
                $q->where('locale', $locale);
                $q->where("value", 'like', "%$key%");
            })
            ->where('tag_type', $tagType)
            ->where('status', 1)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function create(?string $tagName, ?string $tagType) {
        $tag = UserTag::create([
            'tag_type' => $tagType,
            'status'   => 3, // pending
        ]);
        foreach (config('superadmin.moduleLanguages') as $k => $v) {
            UserTagLocale::create([
                'tag_id' => $tag->id,
                'locale' => strtolower("$v"),
                'value'  => $tagName,
            ]);
        }
        return $tag->load('locales');
    }

    /**
     * @inheritDoc
     */
    public function getAll($status = null): Collection {
        return $status ? UserTag::where('status', $status)->get() : UserTag::all();
    }
}
