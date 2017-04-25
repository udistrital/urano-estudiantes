<?php

namespace gui\inicio\funcion;
include_once ($this->ruta . '../menuPrincipal/funcion/encriptar.class.php');
use gui\menuPrincipal\funcion\encriptar as encriptar;
require_once ($this->miConfigurador->getVariableConfiguracion('raizDocumento').'/core/log/logger.class.php');

class Logout {
	
	var $miEncriptador;
	var $configuracion_appserv;
	var $sesionUsuario;
	
	function __construct($sql) {
		$this->miSql = $sql;
		// Se crea una instancia del objeto encriptador.
		$this->miEncriptador = new encriptar ( $this->miSql );
		
		$this->configuracion_appserv = $this->miEncriptador->getConfiguracion();
		
		$this->sesionUsuario = \Sesion::singleton ();
	}

	function procesarFormulario() {
		//$user = $this->sesionUsuario->getSesionUsuarioId();
		$user = trim($this->sesionUsuario->getValorSesion('idUsuario'));
		//Inicia para el log
    	$this->logger = new \logger ();//Se agrega para log
    	$registro = $_REQUEST;
		$registro['opcion'] = 'SALIDA';
		$registro['usuario'] = $user;
		$this->logger->log_usuario($registro);
		//Termina para el log
		
		$sesionUsuarioId = $this->sesionUsuario->numeroSesion();
    	$this->sesionUsuario->terminarSesion($sesionUsuarioId);
		
		session_name($this->configuracion_appserv["usuarios_sesion"]);
		//session_start();
		include('logout_appserv.class.php');
	}

}

$miProcesador = new Logout($this->sql);
$miProcesador -> procesarFormulario();
?>