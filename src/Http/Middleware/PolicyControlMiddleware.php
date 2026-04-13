<?php

namespace iProtek\PolicyControl\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        Log::error($user);

        if (!$user) {
            abort(401, 'Login Required');
        }

        // Convert resource → Model
        //$model = 'App\\Models\\' . ucfirst($resource);

        //if (!class_exists($model)) {
        //    abort(403, "Model not found for policy");
       // }

        // Laravel policy check
        //if (!$user->can($action, $model)) {
            abort(403, "Unauthorized via policy");
        //}

        return $next($request);
    }
}
