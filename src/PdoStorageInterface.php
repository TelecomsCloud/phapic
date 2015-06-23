<?php
namespace Tc\Phapic;

use PDO;

class PdoStorageInterface implements StorageInterface
{
    protected $clientId;
    protected $clientSecret;

    /**
     * @var PDO $pdo
     */
    protected $pdo;
    protected $tableName;

    /**
     * @param string $clientId Registered client ID
     * @param string $clientSecret Registered client secret
     * @param PDO $pdo Database connection
     * @param string $tableName Database table name
     */
    public function __construct($clientId, $clientSecret, PDO $pdo, $tableName = 'tc_api_credentials')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tableName = $tableName;
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function createTable()
    {
        try {
            $query = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' ('
                . '`client_id` varchar(80) NOT NULL,
                   `client_secret` varchar(80) NOT NULL,
                   `access_token` varchar(40) NOT NULL,
                   `expires_date` datetime NOT NULL,
                   `refresh_token` varchar(40) NOT NULL,
                   `refresh_expires_date` datetime NOT NULL,
                   PRIMARY KEY (`client_id`)
                   ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';

            $this->pdo->exec($query);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * setTokens
     *
     * Commit tokens to persistent storage
     *
     * @param string $clientId
     * @param string $accessToken
     * @param int $expiresInSeconds
     * @param string $refreshToken
     * @param int $refreshExpiresInSeconds
     *
     * @return bool result of update query
     */
    public function setTokens($clientId, $accessToken, $expiresInSeconds, $refreshToken, $refreshExpiresInSeconds)
    {
        $query = 'UPDATE ' . $this->tableName
            . ' SET `access_token` = :accessToken,'
            . ' `expires_date` = :expiresInSeconds,'
            . ' `refresh_token` = :refreshToken,'
            . ' `refresh_expires_date` = :refreshExpiresInSeconds'
            . ' WHERE `client_id` = :clientId';

        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':accessToken', $accessToken);
        $statement->bindParam(':expiresInSeconds', $expiresInSeconds);
        $statement->bindParam(':refreshToken', $refreshToken);
        $statement->bindParam(':refreshExpiresInSeconds', $expiresInSeconds);
        $statement->bindParam(':clientId', $clientId);

        return $statement->execute();
    }


    /**
     * getTokens
     *
     * Retrieve tokens from persistent storage
     *
     * @return bool|mixed access credentials if found, else false
     */
    public function getTokens()
    {
        $query = 'SELECT `access_token`, `expires_date`, `refresh_token`, `refresh_expires_date`'
            . ' FROM ' . $this->tableName
            . ' WHERE `client_id` = :clientId';

        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':clientId', $clientId);

        if (!$statement->execute() || $statement->rowCount() !== 1) {
            return false;
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}