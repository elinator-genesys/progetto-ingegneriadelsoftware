<?php
require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');
class LaureandoIngegneriaInformatica extends CarrieraLaureando
{
    private $annoImmatricolazione;
    private $mediaInformatici;
    private $Bonus;


    public function __construct($anagrafica, $carriera, $CdL, $dataAppello, $annoDiImmatricolazione)
    {
        parent::__construct($anagrafica, $carriera, $CdL, $dataAppello);

        $this->annoImmatricolazione = $annoDiImmatricolazione;
        $this->mediaInformatici = $this->calcolaMediaInfo();
        $this->Bonus = $this->calcolaBonus();
    }

    private function calcolaMediaInfo()
    {
        $totCfuMediainfo = 0;
        $sommeInfo = 0;
        for ($i = 0; sizeof($this->Esami) > $i; $i++) {
            if ($this-> Esami[$i]["INFO"]) {
                $totCfuMediainfo += $this->Esami[$i]["PESO"];
                $sommeInfo += $this->Esami[$i]["VOTO"] * $this->Esami[$i]["PESO"];

            }
        }

        return round($sommeInfo / $totCfuMediainfo, 3);
    }

    private function calcolaBonus()
    {
        $Data = new DateTime($this->dataLaurea);
        $Anno = $Data->format('Y');
        if ($Anno - $this->annoImmatricolazione <= 4) {
            $this->aggiornaMedia();
            return true;

        }
        else {
            return false;
        }
    }

    private function aggiornaMedia()
    {
        $minVoto = $this->Esami[0]["VOTO"];
        $minPeso = $this->Esami[0]["PESO"];
        $nomeMin = $this->Esami[0]["NOME"];

        for ($i = 1; $i < sizeof($this->Esami); $i++) {
            if($this->Esami[$i]["VOTO"] == 0) {
                continue;
            }

            if ($this->Esami[$i]["VOTO"] < $minVoto) {
                $minVoto = $this->Esami[$i]["VOTO"];
                $minPeso = $this->Esami[$i]["PESO"];
                $nomeMin = $this->Esami[$i]["NOME"];
            }
            elseif ($this->Esami[$i]["VOTO"] == $minVoto && $this->Esami[$i]["PESO"] > $minPeso) {
                $minVoto = $this->Esami[$i]["VOTO"];
                $minPeso = $this->Esami[$i]["PESO"];
                $nomeMin = $this->Esami[$i]["NOME"];
            }

        }

        $totCfu = 0;
        $Somme2 = 0;
        for ($j = 0; $j < sizeof($this->Esami); $j++) {
            if($nomeMin == $this->Esami[$j]["NOME"]) {
                $this->Esami[$j]["MEDIA"] = false;
                $this->totCfuMedia -= $this->Esami[$j]["PESO"];
            }
            elseif ($this->Esami[$j]["MEDIA"]) {
                $totCfu += $this->Esami[$j]["PESO"];
                $Somme2 += $this->Esami[$j]["VOTO"] * $this->Esami[$j]["PESO"];
            }
        }

        $this->Media = $Somme2 / $totCfu;
    }

    public function getBonus()
    {
        if ($this->Bonus)
        {
            return 'SI';
        }
        else
        {
            return 'NO';
        }
    }

    public function getMediaInformatici()
    {
        return $this->mediaInformatici;
    }
}