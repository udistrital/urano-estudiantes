<?php

namespace misDatos;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion
class Funcion {
	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $error;
	var $miRecursoDB;
	var $crypto;
	var $miInspectorHTML;
	function verificarCampos() {
		include_once ($this->ruta . "/funcion/verificarCampos.php");
		if ($this->error == true) {
			return false;
		} else {
			return true;
		}
	}
	function redireccionar($opcion, $valor = "") {
		include_once ($this->ruta . "/funcion/redireccionar.php");
	}	
	function registrarDatos($opcion, $valor = "") {
		include_once ($this->ruta . "/funcion/redireccionar.php");
	}
	function action() {
		/*
         * Se realiza la decodificación de los campos "validador" de los 
         * componentes del FormularioHtml. Se realiza la validación. En caso de que algún parámetro
         * sea ingresado fuera de lo correspondiente en el campo "validador", este será ajustado
         * (o convertido a) a un parámetro permisible o simplemente de no ser válido se devolverá 
         * el valor false. Si lo que se quiere es saber si los parámetros son correctos o no, se
         * puede introducir un tercer parámetro $arreglar, que es un parámetro booleano que indica,
         * si es pertinente o no realizar un recorte de los datos "string" para que cumpla los requerimientos
         * de longitud (tamaño) del campo.
         */
        if(isset($_REQUEST['validadorCampos'])){
            $validadorCampos = $this->miInspectorHTML->decodificarCampos($_REQUEST['validadorCampos']);
            $respuesta = $this->miInspectorHTML->validacionCampos($_REQUEST,$validadorCampos,false);
            if ($respuesta != false){
                $_REQUEST = $respuesta;
            } else {
                //Lo que se desea hacer si los parámetros son inválidos
                $respuesta = $this->miInspectorHTML->validacionCampos($_REQUEST,$validadorCampos,false,true);
                echo "<p>Usted ha ingresado parámetros de forma incorrecta al sistema.
                 El acceso incorrecto ha sido registrado en el sistema con la IP: ".$_SERVER['REMOTE_ADDR']."</p><br><p>".$respuesta."</p>";
				exit();
                //$url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php";
                //echo "<script>location.replace('" . $url . "')</script>";
            }
        }
		
		$resultado = true;
				
		// Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
		// aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
		// se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
		// en la carpeta funcion
		
		// Importante: Es adecuado que sea una variable llamada opcion o action la que guie el procesamiento:		
		
		if (isset ( $_REQUEST ['opcion'] )) {		
			switch ($_REQUEST ['opcion']) {		
				case 'actualizar' :
					include ($this->ruta.'/funcion/actualizarUsuario.php');
					break;
			}
		} else {
			$_REQUEST ['opcion'] = "mostrar";
			include_once ($this->ruta . "/funcion/formProcessor.php");
		}
		
		return $resultado;
	}
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->miMensaje = \Mensaje::singleton ();
		
		$this->miInspectorHTML = \InspectorHTML::singleton ();
		
		$conexion = "aplicativo";
		$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (! $this->miRecursoDB) {
			
			$this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
			$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		}
	}
	public function setRuta($unaRuta) {
		$this->ruta = $unaRuta;
	}
	function setSql($a) {
		$this->sql = $a;
	}
	function setFuncion($funcion) {
		$this->funcion = $funcion;
	}
	public function setLenguaje($lenguaje) {
		$this->lenguaje = $lenguaje;
	}
	public function setFormulario($formulario) {
		$this->formulario = $formulario;
	}
}

?>
