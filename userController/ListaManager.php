<?php

/**
 * Description of ListaManager
 *
 * @author angelo
 */
require_once './storage/DBproperties.php';

class ListaManager {

    public function printAllObject($pIdUser) {
        $db = new DBproprierties();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND cancellato <=> NULL";

        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            FunctionalityBot::sendMessage("Non ho nessuno oggetto da mostrarti " . json_decode('"' . Emoticon::rage() . '"'));
        } else {
            for ($index = 0; $index < mysqli_num_rows($result); $index++) {
                $row = mysqli_fetch_array($result);
                $messaggio = "<b>Oggetto:</b> " . $row['nome']
                        . "\n<b>Posizione:</b> " . $row['posizione'] . "\n\n";
                FunctionalityBot::sendMessage($messaggio);
            }
        }
    }

    public function getAllObject($pIdUser) {
        $db = new DBproprierties();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND cancellato <=> NULL";
        $result = mysqli_query($conn, $sql);
        $arrayObject = array();
        if (mysqli_num_rows($result) == 0) {
            return $arrayObject;
        } else {
            for ($index = 0; $index < mysqli_num_rows($result); $index++) {
                $row = mysqli_fetch_array($result);
                $arrayObject[$index] = $row['nome'];
            }
            return $arrayObject;
        }
    }

    public function getAllPosition($pIdUser) {
      $db = new DBproprierties();
      $conn = $db->getConnection();

      $sql = "SELECT * FROM oggetti WHERE id_user = $pIdUser AND cancellato <=> NULL";
      $result = mysqli_query($conn, $sql);
      $arrayObject = array();
      if (mysqli_num_rows($result) == 0) {
          return $arrayObject;
      } else {
          for ($index = 0; $index < mysqli_num_rows($result); $index++) {
              $row = mysqli_fetch_array($result);
              if (!in_array($row['posizione'], $arrayObject)) {
                  array_push($arrayObject, $row['posizione']);
              }
          }
          return $arrayObject;
      }
    }

}
