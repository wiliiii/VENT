<?php

	namespace app\controllers;
	use app\models\mainModel;

	class searchController extends mainModel{

		/*----------  Controlador modulos de busquedas  ----------*/
		public function modulosBusquedaControlador($modulo){

			$listaModulos=['userSearch','cashierSearch','clientSearch','categorySearch','productSearch','saleSearch'];

			if(in_array($modulo, $listaModulos)){
				return false;
			}else{
				return true;
			}
		}


		/*----------  Controlador iniciar busqueda  ----------*/
		public function iniciarBuscadorControlador() {
			$url = $this->limpiarCadena($_POST['modulo_url']);
			$texto = $this->limpiarCadena($_POST['txt_buscador']);
		
			// Validar módulo de búsqueda
			if ($this->modulosBusquedaControlador($url)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No podemos procesar la petición en este momento",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			// Validar texto de búsqueda
			if (empty($texto)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "Introduce un término de búsqueda",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			// Verificación de formato del texto
			if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\- ]{1,30}", $texto)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "El término de búsqueda no coincide con el formato solicitado",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			// Guardar el término de búsqueda en la sesión
			$_SESSION[$url] = $texto;
		
			$alerta = [
				"tipo" => "redireccionar",
				"url" => APP_URL . $url . "/"
			];
		
			return json_encode($alerta);
		}
		

		/*----------  Controlador eliminar busqueda  ----------*/
		public function eliminarBuscadorControlador() {
			$url = $this->limpiarCadena($_POST['modulo_url']);
		
			// Validar módulo de búsqueda
			if ($this->modulosBusquedaControlador($url)) {
				$alerta = [
					"tipo" => "simple",
					"titulo" => "Ocurrió un error inesperado",
					"texto" => "No podemos procesar la petición en este momento",
					"icono" => "error"
				];
				return json_encode($alerta);
				exit();
			}
		
			// Eliminar término de búsqueda de la sesión
			unset($_SESSION[$url]);
		
			$alerta = [
				"tipo" => "redireccionar",
				"url" => APP_URL . $url . "/"
			];
		
			return json_encode($alerta);
		}
		
	}