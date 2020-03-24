<?php

class cMovimientos
{

    const NOMBRE_TABLA = "tMovimientos";	
    
    const MOVIMIENTO_ID = "MovimientoId";
    const TICKER = "ticker";
    const TIPO_MOVIMIENTO = "tipoMovimiento";
    const CANTIDAD = "cantidad";
    const VALOR_UNITARIO = "valorUnitario";
    const COMISION = "comision";
    const FECHA_MOVIMIENTO = "fechaMovimiento";
    const LOTE = "lote";
    const RETENCION_ORIGEN = "retencionOrigen";
    const RETENCION_DESTINO = "retencionDestino";
    const AJUSTE = "ajuste";
	const TIPO_CAMBIO = "tipoCambio";
        
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

    public static function get($idMovimiento = NULL)
    {
        return self::obtenermovimientos($idMovimiento);
    }

    public static function post()
    {
        $body = file_get_contents('php://input');
        $dato = json_decode($body);

        $idMovimiento = cMovimientos::crear($dato);

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            "mensaje" => "Movimiento creado",
            "id" => $idMovimiento
        ];
    }

    public static function put()
    {
        $body = file_get_contents('php://input');
        $dato = json_decode($body);

        if (self::actualizar($dato)) {
			http_response_code(200);
            return [
                "estado" => self::CODIGO_EXITO,
                "mensaje" => "Registro actualizado correctamente"
            ];
        } else {
            throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                "El movimiento al que intentas acceder no existe", 404);
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
                "El movimiento al que intentas acceder no existe", 404);
        }
    }

    /**
     * Obtiene la colección de movimientos o un solo movimiento indicado por el identificador
     * @param null $idMovimiento identificador del movimiento (Opcional)
     * @return array registros de la tabla movimiento
     * @throws Exception
     */
    private static function obtenerMovimientos($idMovimiento = NULL)
    {
        try {
            if (!$idMovimiento) {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA . " ORDER BY fechaMovimiento";

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                
            } else {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                    " WHERE " . self::MOVIMIENTO_ID . "=?"  . " ORDER BY fechaMovimiento";

                echo $comando;

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                // Ligar idMovimiento
                $sentencia->bindParam(1, $idMovimiento, PDO::PARAM_INT);                
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
     * A�ade un nuevo contacto asociado a un usuario
     * @param int $idUsuario identificador del usuario
     * @param mixed $movimiento datos del movimiento
     * @return string identificador del movimiento
     * @throws ExcepcionApi
     **/
    private function crear($movimiento)
    {
        if ($movimiento) {
            try {

                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .                    
                    self::TICKER . "," .
                    self::TIPO_MOVIMIENTO . "," .
                    self::CANTIDAD . "," .
                    self::VALOR_UNITARIO . "," .
                    self::COMISION . "," .
                    self::FECHA_MOVIMIENTO . "," .
                    self::LOTE . "," .
                    self::RETENCION_ORIGEN . "," .
                    self::RETENCION_DESTINO . "," .
                    self::AJUSTE . "," .
                    self::TIPO_CAMBIO . "," .
                    self::AUDIT_INIT_USER . "," .
                    self::AUDIT_INIT_DATE . "," .
                    self::AUDIT_LAST_USER . "," .
                    self::AUDIT_LAST_DATE . "," .                   
                    " VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                // Preparar la sentencia
                $sentencia = $pdo->prepare($comando);

                $sentencia->bindParam(1, $ticker);
                $sentencia->bindParam(2, $tipoMovimiento);
                $sentencia->bindParam(3, $cantidad);
                $sentencia->bindParam(4, $valorUnitario);
                $sentencia->bindParam(5, $comision);
                $sentencia->bindParam(6, $fechaMovimiento);
                $sentencia->bindParam(7, $lote);
                $sentencia->bindParam(8, $retencionOrigen);
                $sentencia->bindParam(9, $retencionDestino);
                $sentencia->bindParam(10, $ajuste);
                $sentencia->bindParam(11, $tipoCambio);               
                $sentencia->bindParam(12, $auditInitUser);
				$sentencia->bindParam(13, $auditInitDate);
				$sentencia->bindParam(14, $auditLastUser);
				$sentencia->bindParam(15, $auditLastDate);
                $sentencia->bindParam(16, $movimientoId);

                $ticker = $movimiento->ticker;
                $tipoMovimiento = $movimiento->tipoMovimiento;
                $cantidad = $movimiento->cantidad;
                $valorUnitario = $movimiento->valorUnitario;
                $comision = $movimiento->comision;
                $fechaMovimiento = $movimiento->fechaMovimiento;
                $lote = $movimiento->lote;
                $retencionOrigen = $movimiento->retencionOrigen;
                $retencionDestino = $movimiento->retencionDestino;
                $ajuste = $movimiento->ajuste;
                $tipoCambio = $movimiento->tipoCambio;                               
                $auditInitUser = $movimiento->auditInitUser;
                $auditInitDate = $movimiento->auditInitDate;
				$auditLastUser = $movimiento->auditLastUser;
                $auditLastDate = $movimiento->auditLastDate;
				$movimientoId = $movimiento->movimientoId;
				
                $sentencia->execute();

                // Retornar en el último id insertado
                return $pdo->lastInsertId();

            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_ERROR_PARAMETROS,
                utf8_encode("Error en existencia o sintaxis de par�metros"));
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
    private function actualizar($movimiento)
    {
        try {
            // Creando consulta UPDATE
            $consulta = "UPDATE " . self::NOMBRE_TABLA .                
                            " SET " . self::MOVIMIENTO . "=?," .
                            self::TIPO_MOVIMIENTO . "=?," .
                            self::CANTIDAD . "=?," .
                            self::VALOR_UNITARIO . "=?," .
                            self::COMISION . "=?," .
                            self::FECHA_MOVIMIENTO . "=?," .
                            self::LOTE . "=?," .
                            self::RETENCION_ORIGEN . "=?," .
                            self::RETENCION_DESTINO . "=?," .
                            self::AJUSTE . "=?," .
                            self::TIPO_CAMBIO . "=?," .                            
							self::COD_PAIS . "=?," .
							self::AUDIT_LAST_USER . "=?," .
							self::AUDIT_LAST_DATE . "=? " .
						" WHERE " . self::MOVIMIENTO_ID . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $ticker);
            $sentencia->bindParam(2, $tipoMovimiento);
            $sentencia->bindParam(3, $cantidad);
            $sentencia->bindParam(4, $valorUnitario);
            $sentencia->bindParam(5, $comision);
            $sentencia->bindParam(6, $fechaMovimiento);
            $sentencia->bindParam(7, $lote);
            $sentencia->bindParam(8, $retencionOrigen);
            $sentencia->bindParam(9, $retencionDestino);
            $sentencia->bindParam(10, $ajuste);
            $sentencia->bindParam(11, $tipoCambio);               
            $sentencia->bindParam(12, $auditInitUser);
			$sentencia->bindParam(13, $auditInitDate);
			$sentencia->bindParam(14, $auditLastUser);
			$sentencia->bindParam(15, $auditLastDate);
            $sentencia->bindParam(16, $movimientoId);          

            $ticker = $movimiento->ticker;
            $tipoMovimiento = $movimiento->tipoMovimiento;
            $cantidad = $movimiento->cantidad;
            $valorUnitario = $movimiento->valorUnitario;
            $comision = $movimiento->comision;
            $fechaMovimiento = $movimiento->fechaMovimiento;
            $lote = $movimiento->lote;
            $retencionOrigen = $movimiento->retencionOrigen;
            $retencionDestino = $movimiento->retencionDestino;
            $ajuste = $movimiento->ajuste;
            $tipoCambio = $movimiento->tipoCambio;    
            $auditLastUser = $movimiento->auditLastUser;
            $auditLastDate = $movimiento->auditLastUser;
			$movimientoId = $movimiento->movimientoId;

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
     * @return bool true si la eliminaci�n se pudo realizar, en caso contrario false
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

