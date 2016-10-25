<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use App\Http\Requests;

class PasswordController extends Controller
{
  use ResetsPasswords;

  protected $redirectPath = 'login';

  protected $subject = "Your Password Reset Link for website.com";

  public function __construct()
  {
    $this->middleware('guest');
  }

  public function getEmail()
  {
    return view('auth.passwords.email');
  }

  public function postEmail(Request $request)
  {
    $this->validate($request, ['email' => 'required|email']);

    $response = Password::sendResetLink($request->only('email'), function (Message $message) {
      $message->subject($this->getEmailSubject());
    });

    switch ($response)
    {
      case Password::RESET_LINK_SENT:
        return redirect()->back()->with('flash_message', trans($response));
      case Password::INVALID_USER:
        return redirect()->back()->withErrors(['email'  =>  trans($response)]);
    }
  }

  public function getReset($token = null)
  {
    if (is_null($token))
    {
      throw new NotFoundHttpException;
    }
    return view('auth.passwords.reset')->with('token',$token);
  }

  public function postReset(Request $request)
  {
    $this->validate($request, [
          'token' =>  'required',
          'email' =>  'required|email',
          'password'  =>  'required|confirmed',
    ]);

    $credentials = $request->only (
          'email','password','password_confirmation','token'
      );

    $response = Password::reset($credentials, function ($user, $password) {
        $this->resetPassword($user, \Hash::make($password));
    });

    switch ($response)
    {
      case PASSWORD::PASSWORD_RESET:
        return redirect('/login')->withFlashMessage('Password Reset Successfully!');

      default:
        return redirect()->back()
            ->withInput($request->only('email'))
              ->withErrors(['email'  =>  trans($response)]);

    }
  }

  public function resetPassword($user, $password)
  {
    $user->password = $password;
    $user->save();

    // Auth::Login($user);
  }

}
