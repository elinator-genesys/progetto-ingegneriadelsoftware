<?php
require_once(realpath(dirname(__FILE__)) . '/GestioneCarrieraStudente.php');

class Esame
{
    private $Nome;
    private $Codice;
    private int $Cfu;
    private $Voto;
    private $Curricolare;
    private $faMedia;
    private $Informatico;
    private $Vettore;
    private int $totCfuMedia = 0;
    private $Somme;
    private $totCfuConseguiti;


    public function __construct($esamiLaureando)
    {
        for ($i = 0;  $i < sizeof($esamiLaureando["Esami"]["Esame"]); $i++) {
            $infoEsame = array();

            if(is_array($esamiLaureando["Esami"]["Esame"][$i]["PESO"])){
                continue;
            }

            if ($esamiLaureando["Esami"]["Esame"][$i]["DES"] == "LIBERA SCELTA PER RICONOSCIMENTI") {
                continue;
            }
            if ($esamiLaureando["Esami"]["Esame"][$i]["DES"] == "PROVA FINALE") {
                continue;
            }
            if ($esamiLaureando["Esami"]["Esame"][$i]["DES"] == "TEST DI VALUTAZIONE DI INGEGNERIA") {
                continue;
            }

            $this->Nome = $esamiLaureando["Esami"]["Esame"][$i]["DES"];

            $this->Codice = $esamiLaureando["Esami"]["Esame"][$i]["COD"];

            $this->Cfu = $esamiLaureando["Esami"]["Esame"][$i]["PESO"];

            if ($esamiLaureando["Esami"]["Esame"][$i]["VOTO"] == "30  e lode" && $esamiLaureando["Esami"]["Esame"][$i]["CORSO"] != "Cybersecurity (WAI-LM)") {
                $this->Voto = "33";
            } elseif ($esamiLaureando["Esami"]["Esame"][$i]["VOTO"] == "30 e lode" && $esamiLaureando["Esami"]["Esame"][$i]["CORSO"] == "Cybersecurity (WAI-LM)") {
                $this->Voto = "32";
            } else {
                $this->Voto = $esamiLaureando["Esami"]["Esame"][$i]["VOTO"];
            }

            $this->faMedia = true;
            if ($this->Nome == "PROVA DI LINGUA INGLESE (B1)" || $this->Nome == "PROVA DI LINGUA INGLESE B2") {
                $this->Voto = "0";
                $this->faMedia = false;
            }

            $this->Curricolare = false;
            if ($this->Voto != null){
                $this->Curricolare = true;
            }

            $this->Informatico = false;
            $string = file_get_contents("../File_Configurazione/Esami_Informatici.json");
            $esamiInformatici = json_decode($string, true);
            if (in_array($this->Nome, $esamiInformatici["T. Ing. Informatica"])) {
                $this->Informatico = true;
            }

            $infoEsame["NOME"] = $this->Nome;
            $infoEsame["COD"] = $this->Codice;
            $infoEsame["VOTO"] = $this->Voto;
            $infoEsame["PESO"] = $this->Cfu;
            $infoEsame["DATA_ESAME"] = $esamiLaureando["Esami"]["Esame"][$i]["DATA_ESAME"];
            $infoEsame["INFO"] = $this->Informatico;
            $infoEsame["MEDIA"] = $this->faMedia;
            $this->Vettore[] = $infoEsame;

            if ($this->faMedia) {
                $this->totCfuMedia += $this->Cfu; //AGGIORNO I CREDITI CHE FANNO MEDIA
                $this->Somme += $this->Voto * $this->Cfu; //SERVE PER LA MEDIA PONDERATA
            }

            if ($this->Curricolare) {
                $this->totCfuConseguiti += $this->Cfu;
            } //AGGIORNO I CREDITI CONSEGUITI

            usort($this->Vettore, function ($element1, $element2) { // Ordinamento degli esami in array esame in base alla data di conseguimento registrata nella carriera
                $datetime1 = strtotime(str_replace('/', '-', $element1["DATA_ESAME"]));
                $datetime2 = strtotime(str_replace('/', '-', $element2["DATA_ESAME"]));
                return $datetime1 - $datetime2;
            });

        }
    }


    public function prelevaEsame()
    {
        return $this->Vettore;
    }

    public function calcolaMedia()
    {
        return round($this->Somme/$this->totCfuMedia, 3);
    }

    public function prelevaCreditiConseguiti()
    {
        return $this->totCfuConseguiti;
    }

    public function prelevaTotCfuMedia()
    {
        return $this->totCfuMedia;
    }
}

?>