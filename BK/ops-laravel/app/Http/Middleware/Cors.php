<?php
    
    namespace App\Http\Middleware;
    
    use Closure;
    
    class Cors
    {
        /**
         * Handle an incoming request.
         *
         * @param \Illuminate\Http\Request $request
         * @param \Closure $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
           // var_export($request->header('origin'));
            if (in_array($request->header('origin'), config('constants.WHITE_LIST_DOMAIN'))) {
                
                return $next($request)
                    // ->header('Access-Control-Allow-Origin', 'file://')
//            ->header('Access-Control-Allow-Origin', '*')
                    // ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Origin', $request->header('origin'))
//            ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
                    // ->header('Access-Control-Allow-Origin', 'http://192.168.1.32:5000')
                    // ->header('Access-Control-Allow-Origin', 'http://localhost:18788')
                    // ->header('Access-Control-Allow-Origin', 'http://localhost:9000')
                    
                    ->header('Access-Control-Allow-Credentials', 'true')
                  //  ->header('Access-Control-Allow-Origin', '*')
//                  ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization')
                    ->header('Access-Control-Allow-Headers', 'Content-Type,User-Id,API-Token,Origin,X-XSRF-TOKEN,ops_session,Accept,Authorization,authorization,X-Auth-Token')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE,OPTIONS')
                    ->header('X-Requested-With', 'XMLHttpRequest');
                return $next($request);
            } else if( $this->checkPattern($request->header('origin')) ) { // to check by regex pattern of url
                return $next($request)
                    ->header('Access-Control-Allow-Origin', $request->header('origin'))
                    ->header('Access-Control-Allow-Credentials', 'true')
                    ->header('Access-Control-Allow-Headers', 'Content-Type,User-Id,API-Token,Origin,X-XSRF-TOKEN,ops_session,Accept,Authorization,authorization,X-Auth-Token')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE,OPTIONS')
                    ->header('X-Requested-With', 'XMLHttpRequest');
            } else {
                return $next($request);
                // ->header('Access-Control-Allow-Origin', 'file://')
//            ->header('Access-Control-Allow-Origin', '*')
                // ->header('Access-Control-Allow-Origin', '*')
//                ->header('Access-Control-Allow-Origin', 'http://carte.projectdevzone.com')

//            ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
                // ->header('Access-Control-Allow-Origin', 'http://192.168.1.32:5000')
                // ->header('Access-Control-Allow-Origin', 'http://localhost:18788')
                // ->header('Access-Control-Allow-Origin', 'http://localhost:9000')

//                ->header('Access-Control-Allow-Credentials', 'true')
//                ->header('Access-Control-Allow-Headers', 'Content-Type,User-Id,API-Token,Origin,X-XSRF-TOKEN,ops_session,Accept')
//                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE,OPTIONS');
            }
            
        }
    
        /**
         * @param string $subject // subject origin to check
         *
         * @return boolean
         */
        protected function checkPattern($subject)
        {
            $whitelist = config('constants.WHITE_LIST_DOMAIN_PATTERN', []);
            foreach ($whitelist as $pattern) {
                if(preg_match($pattern, $subject)) {
                    return true;
                }
            }
            return false;
        }
    }
