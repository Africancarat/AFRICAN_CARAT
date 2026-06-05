<?php

namespace App\Http\Middleware;

use Closure;

class Demo
{
    public function handle($request, Closure $next)
    {
        // if request type is post, put and delete, then return error with session flash message
        if (config('services.demo.enabled')) {

            // check route name
            if ($request->route()->getName() == 'back.login.submit') {
                return $next($request);
            }

            if($request->route()->getName() == 'back.system.backup' || $request->route()->getName() == 'back.database.backup') {
                return redirect()->back()->with('error', 'This action is not allowed in demo mode');
            }

            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
                return redirect()->back()->with('error', 'This action is not allowed in demo mode');
            }
        }

        return $next($request);
        
    }
}
