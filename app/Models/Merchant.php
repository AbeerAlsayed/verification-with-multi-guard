<?php

namespace App\Models;

use App\Notifications\MerchantVerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Merchant extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public function sendEmailVerificationNotification(){
        if (config('verification.way') == 'email'){
            $url=URL::temporarySignedRoute(
                'merchant.verification.verify',
                now()->addMinutes(60),
                [
                    'id'=>$this->getKey(), // getKey the same  id
                    'hash'=> sha1($this->getEmailForVerification()) // getEmailForVerification() the same Email
                ]
            );
            $this->notify(new MerchantVerificationEmail($url));
        }
        if (config('verification.way') == 'cvt'){
            $this->generateVerificationToken();
            $url=route('merchant.verification.verify',[
                'id'=>$this->getKey(),
                'token'=>$this->verification_token,
            ]);
            $this->notify(new MerchantVerificationEmail($url));
        }
        if (config('verification.way') == 'passwordless'){
            $url=URL::temporarySignedRoute(
                'merchant.login.verify',
                now()->addMinutes(60),
                [
                    'id'=>$this->getKey(), // getKey the same  id
                    'hash'=> sha1($this->getEmailForVerification()) // getEmailForVerification() the same Email
                ]
            );
            $this->notify(new MerchantVerificationEmail($url));
        }
    }

    //=============================================== Custom Verification Token
   public function generateVerificationToken(){
        if (config('verification.way')== 'cvt'){
            $this->verification_token=Str::random(40);
            $this->verification_token_till =now()->addMinutes(10);
            $this->save();
        }
   }

    public function verifyUsingVerificationToken(){
        if (config('verification.way')== 'cvt'){
            $this->email_verified_at=now();
            $this->verification_token=null;
            $this->verification_token_till =null;
            $this->save();
        }
    }
    //=============================================== Custom Verification Token


    //=============================================== OTP
    public function generateOtp(){
        if (config('verification.way')== 'otp'){
            $this->otp=rand(111111,999999);
            $this->otp_till =now()->addMinutes(1);
            $this->save();
        }
    }

    public function resentOtp(){
        if (config('verification.way')== 'otp'){
            $this->otp=null;
            $this->otp_till =null;
            $this->save();
        }
    }
    //=============================================== OTP
    protected $guarded=['id'];
}
