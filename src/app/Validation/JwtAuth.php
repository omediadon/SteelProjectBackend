<?php

namespace App\Validation;

use Cake\Chronos\Chronos;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use function is_array;

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
	public function getLifetime(): int{
		return $this->lifetime;
	}

	/**
	 * Create JSON web token.
	 *
	 * @param array $context
	 * @param bool  $forRefresh Can this token be used to refresh another token
	 *
	 * @return string The jwt
	 */
	public function createJwt(array $context, bool $forRefresh = false): string{
		$secret_Key = $_ENV['JWT_SECRET'];
		$now        = Chronos::now();
		$issuedAt   = $now->getTimestamp();
		$expire_at  = $now->addSeconds($this->lifetime)
						  ->getTimestamp();
		if($forRefresh){
			$expire_at = $now->addWeek(2)
							 ->getTimestamp();
		}
		$request_data = [
			'iat'     => $issuedAt,
			'iss'     => $this->issuer,
			'nbf'     => $issuedAt,
			'exp'     => $expire_at,
			'context' => $context,
		];

		if($forRefresh){
			$request_data['context']['canRefresh'] = true;
		}

		return JWT::encode($request_data, $secret_Key, 'HS512');
	}

	/**
	 * Validate the access token.
	 *
	 * @param string $accessToken The JWT
	 *
	 * @return bool The status
	 */
	public function validateToken(string $accessToken): bool{
		$secret_Key = $_ENV['JWT_SECRET'];
		$now        = Chronos::now()
							 ->getTimestamp();
		try{
			$token = JWT::decode($accessToken, new Key($secret_Key, 'HS512'));
		}
		catch(Exception $e){
			return false;
		}

		if($token->iss !== $this->issuer || $token->nbf > $now || empty($token->context) || $token->exp < $now ||
		   !is_array($token->context)){
			return false;
		}

		return true;
	}
}
