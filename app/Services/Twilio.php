<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
require_once(__DIR__ . '/../../vendor/autoload.php');

use Twilio\Rest\Client;


class Twilio
{
    public function send($merchant){
// Your Account SID and Auth Token from console.twilio.com
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $client = new Client($sid, $token);

        try {
            // Use the Client to make requests to the Twilio REST API
            $client->messages->create(
            // The number you'd like to send the message to
                $merchant->phone,
                [
                    // A Twilio phone number you purchased at https://console.twilio.com
                    'from' => env('TWILIO_FROM_NUMBER'),
                    // The body of the text message you'd like to send
                    'body' => "Hey $merchant->name! your Otp is $merchant->otp!"
                ]
            );
        }catch (TwilioException $e){

            Log::alert($e->getMessage());
        }
    }
}
