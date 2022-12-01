<?php

namespace Modules\Cocktail\Http\Controllers\V1\UserSideControllers;

use App\EntityUser;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Excel;
use Exception;
use Illuminate\Support\Facades\Hash;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V1\SocialLinkDeleteRequest;
use Modules\Cocktail\Http\Requests\V1\UserEntityDeleteRequest;
use Modules\Cocktail\Http\Requests\V1\UserPasswordUpdateRequest;
use Modules\Cocktail\Http\Requests\V1\UserUpdateEntityRequest;
use Modules\Cocktail\Http\Requests\V1\UserUpdateProfileFieldRequest;
use Modules\Cocktail\Http\Requests\V1\UserUpdateProfileRequest;
use Modules\Cocktail\Http\Requests\V1\UserUpdateSocialLinksRequest;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\UserBadgeResource;
use Modules\Cocktail\Transformers\UserSide\EntitySearchResource;
use Modules\Crm\Entities\UserSocialAccountLink;
use Validator;

class UserController extends Controller {
    /**
     * @var KctService|null
     */
    private $service;
    
    public function __construct() {
        $this->service = KctService::getInstance();
    }
    
    public function getBadge() {
        try {
            $user = $this->service->getUserBadge(Auth::user()->id);
            return (new UserBadgeResource($user))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function updatePassword(UserPasswordUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = User::find(Auth::user()->id);
            if (Hash::check($request->old_password, $user->password)) {
                $result = User::where('id', Auth::user()->id)->update(['password' => Hash::make($request->password)]);
                if ($result) {
                    User::where('id', Auth::user()->id)->increment('login_count');
                }
                DB::connection('tenant')->commit();
                return response()->json(['status' => true, 'data' => KctService::getInstance()
                    ->trans('updated', 'password')], 200);
            } else {
                throw new CustomValidationException('invalid_password', '', 'message');
            }
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    
    public function updateProfileField(UserUpdateProfileFieldRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            
            $value = $request->field == 'avatar' ? $this->service->uploadUserProfile($request->value) : $request->value;
            $update = User::where('id', Auth::user()->id)->update([$request->field => $value]);
            if (!$update) {
                throw new Exception();
            }
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($this->service->getUserBadge(Auth::user()->id)))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function updateSocialLink(UserUpdateSocialLinksRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            UserSocialAccountLink::updateOrCreate(
                ['user_id' => Auth::user()->id, 'channel' => $request->field, 'is_main' => 1],
                ['url' => $request->value]
            );
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($this->service->getUserBadge(Auth::user()->id)))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function updateEntity(UserUpdateEntityRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->updateUserEntity($request);
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($this->service->getUserBadge(Auth::user()->id)))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @return UserBadgeResource
     */
    public function getUserProfile() {
        return $this->getBadge();
    }
    
    public function updateProfile(UserUpdateProfileRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'fname' => $request->fname,
                'lname' => $request->lname,
            ];
            DB::connection('tenant')->commit();
            $user = $this->service->updateUserProfile($param, $request);
            return (new UserBadgeResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
    
    /**
     *
     * To search the entity to add
     *
     * @param $val
     * @param $type
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function searchEntity($val, $type) {
        $entities = $this->service->searchEntity($val, $type);
        return $entities ? EntitySearchResource::collection($entities) : response()->json([]);
    }
    
    public function deleteEntity(UserEntityDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $removeEntityUser = EntityUser::where('user_id', Auth::user()->id)
                ->where('entity_id', $request->entity_id)
                ->delete();
            if (!$removeEntityUser) {
                throw new Exception();
            }
            $user = $this->service->getUserBadge(Auth::user()->id);
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
    
    public function deleteSocialLink(SocialLinkDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            UserSocialAccountLink::where('user_id', Auth::user()->id)
                ->where('channel', $request->input('channel'))
                ->delete();
            $user = $this->service->getUserBadge(Auth::user()->id);
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
    
    public function deleteProfilePicture() {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->service->deleteProfilePic();
            DB::connection('tenant')->commit();
            return (new UserBadgeResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function updateLang(Request $request) {
        try {
            $request->merge(['lang' => strtoupper($request->lang)]);
            $validator = Validator::make($request->all(), [
                'lang' => 'required|in:' . implode(',', config('cocktail.available_lang')),
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg'    => implode($validator->errors()->all()),
                    'lang'   => $request->lang,
                ]);
            }
            DB::connection('tenant')->beginTransaction();
            $user = Auth::user();
            $setting = $user->setting;
            $setting = json_decode($setting, 1);
            $setting['lang'] = $request->lang;
            User::where('id', $user->id)->update(['setting' => json_encode($setting)]);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => ['lang' => $request->lang]], 200);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
    
    public function getUserLevelData(Request $request) {
        $activeEvent = KctEventService::getInstance()->getUserActiveEventUuid($request);
        $lang = $this->service->getUserLang($request);
        $auth = $this->service->getUserDetails($request);

        return [
            'active_event_uuid' => $activeEvent ? $activeEvent : null,
            'lang'              => $lang,
            'auth'              => $auth,
        ];
    }
}
