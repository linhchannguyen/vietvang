<?php

namespace App\Http\Middleware;

use App\Http\Models\Admin;
use Closure;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IsAdmin
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
        if(!Session::get('login', true))
        {
            $errors = new MessageBag(['errors' => 'Vui lòng đăng nhập lại!']);
            return redirect('/login')->withInput()->withErrors($errors);
        }

        $role = Session::get('role');
        $email = Session::get('email');
        $ad = Admin::where('email', $email)->first();
        if(!$ad)
        {
            $errors = new MessageBag(['errors' => 'Bạn không phải Admin, vui lòng đăng nhập lại!']);
            return redirect('/login')->withInput()->withErrors($errors);
        }
        if($ad['role'] != 1)
        {
            $errors = new MessageBag(['errors' => 'Bạn không phải Admin!']);
            return redirect('/login')->withInput()->withErrors($errors);
        }
        if($ad['token'] == null)
        {
            $errors = new MessageBag(['errors' => 'Vui lòng đăng nhập lại!']);
            return redirect('/login')->withInput()->withErrors($errors);
        }
        // if(Auth::check())
        // {
        //     $user = \auth()->user();
        //     if($user) {
        //         if($user->role != 1)
        //         {
        //             $errors = new MessageBag(['errors' => 'Bạn không phải Admin!']);
        //             return redirect('/login')->withInput()->withErrors($errors);
        //         }
        //     }
        // }    
        return $next($request);
    }
}
