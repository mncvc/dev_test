<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $strPath = trim($request->getPathInfo());
        $rgPath = explode('/',$strPath);
        $strThisURI = substr($strPath,0,strrpos($strPath,'/'));

        if(count($rgPath) > 3 && $rgPath[count($rgPath) - 1 ] != 'excel'){
            $strPath = $strThisURI;
        }

        $rgFreePage = [
            '/home',
            '/admin/change_password'
        ];

        if(Auth::check() == false){

            return redirect('login');
        }

//        if($rgPath[1] == 'login'){
//            return redirect('admin');
//        }


        return $next($request);
    }
}
