<?php
    
    namespace Modules\Events\Http\Middleware;
    
    use Closure;
    use Illuminate\Http\Request;
    use Modules\Resilience\Services\ResilienceService;
    
    class SaveUserMeta
    {
        public function __construct()
        {
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
            $service = ResilienceService::getInstance();
            $service->saveMetaData($request);
            return $next($request);
        }
    }
