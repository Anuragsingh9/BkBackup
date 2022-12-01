<?php
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 5/28/2019
     * Time: 12:14 PM
     */

    namespace App\Services;

    use App\Exceptions\CustomValidationException;
    use App\Exceptions\EmailExistsException;
    use App\Signup;
    use App\User;
    use Aws\Ec2\Exception\Ec2Exception;
    use Exception;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Auth;

    class UserService extends Service
    {

        /**
         * @param User $user
         * @param $event // if 1 then it will send email with the event so it will done in background|async
         * @throws Exception
         */
        public function sendVerificationMail(User $user, $event = 0)
        {
            if (!$user)
                throw new \Exception();
            // todo send mail here
        }

        /**
         * @param $request
         * @return User|JsonResponse
         * @throws Exception|CustomValidationException
         */
        public function register($request, $throwEmailExists = 1, $data = [])
        {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if ($throwEmailExists)
                    throw new CustomValidationException(__('validation.exists', ['attribute' => 'email']));
                else
                    return response()->json([
                        'status' => TRUE,
                        'data'   => [
                            'already_exists' => TRUE,
                            'fname'          => $user->fname,
                            'lname'          => $user->lname,
                        ]], 200);
            }
            if (empty($data)) {
                $data = [
                    'email'    => $request->email,
                    'fname'    => $request->fname,
                    'lname'    => $request->lname,
                    'password' => bcrypt($request->email),
                    'on_off'   => 0, // as user is not verified yet,
                ];
            }
            $user = User::create($data);
            if (!$user)
                throw new Exception();
            return $user;
        }

        /**
         * @param $otp
         * @param null $user
         * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
         * @throws CustomValidationException
         */
        public function otpVerify($otp, $user = NULL)
        {
            $user = $user ? $user : Auth::user();
            $sentOtp = Signup::where('email', $user->email)->first();
            if ($sentOtp && $otp == $sentOtp->code) {
                $validate = User::where('id', Auth::user()->id)->update(['on_off' => 1]);
                if (!$validate) throw new Exception();
                return $user;
            } else {
                throw new CustomValidationException();
            }

        }

        /**
         * @param $fields
         * @param $id
         * @return $user|null
         * @throws CustomValidationException
         * to get user using id,with specific fields
         */
        public static function getUser($fields = ['*'], $id)
        {
            if (empty($id))
                throw new Exception();

            $user = User::find($id, $fields);
            if (!$user)
                throw new Exception();
            else
                return $user;
        }
    
        /**
         * -------------------------------------------------------------------------------------------------------------
         * @description to prepare the hash code for the user
         * -------------------------------------------------------------------------------------------------------------
         *
         * @return array
         */
        public function prepareHashCode() {
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $hostname = $tenancy->hostname();
            $hostCode = \DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
            $randCode = generateRandomValue(3);
            return setPasscode($hostCode->hash, $randCode);
        }
    }
