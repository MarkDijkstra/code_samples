<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
         $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    
    /**
     * Method verify
     *
     * @param Integer $user_id
     * @param Request $request
     *
     * @return void
     */
    public function verify($user_id, Request $request) 
    {
        $user = User::findOrFail($user_id);

        if (!$request->hasValidSignature()) {
            return response()->json([
                "status" => "Failed",
                "email"  => $user->email ?? null
            ], 401);
        }    

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Method resend
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function resend(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (is_null($user)) {
            $status = 'error';
        } elseif ($user->hasVerifiedEmail()) {
            $status = 'verified';
        } else {
            $user->sendEmailVerificationNotification();
            $status = 'resend';
        }

        return response()->json(['status' => $status]);
    }
}
