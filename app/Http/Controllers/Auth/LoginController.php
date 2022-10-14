<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Student;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\General;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use General;
    protected function showLoginForm()
    {
        $data['pageTitle'] = 'Login';
        return view('auth.login', $data);
    }
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        /*
        role 2 = instructor
        role 3 = student
        -----------------
        status 1 = Approved
        status 2 = Blocked
        status 0 = Pending
        */
        if (Auth::attempt($credentials)) {
            if (Auth::user()->role == 3 && Auth::user()->student->status == 2){
                Auth::logout();
                $this->showToastrMessage('error', 'Your account has been blocked!');
                return redirect("login");
            }

            if (Auth::user()->role == 2 && Auth::user()->student->status == 2 && Auth::user()->instructor->status == 2){
                Auth::logout();
                $this->showToastrMessage('error', 'Your account has been blocked!');
                return redirect("login");
            }
            if (get_option('registration_email_verification') == 1){
                $user = Auth::user()->hasVerifiedEmail();
                if (!$user){
                    Auth::logout();
                    $this->showToastrMessage('error', 'Your email is not verified!');
                    return redirect("login");
                }
            }

            if (Auth::user()->is_admin())
            {
                return redirect(route('admin.dashboard'));

            } else {
                return redirect(route('main.index'));
            }
        }

        $this->showToastrMessage('error', 'Ops! You have entered invalid credentials');
        return redirect("login");
    }

    //Google login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    // Google callback
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        $this->_registerOrLoginUser($user);

        // Return home after login
        return redirect()->route('main.index');
    }

    //Facebook login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    // Facebook callback
    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();

        $this->_registerOrLoginUser($user);

        // Return home after login
        return redirect()->route('main.index');
    }

    //Twitter login
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }
    // Twitter callback
    public function handleTwitterCallback()
    {
        $user = Socialite::driver('twitter')->user();

        $this->_registerOrLoginUser($user);

        // Return home after login
        return redirect()->route('main.index');
    }

    protected function _registerOrLoginUser($data)
    {
        $user = User::where('email', '=', $data->email)->first();
        if (!$user) {
            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->provider_id = $data->id;
            $user->avatar = $data->avatar;
            $user->role = 3;
            $user->email_verified_at = now();
            $user->save();

            $full=$data->name;
            $full1=explode(' ', $full);
            $first=$full1[0];
            $rest=ltrim($full, $first.' ');

            $student  = new Student();
            $student->user_id = $user->id;
            $student->first_name = $first;
            $student->last_name = $rest;
            $student->save();
        }

        Auth::login($user);
    }

    public function forgetPassword()
    {
        $data = array();
        return view('auth.forgot', $data);
    }

    public function forgetPasswordEmail(Request $request)
    {
        $email = $request->email;

        $user = User::whereEmail($email)->first();
        if ($user)
        {
            $verification_code = rand(10000, 99999);
            if ($verification_code)
            {
                $user->forgot_token = $verification_code;
                $user->save();
            }

            try {
                Mail::to($user->email)->send(new ForgotPasswordMail($user, $verification_code));
            } catch (\Exception $exception) {
                toastrMessage('error', 'Something is wrong. Try after few minutes!');
                return redirect()->back();
            }

            Session::put('email', $email);
            Session::put('verification_code', $verification_code);

            $this->showToastrMessage('success', 'Verification code sent your email. Please check.');
            return redirect()->route('reset-password');
        }

        $this->showToastrMessage('error', 'Your Email is incorrect!');
        return redirect()->back();
    }

    public function resetPassword()
    {

        return view('auth.reset-password');
    }

    public function resetPasswordCheck(Request $request)
    {
        $request->validate([
            'verification_code' => 'required',
        ]);

        $email = Session::get('email');
        $verification_code = Session::get('verification_code');
        if ($request->verification_code == $verification_code)
        {
            $user = User::whereEmail($email)->whereForgotToken($verification_code)->first();

            if (!$user) {
                $this->showToastrMessage('error', 'Your verification code is incorrect!');
            } else {
                $request->validate([
                    'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'min:6'
                ]);

                $user->password = Hash::make($request->password);
                $user->email_verified_at = now();
                $user->forgot_token = null;
                $user->save();
                Session::put('email', '');
                Session::put('verification_code', '');
                $this->showToastrMessage('success', 'Successfully changed your password.');
                return redirect()->route('login');
            }
        } else {
            $this->showToastrMessage('error', 'Your verification code is incorrect!');
        }


        return redirect()->back();
    }
}
