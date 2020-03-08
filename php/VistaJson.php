<?php

require_once "VistaApi.php";

/**
 * Clase para imprimir en la salida respuestas con formato JSON
 */
class VistaJson extends VistaApi
{
    public function __construct($estado = 400)
    {
        $this->estado = $estado;
    }

    /**
     * Construye una respuesta HTTP incluyendo:
     * + el estado de la respuesta
     * + el encabezado con el tipo de datos que va a envia/imprimir
     * + el cuerpo de la respuesta con la información
     */
    public function imprimir($cuerpo)
    {
        /** Los caso de éxito de las peticiones $cuerpo["estado"] = 1 son tratados en cada controlador
        *   donde se incluye ya el http_response_code() particular
        *   para el resto de casos o bien se lanza una excepción o bien simplemente el http_response_code 
        *   no se lanza, con lo que es necesario, en estos casos usar el que se haya usado en la vista,
        *   por defecto 400.
        */
        if ($cuerpo["estado"] != 1) {
            http_response_code($this->estado);//Cambia el estado de http
        }

        header('Content-Type: application/json; charset=utf8'); //Crea el encabezado
        echo json_encode($cuerpo, JSON_PRETTY_PRINT); //Cuerpo de la respuesta en JSON
        exit;
    }
}