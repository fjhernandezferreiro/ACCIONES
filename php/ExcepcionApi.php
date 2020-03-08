<?php

class ExcepcionApi extends Exception
{
    public $estado;

    public function __construct($estado, $mensaje, $codigo = 400)
    {
        $this->estado = $estado;
        $this->message = $mensaje;//Ya existe en Exceptio, la clase ancestra
        $this->code = $codigo;//Ya existe en Exceptio, la clase ancestra
    }

}
/*ESta clase nos va a permitir lanzar llamadas de este tipo
 throw new ExcepcionApi(2, "Error con estado 2", 404);

Controlando mejor los datos que se muestran al usuario
*/
