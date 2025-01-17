<?php
//File per testare la correttezza dei dati generati
require_once '../Classi/CarrieraLaureando.php';
require_once '../Classi/GestioneCarrieraStudente.php';
require_once '../Classi/LaureandoIngegneriaInformatica.php';

$gestoreCarriere = new GestioneCarrieraStudente('./');
$string = file_get_contents("./Test.json");
$json_string = json_decode($string, true);
$datiTest = $json_string;
$string = file_get_contents("../File_Configurazione/CdL_Dati.json");
$corsiJSON = json_decode($string, true);
$corsiDiLaurea = $corsiJSON;
    
for ($i = 0; $i < sizeof($datiTest); $i++) {
    $matricola = $datiTest["laureando" . $i]["matricola"];
    $CdL = $datiTest["laureando" . $i]["cdl"];
    if (!isset($corsiDiLaurea[$CdL])) { // se il CdL non è presente nel file "info-CdL.json", l'automation test deve fallire. Guardasi caso in cui CdL è uguale a "M. Cybersecurity"
        echo "Corso di laurea " . $CdL . " non presente nel file corsi!" . "\n";
        continue;
    }

    $anagrafica = $gestoreCarriere-> restituisciAnagraficaStudente($matricola);
    $carriera =  $gestoreCarriere-> restituisciCarrieraStudente($matricola);

    if ($CdL == "T. Ing. Informatica") {
        $annoImmatricolazione = $datiTest["laureando" . $i]["anno_immatricolazione"];
        $annoChiusura = $datiTest["laureando" . $i]["anno_chiusura"];
        $Laureando = new LaureandoIngegneriaInformatica($anagrafica, $carriera, $CdL, "2023-01-04", $annoImmatricolazione);
    } else {
        $Laureando = new CarrieraLaureando($anagrafica, $carriera, $CdL, "2023-01-04");
    }

    $nome =  $datiTest["laureando" . $i]["nome"];
    $cognome = $datiTest["laureando" . $i]["cognome"];
    $media = $datiTest["laureando" . $i]["media_pesata"];
    $creditiInMedia = $datiTest["laureando" . $i]["crediti_media"];
    $creditiCurriculariConseguiti = $datiTest["laureando" . $i]["crediti_curriculari_conseguiti"];

    echo $nome . " " . $cognome . "\n";
    echo "Nome: ";
    if($nome == $Laureando->getNome())
        echo "\033[32m Test Passato \033[0m" . "\n";
    else
        echo "\033[31m Test Fallito \033[0m" . "\n";

    echo "Cognome: ";
    if($cognome == $Laureando->getCognome())
        echo "\033[32m Test Passato \033[0m" . "\n";
    else
        echo "\033[31m Test Fallito \033[0m" . "\n";

    echo "Media: ";
    if($media == $Laureando->getMedia())
        echo "\033[32m Test Passato \033[0m" . "\n";
    else
        echo "\033[31m Test Fallito \033[0m" . "\n";

    echo "CFU Media: ";
    if($creditiInMedia == $Laureando->getTotCfuMedia())
        echo "\033[32m Test Passato \033[0m" . "\n";
    else
        echo "\033[31m Test Fallito \033[0m" . "\n";

    echo "CFU Conseguiti: ";
    if($creditiCurriculariConseguiti == $Laureando->getCreditiConseguiti())
        echo "\033[32m Test Passato \033[0m" . "\n";
    else
        echo "\033[31m Test Fallito \033[0m" . "\n";

    if ($CdL == "T. Ing. Informatica") { // se il laureando è ing. informatica, controllo altri attributi
        $bonus = $datiTest["laureando" . $i]["bonus"];
        $mediaInf = $datiTest["laureando" . $i]["media_pesata_inf"];

        echo "Bonus: ";
        if($bonus == $Laureando->getBonus())
            echo "\033[32m Test Passato \033[0m" . "\n";
        else
            echo "\033[31m Test Fallito \033[0m" . "\n";

        echo "Media Informatici: ";
        if($mediaInf == $Laureando->getMediaInformatici())
            echo "\033[32m Test Passato \033[0m" . "\n";
        else
            echo "\033[31m Test Fallito \033[0m" . "\n";
    }
    echo "\n";
}