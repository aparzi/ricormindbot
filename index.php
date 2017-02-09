<?php

require_once './webservice/class-http-request.php';
require_once './storage/DBproperties.php';
require_once './botController/FunctionalityBot.php';
require_once './utils/Emoticon.php';
require_once './userController/ObjectManager.php';
require_once './userController/ListaManager.php';
require_once './userController/DeleteManager.php';

define('BOT_TOKEN', '304058486:AAEb-CnOf7vJnt_7ZE5d1BOzUc89lbweARA');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

$updates = file_get_contents("php://input");
$updates = json_decode($updates, TRUE);

//$updates = array(
//    "update_id" => 624792369,
//    "message" => array(
//        "message_id" => 1270,
//        "from" => array(
//            "id" => 29508113,
//            "first_name" => "Angelo",
//            "username" => "Parziale"
//        ),
//        "chat" => array(
//            "id" => 52149724,
//            "first_name" => "Angelo",
//            "username" => "Parziale",
//            "type" => "private"
//        ),
//        "date" => 1482502325,
//        "text" => "test",
//        "entities" => array(
//            "type" => "bot_command",
//            "offset" => 0,
//            "length" => 9
//        )
//    )
//);
//
//$privateMessage = array(
//    "update_id" => 143859697,
//    "callback_query" => array(
//        "id" => "223981359509260125",
//        "from" => array(
//            "id" => 52149724,
//            "first_name" => "Angelo",
//            "username" => "aparzi"
//        ),
//        "message" => array(
//            "message_id" => 11517,
//            "from" => array(
//                "id" => 216729606,
//                "first_name" => "Json Dump Bot",
//                "username" => "Json Dump Bot"
//            ),
//            "chat" => array(
//                "id" => 52149724,
//                "first_name" => "Angelo",
//                "last_name" => "Parziale",
//                "username" => "aparzi",
//                "type" => "private"
//            ),
//            "date" => 1486591858,
//            "text" => "Message with inline keyboard",
//        ),
//        "chat_instance" => "-7630782695419680086",
//        "data" => "Some data"
//    )
//);




$om = new ObjectManager();
$dm = new DeleteManager();
$callback_query = $updates['callback_query'];
switch ($callback_query['data']) {
    case 'ita':
        $url = API_URL . "sendMessage?parse_mode=HTML&chat_id=" . $callback_query['message']['chat']['id'] . "&text=" . urlencode("lingua italiano");
        file_get_contents($url);
        break;
    
    case 'eng':
        $url = API_URL . "sendMessage?parse_mode=HTML&chat_id=" . $callback_query['message']['chat']['id'] . "&text=" . urlencode("lingua inglese");
        file_get_contents($url);
        break;

    default :
        $text = $updates['message']['text'];
        break;
}

switch ($text) {

    case '/restart':
        FunctionalityBot::sendMessage("Ciao " . $updates['message']['from']['first_name'] . " sono un bot che ti aiuta a ricordare dove metti i tuoi oggetti, se non sai come usarmi ricordati"
                . " di eseguire il comando /help. Buon divertimento." . json_decode('"' . Emoticon::smiley() . '"') . json_decode('"' . Emoticon::smiley() . '"'));
        break;

    case '/start':
        FunctionalityBot::sendMessage("Ciao " . $updates['message']['from']['first_name'] . " sono un bot che ti aiuta a ricordare dove metti i tuoi oggetti, se non sai come usarmi ricordati"
                . " di eseguire il comando /help. Buon divertimento." . json_decode('"' . Emoticon::smiley() . '"') . json_decode('"' . Emoticon::smiley() . '"'));
        break;

    case 'test':
        $keyboardInline = array(
            array(
                "text" => "". json_decode('"' . Emoticon::flagIt() . '"'). " Italiano",
                "callback_data" => "ita"
            ),
            array(
                "text" => "". json_decode('"' . Emoticon::flagIt() . '"')." Inglese",
                "callback_data" => "eng"
            )
        );
        FunctionalityBot::sendMessageInlineKeyboard("Seleziona la lingua", $keyboardInline);
        break;

    case '/oggetto':
        $result = $om->saveOperation($updates['message']['from']['id'], $updates['message']['from']['first_name'], $updates['message']['from']['last_name'], 'oggetto');
        if ($result == TRUE) {
            FunctionalityBot::sendMessageKeyboardMarkup("Inserisci l'oggetto da salvare", array(json_decode('"' . Emoticon::cross() . '"') . " Annulla"));
        } else {
            FunctionalityBot::sendMessage("Si è verificato un errore riprovare");
        }
        break;

    case '/lista':
        $lm = new ListaManager();
        $lm->printAllObject($updates['message']['from']['id']);
        break;

    case '/elimina':
        $result = $dm->saveOperation($updates['message']['from']['id'], $updates['message']['from']['first_name'], $updates['message']['from']['last_name'], 'elimina');
        if ($result == TRUE) {
            FunctionalityBot::sendMessageKeyboardMarkup("Inserisci l'oggetto che vuoi eliminare", array(json_decode('"' . Emoticon::cross() . '"') . " Annulla"));
        } else {
            FunctionalityBot::sendMessage("Si è verificato un errore riprovare");
        }
        break;

    case '/help':
        $message = json_decode('"' . Emoticon::openBook() . '"') . " <b>Comandi</b>: \n"
                . "/oggetto: questo comando ti consente di far ricordare un oggetto al bot. \n"
                . "/lista: questo comando mostra tutti gli oggetti ricordati dal bot compreso il luogo in cui essi si trovano. \n"
                . "/elimina: questo comando ti consente di far dimenticare un oggetto al bot, in questo modo lui non ricorderà l'oggetto dove si trova. \n\n"
                . "Cosa aspetti ancora? Ora che hai visto come funziono prova ad usarmi, e ricorda che la memorira è una cosa FONDAMENTALE " . json_decode('"' . Emoticon::wave() . '"');
        FunctionalityBot::sendMessage($message);
        break;

    default:
        $result = checkOperation($updates['message']['from']['id']);
        if ($result == FALSE) {
            FunctionalityBot::sendMessage("Seleziona un comando grazie. Se non sai come funziono puoi eseguire il comando /help.");
        }
        break;
}

function checkOperation($pIdUser) {
    $db = new DBproprierties();
    $conn = $db->getConnection();

    $sql = "SELECT * FROM users WHERE id_user = $pIdUser AND conclusa = 'false'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        return FALSE;
    } else {
        $details = mysqli_fetch_array($result);
        switch ($details['operazione']) {
            case 'oggetto':
                global $text;
                global $updates;
                // l'utente annulla l'azione
                if ($text === json_decode('"' . Emoticon::cross() . '"') . ' Annulla') {
                    global $om;
                    if ($om->cancelOperation($updates['message']['chat']['id'])) {
                        FunctionalityBot::removeKeyboard("L' azione è stata annullata " . json_decode('"' . Emoticon::check() . '"'));
                    } else {
                        FunctionalityBot::removeKeyboard("Ho riscontrato un errore " . json_decode('"' . Emoticon::danger() . '"'));
                    }
                } else {
                    global $om;
                    $result = $om->insertObject($pIdUser, $updates);
                    return $result;
                }
                break;

            case 'elimina':
                global $text;
                global $updates;
                if ($text === json_decode('"' . Emoticon::cross() . '"') . ' Annulla') {
                    global $dm;
                    if ($dm->cancelOperation($updates['message']['chat']['id'])) {
                        FunctionalityBot::removeKeyboard("L' azione è stata annullata " . json_decode('"' . Emoticon::check() . '"'));
                    } else {
                        FunctionalityBot::removeKeyboard("Ho riscontrato un errore " . json_decode('"' . Emoticon::danger() . '"'));
                    }
                } else {
                    global $dm;
                    $result = $dm->deleteObject($pIdUser, $text);
                    return $result;
                }
                break;
        }
    }
}
