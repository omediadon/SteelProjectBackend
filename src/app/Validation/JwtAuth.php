<?php

namespace App\Validation;

use Cake\Chronos\Chronos;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

final class JwtAuth{

	/**
	 * The constructor.
	 *
	 * @param string $issuer   The issuer name
	 * @param int    $lifetime The max lifetime
	 */
	public function __construct(private string $issuer, private int $lifetime){
	}

	/**
	 * Get JWT max lifetime.
	 *
	 * @return int The lifetime in seconds
	 */
	public function getLifetime() : int{
		return $this->lifetime;
	}

	/**
	 * Create JSON web token.
	 *
	 * @param array $context
	 *
	 * @return string The jwt
	 * @throws UnexpectedValueException
	 *
	 */
	public function createJwt(array $context) : string{
		$secret_Key   = $_ENV['JWT_SECRET'];
		$now          = Chronos::now();
		$issuedAt     = $now->getTimestamp();
		$expire_at    = $now->addSeconds($this->lifetime)
							->getTimestamp();
		$request_data = [
			'iat'     => $issuedAt,
			'iss'     => $this->issuer,
			'nbf'     => $issuedAt,
			'exp'     => $expire_at,
			'context' => $context,
		];

		return JWT::encode($request_data, $secret_Key, 'HS512');
	}

	/**
	 * Validate the access token.
	 *
	 * @param string $accessToken The JWT
	 *
	 * @return bool The status
	 */
	public function validateToken(string $accessToken) : bool{
		$secret_Key = $_ENV['JWT_SECRET'];
		$now        = Chronos::now()->getTimestamp();
		try{
			$token = JWT::decode($accessToken, new Key($secret_Key, 'HS512'));
		}
		catch(Exception $e){
			return false;
		}

		if($token->iss !== $this->issuer || $token->nbf > $now || $token->exp < $now){
			return false;
		}

		return true;
	}
}