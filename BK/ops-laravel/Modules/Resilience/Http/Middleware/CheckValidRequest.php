<?php
    
    namespace Modules\Resilience\Http\Middleware;
    
    use Closure;
    use Illuminate\Http\Request;
    
    class CheckValidRequest
    {
        public function __construct()
        {
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->host = app(\Hyn\Tenancy\Environment::class);
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
            if (isset($this->host->id)) {
                $hostname = $this->host->hostname()['fqdn'];
                $domain = strtok($hostname, '.');
                $requestDomain = strtok($request->domain, '.');
                if (($domain == $requestDomain) && (in_array($request->header('origin'), config('resilience.WHITE_LIST_DOMAIN')))) {
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
