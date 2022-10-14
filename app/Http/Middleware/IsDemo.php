<?php

namespace App\Http\Middleware;

use App\Traits\General;
use Closure;
use Illuminate\Http\Request;

class IsDemo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    use General;
    public function handle(Request $request, Closure $next)
    {
        if(env('APP_DEMO') == 1){
            $this->showToastrMessage('error', 'This is a demo version! You can get full access after purchasing the application.');
            return redirect()->back();
        }

        return $next($request);
    }
}
