<?php

/*
 * This file controls who do not interact with the bot for a long time 
 */
define('BOT_TOKEN', '304058486:AAEb-CnOf7vJnt_7ZE5d1BOzUc89lbweARA');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
require_once '../storage/DBproperties.php';
require_once '../utils/Date.php';

$dateToday = Date::getCurrentDate();
$db = new DBproprierties();
$conn = $db->getConnection();
$sql = "SELECT id_user, firstName, max(interazione) as max_date FROM `users` group by id_user";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) != 0) {
    while ($row = mysqli_fetch_assoc($result)) {
    	$dateToday = date_create(date('Y-m-d'));
        $lastInteraction = date_create($row['max_date']);
        $diff = date_diff($lastInteraction, $dateToday);
        (int)$controlDays = $diff->format('%a');
        if ($controlDays >= 7) {
            $url = API_URL . "sendMessage?chat_id=".$row['id_user']."&text=" . urlencode("Ciao " . $row['firstName'] . " come mai non mi usi più? Se non ti sono utile puoi suggerire nuove funzionalità al mio capo: https://www.angeloparziale.it/");
            file_get_contents($url);
        }
    }
}
