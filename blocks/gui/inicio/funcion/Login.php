<?php
namespace gui\inicio\funcion;
include_once('verifica_appserv.class.php');
use gui\inicio\funcion\verifica_appserv as verifica_appserv;
require_once ($this->miConfigurador->getVariableConfiguracion('raizDocumento').'/core/log/logger.class.php');

class Login
{
    var $miConfigurador;
    var $miAutenticador;
	var $sesionUsuario;
    
    function __construct()
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miAutenticador = \Autenticador::singleton();
		$this->sesionUsuario = \Sesion::singleton ();
    }
    function procesarFormulario()
    {
        $resultado = $this->miAutenticador->iniciarAutenticacion();
        $verifica = new verifica_appserv();
        //die('hi');
        $user = $verifica->action();
        
		if($user){
			$this->sesionUsuario->crearSesion($user);
			//Inicia para el log
        	$this->logger = new \logger ();//Se agrega para log
        	unset($_REQUEST['clave']);
        	$registro = $_REQUEST;
			$registro['opcion'] = 'INGRESO';
			$registro['usuario'] = $user;
			$this->logger->log_usuario($registro);
			//Termina para el log
		}
        
        //Si la autenticacion fue exitosa va a la pagina bienvenido
        if ($resultado) {
            $directorio = $this->miConfigurador->getVariableConfiguracion("host");
            $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
            $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");
            $valorCodificado = "pagina=bienvenido";
            //$valorCodificado .= "&autenticado=true";
            $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);
            $enlace          = $directorio . '=' . $valorCodificado;
            
            header('Location: ' . $enlace);
        }
        return $resultado;
    }
}

$miProcesador = new Login();
$miProcesador->procesarFormulario();
?>