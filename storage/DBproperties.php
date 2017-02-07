<?php

define("DB_SERVER", "localhost");
define("DB_USERNAME", "gmonuments");
define("DB_PASSWORD", "fuvsacedbu95");
define("DB_DATABASE", "my_gmonuments");

/**
 * Description of DBproprierties
 *
 * This class describes information about MySql Data Base Configuration
 * 
 * @author Angelo
 */
class DBproprierties {

    private $dbConn;

    /**
     * Effettua la connessione al database
     * @return type
     */
    public function getConnection() {
        $whitelist = array('127.0.0.1', "::1");
        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            $this->dbConn = mysqli_connect("localhost", "root", "", "remindBot");
        } else {
            $this->dbConn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        }
        
        return $this->dbConn;
    }

    /**
     * Chiude la connessione con il database
     */
    public function closeConnection() {

        unset($this->dbConn);
    }

}
