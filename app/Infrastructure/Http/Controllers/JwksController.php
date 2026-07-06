<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrastructure\Http\Controllers\Controller;
use Laravel\Passport\Passport;

class JwksController extends Controller
{
    public function getCerts() : JsonResponse{

        //Caminho da chave publica
        $publicKeyPath = storage_path('oauth-public.key');
        if( !file_exists($publicKeyPath)){
            return response() ->json (['error' => 'Public key file not found'], 500);
        }

        $publicKeyString = file_grt_contents($publicKeyPath);

        // LÃƒÆ’Ã‚Âª os detalhes estruturais da chave RSA
        $res = openssl_pkey_get_public($publicKeyPath);
        if(!$res){
            return response()->json(['error' => 'Invalid public key format.'], 500);
        }

        $details = openssl_pkey_get_details($res);

        //Extrai 'n' (modulo) e 'e' (exponente) da chave RSA
        $modulus = $this->base64UrlEncode($details['rsa']['n']);
        $exponent = $this->base64UrlEncode($details['rsa']['e']);

        //ID ÃƒÆ’Ã‚Âºnico do key
        $jwks =[
            'keys' =>[
                [
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'use' => 'sig',
                    'kid' => 'velo-active-key', // Chave identificadora ÃƒÆ’Ã‚Âºnica (Key ID)
                    'n'   => $modulus,
                    'e'   => $exponent,
                ]
            ]
        ];

        return response() ->json($jwks);
    }
        private function base64UrlEncode(string $data): string
        {
            return str_replace(['+','/'],['-','_',''], base64_encode($data));
        }
}
