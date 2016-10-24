<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Http\Request;
use App\Http\Requests;
use Redirect;
use Sentinel;
use Activation;
use Reminder;
use Validator;
use Mail;
use Storage;
use CurlHttp;

class RegistrationController extends Controller
{
    public function getRegister()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
//        $this->validate($request, [
//            'email' => 'required|email',
//            'password' => 'required',
//            'password_confirm' => 'required|same:password',
//        ]);

        $input = $request->all();

        $credentials = [ 'email' => $request->email ];

        if($user = Sentinel::findByCredentials($credentials))
        {
            return Redirect::to('register')
                ->withErrors('This Email is already registered.');
        }

        if ($sentuser = Sentinel::register($input))
        {
            $activation = Activation::create($sentuser);
            $code = $activation->code;
            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function($m) use ($sentuser)
            {
                $m->from('noreply@rukhsar.me', 'LaravelSite');
                $m->to($sentuser->email)->subject('Account Activation');
            });
            if ($sent === 0)
            {
                return Redirect::to('register')
                    ->withErrors('Sending activation error.');
            }
            $role = Sentinel::findRoleBySlug('user');
            $role->users()->attach($sentuser);
            return Redirect::to('login')
                ->withSuccess('Your account has been created. Check Email to activate.')
                ->with('userId', $sentuser->getUserId());
        } else
            {
            return Redirect::to('register')
                ->withInput()
                ->withErrors('Failed to register.');
            }
    }

    public function activate($id, $code)
    {
        $sentuser = Sentinel::findById($id);
        if ( ! Activation::complete($sentuser, $code))
        {
            return Redirect::to("login")
                ->withErrors('Invalid or expired activation code.');
        }
        return Redirect::to('login')
            ->withSuccess('Account activated.');
    }


}
