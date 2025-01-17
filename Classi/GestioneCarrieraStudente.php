<?php

class GestioneCarrieraStudente
{
    private $dataPath;

    public function __construct($data_path)
    {
        $this->dataPath = $data_path;
    }
    public function restituisciAnagraficaStudente($matricola)
    {
        $string = file_get_contents($this->dataPath . "../data/" . $matricola . "_anagrafica.json");
        $json_string = json_decode($string, true);
        return $json_string;
    }

    public function restituisciCarrieraStudente($matricola)
    {
        $string = file_get_contents($this->dataPath . "../data/" . $matricola . "_esami.json");
        $json_string = json_decode($string, true);
        return $json_string;
    }
}
?>