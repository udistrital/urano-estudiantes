<?php

namespace gui\inicio;

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
	var $miInspectorHTML;  //Línea Agregada
	function redireccionar($opcion, $valor = "") {
		include_once ($this->ruta . "/funcion/redireccionar.php");
	}	
	function procesarAjax() {
		include_once ($this->ruta . "funcion/ProcesarAjax.php");
	}
	function __construct() {
		$this->miInspectorHTML = \InspectorHTML::singleton (); //Línea Agregada
		
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->miMensaje = \Mensaje::singleton ();
		
		$conexion = "aplicativo";
		$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (! $this->miRecursoDB) {
			
			$this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
			$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		}
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
				$enlace = 'pagina=index&';
				$enlace .= 'msgIndex=validacion';
				$urlUrano = $this->miConfigurador->getVariableConfiguracion ( 'host' );
				$urlUrano .= $this->miConfigurador->getVariableConfiguracion ( 'site' ) . '/index.php?';
				$urlUrano .= $this->miConfigurador->getVariableConfiguracion ( 'enlace' );
				
				$enlace = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $enlace, $urlUrano );
				echo "<script type='text/javascript'> window.location='$enlace';</script>";
                // echo "Usted ha ingresado parámetros de forma incorrecta al sistema.
                // El acceso incorrecto ha sido registrado en el sistema con la IP: ".$_SERVER['REMOTE_ADDR'];
                // $url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php";
                // echo "<script>location.replace('" . $url . "')</script>";
            }
        }
		$resultado = true;
		
		// Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
		// aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
		// se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
		// en la carpeta funcion
		
		// Importante: Es adecuado que sea una variable llamada opcion o action la que guie el procesamiento:
		
		if (isset ( $_REQUEST ['procesarAjax'] )) {
			$this->procesarAjax ();
		} else if (isset ( $_REQUEST ["opcion"] )) {		
			switch ($_REQUEST ['opcion']) {		
				case "logout" :
					include ($this->ruta.'/funcion/Logout.php');
					break;
				case "login" :
					include ($this->ruta.'/funcion/Login.php');
					break;
			}
		} else {
			$_REQUEST ['opcion'] = "mostrar";
			include_once ($this->ruta . "/funcion/formProcessor.php");
		}
		
		return $resultado;
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
