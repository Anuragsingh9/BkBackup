<?php

namespace Modules\Newsletter\Http\Middleware;

use App\AccountSettings;
use Closure;
use Illuminate\Http\Request;

class NewsModeration {
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next) {
        $enable = AccountSettings::where('account_id', '=', 1)->first();
        if (isset($enable->setting['news_moderation_enable']) && isset($enable->setting['news_letter_enable'])
            && $enable->setting['news_moderation_enable'] == 1 && $enable->setting['news_letter_enable'] == 1) {
            return $next($request);
        }
        return response()->json(['status' => false, 'msg' => 'Unauthorized Action'], 401);
    }
}
