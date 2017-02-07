<?php

/**
 * Description of FunctionalityBot
 *
 * @author angelo
 */
class FunctionalityBot {

    public static function sendMessage($pText) {
        $updates = file_get_contents("php://input");
        $updates = json_decode($updates, TRUE);
        $url = API_URL . "sendMessage?parse_mode=HTML&chat_id=" . $updates['message']['chat']['id'] . "&text=" . urlencode($pText);
        file_get_contents($url);
    }

    public static function sendMessageKeyboardMarkup($pMessage, array $pKeyboard) {
        $updates = file_get_contents("php://input");
        $updates = json_decode($updates, TRUE);

        $keyboard = array('keyboard' => array($pKeyboard), 'resize_keyboard' => true);
        $keyboard = '&reply_markup=' . json_encode($keyboard) . '';
        $url = 'sendMessage?parse_mode=HTML&chat_id=' . $updates['message']['chat']['id'] . '&text=' . urlencode($pMessage) . $keyboard;

        $request = new HttpRequest("get", API_URL . $url);
        $this->logError($request);
    }
    
    public static function removeKeyboard($pMessage) {
        $updates = file_get_contents("php://input");
        $updates = json_decode($updates, TRUE);
        $url = 'sendMessage?parse_mode=HTML&chat_id=' . $updates['message']['chat']['id'] . '&text='. urlencode($pMessage) .'&reply_markup={"remove_keyboard":true}';
        $request = new HttpRequest("get", API_URL  . $url);
        $this->logError($request);
    }
    
    private function logError($pRequest) {

        $response = $pRequest->getResponse();
        $data = json_decode($response, true);
        $ok = $data["ok"]; //false
        if ($ok == 0) {
            $error = $data["error_code"];
            switch ($error) {
                case 403:
                    //imposta che tale utente ha disattivato il bot.                        
                    break;
                case 400:
                default:
                    $errorFile = "../utils/errors.txt";
                    if (!file_exists($errorFile)) {
                        $eF = fopen($errorFile, "wr");
                        fclose($eF);
                    }
                    $errorCurrent = file_get_contents($errorFile);
                    $errorCurrent .= date("d/m/Y H:i:s / ");
                    $errorCurrent .= $data["description"];
                    $errorCurrent .= "\n";
                    file_put_contents($errorFile, $errorCurrent);
                    break;
            }
        }
    }

}
