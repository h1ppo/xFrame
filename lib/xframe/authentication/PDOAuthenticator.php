<?php
namespace xframe\authentication;
use \PDO;

/**
 * A PDO implementation of the Authenticator
 */
class PDOAuthenticator implements Authenticator {

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $identityColumn;

    /**
     * @var string
     */
    protected $credentialColumn;

    /**
     * @var \Closure
     */
    protected $credentialCheck;

    /**
     * @var Result
     */
    protected $result;

    /**
     *
     * @param \PDO $pdo
     * @param string $table
     * @param string $identityColumn
     * @param string $credentialColumn
     */
    public function __construct($pdo, $table, $identityColumn, $credentialColumn, \Closure $credentialCheck = null) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->identityColumn = $identityColumn;
        $this->credentialColumn = $credentialColumn;
        $this->credentialCheck = $credentialCheck;
        $this->result = new Result();
    }

    /**
     * @param string $identity
     * @param string $credential
     * @return \xframe\authentication\Result
     */
    public function authenticate($identity, $credential) {

        try {
            $dbResult = $this->fetchDbResult($identity);
            $this->processDbResult($dbResult, $credential);

        } catch (\Exception $ex) {
            $this->result->setCode(Result::GENERAL_FAILURE);
            $this->result->setMessages(
                array(
                    "code" => $ex->getCode(),
                    "message" => $ex->getMessage(),
                    "file" => $ex->getFile(),
                    "line" => $ex->getLine(),
                    "trace" => $ex->getTraceAsString()
                )
            );
        }
        return $this->result;
    }

    /**
     * Query a database for a specific identity
     * @param string $identity
     * @return array
     */
    protected function fetchDbResult($identity) {
        $stmt = $this->pdo->prepare("SELECT
                                        `{$this->identityColumn}`,
                                        `{$this->credentialColumn}`
                                    FROM `{$this->table}`
                                    WHERE
                                        `{$this->identityColumn}` = :identity");
        $stmt->bindParam(":identity", $identity);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }

    /**
     * Processes the result from the db and assigns approritate codes to the
     * authentication
     * @param array $result
     * @param array $credential
     */
    protected function processDbResult($result, $credential) {
        $num_results = count($result);
        if ($num_results == 0) {
            $this->result->setCode(Result::IDENTITY_NOT_FOUND);
        }
        else if ($num_results > 1) {
            $this->result->setCode(Result::AMBIGUOUS_IDENTITY);
        }
        else if ($this->credentialCheck != null) {
            $credentialCheck = $this->credentialCheck;
            if (!$credentialCheck($credential, $result[0]->{$this->credentialColumn})) {
                $this->result->setCode(Result::INVALID_CREDENTIAL);
            } else {
                $this->result->setCode(Result::SUCCESS);
            }
        }
        else if ($result[0]->{$this->credentialColumn} != $credential) {
            $this->result->setCode(Result::INVALID_CREDENTIAL);
        } else {
            $this->result->setCode(Result::SUCCESS);
        }
    }

}

