<?php

namespace core\crypto;

require_once "InvalidTokenException.php";

const secret = 'test';

class JwtToken
{

    private array $payload;
    private \DateTime $expiration;

    public function __construct(array $payload, \DateTime $expiration)
    {
        $this->payload = $payload;
        $this->expiration = $expiration;
    }

    public static function fromString(string $token, \DateTime $now): JwtToken
    {
        $parts = explode('.', $token, 3);
        if (count($parts) != 3)
            throw new InvalidTokenException();

        $header = $parts[0];
        $payload = $parts[1];
        
        $decodedSignature = JwtToken::decodeString($parts[2]);
        
        if ($decodedSignature != hash_hmac('sha256', "$header.$payload", secret))
            throw new InvalidTokenException();

        $jsonPayload = json_decode(JwtToken::decodeString($parts[1]), true);
        
        $expires = new \DateTime($jsonPayload['expires']);
        if($now > $expires)
            throw new InvalidTokenException();

        return new JwtToken($jsonPayload['data'], $expires);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function toToken(): string
    {
        $header = $this->encodeString('{"typ":"JWT","alg":"HS256"}');
        $payload = $this->encodeString(json_encode([
            'expires' => $this->expiration->format('Y-m-d H:i:s'),
            'data' => $this->payload
        ]));
        $signature = $this->encodeString(hash_hmac('sha256', "$header.$payload", secret));

        return "$header.$payload.$signature";
    }

    private static function encodeString(string $str): string
    {
        return str_replace("=", "", base64_encode($str));
    }

    private static function decodeString(string $str): string
    {
        return base64_decode($str);
    }

    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }

    public function setExpiration(\DateTime $expiration): void
    {
        $this->expiration = $expiration;
    }


}