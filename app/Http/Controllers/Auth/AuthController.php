<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;

use Illuminate\Support\Facades\Password;
use Session;
use Hash;
use Exception;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('verifyEmail', 'verifyEmailRequest', 'verifyEmailNotice');
        $this->middleware('signed')->only('verifyEmail');
        $this->middleware('throttle:6,1')->only('verifyEmail', 'verifyResend','login', 'register');
    }

    public function loginForm()
    {
        if (Auth::check()) {
            return redirect('');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $login_type = filter_var($request->input('username_email'), FILTER_VALIDATE_EMAIL)? "email" : "username";
        
        $request->merge([
            $login_type => $request->input('username_email')
        ]);

        $request->validate([
            $login_type => 'required|max:255',
            'password' => 'required|max:255'
        ]);

        $credentials = $request->only($login_type, 'password');
        $remenber_me = $request->has('remember_me')? true : false;

        if (Auth::attempt($credentials, $remenber_me)) {
            $location = match(Auth::user()->role) {
                'admin' => '/admin/dashboard',
                'user' => '/user/dashboard',
                default => '/',
            };
            
            return redirect($location);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function registerForm()
    {
        if (Auth::check()) {
            return redirect('');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
         $request->validate([
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|max:255|confirmed',
            'password_confirmation' => 'required',
            'agree' => 'required'
        ]);

        $data = $request->all();
        $check = User::create([
            'firstname' => Str::ucfirst($data['firstname']),
            'lastname' => Str::ucfirst($data['lastname']),
            'username' => $data['email'],
            'email' => $data['email'],
            'role' => 'user',
            'password' => Hash::make($data['password']),
        ]);

        return redirect("login");
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function forgotPasswordForm(Request $request)
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink($request->only('email'));
        if($status !== Password::RESET_LINK_SENT){
            abort(500);
        }

        //clean old resets bad idea  use crons job
       // PasswordReset::where('created_at', '<=', Carbon::now()->subHours(24)->toDateTimeString())->delete();

        return back()->with('success', 'Password reset link has been sent successfully.');
    }

    public function resetPasswordForm(Request $request)
    {
        return view('auth.reset-password')->with('token', $request->token)
            ->with('email', $request->email);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
            'email' => 'required|email'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
     
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function verifyEmailNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()? redirect('/user/dashboard'): view('auth.verify-email');
    }

    public function verifyEmailRequest(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        
        return back()->with('success','A fresh verification link hasbeen sent to your emailaddress.');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/user/dashboard');
    }

    private function recaptchaCode()
    {
        /*TODO*/
    }
}
