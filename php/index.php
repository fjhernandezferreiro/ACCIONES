<?php

/*
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
*/

//<Opciones depuración y control------------------->
ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
ini_set('display_errors', '1');

//TRAZA Para saber si está activo el mod_rewrite
/**
echo "<pre>";
var_dump(apache_get_modules());
echo "</pre>";
**/
//<--Opciones depuración y control------------------/>


//Ficheros requeridos
require 'cTickers.php';     // Clase Tickers, de la tabla ttickers
require 'cMovimientos.php';     // Clase Movimientos, de la tabla tMovimientos
require 'VistaJson.php';    // Transforma los datos recuperados en JSON
require 'ConexionBD.php';   // Datos de conexión con la BBDD

// Constantes de estado
const ESTADO_URL_INCORRECTA = 2;
const ESTADO_EXISTENCIA_RECURSO = 3;
const ESTADO_METODO_NO_PERMITIDO = 4;

// Por defecto usaremos Json para las respuestas aunque en formato podemos recibir la opción de XML
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'json';
// Creamos un objeto $vista adecuado al formato requerido
switch ($formato) {
    case 'xml':
        $vista = new VistaXML();//Estas clases construyen las respuestas
        break;
    case 'json':
    default:
        $vista = new VistaJson();
}

//echo "<br><hr>REQUEST_METHOD    --> " . $_SERVER['REQUEST_METHOD'] . "<br>";        // GET
//echo "<br><hr>PATH_INFO         --> " . $_SERVER['PATH_INFO'] . "<br>";             // /cTickers

$arrURL = explode('/',$_SERVER['PATH_INFO']);           // "cTickers"

//echo "<br><hr>arrURL.count         --> " . count($arrURL) . "<br>";    // longitud
//echo "<br><hr>arrURL[0]         --> " . $arrURL[0] . "<br>";    // [blank]
//echo "<br><hr>arrURL[1]         --> " . $arrURL[1] . "<br>";    // cTickers
//echo "<br><hr>arrURL[2]         --> " . $arrURL[2] . "<br>";    // Siguiente parámetro
//echo "<br><hr>arrURL[3]         --> " . $arrURL[3] . "<br>";    // Y otro

if (count($arrURL)>1) {
    $recurso = $arrURL[1];
}

if (count($arrURL)>2) {
    $peticion = $arrURL[2];          // Esto se usará si queremos pasar el ID de algún elemento en particular
} else {
    $peticion = null;
}

$metodo = $_SERVER['REQUEST_METHOD'];       // "get"



//La función method exists recibe por parámetro una clase y un nombre de método para verificar si está definido
if (method_exists($recurso, $metodo)) {
	//call_user_func permite llamar a una función que sea un método de clase estático
	//call_user_func(array('MiClase', 'miMétodoDeLlamadaDeRetorno'),parámetro);
	//(usuarios,delete,2)
	//En $peticion queda sólo el id del recurso que deseamos eliminar en este caso
    $respuesta = call_user_func(array($recurso, $metodo), $peticion);
    $vista->imprimir($respuesta);
}