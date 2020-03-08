<?php

abstract class VistaApi{
    
    // Código de error
    public $estado; //Enviado en la respuesta HTTP

    public abstract function imprimir($cuerpo);
}