<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomVerificationTokenController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user('merchant')->hasVerifiedEmail()
            ?to_route('merchant.index')
            : view('merchant.auth.verify-email');
    }

    public function verify(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return redirect()->intended(route('merchant.index').'?verified=1');
        }

        if ($request->user('merchant')->markEmailAsVerified()) {
            event(new Verified($request->user('merchant')));
        }

        return redirect()->intended(route('merchant.index').'?verified=1');
    }

    public function resend(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return to_route('merchant.index');
        }

        $request->user('merchant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
