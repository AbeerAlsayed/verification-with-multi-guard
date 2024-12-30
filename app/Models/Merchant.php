<?php

namespace App\Models;

use App\Notifications\MerchantVerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class Merchant extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public function sendEmailVerificationNotification(){
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
    protected $guarded=['id'];
}
