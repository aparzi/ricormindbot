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

        $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND nome = '$pObject' and cancellato <=> NULL";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0) {

            //$sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND nome like '%$pObject%' and cancellato <=> NULL";
            $pObjectSplit = array();
            $pObjectSplit = explode(" ", $pObject);
            $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND nome like '%$pObjectSplit[0]%' and cancellato <=> NULL";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 0) {
                $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
                if (mysqli_query($conn, $sql)) {
                    FunctionalityBot::removeKeyboard("L'oggetto <b>$pObject</b> non esiste. Ti consiglio di usare il comando /lista per vedere gli oggetti inseriti " . json_decode('"' . Emoticon::grin() . '"'));
                    return TRUE;
                } else {
                    FunctionalityBot::sendMessage("Ho riscontrato un errore riprovare");
                    return TRUE;
                }
            } else {
                $hints = array();
                for ($index1 = 0; $index1 < mysqli_num_rows($result); $index1++) {
                    $row = mysqli_fetch_array($result);
                    $hints[$index1] = $row['nome'];
                }
                array_push($hints, json_decode('"' . Emoticon::cross() . '"') . " Annulla");
                FunctionalityBot::sendMessageKeyboardMarkup("L'oggetto <b>$pObject</b> non esiste. Ho questi oggetti simili, se desideri cancellare uno di essi cliccaci sopra altrimenti scrivi un oggetto", $hints);
                return TRUE;
                
            }

//            $result = mysqli_query($conn, $sql);
//            if (mysqli_num_rows($result) == 0) {
//                $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
//                if (mysqli_query($conn, $sql)) {
//                    FunctionalityBot::removeKeyboard("L'oggetto <b>$pObject</b> non esiste. Ti consiglio di usare il comando /lista per vedere gli oggetti inseriti " . json_decode('"' . Emoticon::grin() . '"'));
//                } else {
//                    FunctionalityBot::sendMessage("Ho riscontrato un errore riprovare");
//                    return TRUE;
//                }
//            } else {
//                // mi creo un array di suggerimenti
//                $hints = array();
//                for ($index = 0; $index < mysqli_num_rows($result); $index++) {
//                    $row = mysqli_fetch_array($result);
//                    $hints[$index] = $row['nome'];
//                }
//                FunctionalityBot::sendMessageKeyboardMarkup("L'oggetto scritto non esiste. Ho questi oggetti simili, se desideri cancellare uno di essi cliccaci sopra altrimenti scrivi un oggetto", $hints);
//            }
        } else {
            $sql = "UPDATE oggetti SET cancellato='true' WHERE id_user = $pIdUser and nome = '$pObject'";
            if (mysqli_query($conn, $sql)) {
                $sql = "UPDATE users SET conclusa='true' WHERE id_user= $pIdUser and conclusa = 'false'";
                if (mysqli_query($conn, $sql)) {
                    FunctionalityBot::removeKeyboard("L'oggetto è stato cancellato, non ricordo più la sua posizione. " . json_decode('"' . Emoticon::grin() . '"'));
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
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
