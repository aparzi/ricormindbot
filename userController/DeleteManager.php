<?php

/**
 * Description of DeleteManager
 *
 * @author angelo
 */
class DeleteManager {

    /**
     * 
     * La funzione salva l'operazione scelta dall'utente, in questo caso l'operazione elimina.
     * 
     * @param type $pIdUser
     * @param type $pName
     * @param type $pSurname
     * @param type $pOperation
     * @return boolean
     */
    public function saveOperation($pIdUser, $pName, $pSurname, $pOperation) {
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

    public function deleteObject($pIdUser, $pObject) {
        $db = new DBproprierties();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND nome = '$pObject'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0) {
            $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
            FunctionalityBot::removeKeyboard("L'oggetto $pObject non esiste. Ti consiglio di usare il comando /lista per vedere gli oggetti inseriti " . json_decode('"' . Emoticon::grin() . '"'));
        } else {
            $sql = "UPDATE oggetti SET cancellato='true' WHERE id_user = $pIdUser and nome = '$pObject'";
            if (mysqli_query($conn, $sql)) {
                $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
                if (mysqli_query($conn, $sql)) {
                    FunctionalityBot::removeKeyboard("L'oggetto è stato cancellato, non ricordo più la sua posizione. " . json_decode('"' . Emoticon::grin() . '"'));
                    return TRUE;
                } else {
                    $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
                    FunctionalityBot::sendMessage("Ho riscontrato un errore riprovare");
                    return TRUE;
                }
            } else {
                $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
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
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
