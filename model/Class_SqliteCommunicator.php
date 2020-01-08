<?php
/**
 * Class Class_SqliteCommunicator
 */
class Class_SqliteCommunicator
{
    private $pdo; // PDO object
    private $dsn = NULL;
    private $dbUser = NULL;
    private $dbPass = NULL;

    /**
     * Class_SqliteConnection constructor.
     * @param string $dsn
     * @param string $dbUser
     * @param string $dbPass
     */
    public function __construct($dsn, $dbUser = NULL, $dbPass = NULL)
    {
        $this->dsn = $dsn;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
    }

    /**
     * Set PDO object
     * @return boolean TRUE if succesful FALSE if failure
     */
    public function setPdo(){
        rfd_debugger($this->dsn);
        try {
            if ($this->dbUser === NULL && $this->dbPass === NULL) {
                if ($this->pdo === NULL) {
                    $this->pdo = new PDO(
                        'sqlite::' . $this->dsn,
//                                            'sqlite::memory:',
//                        null,
                        null
                    );
                }
            }
            else {
                $this->pdo = new PDO(
                    'sqlite::' . $this->dsn,
                    $this->dbUser,
                    $this->dbPass
                );
            }
            return TRUE;
        }
        catch (PDOException $e){
            echo $e->getMessage();
            return FALSE;
        }
    }

    public function getPdo(){
        return $this->pdo;
    }

    /**
     * @param $sql - the sql string
     * @param $args - array of argument values for sql
     * @param PDO $PDOobj - PDO connection object
     * @return mixed
     */
    public function prepareAndExecuteStatement( $sql, $args, $PDOobj = NULL ){
        try {
            if ($PDOobj !== NULL):
                $prepared = $PDOobj->prepare($sql);
            else:
                $PDOobj = $this->getPdo();
                $prepared = $PDOobj->prepare($sql);
            endif;
        }
        catch (Exception $e){
            echo $e->getMessage();
        }
        // Execute statement and return array index 0 = TRUE or FALSE & index 1 = PDOStatement Object
        return [$prepared->execute($args), $prepared];
    }
}