<?php

namespace App\Controller\Component;

use App\Model\Entity\User;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

class AuthenticationComponent extends Component
{
    /**
     * Return content of a token
     *
     * @return array
     */
    public function getTokenContent(string $token): array
    {
        $tokenParts = explode('.', $token);
        return json_decode(base64_decode($tokenParts[1]), true);
    }

    /**
     * Checks if a password and a hashed password match.
     *
     * @param string $password
     * @param string $hashed_password
     * @return boolean
     */
    public function passwordVerify(string $password, string $hashed_password): bool
    {
        return password_verify($password, $hashed_password);
    }

    /**
     * Return hashed password.
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        if (!preg_match('^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$^', $password))
            throw new BadRequestException('Mot de passe non conforme.');

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Transforms a payload into a JWT token.
     *
     * @param User $user
     * @param array $payload
     * @return string
     */
    public function generateJwt(User $user, array $payload = []): string
    {
        $payload = array_merge($this->generageTokenContent($user), $payload);
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
        if (count($tokenParts) !== 3) {
            return false;
        }

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

    /**
     * Generation of the token content.
     *
     * @param User $user
     * @return array
     */
    public function generageTokenContent(User $user): array
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->get($user->user_id, [
            'fields' => ['user_id', 'uid', 'is_admin'],
            'contain' => [
                'Churches' => [
                    'fields' => ['church_id', 'uid', 'name'],
                ]
            ]
        ]);
        $churches = $user->churches;

        foreach ($churches as $church) {
            unset($church->church_id);
            unset($church->_joinData);
        }

        unset($user->churches);
        unset($user->user_id);

        $payload['exp'] = time() + 3600;
        $payload['user'] =  $user;
        $payload['churches'] =  $churches;

        return $payload;
    }
}
