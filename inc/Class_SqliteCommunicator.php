<?php
/**
 * @TODO Create more other functionality based off Class_JournalCommunicator.php which extends this class
 * Class Class_SqliteCommunicator
 * Created for PHP TechDegree UNIT 03
 */
class Class_SqliteCommunicator
{
    private $pdo; // PDO object
    private $dsn = NULL;
    private $dbUser = NULL;
    private $dbPass = NULL;

    /**
     * Class_SqliteConnection constructor. Pass user and pass if needed or set later
     * @param string $dbUser
     * @param string $dbPass
     */

    public function __construct($dbUser = NULL, $dbPass = NULL)
    {
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
    }

    /**
     * Set PDO object
     * @param string $dsn - Overrides object dsn
     * @return boolean TRUE if succesful FALSE if failure
     */
    public function setPdoConnection(){
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
     * @param $sql - the sql string
     * @param $args - array of argument values for sql
     * @return mixed
     */
    public function prepareAndExecuteStatement( $sql, $args){
        try {
            if ($this->pdo !== NULL):
                $prepared = $this->pdo->prepare($sql);
            else:
                $this->setPdoConnection();
                $prepared = $this->pdo->prepare($sql);
            endif;
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
        // Execute statement and return array index 0 = TRUE or FALSE & index 1 = PDOStatement Object
        return [$prepared->execute($args), $prepared];
    }

    /**
     * @param $id
     * @param $table
     * Query db for a single row in associative format using entryID passed
     * @return array entryData
     */
    public function getAssocRowById($id, $table)
    {
        $sql = "SELECT * FROM $table WHERE id = ?";
        $pdoSObj = $this->prepareAndExecuteStatement($sql, [$id]);
        $data = $pdoSObj[1]->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Delete row
     * @param $entryID
     */
    public function deleteRow($id, $table){
        $sql = "DELETE FROM $table WHERE id = ?";
        $this->prepareAndExecuteStatement(
            $sql,
            [$id]
        );
        // Redirect to detail page using newly inserted id
        header("Location: ./index.php");
    }
}