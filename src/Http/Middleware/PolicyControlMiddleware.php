<?php

namespace iProtek\PolicyControl\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use iProtek\PolicyControl\Models\PolicyControl;

class PolicyControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public function handle($request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $next($request);
        }

        

        // api.post.add
        //[$domain, $resource, $action] = explode('.', $routeName);
        $user = $request->attributes->get('user');

        if (!$user) {
            abort(401, 'Login Required');
        }

        //Policy existing
        $policy = PolicyControl::where('name', $routeName)->first();
        if(!$policy){
            return $next($request);
        }

        //TODO:: CHECK USER ROLE AND CUSTOMIZATION
        




        if($policy->default_is_allow){
            return $next($request);
        }


        // Laravel policy check
        abort(403, "Not Allowed.");
    }
}
