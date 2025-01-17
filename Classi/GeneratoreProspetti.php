<?php
require_once '../vendor/autoload.php';
require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');
require_once(realpath(dirname(__FILE__)) . '/LaureandoIngegneriaInformatica.php');
class GeneratoreProspetti
{
    private $_Laureando;
    private $pdfLaureandi;
    private $_simulazioneVotoLaurea;

    public function creaProspetti($aMatricole, $_cdl, $_dataAppello)
    {
        $_matricole = preg_split("/\n/", $aMatricole);
        $this->_Laureando = array();
        $this->_simulazioneVotoLaurea = array();
        $this->pdfLaureandi = array();
        $gestoreCarriere = new GestioneCarrieraStudente('./');

        for ($i = 0; $i < sizeof($_matricole); $i++) {
            $anagrafica = $gestoreCarriere->restituisciAnagraficaStudente($_matricole[$i]);
            $carriera = $gestoreCarriere->restituisciCarrieraStudente($_matricole[$i]);
            if ($_cdl == "T. Ing. Informatica") {
                $Laureando = new LaureandoIngegneriaInformatica($anagrafica, $carriera, $_cdl, $_dataAppello, $carriera["Esami"]["Esame"][0]["ANNO_IMM"]);
            } else {
                $Laureando = new CarrieraLaureando($anagrafica, $carriera, $_cdl, $_dataAppello);
            }
            $simulazioneLaurea = $Laureando->calcolaSimulazioneVotoLaurea();
            $this->generaProspettoLaureando($Laureando, $i, $simulazioneLaurea);
            $this->salvaProspettoL($i);

        }
        $this->generaProspettoCommissione($_cdl, $_matricole);
        echo "Prospetti Generati";
    }
    public function generaProspettoLaureando($Laureando, $j, $simulazioneVoto)
    {
        $this->_Laureando[$j] = $Laureando;
        $this->_simulazioneVotoLaurea[$j] = $simulazioneVoto;
        $this->pdfLaureandi[$j] = new \Mpdf\Mpdf();
        $this->pdfLaureandi[$j]->addPage();
        $this->pdfLaureandi[$j]->setFont('Times', '', 8);
        $this->pdfLaureandi[$j]->Cell(0, 6 ,$Laureando->getCdL(), 0 ,1, 'C');
        $this->pdfLaureandi[$j]->Cell(0, 8 , 'CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA', 0 , 1,'C');

        $str = "Matricola:                           ".$Laureando->getMatricola()."\nNome:                                 ".$Laureando->getNome()."\nCognome:                           ".$Laureando->getCognome()."\nE-MAIL:                              ".$Laureando->getEmail()."\nData:                                   ".$Laureando->getDataLaurea()."";
        $widthEsame = 153;
        $widthEsameInfo = 9;
        $newLine = 1;
        if($Laureando->getCdL() == "T. Ing. Informatica"){
            $str .= "\nBONUS:                              ".$Laureando->getBonus();
            $widthEsame = 140;
            $widthEsameInfo = 10;
            $newLine = 0;
        }

        $this->pdfLaureandi[$j]->MultiCell(0 , 4, $str, 1, 'L');  //ok
        $this->pdfLaureandi[$j]->Cell(0 , 2,"",0,1,'C');
        $this->pdfLaureandi[$j]->SetFont('Times', '' , 8.5);
        $esami = $Laureando->getEsami();
        for($i = 0; $i < sizeof($esami); $i++){
            if($i == 0){
                $this->pdfLaureandi[$j]->Cell($widthEsame, 6 , "ESAME", 1 , 0, 'C');
                $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 6 , "CFU", 1 , 0, 'C');
                $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 6, "VOT", 1 , 0, 'C');
                $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 6 , "MED", 1 , $newLine, 'C');
                if($Laureando->getCdL() == "T. Ing. Informatica")
                    $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 6 , "INF", 1 , 1, 'C');
            }

            $this->pdfLaureandi[$j]->Cell($widthEsame, 4.5 , $esami[$i]["NOME"], 1 , 0, 'L');
            $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 4.5 , $esami[$i]["PESO"], 1 , 0, 'C');
            $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 4.5 , $esami[$i]["VOTO"], 1 , 0, 'C');
            $str = "X";
            if(!$esami[$i]["MEDIA"])
                $str = "";

            $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 4.5 , $str , 1 , $newLine , 'C');
            if($Laureando->getCdL() == "T. Ing. Informatica") {
                $str = "X";
                if(!$esami[$i]["INFO"]) {
                    $str = "";
                }

                $this->pdfLaureandi[$j]->Cell($widthEsameInfo, 4.5 , $str , 1 , 1, 'C'); //se esame informatico
            }
        }
        $this->pdfLaureandi[$j]->cell(0 , 4 ,"",0,1,'C');
        $this->pdfLaureandi[$j]->SetFont('Times', '' , 10);
        if($Laureando->getCdL() == "T. Ing. Informatica") {
            $str = "
            Media Pesata (M):                                                ".$Laureando->getMedia()."\n	
            Crediti che fanno media (CFU):                           ".$Laureando->getTotCfuMedia()."\n
            Crediti curriculari conseguiti:                              ".$Laureando->getCreditiConseguiti()."/".$Laureando->getCreditiLaurea()."\n
            Voto di tesi (T):                                                     0 \n
            Formula calcolo voto di laurea:                            ".$Laureando->getFormulaVotoLaurea()."\n 
            Media pesata esami INF:                                      ".$Laureando->getMediaInformatici()."
            ";
        }
        else {
            $str = "
            Media Pesata (M):                                                ".$Laureando->getMedia()."\n	
            Crediti che fanno media (CFU):                          ".$Laureando->getTotCfuMedia()."\n 
            Crediti curriculari conseguiti:                              ".$Laureando->getCreditiConseguiti()."/".$Laureando->getCreditiLaurea()."\n
            Formula calcolo voto di laurea:                            ".$Laureando->getFormulaVotoLaurea()."\n ";
        }

        $this->pdfLaureandi[$j]->MultiCell(0 , 2.7 ,$str, 1, 'L');
        $this->pdfLaureandi[$j]->SetFont('Times', '' , 10);
        $this->pdfLaureandi[$j]->Cell(0 , 2,"",0,1,'C');

    }

    private function salvaProspettoL($i)
    {
        $this->pdfLaureandi[$i]->Output("../prospetti/prospetti_laureando/".$this->_Laureando[$i]->getMatricola().".pdf", 'F');
    }

    public function generaProspettoCommissione($_cdl, $_matricole)
    {
        $pdf = new \Mpdf\Mpdf();
        $pdf->addPage();
        $pdf->setFont('Times', '', 12);
        $pdf->Cell(0, 6 ,$_cdl, 0,1, 'C');
        $pdf->Cell(0, 8 , 'LAUREANDOSI 2 - progettazione: mario.cimino@unipi.it, Amministrazione: rose.rossiello@unipi.it', 0, 1,'C');
        $pdf->Cell(0, 6 ,'LISTA LAUREANDI', 0,1, 'C');
        $pdf->Cell(45, 6 ,'COGNOME', 1,0, 'C');
        $pdf->Cell(45, 6 ,'NOME', 1,0, 'C');
        $pdf->Cell(45, 6 ,'CDL', 1,0, 'C');
        $pdf->Cell(45, 6 ,'VOTO LAUREA', 1,1, 'C');

        for ($i = 0; $i < sizeof($_matricole); $i++) {
            $pdf->Cell(45, 6 ,$this->_Laureando[$i]->getCognome(), 1,0, 'C');
            $pdf->Cell(45, 6 ,$this->_Laureando[$i]->getNome(), 1,0, 'C');
            $pdf->Cell(45, 6 ,' ', 1,0, 'C');
            $pdf->Cell(45, 6 ,'/110', 1,1, 'C');
        }

        $pdf->addPage();

        for ($h = 0; $h < sizeof($_matricole); $h++) {
            $pdf->setFont('Times', '', 10);
            if($h != 0)
                $pdf->addPage();
            $pdf->setSourceFile("../prospetti/prospetti_laureando/".$this->_Laureando[$h]->getMatricola().".pdf");
            $tplId = $pdf->ImportPage(1);
            $pdf->useTemplate($tplId);

            if($_cdl == "T. Ing. Informatica")
                $pdf->Cell(0, 200, " ", 0, 1, 'C');
            else
                $pdf->Cell(0, 135, " ", 0, 1, 'C');


            $pdf->Cell(180, 5.5, "SIMULAZIONE VOTO LAUREA", 1, 1, 'C');
            (sizeof($this->_simulazioneVotoLaurea[$h]) > 7) ? $width = 45 : $width = 90;
            $arr = array_keys($this->_simulazioneVotoLaurea[$h][0]);
            if ($width == 90) {

                $pdf->Cell($width, 6, $arr[0], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[1], 1, 1, 'C');

                for ($i = 0; $i < sizeof($this->_simulazioneVotoLaurea[$h]); $i++) {
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[0]], 1, 0, 'C');
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[1]], 1, 1, 'C');
                }
            }
            elseif ($arr[0] == "VOTO COMMISSIONE (C)") {

                $pdf->Cell($width, 6, $arr[0], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[1], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[0], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[1], 1, 1, 'C');

                for ($i = 0; $i < sizeof($this->_simulazioneVotoLaurea[$h]) - 5; $i++) {
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[0]], 1, 0, 'C');
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[1]], 1, 0, 'C');
                    if ($this->_simulazioneVotoLaurea[$h][$i][$arr[0]] + 6 <= sizeof($this->_simulazioneVotoLaurea[$h])) {
                        $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i + 6][$arr[0]], 1, 0, 'C');
                        $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i + 6][$arr[1]], 1, 1, 'C');
                    }
                }
            }
            else {

                $pdf->Cell($width, 6, $arr[0], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[1], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[0], 1, 0, 'C');
                $pdf->Cell($width, 6, $arr[1], 1, 1, 'C');

                for ($i = 0; $i < sizeof($this->_simulazioneVotoLaurea[$h]) - 6; $i++) {
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[0]], 1, 0, 'C');
                    $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i][$arr[1]], 1, 0, 'C');
                    if ($this->_simulazioneVotoLaurea[$h][$i][$arr[0]] + 7 <= 30) {
                        $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i + 7][$arr[0]], 1, 0, 'C');
                        $pdf->Cell($width, 6, $this->_simulazioneVotoLaurea[$h][$i + 7][$arr[1]], 1, 1, 'C');
                    }
                }
            }

            $pdf->Cell(0, 10, "", 0, 1, 'C');
            $string = file_get_contents("../File_Configurazione/CDL_Dati.json");
            $corsiJSON = json_decode($string, true);
            $messaggioCommissione = $corsiJSON[$this->_Laureando[$h]->getCdL()]["MessaggioCommissione"];
            $pdf->setFont('Times', '', 8);
            $pdf->Cell(0, 1, $messaggioCommissione, 0, 1, 'L');
        }


        $pdf->Output("../prospetti/prospetti_commissione/" . "commissione". ".pdf", 'F');

    }
}



?>