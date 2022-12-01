<?php


namespace Modules\UserManagement\Repositories\factory;


use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Entities\UserMetas;
use Modules\UserManagement\Entities\Entity;
use Modules\UserManagement\Entities\UserInfo;
use Modules\UserManagement\Entities\UserMobile;
use Modules\UserManagement\Repositories\IUserRepository;
use Modules\UserManagement\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the user management repository
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * class UserRepository
 * @package Modules\UserManagement\Repositories\factory
 */
class UserRepository implements IUserRepository {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function updateUserLanguage(?string $lang, ?User $user = null): ?User {
        $user = $user ?: Auth::user();
        if ($lang) {
            $setting = $user->setting;
            $setting['lang'] = strtolower($lang);
            $user->setting = $setting;
            $user->update();
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function update(int $id, array $data): User {
        $user = User::find($id);
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
        $user->update();
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function createUser(array $userData, array $roles = []): User {
//        dd(Auth::id());
        $avatar = $userData['avatar'] ?? null;
        $email = strtolower($userData['email'] ?? null);
        // using email as password if password not provided
        $password = $userData['password'] ?? $email;
        // checking if password is hashed then use as it is, else hash the password
        $password = strlen($password) == 60 && preg_match('/^\$2y\$/', $password)
            ? $password : Hash::make($password);

        // if avatar is file upload and get url
        if ($avatar instanceof UploadedFile) {
            $avatar = $this->umServices()->fileService->uploadUserAvatar($avatar);
        }

        $user = User::create([
            'fname'             => ucwords(strtolower($userData['fname'])),
            'lname'             => ucwords(strtolower($userData['lname'])),
            'email'             => strtolower($email),
            'password'          => $password,
            'avatar'            => $avatar,
            'loginCount'        => 0,
            'setting'           => [
                'lang' => $userData['lang'] ?? App::getLocale(),
            ],
            'identifier'        => $userData['identifier'] ?? null,
            'login_count'       => $userData['login_count'] ?? 0,
            'internal_id'       => $userData['internal_id'] ?? null,
            'email_verified_at' => $userData['email_verified_at'] ?? null,
            'gender'            => $userData['gender'] ?? null,
        ]);

        UserMetas::create([
            'user_id'     => $user->id,
            'signup_type' => "2",
        ]);

        $this->createUserInfo($user->id, $userData);

        if (isset($userData['mobiles'])) {
            $this->addMultipleMobiles($user->id, $userData['mobiles'], UserMobile::$type_mobile);
        }
        if (isset($userData['phones'])) {
            $this->addMultipleMobiles($user->id, $userData['phones'], UserMobile::$type_landLine);
        }

        $user->load('phones');
        $user->load('mobiles');
        $user->load('personalInfo');

        foreach ($roles as $role) {
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function addMultipleMobiles(int $userId, array $userData, int $type) {
        foreach ($userData as $mobiles) {
            $mobiles['type'] = $type;
            $this->addMobile($userId, $mobiles);
        }
        $this->validateUserHasPrimaryNumber($userId, $type);
    }

    /**
     * @inheritDoc
     */
    public function validateUserHasPrimaryNumber(int $userId, int $type) {
        $isPrimaryPresent = UserMobile::where("user_id", $userId)
            ->where('type', $type)
            ->where('is_primary', 1)
            ->first();

        // if primary not found fetch first record, if that found make it primary
        if (!$isPrimaryPresent && $first = UserMobile::where([
                'user_id' => $userId,
                'type'    => $type,
            ])->first()) {
            $first->is_primary = 1;
            $first->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function createUserInfo(int $userId, array $userData): UserInfo {
        return UserInfo::updateOrCreate([
            'user_id' => $userId,
        ], [
            'city'    => $userData['city'] ?? null,
            'country' => $userData['country'] ?? null,
            'address' => $userData['address'] ?? null,
            'postal'  => $userData['postal'] ?? null,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail($email, $trashed = false): ?User {
        return User::withTrashed($trashed)->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function findById(?int $id): ?User {
        return User::find($id);
    }

    /**
     * @inheritDoc
     */
    public function getUserByEmail($email, bool $includeDeleted = false) {
        if ($includeDeleted) {
            return User::withTrashed()->whereIn('email', $email)->get();
        }
        return User::whereIn('email', $email)->get();
    }

    /**
     * @inheritDoc
     */
    public function getUsersByEmail(array $emails, bool $allowTrashed = false): Collection {
        return User::withTrashed($allowTrashed)->whereIn('email', $emails)->get();
    }

    public function getByNameOrEmail(?string $key, array $filters = []): Collection {
        if ($filters['like'] ?? false) {
            $b = User::where(function ($q) use ($key) {
                $q->where('fname', 'like', "%$key%");
                $q->orWhere('lname', 'like', "%$key%");
                $q->orWhere(DB::raw("CONCAT(fname, ' ', lname) LIKE '%$key%'"));
                $q->orWhere('email', 'like', "%$key%");
            });
        } else {
            $b = User::where('fname', "%$key%")
                ->orWhere('lname', "%$key%")
                ->orWhere(DB::raw("CONCAT(fname, ' ', lname) IS '%$key%'"))
                ->orWhere('email', "%$key%");
        }
        if ($filters['group_role'] ?? null) {
            $b = $b->whereHas('group', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
                $q->where('role', $filters['group_role']);
            });
        }
        if ($filters['all_data'] ?? false) {
            return $b->with(['group', 'company', 'unions'])->get();
        }
        return $b->get();
    }

    /**
     * @inheritDoc
     */
    public function getForSearch(?string $key, ?array $search = [], array $filters = []) {
        if (!$search || count($search) == 0) {
            $b = User::where(function ($q) use ($key) {
                $q->where('fname', 'like', "%$key%");
                $q->orWhere('lname', 'like', "%$key%");
                $q->orWhere('email', 'like', "%$key%");
                $q->orWhere(DB::raw("CONCAT(fname, ' ', lname) LIKE '%$key%'"));
            });
        } else {
            //search according to user want like fname, lname, email
            $b = User::where(function ($q) use ($key, $search) {
                if (array_search('fname', $search) !== false) {
                    $q->orWhere('fname', 'like', "%$key%");
                }
                if (array_search('lname', $search) !== false) {
                    $q->orWhere('lname', 'like', "%$key%");
                }
                if (array_search('email', $search) !== false) {
                    $q->orWhere('email', 'like', "%$key%");
                }
                if (array_search('union', $search) !== false) {
                    $q->orWhereHas('unions', function ($q) use ($key) {
                        $q->where('long_name', 'like', "%$key%");
                    });
                }
                if (array_search('position_union', $search) !== false) {
                    $q->orWhereHas('unions', function ($q) use ($key) {
                        $q->where('position', 'like', "%$key%");
                    });
                }
                if (array_search('company', $search) !== false) {
                    $q->orWhereHas('company', function ($q) use ($key) {
                        $q->where('long_name', 'like', "%$key%");
                    });
                }
                if (array_search('position_company', $search) !== false) {
                    $q->orWhereHas('company', function ($q) use ($key) {
                        $q->where('position', 'like', "%$key%");
                    });
                }

            });
        }
        if ($filters['group_role'] ?? null) {
            $b = $b->whereHas('group', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
                $q->whereIn('role', $filters['group_role']);
            });
        }
        if ($filters['all_data'] ?? false) {
            $b = $b->with(['group' => function ($q) use ($filters) {
                if (isset($filters['group_id']))
                    $q->where('group_id', $filters['group_id']);
                $q->whereIn('role', [$filters['group_role']]);
            }, 'company', 'unions']);
        }
        return $b;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleUser($userId) {
        return User::whereIn('id', $userId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function updateUserProfile($email, $param): User {
        $user = $this->findByEmail($email);

        $avatar = $param['avatar'] ?? null;
        // using email as password if password not provided
        $password = $param['password'] ?? $param['email'];
        // checking if password is hashed then use as it is, else hash the password
        $password = strlen($password) == 60 && preg_match('/^\$2y\$/', $password)
            ? $password : Hash::make($password);

        // if avatar is file upload and get url
        if ($avatar instanceof UploadedFile) {
            $avatar = $this->umServices()->fileService->uploadUserAvatar($avatar);
        }
        $userData = [
            'fname'       => $param['fname'],
            'lname'       => $param['lname'],
            'password'    => $password,
            'avatar'      => $avatar,
            'loginCount'  => 0,
            'setting'     => [
                'lang' => $param['lang'] ?? App::getLocale(),
            ],
            'internal_id' => $param['internal_id'] ?? null,
            'gender'      => $param['gender'] ?? null ? strtolower($param['gender'][0]) : $user->gender,
        ];
        if ($user->hasRole(User::$userRoles)) {
            foreach (User::$userRoles as $role) {
                $user->removeRole($role);
            }
        }

        if (Arr::exists($param, 'grade') && $param['grade']) {
            $user->assignRole(strtolower($param['grade']));
        }

        $user->update($userData);
        $this->createUserInfo($user->id, $param);
        if (isset($param['company']) || isset($param['company_id'])) {
            $companyData = [];
            if (isset($param['company_id'])) {
                $companyData = [
                    'id'       => $param['company_id'],
                    'position' => $param['company_position'],
                ];
            } else if (isset($param['company'])) {
                $companyData = [
                    'long_name'      => $param['company'],
                    'entity_type_id' => Entity::$type_companyType,
                    'position'       => $param['company_position'],
                ];
            }
//            $this->services->userService->updateUserEntity($companyData, $user->id);
            $this->umServices()->userService->updateUserEntity($user->id, $companyData);
        }
        if (isset($param['union']) || isset($param['union_id'])) {
            $unionData = [];
            if (isset($param['union_id'])) {
                $unionData = [
                    'id'       => $param['union_id'],
                    'position' => $param['union_position'],
                ];
            } elseif (isset($param['union'])) {
                $unionData = [
                    'long_name'      => $param['union'],
                    'entity_type_id' => Entity::$type_unionType,
                    'position'       => $param['union_position'],
                ];
            }
            $this->umServices()->userService->updateUserEntity($user->id, $unionData);
        }
        if (isset($param['mobiles'])) {
            $this->addMultipleMobiles($user->id, $param['mobiles'], UserMobile::$type_mobile);
        }
        if (isset($userData['phones'])) {
            $this->addMultipleMobiles($user->id, $userData['phones'], UserMobile::$type_landLine);
        }
        $user->load('phones');
        $user->load('mobiles');
        $user->load('personalInfo');

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function fetchUserGroups($data) {
        return GroupUser::where('user_id', $data['id'])->get();
    }

    /**
     * @inheritDoc
     */
    public function isUserPitotOfGroups($userGroups) {
        return GroupUser::whereIN('group_id', $userGroups)->where('user_id', Auth::id())->where('role', 2)->get();
    }

    /**
     * @inheritDoc
     */
    public function getUsersById($usersId) {
        return User::whereIn('id', $usersId)->get();
    }

    /**
     * @inheritDoc
     */
    public function deleteMobile(int $userId) {
        UserMobile::where('user_id', $userId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function addMobile(int $id, array $data): ?UserMobile {
        $data['user_id'] = $id;
        return UserMobile::updateOrCreate(
            [
                'user_id' => $id,
                'type'    => $data['type']
            ],
            $data
        );
    }

    public function findByName(?string $name, $like = false): Collection {
        return $this->getUserSearchBuilder($name)->get();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get builder according to user search
     * -----------------------------------------------------------------------------------------------------------------
     * @param string|null $name
     * @return Builder
     */
    private function getUserSearchBuilder(?string $name): Builder {
        return User::where(function ($q) use ($name) {
            $q->where('fname', 'like', "%$name%");
            $q->orWhere('lname', 'like', "%$name%");
            $q->orWhere('email', 'like', "%$name%");
            $q->orWhere(DB::raw("CONCAT(fname, ' ', lname)"), 'like', "%$name%");
        });
    }

    /**
     * @inheritDoc
     */
    public function getUserNotInEvent(?string $key, ?string $uuId): Collection {
        $builder = $this->getUserSearchBuilder($key);
        return $builder->whereDoesntHave('eventUser', function ($q) use ($uuId) {
            $q->where('event_uuid', $uuId);
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function getUsersInOrder($userIds, $orderBy, $order) {
        return User::whereIn('id', $userIds)->orderBy($orderBy, $order);
    }

}
