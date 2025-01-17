<?php
require_once(realpath(dirname(__FILE__)) . '/Esame.php');
require_once(realpath(dirname(__FILE__)) . '/GestioneCarrieraStudente.php');


class CarrieraLaureando
{
    protected $Nome;
    protected $Cognome;
    protected $Email;
    protected $Matricola;
    protected $CdL;
    protected $Media;
    protected $formulaVotoLaurea;
    protected $Esami;
    protected $creditiConseguiti;
    protected $dataLaurea;
    protected $creditiLaurea;
    protected $totCfuMedia;


    public function __construct($anagrafica, $carriera, $CdL, $dataAppello)
    {
        $_esami = new Esame ($carriera);
        $string = file_get_contents("../File_Configurazione/CDL_Dati.json");
        $corsiJSON = json_decode($string, true); //Trasforma il file json preso in un array associativo

        $this->Nome = $anagrafica["Entries"]["Entry"]["nome"];
        $this->Cognome = $anagrafica["Entries"]["Entry"]["cognome"];
        $this->Email = $anagrafica["Entries"]["Entry"]["email_ate"];
        $this->Matricola = $carriera["Esami"]["Esame"][0]["MATRICOLA"];
        $this->CdL = $CdL;
        $this->Media = $_esami->calcolaMedia();
        $this->formulaVotoLaurea = $corsiJSON[$this->CdL]["formulaLaurea"];
        $this->Esami = $_esami->prelevaEsame();
        $this->creditiConseguiti = $_esami->prelevaCreditiConseguiti();
        $this->dataLaurea = $dataAppello;
        $this->creditiLaurea = $corsiJSON[$this->CdL]["CFUCurriculari"];
        $this->totCfuMedia = $_esami->prelevaTotCfuMedia();
    }

    public function calcolaSimulazioneVotoLaurea()
    {
        $string = file_get_contents("../File_Configurazione/CDL_Dati.json");
        $corsiJSON = json_decode($string, true);
        $_formulaVotoLaurea = $corsiJSON[$this->CdL]["formulaLaureaCalcolo"];

        $simulazioneLaurea = array();
        if ($corsiJSON[$this->CdL]["Cmin"] != 0) {
            $Cmax = $corsiJSON[$this->CdL]["Cmax"];
            $Cmin = $corsiJSON[$this->CdL]["Cmin"];
            $Cstep = $corsiJSON[$this->CdL]["Cstep"];

            for ($i = $Cmin; $i <= $Cmax; $i += $Cstep) {
                $simulazioneVoto = array();
                $simulazioneVoto["VOTO COMMISSIONE (C)"] = $i;
                $simulazioneVoto["VOTO LAUREA"] = $this->calcoloVotoPartenza($_formulaVotoLaurea, $this->getMedia(), $this->getCreditiLaurea()) + $i;
                $simulazioneLaurea[] = $simulazioneVoto;
            }
        } else {
            $Tmax = $corsiJSON[$this->CdL]["Tmax"];
            $Tmin = $corsiJSON[$this->CdL]["Tmin"];
            $Tstep = $corsiJSON[$this->CdL]["Tstep"];

            for ($i = $Tmin; $i <= $Tmax; $i += $Tstep) {
                $simulazioneVoto = array();
                $simulazioneVoto["VOTO TESI (T)"] = $i;
                $simulazioneVoto["VOTO LAUREA"] = $this-> calcoloVotoPartenza($_formulaVotoLaurea, $this->getMedia(), $this->getCreditiLaurea(), $i); //Da guardare
                $simulazioneLaurea[] = $simulazioneVoto;
            }
        }

        return $simulazioneLaurea;
    }

    private function calcoloVotoPartenza($_formulaVotoLaurea, $M, $CFU, $T = 0)
    {
        $votoPartenza = 0;
        $C = 0;
        $arr = explode(" ", $_formulaVotoLaurea);
        $voa = "\$votoPartenza = " . $_formulaVotoLaurea . ";";
        eval($voa);
        if($T == 0)
            return round($votoPartenza, 3);
        else
            return round($votoPartenza, 1);
    }

    public function getNome()
    {
        return $this->Nome;
    }
    public function getCognome()
    {
        return $this->Cognome;
    }
    public function getEmail()
    {
        return $this->Email;
    }
    public function getMatricola()
    {
        return $this->Matricola;
    }
    public function getCdL()
    {
        return $this->CdL;
    }
    public function getMedia()
    {
        return round($this->Media, 3);
    }
    public function getFormulaVotoLaurea()
    {
        return $this->formulaVotoLaurea;
    }
    public function getEsami()
    {
        return $this->Esami;
    }
    public function getCreditiConseguiti()
    {
        return $this->creditiConseguiti;
    }
    public function getDataLaurea()
    {
        return $this->dataLaurea;
    }
    public function getCreditiLaurea()
    {
        return $this->creditiLaurea;
    }
    public function getTotCfuMedia()
    {
        return $this->totCfuMedia;
    }
}



?>