<?php

namespace misDatos\funcion;
use misDatos\funcion\redireccionar;
include_once ('redireccionar.php');

if (!isset($GLOBALS["autorizado"])) {
	include ("../index.php");
	exit();
}
class Registrar {

	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;

	function __construct($lenguaje, $sql, $funcion) {

		$this -> miConfigurador = \Configurador::singleton();
		$this -> miConfigurador -> fabricaConexiones -> setRecursoDB('principal');
		$this -> lenguaje = $lenguaje;
		$this -> miSql = $sql;
		$this -> miFuncion = $funcion;
	}

	function procesarFormulario() {
		//Se limpia el escape de inyección SQL para cada campo.
				
		$_REQUEST['direccion'] = isset($_REQUEST['direccion']) ? $_REQUEST['direccion'] : '';
		$_REQUEST['direccion'] = str_replace('\\_', '_', $_REQUEST['direccion']);
		
		$_REQUEST['telefono'] = isset($_REQUEST['telefono']) ? $_REQUEST['telefono'] : '';
		$_REQUEST['telefono'] = str_replace('\\_', '_', $_REQUEST['telefono']);
		
		$_REQUEST['celular'] = isset($_REQUEST['celular']) ? $_REQUEST['celular'] : '';
		$_REQUEST['celular'] = str_replace('\\_', '_', $_REQUEST['celular']);

		$_REQUEST['correo_institucional'] = isset($_REQUEST['correo_institucional']) ? $_REQUEST['correo_institucional'] : '';
		$_REQUEST['correo_institucional'] = str_replace('\\_', '_', $_REQUEST['correo_institucional']);

		$_REQUEST['correo_personal'] = isset($_REQUEST['correo_personal']) ? $_REQUEST['correo_personal'] : '';
		$_REQUEST['correo_personal'] = str_replace('\\_', '_', $_REQUEST['correo_personal']);

		$conexion = "academica_ac";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		//Actualizar datos de estudiante en la académica tabla ACEST
		$cadenaSql = $this -> miSql -> getCadenaSql('actualizarEstudiante', $_REQUEST);
		$resultadoEstudiante = $esteRecursoDB -> ejecutarAcceso($cadenaSql, "actualizar");
		//oci8 solo direrencia 'busqueda'

		//Actualizar datos de estudiante en la académica tabla ACESTOTR
		$cadenaSql = $this -> miSql -> getCadenaSql('actualizarEstudianteOtros', $_REQUEST);
		$resultadoEstudianteOtros = $esteRecursoDB -> ejecutarAcceso($cadenaSql, "actualizar");
		//oci8 solo direrencia 'busqueda'

		///////////////////////////////////////////////
		$conexionLamasu = 'lamasu';
		$esteRecurso = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexionLamasu);

		$cadenaSql = $this -> miSql -> getCadenaSql('actualizarDatos', $_REQUEST);
		$resultadoLamasu = $esteRecurso -> ejecutarAcceso($cadenaSql, "actualizar");

		if ($resultadoLamasu and $resultadoEstudiante and $resultadoEstudianteOtros) {
			redireccion::redireccionar('inserto');
			exit();
		} else {
			redireccion::redireccionar('noInserto');
			exit();
		}

	}

	function resetForm() {
		foreach ($_REQUEST as $clave => $valor) {

			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset($_REQUEST[$clave]);
			}
		}
	}

}

$miRegistrador = new Registrar($this -> lenguaje, $this -> sql, $this -> funcion);

$resultado = $miRegistrador -> procesarFormulario();
?>
