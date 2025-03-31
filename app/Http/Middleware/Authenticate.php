<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
//    protected function redirectTo($request)
//    {
//        if (! $request->expectsJson()) {
//            return route('admin.login');
//        }
//    }

    public function redirectTo($request)
    {
           $session = $request->session();
           if(empty($request->session()->get('rgLogin')) ){
               return route('admin.login');
           }


    }

}
