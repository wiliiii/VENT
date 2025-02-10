<?php
	
	namespace app\models;
	use \PDO;
	use \PDOException;
	use \Exception;
	if(file_exists(__DIR__."/../../config/server.php")){
		require_once __DIR__."/../../config/server.php";
	}

	class mainModel {
    protected function conectar() {
        try {
            // Usamos la URL de Railway para establecer la conexión
			$dsn = "pgsql:host=" . DB_SERVER . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
			$conexion = new PDO($dsn, DB_USER, DB_PASS, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]);

            return $conexion;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();  // Mostrar el error en caso de fallo de la conexión
            exit();
        }
    }

		/*----------  Funcion ejecutar consultas (solo para añadir producto)  ----------*/
		protected function ejecutarConsultaGenerica($sql, $params = [], $fetch = false) {
			try {
				$pdo = $this->conectar(); // Conectar a la base de datos
				$stmt = $pdo->prepare($sql); // Prepara la consulta
		
				// Verifica si los parámetros están vacíos
				if (empty($params)) {
					error_log("Advertencia: No se pasaron parámetros a la consulta.");
				}
		
				// Depurar la consulta y los parámetros
				error_log("Consulta SQL: " . $sql);
				error_log("Parámetros: " . json_encode($params));
		
				// Ejecuta la consulta con los parámetros
				$stmt->execute($params);
		
				// Si se requiere obtener resultados, devuelve los resultados
				if ($fetch) {
					return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Devuelve los resultados como array asociativo
				}
		
				return $stmt;  // Si no se requiere obtener resultados, devuelve el objeto PDOStatement
			} catch (PDOException $e) {
				throw new Exception("Error en la consulta SQL: " . $e->getMessage());
			}
		}
		

		/*----------  Funcion ejecutar consultas  ----------*/
		protected function ejecutarConsulta($consulta, $params = []) {
			$sql = $this->conectar()->prepare($consulta);
	
			// Vinculamos los parámetros si se proporcionan
			foreach ($params as $key => $value) {
				$sql->bindParam($key, $value);
			}
	
			// Ejecutamos la consulta
			$sql->execute();
			return $sql;
		}
		/*----------  Funcion limpiar cadenas  ----------*/
		public function limpiarCadena($cadena) {

			$palabras = ["<script>", "</script>", "<script src", "<script type=", "SELECT * FROM", "SELECT ", " SELECT ", "DELETE FROM", "INSERT INTO", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASES", "<?php", "?>", "--", "^", "<", ">", "==", ";", "::"];

			$cadena = trim($cadena);
			$cadena = stripslashes($cadena);

			foreach ($palabras as $palabra) {
				$cadena = str_ireplace($palabra, "", $cadena);
			}

			$cadena = trim($cadena);
			$cadena = stripslashes($cadena);

			return $cadena;
		}


		/*---------- Funcion verificar datos (expresion regular) ----------*/
		protected function verificarDatos($filtro, $cadena) {
			if(preg_match("/^".$filtro."$/", $cadena)){
				return false;
			}else{
				return true;
			}
		}


		/*----------  Funcion para ejecutar una consulta INSERT preparada  ----------*/
		protected function guardarDatos($tabla, $datos) {
			// Validación básica del nombre de la tabla
			if (!preg_match("/^[a-zA-Z0-9_]+$/", $tabla)) {
				throw new Exception("Nombre de tabla inválido.");
			}
		
			// Construcción de la consulta SQL
			$columnas = [];
			$valores = [];
			foreach ($datos as $clave) {
				$columnas[] = $clave["campo_nombre"];
				$valores[] = $clave["campo_marcador"];
			}
		
			$query = "INSERT INTO $tabla (" . implode(",", $columnas) . ") VALUES (" . implode(",", $valores) . ")";
		
			try {
				// Obtener la conexión y preparar la consulta
				$pdo = $this->conectar();
				$sql = $pdo->prepare($query);
		
				// Depurar la consulta antes de ejecutarla
				// Esto imprimirá la consulta y parámetros en el log para verificar si hay errores
				error_log("SQL Query: " . $query);
				error_log("Params: " . json_encode($datos));
		
				// Vincular parámetros usando bindValue()
				foreach ($datos as $clave) {
					$sql->bindValue($clave["campo_marcador"], $clave["campo_valor"]);
				}
		
				// Ejecutar la consulta
				$sql->execute();
		
				// Verificar si se insertó alguna fila
				if ($sql->rowCount() > 0) {
					return $pdo->lastInsertId(); // Retornar el ID del último registro insertado
				}
		
				return false; // Retorna false si no se insertaron registros
		
			} catch (PDOException $e) {
				throw new Exception("Error al insertar los datos: " . $e->getMessage());
			}
		}
		


		/*---------- Funcion seleccionar datos ----------*/
        public function seleccionarDatos($tipo, $tabla, $campo, $id) {
			$tipo = $this->limpiarCadena($tipo);
			$tabla = $this->limpiarCadena($tabla);
			$campo = $this->limpiarCadena($campo);
			$id = $this->limpiarCadena($id);

            if($tipo == "Unico") {
                $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo = :ID");
                $sql->bindParam(":ID", $id);
            } elseif($tipo == "Normal") {
                $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");
            }
            $sql->execute();

            return $sql;
		}


		/*----------  Funcion para ejecutar una consulta UPDATE preparada  ----------*/
		protected function actualizarDatos($tabla, $datos, $condicion) {

			$query = "UPDATE $tabla SET ";

			$C = 0;
			foreach ($datos as $clave) {
				if($C >= 1) { $query .= ","; }
				$query .= $clave["campo_nombre"] . " = " . $clave["campo_marcador"];
				$C++;
			}

			$query .= " WHERE " . $condicion["condicion_campo"] . " = " . $condicion["condicion_marcador"];

			$sql = $this->conectar()->prepare($query);

			foreach ($datos as $clave) {
				$sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
			}

			$sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);

			$sql->execute();

			return $sql;
		}


		/*---------- Funcion eliminar registro ----------*/
        protected function eliminarRegistro($tabla, $campo, $id) {
            $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo = :id");
            $sql->bindParam(":id", $id);
            $sql->execute();
            
            return $sql;
        }


		/*---------- Paginador de tablas ----------*/
		protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones) {
	        $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

	        if($pagina <= 1) {
	            $tabla .= '
	            <a class="pagination-previous is-disabled" disabled ><i class="fas fa-arrow-alt-circle-left"></i> &nbsp; Anterior</a>
	            <ul class="pagination-list">
	            ';
	        } else {
	            $tabla .= '
	            <a class="pagination-previous" href="'.$url.($pagina-1).'/"><i class="fas fa-arrow-alt-circle-left"></i> &nbsp; Anterior</a>
	            <ul class="pagination-list">
	                <li><a class="pagination-link" href="'.$url.'1/">1</a></li>
	                <li><span class="pagination-ellipsis">&hellip;</span></li>
	            ';
	        }

	        $ci = 0;
	        for($i = $pagina; $i <= $numeroPaginas; $i++) {

	            if($ci >= $botones) {
	                break;
	            }

	            if($pagina == $i) {
	                $tabla .= '<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a></li>';
	            } else {
	                $tabla .= '<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>';
	            }

	            $ci++;
	        }

	        if($pagina == $numeroPaginas) {
	            $tabla .= '
	            </ul>
	            <a class="pagination-next is-disabled" disabled ><i class="fas fa-arrow-alt-circle-right"></i> &nbsp; Siguiente</a>
	            ';
	        } else {
	            $tabla .= '
	                <li><span class="pagination-ellipsis">&hellip;</span></li>
	                <li><a class="pagination-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li>
	            </ul>
	            <a class="pagination-next" href="'.$url.($pagina+1).'/"><i class="fas fa-arrow-alt-circle-right"></i> &nbsp; Siguiente</a>
	            ';
	        }

	        $tabla .= '</nav>';
	        return $tabla;
	    }


	    /*----------  Funcion generar select ----------*/
		public function generarSelect($datos, $campo_db) {
			$check_select = '';
			$text_select = '';
			$count_select = 1;
			$select = '';
			foreach($datos as $row) {

				if($campo_db == $row) {
					$check_select = 'selected=""';
					$text_select = ' (Actual)';
				}

				$select .= '<option value="'.$row.'" '.$check_select.'>'.$count_select.' - '.$row.$text_select.'</option>';

				$check_select = '';
				$text_select = '';
				$count_select++;
			}
			return $select;
		}

		/*----------  Funcion generar codigos aleatorios  ----------*/
		protected function generarCodigoAleatorio($longitud, $correlativo) {
			$codigo = "";
			$caracter = "Letra";
			for($i = 1; $i <= $longitud; $i++) {
				if($caracter == "Letra") {
					$letra_aleatoria = chr(rand(ord("a"), ord("z")));
					$letra_aleatoria = strtoupper($letra_aleatoria);
					$codigo .= $letra_aleatoria;
					$caracter = "Numero";
				} else {
					$numero_aleatorio = rand(0, 9);
					$codigo .= $numero_aleatorio;
					$caracter = "Letra";
				}
			}
			return $codigo . "-" . $correlativo;
		}


		/*----------  Limitar cadenas de texto  ----------*/
		public function limitarCadena($cadena, $limite, $sufijo) {
			if(strlen($cadena) > $limite) {
				return substr($cadena, 0, $limite) . $sufijo;
			} else {
				return $cadena;
			}
		}
	}
?>