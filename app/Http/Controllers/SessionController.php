<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class SessionController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        try
        {
            $this->validate($request, [
                'email' =>  'required|email',
                'password'  =>  'required',
            ]);

            $remember = (bool) $request->remember;

            if (Sentinel::authenticate($request->all(),$remember))
            {
                return Redirect::intended('/');
            }
            $errors = 'Incorrect login or password.';

            return Redirect::back()
                    ->withInput()
                    ->withErrors($errors);
        }

        catch (NotActivatedException $e)

        {
            $sentUser = $e->getUser();

            $activation = Activation::create($sentUser);

            $code = $activation->code;

            $sent = Mail::send('mail.account_activate', compact('sentuser', 'code'), function ($m) use ($sentUser)
            {
                $m->from('noreply@rukhsar.me', 'Laravel Site');
                $m->to($sentUser->email)->subject('Account Activation');
            });

            if ($sent === 0)
            {
                return Redirect::to('login')->withErrors('Error sending activation email.');
            }

            $errors = 'Error sending account activation email.';

            return view('auth.login')->withErrors($errors);
        }

        catch (ThrottlingException $e)
        {
            $delay = $e->getDelay();

            $errors = 'Your account is blocked for '.$delay.' seconds';
        }

        return Redirect::back()
            ->withInput()
            ->withErrors($errors);
    }

    public function logout()
    {
        Sentinel::logout();
        return Redirect::intended('/');
    }
}
