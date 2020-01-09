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
     * Set PDO object
     * @param string $dsn - Overrides object dsn
     * @return boolean TRUE if succesful FALSE if failure
     */
    public function setPdo(){
            try {
                // Ensure only 1 connection
                if ($this->pdo === NULL) {
                    // Anonymous Connection
                    if ($this->dbUser === NULL && $this->dbPass === NULL) {
                        $this->pdo = new PDO("sqlite:".$this->dsn);
                        // Thanks Jen
                        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                        $this->pdo->exec( 'PRAGMA foreign_keys = ON;' );
                    }
                    // User and Pass provided
                    else{
                        $this->pdo = new PDO($this->dsn, $this->dbUser, $this->dbPass);
                        // Thanks Jen
                        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                        $this->pdo->exec( 'PRAGMA foreign_keys = ON;' );
                    }
                }
            }
            catch (PDOException $e){
                // @TODO- Email this message failure notice to admin from $e->getMessage();
                // Tell user to contact admin
                echo "Unable to connect, please contact the administrator.";
                exit;
            }
    }

    /**
     * @return PDO
     */
    public function getPdo(){
        return $this->pdo;
    }

    /**
     * @return null
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param null $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return string|null
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @param string|null $dbUser
     */
    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    /**
     * @return string|null
     */
    public function getDbPass()
    {
        return $this->dbPass;
    }

    /**
     * @param string|null $dbPass
     */
    public function setDbPass($dbPass)
    {
        $this->dbPass = $dbPass;
    }



    /**
     * Class_SqliteConnection constructor. Pass user and pass if needed.
     * @param string $dbUser
     * @param string $dbPass
     */

    public function __construct($dbUser = NULL, $dbPass = NULL)
    {
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
    }

    /**
     * @param $sql - the sql string
     * @param $args - array of argument values for sql
     * @return mixed
     */
    public function prepareAndExecuteStatement( $sql, $args){
        try {
            if ($this->getPdo() !== NULL):
                $prepared = $this->getPdo()->prepare($sql);
            else:
                $this->pdo = $this->getPdo();
                $prepared = $this->getPdo()->prepare($sql);
            endif;
        }
        catch (Exception $e){
            echo $e->getMessage();
        }
        // Execute statement and return array index 0 = TRUE or FALSE & index 1 = PDOStatement Object
        return [$prepared->execute($args), $prepared];
    }
}