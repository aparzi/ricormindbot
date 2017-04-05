<?php

/**
 * Description of ObjectManager
 *
 * @author angelo
 */
require_once './storage/DBproperties.php';

class ObjectManager {

    /**
     *
     * La funzione salva l'operazione scelta dall'utente, in questo caso l'operazione oggetto.
     *
     * @param type $pIdUser
     * @param type $pName
     * @param type $pSurname
     * @param type $pOperation
     * @return boolean
     */
    function saveOperation($pIdUser, $pName, $pSurname, $pOperation) {
        $db = new DBproprierties();
        $conn = $db->getConnection();
        if (!$conn) {
            FunctionalityBot::sendMessage("Connection failed: " . mysqli_connect_error());
        }
        $sql = "INSERT INTO users (id_user, firstName, lastName, operazione, conclusa) VALUES ($pIdUser, '$pName', '$pSurname', '$pOperation', 'false')";

        if (mysqli_query($conn, $sql)) {
            return TRUE;
        } else {
            return FALSE;
        }
        $db->closeConnection();
    }

    public function insertObject($pIdUser, $pUpdates) {
        $db = new DBproprierties();
        $conn = $db->getConnection();
        $sql = 'SELECT * FROM oggetti WHERE posizione <=> NULL';

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0) {
            $sql = "INSERT INTO oggetti (nome, id_user) VALUES ('" . $pUpdates['message']['text'] . "', $pIdUser)";
            if (mysqli_query($conn, $sql)) {
                FunctionalityBot::sendMessage("Inserisci la posizione in cui stai mettendo l'oggetto");
                return TRUE;
            } else {
                FunctionalityBot::sendMessage("Ho riscontrato un problema");
                return TRUE;
            }
        } else {
            $sql = "UPDATE oggetti SET posizione = '" . $pUpdates['message']['text'] . "' WHERE posizione <=> NULL and id_user = $pIdUser";
            if (mysqli_query($conn, $sql)) {
                $date = Date::getCurrentDate();
                $sql = "UPDATE users SET conclusa='true', interazione = '$date' WHERE id_user= $pIdUser and conclusa = 'false'";
                if (mysqli_query($conn, $sql)) {
                    FunctionalityBot::removeKeyboard("Ho memorizzato la posizione dell' oggetto. Sei in una botte di ferro " . json_decode('"' . Emoticon::grin() . '"'));
                    return TRUE;
                } else {
                    FunctionalityBot::sendMessage("Ho riscontrato un errore riprovare");
                    return TRUE;
                }
            } else {
                FunctionalityBot::sendMessage("Ho riscontrato un errore riprovare");
                return TRUE;
            }
        }
    }

    /**
     *
     * La funzione permette di annullare l'operazione oggetto all'utente
     *
     * @param type $pIdUser
     * @return boolean
     */
    public function cancelOperation($pIdUser) {
        $db = new DBproprierties();
        $conn = $db->getConnection();

        $sql = "DELETE FROM users WHERE id_user = $pIdUser AND conclusa = 'false'";

        if (mysqli_query($conn, $sql)) {
            $sql = "DELETE FROM oggetti WHERE id_user = $pIdUser AND posizione <=> NULL";
            if (mysqli_query($conn, $sql)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
