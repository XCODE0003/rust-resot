<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\UserVerify;
use Illuminate\Support\Str;
use Mail;

class EmailVerifyController extends Controller
{
    public function notice() {
        return view('auth.verify');
    }

    public function resend(Request $request): RedirectResponse
    {
        // $request->user()->sendEmailVerificationNotification();
        
		$token = Str::random(64);
		
        $data = UserVerify::create([
            'user_id' => $request->user()->id, 
            'token' => $token,
        ]);
		
		$email = $data->user['email'];
		
		Mail::send('emails.emailVerificationEmail', ['token' => $token], function($message) use($email) {
            $message->to($email);
            $message->subject('Email Verification Mail');
        });
		
		return back()->with('status', 'verification-link-sent');
    }

    public function verify($token): RedirectResponse
    {
		$verifyUser = UserVerify::where('token', $token)->first();
    
        if($verifyUser && !$verifyUser->user['email_verified_at']){
            $verifyUser->user->update([
				'email_verified_at' => now(),
			]);
			
			$verifyUser->delete();
        }
		
        /*
			if (!hash_equals((string) $request->route('id'),
				(string) $request->user()->getKey())) {
				abort(403);
			}

			if (!hash_equals((string)$request->route('hash'),
				sha1($request->user()->getEmailForVerification()))) {
				abort(403);
			}

			$request->user()->markEmailAsVerified();

			event(new Verified($request->user()));
		*/

        return redirect()->route('cabinet');
    }
}
