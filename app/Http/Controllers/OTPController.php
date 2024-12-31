<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\Twilio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OTPController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'email'=>['required','email']
        ]);
        $request->phone='+963934973180';
        $merchant=Merchant::where('email',$request->email)->first();

        if (!$merchant){
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $merchant->generateOtp();

        (new Twilio())->send($merchant);

        return view('merchant.auth.verify-otp',['email'=>$request->email]);
    }
    public function verify(Request $request){
        $request->validate([
            'email'=>['required','email'],
            'otp'=>['required']
        ]);
        $merchant=Merchant::where('email',$request->email)->first();

        if (!$merchant){
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($merchant && $merchant->otp == $request->otp){
            if (now()< $merchant->otp_till){
                $merchant->resentOtp();
                Auth::guard('merchant')->login($merchant);
            return to_route('merchant.index');
            }else{
                throw ValidationException::withMessages([
                    'email' => 'expired OTP',
                ]);
            }
        }
    }
}
