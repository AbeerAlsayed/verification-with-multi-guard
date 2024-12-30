<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class passwordlessAuthController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'email'=>['required','email']
        ]);
        $merchant=Merchant::where('email',$request->email)->first();

        if (!$merchant){
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $merchant->sendEmailVerificationNotification();

        return back()->with('status','Link sent to your email');
    }
    public function verify($id){
        Auth::guard('merchant')->loginUsingId($id);
        return to_route('merchant.index');
    }
}
