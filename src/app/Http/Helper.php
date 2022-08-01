<?php

namespace App\Http;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\PasswordClientNotFoundException;

class Helper 
{
    /**
     * Validation Check
     * 
     * @param Array $input
     * @param Array $rules
     * @param Array $messages
     * 
     * @return Array
     */
    public function validator($input, $rules, $messages)
    {
        $result = [];
        $validator = Validator::make($input, $rules, $messages);

        // Check if the parameters passed are invalid
        if ($validator->fails()) {
            $result =  $validator->messages()->all();
        } 

        return $result;
    }

    /**
     * Check if the string is boolean
     * 
     * @param String $string
     * 
     * @return Boolean
     */
    public function isBool(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get Oauth Token
     * 
     * @param String $email
     * @param String $password 
     * 
     * @return JsonResponse
     */
    public function getToken($email, $password)
    {
        global $app; 

        $client = $this->getOauthClient();

        $proxy = Request::create(
            '/oauth/token',
            'POST', 
            [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'scope' => '*',
                'username' => $email,
                'password' => $password
            ]
        );

        $tokenResult = $app->dispatch($proxy)->getContent();
        $tokenResult = json_decode($tokenResult, true);
        return $tokenResult;
    }

    /**
     * Get the Oauth client
     * 
     * @return Object
     */
    public function getOauthClient()
    {
        $client = DB::table('oauth_clients')
                    ->where('name', "Lumen Password Grant Client")
                    ->first();

        if(!$client) {
            throw new PasswordClientNotFoundException();
        }

        return $client;
    }

    /**
     * One way hashing 
     * 
     * @param String $string
     * 
     * @return String
     */
    public function getHashedString(string $string)
    {
        return Hash::make($string);
    }
}