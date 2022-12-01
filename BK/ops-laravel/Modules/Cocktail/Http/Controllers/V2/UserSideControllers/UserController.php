<?php

namespace Modules\Cocktail\Http\Controllers\V2\UserSideControllers;

use App\EntityUser;
use App;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Entities\EventUserPersonalInfo;
use Modules\Cocktail\Entities\EventUserTagRelation;
use Modules\Cocktail\Entities\UserVisibility;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V2\AttachUserTagRequest;
use Modules\Cocktail\Http\Requests\V2\RemoveUserTagRequest;
use Modules\Cocktail\Http\Requests\V1\UserEntityDeleteRequest;
use Modules\Cocktail\Http\Requests\V1\UserUpdateEntityRequest;
use Modules\Cocktail\Http\Requests\V2\BadgeTagDelete;
use Modules\Cocktail\Http\Requests\V2\BadgeUpdateV2Request;
use Modules\Cocktail\Http\Requests\V2\CreateTagRequest;
use Modules\Cocktail\Http\Requests\V2\LoginV2Request;
use Modules\Cocktail\Http\Requests\V2\UpdatePersonalInfoRequest;
use Modules\Cocktail\Http\Requests\V2\UpdateUserVisibilityRequest;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\UserAccessTokenResource;
use Modules\Cocktail\Transformers\V2\UserSide\BadgeV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\UserTagV2USResource;
use Modules\Events\Service\ValidationService;
use Modules\SuperAdmin\Entities\UserTag;

class UserController extends Controller {
    
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    
    public function __construct(EmailFactory $emailFactory) {
        $this->emailFactory = $emailFactory;
    }
    
    public function getBadge() {
        try {
            $user = KctService::getInstance()->getUserBadge(Auth::user()->id);
            $user->load('userVisibility');
            return (new BadgeV2USResource($user))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function updateVisibility(UpdateUserVisibilityRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $previousData = UserVisibility::where('user_id', Auth::user()->id)->first();
            if ($previousData) {
                $newData = $previousData->fields;
                $newData[$request->input('field')] = checkValSet($request->input('value'));
                $data = UserVisibility::where('user_id', Auth::user()->id)->first();
                $data->update(['fields' => $newData]);
            } else {
                $fields = [$request->input('field') => $request->input('value')];
                $param = [
                    'user_id' => Auth::user()->id,
                    'fields'  => $fields,
                ];
                $data = UserVisibility::create($param);
            }
            DB::connection('tenant')->commit();
            $user = KctService::getInstance()->getUserBadge(Auth::user()->id);
            $user->load('userVisibility');
            return (new BadgeV2USResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function updateProfileField(BadgeUpdateV2Request $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            
            $value = $request->field == 'avatar' ? KctService::getInstance()->uploadUserProfile($request->value) : $request->value;
            $update = User::where('id', Auth::user()->id)->update([$request->field => $value]);
            if (!$update) {
                throw new Exception();
            }
            DB::connection('tenant')->commit();
            return (new BadgeV2USResource(KctService::getInstance()->getUserBadge(Auth::user()->id)))->additional(['status' => true]);
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
            KctService::getInstance()->updateUserEntity($request);
            DB::connection('tenant')->commit();
            return (new BadgeV2USResource(KctService::getInstance()->getUserBadge(Auth::user()->id)))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
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
            $user = KctService::getInstance()->getUserBadge(Auth::user()->id);
            DB::connection('tenant')->commit();
            return (new BadgeV2USResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
    
    public function deleteProfilePicture() {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = KctService::getInstance()->deleteProfilePic();
            DB::connection('tenant')->commit();
            return (new BadgeV2USResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function getUserTags() {
        try {
            DB::connection('tenant')->beginTransaction();
            $tag_detail = KctService::getInstance()->getUserTag();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tag_detail], 200);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function addUserTag(BadgeTagDelete $request) {
        
        try {
            DB::connection('tenant')->beginTransaction();
            $tags = KctService::getInstance()->addUserTag($request->tag_id);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tags], 200);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function deleteUserTag(BadgeTagDelete $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $tags = KctService::getInstance()->deleteTagUser($request->tag_id);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $tags], 200);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function login(LoginV2Request $request) {
        try {
            $msg = '';
            $redirect = null;
            $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
            $eventUuid = $request->input('event_uuid');
            if (Auth::attempt($credentials)) { // login successful
                // if login success set language which is selected
                KctCoreService::getInstance()->updateUserLanguage($request->input('lang'));
                if (Auth::user()->on_off) { // checking email verified
                    if ($request->has('event_uuid') && ValidationService::getInstance()->isEventSpaceOpenOrFuture($request->input('event_uuid'))) {
                        if (AuthorizationService::getInstance()->isUserEventMember($eventUuid)) { // check event member
                            if (!KctCoreService::getInstance()->isFirstLoginToEventAfterRegistration($eventUuid)) { // check is first time login
                                return new UserAccessTokenResource(Auth::user());
                            } else { // user is logging first time after register to this event
                                $redirect = KctCoreService::getInstance()->getRedirectUrl($request, 'quick_user_info', ['EVENT_UUID' => $request->input('event_uuid')]);
                            }
                        } else { // user is not member of event
                            $redirect = KctCoreService::getInstance()->getRedirectUrl($request, 'quick_user_info', ['EVENT_UUID' => $request->input('event_uuid')]);
                        }
                    } else {
                        // login without event or in past event
                        $redirect = KctCoreService::getInstance()->getRedirectUrl($request, 'event-list');
                    }
                } else { // user email not verified
                    $this->emailFactory->sendOtp(Auth::user(), $request, $request->input('event_uuid'));
                    $redirect = KctCoreService::getInstance()->getRedirectUrl($request, 'email_verify', ['EVENT_UUID' => $request->input('event_uuid')]);
                }
            } else { // invalid credentials
                $msg = __('cocktail::message.auth_failed');
            }
            if ($redirect) {
                return (new UserAccessTokenResource(Auth::user()))->additional([
                    'redirect_url' => $redirect,
                ]);
            }
            $data = ['status' => false, 'msg' => $msg,];
            return response()->json($data, 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    public function createUserTag(CreateTagRequest $request) {
        try {
            DB::beginTransaction();
            $lang = App::getLocale();
            $existingTag = UserTag::where("tag_$lang", $request->input("tag_name"))
                ->where('status', 3)
                ->count();
            if ($existingTag) {
                return response()->json(['status' => false, 'msg' => __('validation.unique')], 422);
            }
            $tag = UserTag::create([
                "tag_EN"   => $request->input('tag_name'),
                "tag_FR"   => $request->input('tag_name'),
                'tag_type' => $request->input('tag_type'),
                'status'   => 3,
            ]);
            EventUserTagRelation::create([
                'user_id' => Auth::user()->id,
                'tag_id'  => $tag->id,
            ]);
            
            DB::commit();
            return (new UserTagV2USResource($tag))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function searchTag(Request $request) {
        if ($request->has('key') && strlen($request->input('key')) >= 3) {
            $key = $request->input('key');
            $tags = UserTag::where(function ($q) use ($key) {
                $q->where('tag_EN', 'like', "%$key%");
                $q->orWhere("tag_FR", 'like', "%$key%");
            })
                ->where('tag_type', $request->input('tag_type'))
                ->where('status', 1)
                ->get();
            $alreadyUsedTags = KctCoreService::getInstance()->getUserTags($request->input("tag_type"));
            $tags = $tags->diff($alreadyUsedTags);
            return UserTagV2USResource::collection($tags);
        }
        return response()->json([
            'status' => false,
            'msg'    => __('validation.min.numeric', ['attribute'=>  'key', 'min' => 3]),
        ], 422);
    }
    
    public function removeTag(RemoveUserTagRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            EventUserTagRelation::where('user_id', Auth::user()->id)
                ->where('tag_id', $request->input('tag_id'))
                ->delete();
            
            $tag = UserTag::where('id', $request->input('tag_id'))->first();
            $tags = KctCoreService::getInstance()->getUserTags($tag->tag_type);
            DB::connection('tenant')->commit();
            return UserTagV2USResource::collection($tags)->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function attachTag(AttachUserTagRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            EventUserTagRelation::where('user_id', Auth::user()->id)
                ->where('tag_id', $request->tag_id)
                ->delete();
            
            $tagRelation = EventUserTagRelation::create([
                'user_id' => Auth::user()->id,
                'tag_id'  => $request->tag_id
            ]);

            $tagRelation->load("tag");
            $tagType = $tagRelation->tag->tag_type;
            $tags = KctCoreService::getInstance()->getUserTags($tagType);
            DB::connection('tenant')->commit();
            return UserTagV2USResource::collection($tags)
                ->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    public function updatePersonalInfo(UpdatePersonalInfoRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            EventUserPersonalInfo::updateOrCreate(
                ['user_id' => Auth::user()->id],
                [$request->input('field') => $request->input('value')]);
            $user = KctService::getInstance()->getUserBadge(Auth::user()->id);
            $user->load('userVisibility');
            DB::connection('tenant')->commit();
            return (new BadgeV2USResource($user))->additional(['status' => true]);
        } catch (\Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 500);
        }
    }
    
    
}
