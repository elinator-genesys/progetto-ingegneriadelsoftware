<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(realpath(dirname(__FILE__)) . '/CarrieraLaureando.php');
require_once realpath(dirname(__FILE__) . '/PHPMailer/src/Exception.php');
require_once realpath(dirname(__FILE__) . '/PHPMailer/src/PHPmailer.php');
require_once realpath(dirname(__FILE__) . '/PHPMailer/src/SMTP.php');
class GestoreEmail
{
    public function creaEmail($_email, $_matricola)
    {
        $messaggio = new PHPmailer();
        $messaggio->IsSMTP();
        $messaggio->Host = "mixer.unipi.it";
        $messaggio->SMTPSecure = "tls";
        $messaggio->SMTPAuth = false;
        $messaggio->Port = 25;

        $messaggio->From = 'no-reply-laureandosi@ing.unipi.it';
        $messaggio->AddAddress($_email);
        $messaggio->Subject = 'Appello di laurea in Ing. TEST- indicatori per voto di laurea';
        $messaggio->Body = stripslashes(
            'Gentile laureando/laureanda,
		Allego un prospetto contenente: la sua carriera, gli indicatori e la formula che la commissione adopererà per determinare il voto di laurea.
		La prego di prendere visione dei dati relativi agli esami.
		In caso di dubbi scrivere a: ...
		
		Alcune spiegazioni:
		- gli esami che non hanno un voto in trentesimi, hanno voto nominale zero al posto di giudizio o idoneita\', in quanto non contribuiscono al calcolo della media ma solo al numero di crediti curriculari;
		- gli esami che non fanno media (pur contribuendo ai crediti curriculari) non hanno la spunta nella colonna MED;
		- il voto di tesi (T) appare nominalmente a zero in quanto verra\' determinato in sede di laurea, e va da 18 a 30.
		
		 Cordiali saluti
		 Unità Didattica DII'
        );


        $messaggio->AddAttachment("../prospetti/prospetti_laureando/" . $_matricola . ".pdf");

        return $messaggio;
    }

    public function inviaEmail($aMatricole, $_cdl, $_dataAppello)
    {
        $_matricole = preg_split("/\n/", $aMatricole);

        $gestoreCarriere = new GestioneCarrieraStudente('./');
        for ($i = 0; $i < sizeof($_matricole); $i++) {
            $anagrafica = $gestoreCarriere->restituisciAnagraficaStudente($_matricole[$i]);
            $carriera = $gestoreCarriere->restituisciCarrieraStudente($_matricole[$i]);
            if ($_cdl == "T. Ing. Informatica") {
                $Laureando = new LaureandoIngegneriaInformatica(
                    $anagrafica,
                    $carriera,
                    $_cdl,
                    $_dataAppello,
                    $carriera["Esami"]["Esame"][0]["ANNO_IMM"]
                );
            } else {
                $Laureando = new CarrieraLaureando($anagrafica, $carriera, $_cdl, $_dataAppello);
            }

            $Messaggio = $this->creaEmail($Laureando->getEmail(), $Laureando->getMatricola());
            if (!$Messaggio->Send()) {
                echo $Messaggio->ErrorInfo;
            } else {
                echo 'Email inviata correttamente!';
            }

            $Messaggio->SmtpClose();
            unset($Messaggio);
        }
    }
}
?>