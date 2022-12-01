<?php
    
    namespace Modules\Events\Http\Middleware;
    
    use App\AccountSettings;
    use Closure;
    use Illuminate\Http\Request;
    
    class checkValidKey
    {
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->host = app(\Hyn\Tenancy\Environment::class)->hostname();
        }
        
        /**
         * Handle an incoming request.
         *
         * @param \Illuminate\Http\Request $request
         * @param \Closure $next
         * @return mixed
         */
        public function handle(Request $request, Closure $next)
        {
            $accountSetting = AccountSettings::where('id', $this->host->id)->first(['setting']);
            if (!($accountSetting && $accountSetting->setting && isset($accountSetting->setting['event_enabled']) && $accountSetting->setting['event_enabled'])) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            
            if (isset($this->host->id)) {
                $getKey = $this->core->getApiKey($this->host->id);
                if (($getKey === $request->header('Authorization')) /*&& (in_array($request->header('origin'),config('constants.WHITE_LIST_DOMAIN')))*/) {
                    return $next($request);
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            } else {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            return $next($request);
        }
    }
