<?php

class cTickers
{

    const NOMBRE_TABLA = "tTickers";
	
    const TICKER_ID = "tickerId";
    const TICKER = "Ticker";
    const NOMBRE = "Nombre";
    const COD_PAIS = "codPais";
    
	const AUDIT_INIT_USER = "auditInitUser";
    const AUDIT_INIT_DATE = "auditInitDate";
    const AUDIT_LAST_USER = "auditLastUser";
    const AUDIT_LAST_DATE = "auditLastDate";

    const CODIGO_EXITO = 1;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_ERROR_PARAMETROS = 4;
    const ESTADO_NO_ENCONTRADO = 5;

    public static function get()
    {
        return self::obtenerTickers();
    }

    public static function post()
    {
        $body = file_get_contents('php://input');
        $ticker = json_decode($body);

        $idTicker = cTickers::crear($ticker);

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            "mensaje" => "Ticker creado",
            "id" => $idTicker
        ];
    }

    public static function put()
    {
        $body = file_get_contents('php://input');
        $ticker = json_decode($body);

        if (self::actualizar($ticker)) {
			http_response_code(200);
            return [
                "estado" => self::CODIGO_EXITO,
                "mensaje" => "Registro actualizado correctamente"
            ];
        } else {
            throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                "El ticker al que intentas acceder no existe", 404);
        }        
    }

    public static function delete()
    {
        if (self::eliminar()) {
            http_response_code(200);
            return [
                "estado" => self::CODIGO_EXITO,
                "mensaje" => "Registro eliminado correctamente"
            ];
        } else {
            throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                "El ticker al que intentas acceder no existe", 404);
        }
    }

    /**
     * Obtiene la colección de tickers o un solo ticker indicado por el identificador
     * @param null $idTicker identificador del ticker (Opcional)
     * @return array registros de la tabla ticker
     * @throws Exception
     */
    private static function obtenerTickers($idTicker = NULL)
    {
        try {
            if (!$idTicker) {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA;

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                
            } else {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                    " WHERE " . self::TICKER_ID . "=?";

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                // Ligar idTicker
                $sentencia->bindParam(1, $idTicker, PDO::PARAM_INT);                
            }

            // Ejecutar sentencia preparada
            if ($sentencia->execute()) {
                http_response_code(200);
                return
                    [
                        "estado" => self::ESTADO_EXITO,
                        "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                    ];
            } else
                throw new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Añade un nuevo contacto asociado a un usuario
     * @param int $idUsuario identificador del usuario
     * @param mixed $ticker datos del ticker
     * @return string identificador del ticker
     * @throws ExcepcionApi
     **/
    private function crear($ticker)
    {
        if ($ticker) {
            try {

                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::NOMBRE . "," .
                    self::COD_PAIS . "," .
                    self::AUDIT_INIT_USER . "," .
                    self::AUDIT_INIT_DATE . "," .
                    self::AUDIT_LAST_USER . "," .
                    self::AUDIT_LAST_DATE . "," .
                    self::TICKER_ID . ")" .
                    " VALUES(?,?,?,?,?)";

                // Preparar la sentencia
                $sentencia = $pdo->prepare($comando);

                $sentencia->bindParam(1, $nombre);
                $sentencia->bindParam(2, $codPais);
                $sentencia->bindParam(3, $auditInitUser);
				$sentencia->bindParam(4, $auditInitDate);
				$sentencia->bindParam(5, $auditLastUser);
				$sentencia->bindParam(6, $auditLastDate);
                $sentencia->bindParam(7, $tickerId);


                $nombre = $ticker->nombre;
                $codPais = $ticker->codPais;
                $auditInitUser = $ticker->auditInitUser;
                $auditInitDate = $ticker->auditInitDate;
				$auditLastUser = $ticker->auditLastUser;
                $auditLastDate = $ticker->auditLastDate;
				$tickerId = $ticker->tickerId;
				
                $sentencia->execute();

                // Retornar en el último id insertado
                return $pdo->lastInsertId();

            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_ERROR_PARAMETROS,
                utf8_encode("Error en existencia o sintaxis de parámetros"));
        }

    }

    /**
     * Actualiza el contacto especificado por idUsuario
     * @param int $idUsuario
     * @param object $contacto objeto con los valores nuevos del contacto
     * @param int $idContacto
     * @return PDOStatement
     * @throws Exception
    **/ 
    private function actualizar($ticker)
    {
        try {
            // Creando consulta UPDATE
            $consulta = "UPDATE " . self::NOMBRE_TABLA .                
							" SET " . self::NOMBRE . "=?," .
										self::COD_PAIS . "=?," .
										self::AUDIT_LAST_USER . "=?," .
										self::AUDIT_LAST_DATE . "=? " .
							" WHERE " . self::TICKER_ID . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $codPais);
            $sentencia->bindParam(3, $auditLastUser);
            $sentencia->bindParam(4, $auditLastDate);
            $sentencia->bindParam(5, $tickerId);            

            $nombre = $ticker->nombre;
            $codPais = $ticker->codPais;
            $auditLastUser = $ticker->auditLastUser;
            $auditLastDate = $ticker->auditLastUser;
			$tickerId = $ticker->tickerId;

            // Ejecutar la sentencia
            $sentencia->execute();

            return $sentencia->rowCount();

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }


    /**
     * Elimina un contacto asociado a un usuario
     * @param int $idUsuario identificador del usuario
     * @param int $idContacto identificador del contacto
     * @return bool true si la eliminación se pudo realizar, en caso contrario false
     * @throws Exception excepcion por errores en la base de datos
     
    private function eliminar($idUsuario, $idContacto)
    {
        try {
            // Sentencia DELETE
            $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::ID_CONTACTO . "=? AND " .
                self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $idContacto);
            $sentencia->bindParam(2, $idUsuario);

            $sentencia->execute();

            return $sentencia->rowCount();

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
	**/
}

