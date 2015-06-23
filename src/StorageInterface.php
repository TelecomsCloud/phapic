<?php
namespace Tc\Phapic;


interface StorageInterface
{
    public function setTokens($accessToken, $expiresDate, $refreshToken, $refreshExpiresDate);
    public function getTokens();
}