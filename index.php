<?php
/**

 * Template Name: index

 */
?>

<?php

require_once(realpath(dirname(__FILE__)) . '/Classi/GeneratoreProspetti.php');
require_once(realpath(dirname(__FILE__)) . '/Classi/GestoreEmail.php');
require_once(realpath(dirname(__FILE__)) . '/Classi/AccessoProspetti.php');

if(!empty($_GET["laureandi"]) && isset($_GET["crea_prospetti"]) && isset($_GET["CdL"])){
    $string = file_get_contents("./File_Configurazione/CDL_Dati.json");
    $corsiJSON = json_decode($string,true);

    if(isset($corsiJSON[$_GET["CdL"]])){ // Controllo se il CdL è presente nel file info-CdL.json, se si procedo con la creazione dei prospetti
        if (isset($_GET['crea_prospetti'])){
            $Prospetto = new GeneratoreProspetti();
            $Prospetto->CreaProspetti($_GET["laureandi"], $_GET["CdL"], $_GET["data"]);
            $Accesso = new AccessoProspetti();
            $path = $Accesso->apriProspetti();
        }
        elseif (isset($Prospetto) && isset($_GET['invia_prospetti'])) {
            $mail = new GestoreEmail();
            $mail->inviaEmail($_GET["laureandi"], $_GET["CdL"], $_GET["data"]);
        }
        elseif (!isset($Prospetto) && isset($_GET['invia_prospetti'])) {
            echo "Prospetti non ancora creati";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <style type = "text/css">
        body {
            margin:auto;
            width: 60%;
        }

        table {
            background-color: #C8E1F1;
            border-width: 1px;
            border-style: solid;
            border-color: #4E55B2;
            width: 100%;
            height: 500px;
        }

        #container{
            margin-top:8%;
        }

        #title {
            font-family: "Calibri";
            font-size: 20pt;
            color: #0001CD;
            font-weight: 700;
            text-align: center;
        }

        #text1 {
            color: #0001CD;
            font-size: 13pt;
            font-weight: 700;
            text-align: left;
            margin-left: 48%;
            font-family: 'Times New Roman', Times, serif;
        }

        #text2 {
            color: #0001CD;
            font-size: 13pt;
            font-weight: 700;
            text-align: left;
            margin-left: -31%;
            font-family: 'Times New Roman', Times, serif;
        }

        #text3 {
            color: #0001CD;
            font-size: 13pt;
            font-weight: 700;
            text-align: left;
            margin-left: -58%;
            font-family: 'Times New Roman', Times, serif;
        }

        #date{
            border-width: 2px;
            border-style: solid;
            border-color: #4E55B2;
            width: 70%;
            height: 30%;
            margin-left: -145%;
            background-color: #B0E0E6;
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
        }

        #textarea{
            width: 100%;
            margin-left: 48%;
            border-width: 2px;
            border-style: solid;
            border-color: #4E55B2;
            height: 80%;
        }

        #menu{
            border-width: 2px;
            border-style: solid;
            border-color: #4E55B2;
            width: 34%;
            margin-left: 29%;
            font-family:'Times New Roman', Times, serif;
            font-size: 13pt;
        }

        #crea {
            border:#0073AA;
            background-color: #0073AA;
            color: #FFFFFF;
            font-size: 15pt;
            font-family: 'Calibri';
            border-radius: 6px;
            height: 35%;
            width: 40%;
        }

        #invia {
            border:#0073AA;
            background-color: #0073AA;
            color: #FFFFFF;
            font-size: 15pt;
            font-family: 'Calibri';
            border-radius: 6px;
            margin-left: -80%;
            height: 35%;
            width: 72%;
        }

        #apri {
            margin-left: -120%;
        }

    </style>
</head>

<body>
<div id = "container">
    <form method = "GET" action = "index.php">
        <table>
            <tr>
                <th colspan = 3 height = "10%">
                    <div id = "title">GESTIONE PROSPETTI LAUREA</div>
                </th>
            </tr>
            <tr>
                <th>
                    <label for = "Cdl">
                        <div id = "text1">Cdl:</div>
                    </label>
                    <select name = "CdL" id = "menu">
                        <option>Seleziona Cdl</option>
                        <option name = "CdL">T. Ing. Informatica</option>
                        <option name = "CdL">M. Ing. Elettronica</option>
                        <option name = "CdL">M. Ing. delle Telecomunicazioni</option>
                        <option name = "CdL">M. Cybersecurity</option>
                    </select>
                </th>
                <th width = "0px"></th>
                <th>
                    <div id = "text3">Data Laurea:</div>
                    <input type = "date" id = "date" name = "data"/>
                </th>
            </tr>
            <tr>
                <th>
                    <div id = "text1">Matricole:</div>
                    <textarea id = "textarea" name = "laureandi"></textarea>
                </th>
            </tr>
            <tr>
                <th>
                    <button id = "crea" type = "submit" value = "Crea Prospetti" name = "crea_prospetti">Crea Prospetti</button>
                </th>
                <th>
                    <a href = <?php if(isset($path)) echo $path; ?> style = "font-size: 13pt" id = "apri"> apri prospetti</a>
                </th>                
                <th>
                    <button id = "invia" type = "submit" value = "Invia Prospetti" name = "invia_prospetti">Invia Prospetti</button>
                </th>
            </tr>
        </table>
    </form>
</div>
</body>
</html>

