<?php
namespace Tc\Phapic;


interface StorageInterface
{
    public function setTokens($clientId, $accessToken, $expiresInSeconds, $refreshToken, $refreshExpiresInSeconds);
    public function getTokens();
}