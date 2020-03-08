<?php
/**
 * Clase que envuelve una instancia de la clase PDO para la relación con la base de datos
 * Desde fuera se instanciará de esta forma $conexion = ConexionBD::obtenerInstancia()->obtenerBD();
 */
require_once 'login_mysql.php';

class ConexionBD
{
    private static $db = null; //Esta la instancia que se usará para el exterior
    private static $pdo;  //Instancia de PDO manejada internamente (PHP Data Object Interface)

    // Constructor de la clasede naturaleza privada para que no pueda instanciarse directamente 
    // Si desde fuera hacemos un new conexionBD fallará por ser de ámbito privado
    
    final private function __construct()//Las funciones final no spueden ser sobreescritas
    {
        try {
            // Crear nueva conexión PDO. Usamos self puesto que es una clase con métodos estáticos
            self::obtenerBD(); //Crea, si no existe, un objeto PDO que conecta con la BD $pdo
        } catch (PDOException $e) {
            // Manejo de excepciones si hay error
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * Este será el método que se usará para "crear" las conexión desde fuera
     * Si existe ya un $db conectado no lo crea sino que lo devuelve
     */
    public static function obtenerInstancia() 
    {
        if (self::$db === null) {
            //Si $db está vacío llamamos al constructor internamente y devolvemos  el objeto
            self::$db = new self();
        }
        return self::$db;
    }

    /**
     * Crear una nueva conexión PDO basada
     * en las constantes de conexión
     * Devuelve un Objeto PDO
     */
    public function obtenerBD()
    {
        if (self::$pdo == null) {//Si aun no hay una instancia creada la creamos
            self::$pdo = new PDO('mysql:dbname=' . BASE_DE_DATOS .';host=' . NOMBRE_HOST . ";",USUARIO,CONTRASENA,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") //ESte comando se ejectua enc ada conexión
            );
            // Habilitar excepciones
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        //Si ya tenemos una creada la devolvemos
        return self::$pdo;
    }

    /**
     * Evita la clonación del objeto
     */
    final protected function __clone()
    {
    }

    function _destructor()
    {
        self::$pdo = null; //El recolector de basura se encargará de liberar la memoria
    }
}