<?php
namespace Tc\Phapic;

interface StorageInterface
{
    public function setToken($accessToken, $expiresDate);
    public function getToken();
}
