<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class AuthenticationComponent extends Component
{
    /**
     * Checks if a password and a hashed password match.
     *
     * @param string $password
     * @param string $hashed_password
     * @return boolean
     */
    public function password_verify(string $password, string $hashed_password): bool
    {
        return password_verify($password, $hashed_password);
    }

    /**
     * Transforms a payload into a JWT token.
     *
     * @param array $payload
     * @return string
     */
    public function generateJwt(array $payload = []): string
    {
        $payload['exp'] = time() + 3600;
        $headers_encoded = $this->base64urlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload_encoded = $this->base64urlEncode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", Configure::read('Security.salt'), true);
        $signature_encoded = $this->base64urlEncode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    /**
     * Checks if a token is good or not.
     *
     * @param string $jwt
     * @return boolean
     */
    public function checkJwt(string $jwt): bool
    {
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $base64_url_header = $this->base64urlEncode($header);
        $base64_url_payload = $this->base64urlEncode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, Configure::read('Security.salt'), true);
        $base64_url_signature = $this->base64urlEncode($signature);

        return !$is_token_expired && ($base64_url_signature === $signature_provided);
    }

    /**
     * Returns the string encoded in base 64.
     *
     * @param string $chain
     * @return string
     */
    private function base64urlEncode(string $chain): string
    {
        return rtrim(strtr(base64_encode($chain), '+/', '-_'), '=');
    }
}
