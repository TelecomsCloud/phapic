<?php
namespace Tc\Phapic;

use DateInterval;
use DateTime;
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

    protected $tokens;

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
     * @param string $accessToken
     * @param int $expiresInSeconds
     * @param string $refreshToken
     * @param int $refreshExpiresInSeconds
     *
     * @return bool result of update query
     */
    public function setTokens($accessToken, $expiresInSeconds, $refreshToken, $refreshExpiresInSeconds)
    {
        $now = new DateTime('now');

        $expiresDate = clone $now;
        $expiresDate->add(new DateInterval('PT' . $expiresInSeconds . 'S'));

        $expiresDate = $expiresDate->format('Y-m-d H:i:s');

        $refreshExpiresDate = clone $now;
        $refreshExpiresDate->add(new DateInterval('PT' . $refreshExpiresInSeconds . 'S'));
        $refreshExpiresDate = $refreshExpiresDate->format('Y-m-d H:i:s');

        $query = 'INSERT INTO ' . $this->tableName
            . ' SET `client_id` = :clientId,'
            . ' `client_secret` = :clientSecret,'
            . ' `access_token` = :accessToken,'
            . ' `expires_date` = :expiresDate,'
            . ' `refresh_token` = :refreshToken,'
            . ' `refresh_expires_date` = :refreshExpiresDate'
            . ' ON DUPLICATE KEY UPDATE'
            . ' `access_token` = :accessToken2,'
            . ' `expires_date` = :expiresDate2,'
            . ' `refresh_token` = :refreshToken2,'
            . ' `refresh_expires_date` = :refreshExpiresDate2';


        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':clientId', $this->clientId);
        $statement->bindParam(':clientSecret', $this->clientSecret);
        $statement->bindParam(':accessToken', $accessToken);
        $statement->bindParam(':expiresDate', $expiresDate);
        $statement->bindParam(':refreshToken', $refreshToken);
        $statement->bindParam(':refreshExpiresDate', $refreshExpiresDate);
        $statement->bindParam(':accessToken2', $accessToken);
        $statement->bindParam(':expiresDate2', $expiresDate);
        $statement->bindParam(':refreshToken2', $refreshToken);
        $statement->bindParam(':refreshExpiresDate2', $refreshExpiresDate);

        $result = $statement->execute();

        $this->tokens = [
            `access_token` => $accessToken,
            `expires_date` => $expiresDate,
            `refresh_token` => $refreshToken,
            `refresh_expires_date` => $refreshExpiresDate
        ];

        return $result;
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
        if (isset($this->tokens)) {
            return $this->tokens;
        }

        $query = 'SELECT `access_token`, `expires_date`, `refresh_token`, `refresh_expires_date`'
            . ' FROM ' . $this->tableName
            . ' WHERE `client_id` = :clientId';

        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':clientId', $this->clientId);

        if (!$statement->execute() || $statement->rowCount() !== 1) {
            return false;
        }

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        $this->tokens = $results;

        return $this->tokens;
    }
}