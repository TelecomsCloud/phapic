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

    protected $token;

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
                   PRIMARY KEY (`client_id`)
                   ) ENGINE=InnoDB DEFAULT CHARSET=latin1;';

            $this->pdo->exec($query);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * setToken
     *
     * Commit token to persistent storage
     *
     * @param string $accessToken
     * @param string $expiresDate
     * @return bool result of update query
     *
     */
    public function setToken($accessToken, $expiresDate)
    {
        $query = 'INSERT INTO ' . $this->tableName
            . ' SET `client_id` = :clientId,'
            . ' `client_secret` = :clientSecret,'
            . ' `access_token` = :accessToken,'
            . ' `expires_date` = :expiresDate'
            . ' ON DUPLICATE KEY UPDATE'
            . ' `access_token` = :accessToken2,'
            . ' `expires_date` = :expiresDate2';


        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':clientId', $this->clientId);
        $statement->bindParam(':clientSecret', $this->clientSecret);
        $statement->bindParam(':accessToken', $accessToken);
        $statement->bindParam(':expiresDate', $expiresDate);

        $statement->bindParam(':accessToken2', $accessToken);
        $statement->bindParam(':expiresDate2', $expiresDate);

        $result = $statement->execute();

        $this->token = [
            'access_token' => $accessToken,
            'expires_date' => $expiresDate
        ];

        return $result;
    }


    /**
     * getToken
     *
     * Retrieve token from persistent storage
     *
     * @return bool|mixed access credentials if found, else false
     */
    public function getToken()
    {
        if (isset($this->token)) {
            return $this->token;
        }

        $query = 'SELECT `access_token`, `expires_date`'
            . ' FROM ' . $this->tableName
            . ' WHERE `client_id` = :clientId';

        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':clientId', $this->clientId);

        if (!$statement->execute() || $statement->rowCount() !== 1) {
            return false;
        }

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        if ($statement->rowCount() < 1) {
            return false;
        }

        $this->token = $results;

        return $this->token;
    }
}
