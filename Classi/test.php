<?php
// File utile a testare le funzionalità del progetto in ambiente PHPStorm
require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');
require_once(realpath(dirname(__FILE__)) . '/GeneratoreProspetti.php');
require_once(realpath(dirname(__FILE__)) . '/GestioneCarrieraStudente.php');
require_once(realpath(dirname(__FILE__)) . '/AccessoProspetti.php');
require_once(realpath(dirname(__FILE__)) . '/GestoreEmail.php');

$prospetto = new GeneratoreProspetti();
$prospetto->creaProspetti("123456\n345678", 'T. Ing. Informatica', '04/01/2023');
//$accesso = new AccessoProspetti();
//$path = $accesso->apriProspetti();
//$mail = new GestoreEmail();
//$mail->inviaEmail("123456\n345678", 'T. Ing. Informatica', '15/01/2024');


?>