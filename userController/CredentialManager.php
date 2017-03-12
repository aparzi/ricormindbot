<?php
/**
 * Description of CredentialManager
 *
 * @author angelo
 */
require_once './storage/DBproperties.php';

class CredentialManager {

  public function getUsername($pName) {
      $pseudoString = openssl_random_pseudo_bytes(3);
      $pseudoString = bin2hex($pseudoString);
      $username =  $pseudoString . $pName;
      return $username;
  }

  public function getPassword() {
    $pseudoString = openssl_random_pseudo_bytes(7);
    return bin2hex($pseudoString);
  }

  public function saveCredentials($pUsername, $pPwd, $pIdUser) {
    $db = new DBproprierties();
    $conn = $db->getConnection();

    $sql = "INSERT INTO credenziali (username, password, id_user) VALUES ('$pUsername', '$pPwd', $pIdUser)";
    if(mysqli_query($conn, $sql)) {
      $db->closeConnection();
      return TRUE;
    } else {
      $db->closeConnection();
      return FALSE;
    }
  }

  /**
  * Functione che controlla se l'utente ha già ricevuto le sue credenziali.
  */
  public function checkCredentials($pIdUser) {
    $db = new DBproprierties();
    $conn = $db->getConnection();

    $sql = "SELECT * FROM credenziali WHERE id_user = $pIdUser";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}
?>
