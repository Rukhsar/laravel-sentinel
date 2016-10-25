<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class CustomerUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();
        $customer = Sentinel::findRoleByName('user');
        if (!$user->inRole($customer)) {
            return redirect('login');
        }
        return $next($request);
    }
}
