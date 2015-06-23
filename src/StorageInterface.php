<?php
namespace Tc\Phapic;


interface StorageInterface
{
    public function setTokens($accessToken, $expiresInSeconds, $refreshToken, $refreshExpiresInSeconds);
    public function getTokens();
}