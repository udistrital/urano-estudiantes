<?php

class VerificarSesion {
	var $sesionUsuario;
	
	function __construct() {
		$this->sesionUsuario = \Sesion::singleton ();
	}
	function procesarFormulario() {
		$respuesta = $this->sesionUsuario->getValorSesion('idUsuario');
		return $respuesta;
	}
}

$miProcesador = new VerificarSesion ();
$respuesta = $miProcesador->procesarFormulario();
?>