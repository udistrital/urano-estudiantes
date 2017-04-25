<?PHP
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
 * @name          verifica.class.php 
 * @author        Jairo Lavado
 * @revision      Última revisión 25 de Octubre
 * @author		  Jorge Useche
 * @revision	  5 de Marzo de 2017
 ****************************************************************************
 * @subpackage   
 * @package		  clase
 * @copyright    
 * @version       0.3
 * @author        Jairo Lavado
 * @link		
 * @description   Clase que realiza la verificación de logueo y registro de sesiones
 *
 ******************************************************************************/
namespace gui\inicio\funcion;

require_once ('config.class.php');
require_once ('funcionGeneral_appserv.class.php');
require_once ('sesion.class.php');
require_once ('accesos.class.php');
require_once ('expirar_sesiones.class.php');

use gui\inicio\funcion\funcionGeneral_appserv as funcionGeneral_appserv;
use gui\inicio\funcion\config as config;
use gui\inicio\funcion\sesiones as sesiones;
use gui\inicio\funcion\acceso as acceso;
use gui\inicio\funcion\expira_sesion as expira_sesion;
use gui\inicio\funcion\log as log;

class verifica_appserv extends funcionGeneral_appserv {
	private $configuracion;
	private $acceso_OCI;
	private $acceso_MY;
	private $acceso_Est;
	private $acceso_Fun;
	private $reg_acceso;
	private $cripto;
	private $usser;
	private $pwd;
	private $numero;
	private $nueva_sesion;
	private $nom_us;
	private $tipoUser;
	private $CarpetaSesion;
	private $NumSesionEst;
	private $NumSesionFun;
	private $TimeSesionEst;
	private $TimeSesionFun;
	private $ingresosEst;
	private $expira;
	private $verificador;
	private $varIndex;
	private $veces;
	private $retardo;
	private $semaf_id;
	private $semaforo;
	private $redirLogueo;
	private $varNombres;
	private $histSemilla;
	private $indice;
	public function __construct() {
		$esta_configuracion = new config ();
		$this->configuracion = $esta_configuracion->variable ( '../' );
		$this->cripto = new encriptar ();
		
		$this->nueva_sesion = new sesiones ( $this->configuracion );
		$this->reg_acceso = new acceso ( $this->configuracion );
		$this->expira = new expira_sesion ();
		$this->acceso_MY = '';
		/* Variables para el control de accesos y errores al index */
		$this->varIndex ['verificador'] = date ( "YmdH" );
		$this->varIndex ['enlace'] = $this->configuracion ['enlace'];
		$this->retardo = 4;
		/* rescarta los nombres y valores de la variables de logueo */
		$control = array ();
		$this->usser = $_REQUEST ['usuario'];
		$this->pwd = sha1 ( md5 ( $_REQUEST ['clave'] ) );
		$this->indice = '/index.html';
		$this->redirLogueo = $this->configuracion ['host_logueo'] . $this->configuracion ['site'] . $this->indice;
	}
	function action() {
		$pagina = $this->configuracion ['host'] . $this->configuracion ['site'] . $this->indice;
		
		/* Debe revisar el recaptcha, si falla destruye la sesión */
		
		if ($this->configuracion ['activar_recaptcha'] == 'S') {
			// grab recaptcha library
			require_once 'recaptchalib.php';
			
			// your secret key
			$secret = $this->configuracion ['recaptcha_secret'];
			
			// empty response
			$response = null;
			
			// check secret key
			$reCaptcha = new ReCaptcha ( $secret );
			
			// if submitted check response
			$proxy_settings = $this->configuracion ['activar_proxy'];
			if (isset ( $_POST ['g-recaptcha-response'] ) && $_POST ['g-recaptcha-response']) {
				$response = $reCaptcha->verifyResponse ( $_SERVER ['REMOTE_ADDR'], $_POST ['g-recaptcha-response'], $proxy_settings );
			}
			
			// incorrect captcha
			if (! ($response != null && $response->success)) {
				if (isset ( $_SESSION ) && empty ( $_SESSION ['usuario_login'] )) {
					session_destroy ();
				}
				$variable = 'msj=error117Captcha';
				$this->direccionar ( $this->redirLogueo, $variable );
			}
		}
		
		$pagina_path = $this->configuracion ['host'] . $this->configuracion ['site'];
		if (! isset ( $_SERVER ['HTTP_REFERER'] ) || $_SERVER ['HTTP_REFERER'] == "" || (strrpos ( explode ( "?", $_SERVER ['HTTP_REFERER'] )[0], $pagina_path ))) {
			/* Impide el acceso desde otros servidores */
			$variable = 'msj=error115';
			$this->direccionar ( $this->redirLogueo, $variable );
		} elseif (empty ( $this->usser )) {
			/* Si el usuario está vacío */
			/* invoca la funcion que cuenta los accesos errados simultaneos */
			$variable = 'msj=error4';
			$this->direccionar ( $this->redirLogueo, $variable );
		} elseif (! is_numeric ( $this->usser )) {
			/* Si el usuario no es numerico */
			/* invoca la funcion que cuenta los accesos errados simultaneos */
			$variable = 'msj=error5';
			$this->direccionar ( $this->redirLogueo, $variable );
		} elseif (isset ( $this->usser ) && isset ( $this->pwd )) {
			/* genera las conexiones a BD mysql */
			$this->acceso_MY = $this->conectarDB ( $this->configuracion, "logueo" );
			/* consulta los datos del usuario */
			$cod_consul = $this->cadena_sql ( 'busca_usMY', $this->usser );
			$registro = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $cod_consul, "busqueda" );
			if (is_array ( $registro )) {
				if ($registro [0] ['PWD'] != $this->pwd) /*redirecciona si la contraseña no coincide*/ {
					/* Registra el acceso errado al sistema */
					$this->registro_acceso_errado ( 'Contraseña incorrecta' );
					/* invoca la funcion que cuenta los accesos errados simultaneos */
					$variable = 'msj=error3';
					$this->direccionar ( $this->redirLogueo, $variable );
				} elseif ($registro [0] ['EST'] != 'A') /*redirecciona si el usuario no esta activo*/ {
					/* invoca la funcion que cuenta los accesos errados simultaneos */
					$variable = 'msj=error7';
					$this->direccionar ( $this->redirLogueo, $variable );
				} elseif ($registro [0] ['COD'] == stripslashes ( $this->usser ) && (strtolower ( $this->numero . $registro [0] ['PWD'] ) == strtolower ( $this->pwd ))) {
					/**
					 * Se implementa función de guardar hash de clave en una tabla
					 */
					$this->registrar_password_hash ( $_REQUEST ['usuario'], $_REQUEST ['clave'] );
					
					$this->tipoUser = $registro [0] ['TIP_US'];
					// invocala funcion de actualizar la semilla de codificacion
					$this->actualiza_semilla ();
					// *ejecuta la restriccion de acceso segun franjas - nuevo */
					if (strtoupper ( $this->configuracion ['activar_franjas'] ) == 'S' && $registro [0] ['NIVEL'] == 'PREGRADO' && ($this->tipoUser == '51' || $this->tipoUser == '52')) {
						$usser = array (
								'cod' => $registro [0] ['COD'],
								'facultad' => $registro [0] ['FAC'],
								'proyecto' => $registro [0] ['PROY'] 
						);
						$this->restriccion_franjas ( $usser );
					}
					// *ejecuta la restriccion de acceso adicional que se quiera presentar para los usuarios estudiante que el tamaño sea menos a 11 */
					if (strtoupper ( $this->configuracion ['activar_otras_restricciones_estudiante'] ) == 'S' && ($this->tipoUser == '51' || $this->tipoUser == '52')) {
						$this->restriccion_acceso ();
					}
					ob_start (); // habilita el buffer de salida
					session_cache_limiter ( 'nocache,private' );
					session_name ( $this->configuracion ["usuarios_sesion"] );
					session_start ();
					/* registra en el log el acceso del usuario de oracle geconexlog */
					$this->reg_acceso->registrar ( $this->usser, '2' );
					/* registra el inicio de la sesion en el sistema */
					$_SESSION ['usuario_login'] = $registro [0] ['COD'];
					$_SESSION ['usuario_password'] = $registro [0] ['PWD'];
					$_SESSION ['usuario_nivel'] = $registro [0] ['TIP_US'];
					$sesion_id = $this->inicio_sesion ( 'condor' );
					//$this->selecciona_pagina ( $this->tipoUser );
					return $this->usser;
				}
			} else {
				/* si no existen registros devuelve error */
				$variable = 'msj=error4';
				$variable .= '&' . $this->varNombres ['acceso'] . '=' . $this->veces;
				$this->direccionar ( $this->redirLogueo, $variable );
			}
		}
	}
	
	/**
	 * funcion que redirecciona a alguna pagina especifica
	 * 
	 * @name direccionar
	 * @param type $url        	
	 * @param type $var        	
	 */
	function direccionar($url, $var) {
		echo "<script type='text/javascript'> window.location='$url?$var';</script>";
		exit ();
	}
	function actualiza_semilla() {
		unset ( $sem_actual );
		$sem_actual = date ( "m/d/Y" );
		$actual = strtotime ( $sem_actual );
		$ultima_actualizacion = strtotime ( substr ( $this->configuracion ['fecha_actualizacion_verificador'], 0, 10 ) );
		
		$this->acceso_MY = $this->conectarDB ( $this->configuracion, "logueo" );
		
		if ($actual > $ultima_actualizacion && $this->configuracion ['host'] == $this->configuracion ['host_logueo']) { // genera la nueva clave segun la antigua
			$nva_clave = md5 ( substr ( $this->configuracion ['verificador'], 0, 10 ) );
			// guarda la nueva clave en las bases de datos registradas en la tabla dbms_bd
			
			$cod_consul = $this->cadena_sql ( 'busca_db', '' );
			$registro = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $cod_consul, "busqueda" );
			if (is_array ( $registro )) {
				$i = 0;
				while ( isset ( $registro [$i] [0] ) ) {
					$variable = array (
							'DB' => $registro [$i] [0],
							'PREF' => $registro [$i] [2],
							'vl' => $nva_clave,
							'param' => 'verificador' 
					);
					$sql_actualiza = $this->cadena_sql ( 'actualizar_configuracion', $variable );
					$actualiza_sem = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $sql_actualiza, "" );
					if (isset ( $actualiza_sem )) {
						if (strtoupper ( $this->configuracion ['activar_redireccion_estudiante'] ) == 'S' && isset ( $this->acceso_Est )) {
							/* realiza la conexion a la bd del servidor de Estudiantes */
							$this->ejecutarSQL ( $this->configuracion, $this->acceso_Est, $sql_actualiza, "" );
						}
						if (strtoupper ( $this->configuracion ['activar_redireccion_funcionario'] ) == 'S' && isset ( $this->acceso_Fun )) {
							/* realiza la conexion a la bd del servidor de Funcionarios */
							$this->ejecutarSQL ( $this->configuracion, $this->acceso_Fun, $sql_actualiza, "" );
						}
					}
					$i ++;
				}
				$variable = array (
						'DB' => 'dbms',
						'PREF' => 'dbms_',
						'vl' => $h_actual = date ( "m/d/Y H:i:s" ),
						'param' => 'fecha_actualizacion_verificador' 
				);
				$sql_actualiza = $this->cadena_sql ( 'actualizar_configuracion', $variable );
				$this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $sql_actualiza, "" );
			}
			
			// guardar el historial de semillas
			// $ultima_sem = fopen($this->CarpetaSesion . $this->histSemilla, "a");
			// fwrite($ultima_sem, "$nva_clave \t $h_actual" . PHP_EOL);
			// fclose($ultima_sem);
		}
	} // fin funcion actualizar semilla
	function restriccion_franjas($user) {
		/*
		 * unset($h_actual);
		 * $d_actual=date("m/d/Y");
		 * $dia_actual=strtotime($d_actual);
		 * $h_actual=date("m/d/Y H:i:s");
		 * $hora_actual=strtotime($h_actual);
		 */
		$user ['fec_actual'] = date ( "Y-m-d H:i:s" );
		$this->acceso_MY = $this->conectarDB ( $this->configuracion, "logueo" );
		$Franja_consul = $this->cadena_sql ( 'verFranja', $user );
		$regFranja = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $Franja_consul, "busqueda" );
		if (! $regFranja) {
			/* Registra el acceso incompleto al sistema */
			$this->acceso_MY = $this->conectarDB ( $this->configuracion, 'logueo' );
			$this->registro_acceso_Incompleto ( ' Intento de Acceso a franja que no Corresponde ' );
			$variable = 'msj=error110Franja';
			$this->direccionar ( $this->redirLogueo, $variable );
		}
	} // fin funcion franjas
	function restriccion_acceso() {
		unset ( $h_actual );
		$h_actual = date ( "m/d/Y H:i:s" );
		$dias_actual = strtotime ( $h_actual );
		$restriccion_ini = strtotime ( substr ( $this->configuracion ['restriccion_acceso_est'], 0, 19 ) );
		$restriccion_fin = strtotime ( substr ( $this->configuracion ['restriccion_acceso_est'], 20, 19 ) );
		if ($dias_actual > $restriccion_ini && $dias_actual < $restriccion_fin) {
			/* Registra el acceso incompleto al sistema */
			$this->acceso_MY = $this->conectarDB ( $this->configuracion, 'logueo' );
			$this->registro_acceso_Incompleto ( ' Intento de Acceso a franja de coordinadores ' );
			$variable = 'msj=error116';
			$this->direccionar ( $this->redirLogueo, $variable );
		}
	} // fin funcion restricciones
	
	/**
	 *
	 * Funcion que inicia y registra la sesion del usuario, borra las sesiones antiguas.
	 * 
	 * @param type $acceso        	
	 * @param type $aplicacion        	
	 * @return type
	 * @name inicio_sesion
	 */
	function inicio_sesion($aplicacion) {
		// inicia la busqueda de sesiones antiguas guardadas
		$user ['var'] = 'id_usuario';
		$user ['vl'] = $this->usser;
		$cod_consul = $this->cadena_sql ( 'rescatar_id_sesion', $user );
		// $registro= $this->ejecutarSQL($this->configuracion, $this->acceso_OCI, $cod_consul,"busqueda");
		$reg_ses = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $cod_consul, "busqueda" );
		if ($reg_ses) { // borra las sesiones antiguas guardadas
			foreach ( $reg_ses as $key => $value ) {
				$consulta_borra = $this->cadena_sql ( 'borrar_sesion', $reg_ses [$key] [0] );
				$borra_ses = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $consulta_borra, "" );
			}
		}
		
		if ($this->configuracion ['activar_redireccion_funcionario'] == 'S') {
			$reg_sesFun = $this->ejecutarSQL ( $this->configuracion, $this->acceso_Fun, $cod_consul, "busqueda" );
			if ($reg_sesFun) { // borra las sesiones antiguas guardadas en servidor de funcionarios
				foreach ( $reg_sesFun as $key => $value ) {
					$consulta_borra_sesFun = $this->cadena_sql ( 'borrar_sesion', $reg_sesFun [$key] [0] );
					$borra_sesFun = $this->ejecutarSQL ( $this->configuracion, $this->acceso_Fun, $consulta_borra_sesFun, "" );
				}
			}
		}
		
		if ($this->configuracion ['activar_redireccion_estudiante'] == 'S') {
			$reg_sesEst = $this->ejecutarSQL ( $this->configuracion, $this->acceso_Est, $cod_consul, "busqueda" );
			if ($reg_sesEst) { // borra las sesiones antiguas guardadas en servidor de estudiantes
				foreach ( $reg_sesEst as $key => $value ) {
					$consulta_borra_sesEst = $this->cadena_sql ( 'borrar_sesion', $reg_sesEst [$key] [0] );
					$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $this->acceso_Est, $consulta_borra_sesEst, "" );
				}
			}
		}
		
		/* obtiene una nueva sesion */
		$nva_sesion = $this->crear_sesion ( $this->usser, $this->tipoUser );
		/* registra la sesión */
		if ($this->tipoUser == '51' || $this->tipoUser == '52') { // Sesiones de estudiante horas y créditos respectivamente
			$this->acceso_MY = $this->conectarDB ( $this->configuracion, 'logueo' );
			//Ya se hace desde urano, se comenta
			//$this->registro_acceso_Exitoso ( ' Acceso Exitoso ' );
			
			$this->registrar_sesion ( $this->acceso_MY, $nva_sesion, $aplicacion, 'estudiante' );
		} else { // Funcionarios y docentes
			$this->acceso_MY = $this->conectarDB ( $this->configuracion, 'logueo' );
			//Ya se hace desde urano, se comenta
			//$this->registro_acceso_Exitoso ( ' Acceso Exitoso ' );
			
			$this->registrar_sesion ( $this->acceso_MY, $nva_sesion, $aplicacion, 'funcionario' );
		}
	} // fin funcion inicio_sesion
	
	/**
	 *
	 * @param type $usuario        	
	 * @param type $nivel_acceso        	
	 * @return type
	 */
	function crear_sesion($usuario, $nivel_acceso) {
		// Identificador de sesion
		$fecha = explode ( " ", microtime () );
		$rand = rand ();
		$sesion_id = md5 ( $fecha [1] . substr ( $fecha [0], 2 ) . $usuario . $rand . $nivel_acceso );
		/* Actualizar la cookie */
		setcookie ( "aplicativo", $sesion_id, (time () + $this->configuracion ['expiracion']), "/" );
		return $sesion_id;
	}
	function registrar_sesion($conexion, $sesion, $aplicacion, $tipo_ses) {
		$variable ['ses'] = $sesion;
		$variable ['vr'] = 'usuario';
		$variable ['vl'] = $this->usser;
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
		$variable ['vr'] = 'id_usuario';
		$variable ['vl'] = $this->usser;
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
		$variable ['vr'] = 'acceso';
		$variable ['vl'] = $this->tipoUser;
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
		$variable ['vr'] = 'aplicacion';
		$variable ['vl'] = $aplicacion;
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
		$variable ['vr'] = 'expiracion';
		$variable ['vl'] = (time () + $this->configuracion ["expiracion"]);
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
		$variable ['vr'] = 'tipo_sesion';
		$variable ['vl'] = $tipo_ses;
		$consulta_reg_ses = $this->cadena_sql ( 'guardar_valor_sesion', $variable );
		$borra_sesEst = $this->ejecutarSQL ( $this->configuracion, $conexion, $consulta_reg_ses, "" );
	} // Fin del método registrar_sesion
	function selecciona_pagina($nivel) {
		switch ($nivel) {
			case 4 :
				$this->direccionar ( '../coordinador/coordinador.php', '' );
				break;
			case 16 :
				$this->direccionar ( '../decano/decano.php', '' );
				break;
			case 20 :
				$this->direccionar ( '../administracion/adm_index.php', '' );
				break;
			case 24 :
				$this->direccionar ( '../funcionario/funcionario.php', '' );
				break;
			case 26 :
				$this->direccionar ( '../proveedor/proveedor.php', '' );
				break;
			case 28 :
				$this->direccionar ( '../coordinadorcred/coordinadorcred.php', '' );
				break;
			case 30 :
				$this->direccionar ( '../docentes/docente.php', '' );
				break;
			case 31 :
				$this->direccionar ( '../rector/rector.php', '' );
				break;
			case 32 :
				$this->direccionar ( '../vicerrector/vicerrector.php', '' );
				break;
			case 33 :
				$this->direccionar ( '../registro/registro.php', '' );
				break;
			case 34 :
				$this->direccionar ( '../asesor/asesor.php', '' );
				break;
			case 51 :
				$this->direccionar ( '../estudiantes/estudiante.php', '' );
				break;
			case 52 :
				// $this->direccionar('../estudianteCreditos/estudianteCreditos_redirecciona_adiciones.php', '');//En época de adiciones y cancelaciones
				$this->direccionar ( '../estudianteCreditos/estudianteCreditos.php', '' ); // En temporada baja sin adiciones
				break;
			case 61 :
				$this->direccionar ( '../asisVicerrectoria/asisVicerrectoria.php', '' );
				break;
			case 68 :
				$this->direccionar ( '../bienestarInstitucional/bienestar.php', '' );
				break;
			case 72 :
				$this->direccionar ( '../divRecursosHumanos/divRecursosHumanos.php', '' );
				break;
			case 75 :
				$this->direccionar ( '../admin_sga/admin_sga.php', '' );
				break;
			case 80 :
				$this->direccionar ( '../soporte/soporte.php', '' );
				break;
			case 83 :
				$this->direccionar ( '../secacademico/secacademico.php', '' );
				break;
			case 84 :
				$this->direccionar ( '../desarrolloOAS/desarrolloOAS.php', '' );
				break;
			case 87 :
				$this->direccionar ( '../moodle/moodle.php', '' );
				break;
			case 88 :
				$this->direccionar ( '../docencia/docencia.php', '' );
				break;
			case 105 :
				$this->direccionar ( '../funcionario_planeacion/funcionario_planeacion.php', '' );
				break;
			case 109 :
				$this->direccionar ( '../asistenteContabilidad/asistenteCont.php', '' );
				break;
			case 110 :
				$this->direccionar ( '../asistenteProyecto/asistente.php', '' );
				break;
			case 111 :
				$this->direccionar ( '../asistenteDecanatura/asistente.php', '' );
				break;
			case 112 :
				$this->direccionar ( '../asistenteSecretaria/asistente.php', '' );
				break;
			case 113 :
				$this->direccionar ( '../secretarioGeneral/secgeneral.php', '' );
				break;
			case 114 :
				$this->direccionar ( '../secretarioProyecto/secretario.php', '' );
				break;
			case 115 :
				$this->direccionar ( '../secretarioDecanatura/secretario.php', '' );
				break;
			case 116 :
				$this->direccionar ( '../secretarioSecretaria/secretario.php', '' );
				break;
			case 117 :
				$this->direccionar ( '../asistenteRelInterinstitucionales/asistenteRelInterinstitucionales.php', '' );
				break;
			case 118 :
				$this->direccionar ( '../laboratorios/laboratorios.php', '' );
				break;
			case 119 :
				$this->direccionar ( '../asistenteILUD/asistenteILUD.php', '' );
				break;
			case 120 :
				$this->direccionar ( '../consultor/consultor.php', '' );
				break;
			case 121 :
				$this->direccionar ( '../egresado/egresado.php', '' );
				break;
			case 122 :
				$this->direccionar ( '../asistenteTesoreria/asistenteTesoreria.php', '' );
				break;
			case 123 :
				$this->direccionar ( '../contratista/contratista.php', '' );
				break;
			case 125 :
				$this->direccionar ( '../voto/voto.php', '' );
				break;
			default :
				break;
		}
	}
	function registro_acceso_errado($mensaje) {
		// Registra acceso errado en log
		include_once ('logAcceso_appserv.class.php');
		$this->log_us = new log ();
		$registro ['id_usuario'] = $this->usser;
		$registro ['accion'] = 'LOGUEO';
		$registro ['id_registro'] = $this->usser;
		$registro ['tipo_registro'] = 'Acceso Errado';
		$registro ['nombre_registro'] = $mensaje;
		$registro ['descripcion'] = 'Se registra intento de acceso errado debido a ' . $mensaje . ' , por parte del usuario ' . $this->usser;
		$this->log_us->log_acceso ( $registro );
		// verifica la cantidad de accesos errados por día
		$sql_acceso = $this->cadena_sql ( 'consultaAccesosErrados', $registro );
		$resultadoErrados = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $sql_acceso, 'busqueda' );
		// verifica el maximo de accesos para notificación por correo
		if (is_array ( $resultadoErrados ) && $this->configuracion ['max_notificar_accesos_errados'] > 0 && ($resultadoErrados [0] ['accesos'] % $this->configuracion ['max_notificar_accesos_errados']) == 0) {
// 			include_once ('envioCorreo.class.php');
// 			$this->correo = new correo ();
// 			$cod_consul = $this->cadena_sql ( 'busca_us', $this->usser );
// 			$resultadoUS = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $cod_consul, 'busqueda' );
// 			if (is_array ( $resultadoUS )) {
// 				foreach ( $resultadoUS as $key => $value ) {
// 					if ($resultadoUS [$key] ['EST'] == 'A' && ($resultadoUS [$key] ['TIP_US'] == '51' || $resultadoUS [$key] ['TIP_US'] == '52')) {
// 						$nivelUS = 'estudiante';
// 					} elseif ($resultadoUS [$key] ['EST'] == 'A' && $resultadoUS [$key] ['TIP_US'] == '121') {
// 						$nivelUS = 'egresado';
// 					} elseif ($resultadoUS [$key] ['EST'] == 'A') {
// 						$nivelUS = 'general';
// 					}
// 				}
// 			}
// 			$parametros ['usuario'] = $this->usser;
// 			$parametros ['accesos'] = $resultadoErrados [0] ['accesos'];
// 			$parametros ['fecha'] = $resultadoErrados [0] ['fecha'];
// 			$this->correo->enviarCorreoAccesos ( $parametros, 'accesoErrado', $nivelUS, $this->configuracion ['correo_recibo_oas'] );
		}
	} // registro_acceso_errado
	function registro_acceso_Incompleto($mensaje) {
		// Registra acceso errado en log
		include_once ('logAcceso_appserv.class.php');
		$this->log_us = new log ();
		$registro ['id_usuario'] = $this->usser;
		$registro ['accion'] = 'LOGUEO';
		$registro ['id_registro'] = $this->usser;
		$registro ['tipo_registro'] = 'Acceso Incompleto';
		$registro ['nombre_registro'] = $mensaje;
		$registro ['descripcion'] = 'Se registra intento de acceso incompleto debido a ' . $mensaje . ' , por parte del usuario ' . $this->usser;
		$this->log_us->log_acceso ( $registro );
		
		// verifica la cantidad de accesos errados por día
		$sql_acceso = $this->cadena_sql ( 'consultaAccesos', $registro );
		$resultadoErrados = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $sql_acceso, 'busqueda' );
		// verifica el maximo de accesos para notificación por correo
		if (is_array ( $resultadoErrados ) && $this->configuracion ['max_notificar_accesos'] > 0 && ($resultadoErrados [0] ['accesos'] % $this->configuracion ['max_notificar_accesos']) == 0) {
// 			include_once ($this->configuracion ['raiz_documento'] . $this->configuracion ['clases'] . '/envioCorreo.class.php');
// 			$this->correo = new correo ();
// 			$cod_consul = $this->cadena_sql ( 'busca_us', $this->usser );
// 			$resultadoUS = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $cod_consul, 'busqueda' );
// 			if (is_array ( $resultadoUS )) {
// 				foreach ( $resultadoUS as $key => $value ) {
// 					if ($resultadoUS [$key] ['EST'] == 'A' && ($resultadoUS [$key] ['TIP_US'] == '51' || $resultadoUS [$key] ['TIP_US'] == '52')) {
// 						$nivelUS = 'estudiante';
// 					} elseif ($resultadoUS [$key] ['EST'] == 'A' && $resultadoUS [$key] ['TIP_US'] == '121') {
// 						$nivelUS = 'egresado';
// 					} elseif ($resultadoUS [$key] ['EST'] == 'A') {
// 						$nivelUS = 'general';
// 					}
// 				}
// 			}
// 			$parametros ['usuario'] = $this->usser;
// 			$parametros ['accesos'] = $resultadoErrados [0] ['accesos'];
// 			$parametros ['fecha'] = $resultadoErrados [0] ['fecha'];
// 			$this->correo->enviarCorreoAccesos ( $parametros, 'accesoIncompleto', $nivelUS, $this->configuracion ['correo_recibo_oas'] );		
		}
	} // registro_acceso_errado
	function registrar_password_hash($usuario, $clave) {
		require 'lib_password.php';
		$variable = array (
				'usuario' => $usuario,
				'password_hash' => password_hash ( $clave, PASSWORD_DEFAULT ) 
		);
		$sql_acceso = $this->cadena_sql ( 'guardar_password_hash', $variable );
		$resultado = $this->ejecutarSQL ( $this->configuracion, $this->acceso_MY, $sql_acceso, '' );
	}
	function cadena_sql($tipo, $variable) {
		switch ($tipo) {
			case 'busca_us' :
				$cadena_sql = "SELECT ";
				$cadena_sql .= "cla_codigo COD, ";
				$cadena_sql .= "cla_clave PWD, ";
				$cadena_sql .= "cla_tipo_usu TIP_US, ";
				$cadena_sql .= "cla_estado EST ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['sql_tabla1'] . " ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "cla_codigo='" . $this->usser . "' ";
				$cadena_sql .= "ORDER BY cla_estado,cla_tipo_usu";
				break;
			case "busca_usMY" :
				$cadena_sql = "SELECT ";
				$cadena_sql .= "cla_codigo COD, ";
				$cadena_sql .= "cla_clave PWD, ";
				$cadena_sql .= "cla_tipo_usu TIP_US, ";
				$cadena_sql .= "cla_estado EST, ";
				$cadena_sql .= "cla_facultad FAC, ";
				$cadena_sql .= "cla_proyecto PROY, ";
				$cadena_sql .= "cla_cod_nivel COD_NIVEL, ";
				$cadena_sql .= "cla_nivel NIVEL ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['sql_tabla1'] . " ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "cla_codigo='" . $this->usser . "' ";
				$cadena_sql .= "ORDER BY cla_estado,cla_tipo_usu";
				break;
			case "sesiones" :
				$cadena_sql = "SELECT ";
				$cadena_sql .= "count(distinct id_sesion) NUM_SES ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "valor_sesion ";
				break;
			case "rescatar_id_sesion" :
				$cadena_sql = "SELECT DISTINCT ";
				$cadena_sql .= "id_sesion ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "valor_sesion ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "variable='" . $variable ['var'] . "' ";
				$cadena_sql .= "AND ";
				$cadena_sql .= "valor ='" . $variable ['vl'] . "' ";
				break;
			case "borrar_sesion" :
				$cadena_sql = "DELETE  ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "valor_sesion ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "id_sesion='" . $variable . "' ";
				break;
			case "guardar_valor_sesion" :
				$cadena_sql = "INSERT INTO ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "valor_sesion (id_sesion,variable,valor) ";
				$cadena_sql .= "VALUES (";
				$cadena_sql .= "'" . $variable ['ses'] . "', ";
				$cadena_sql .= "'" . $variable ['vr'] . "', ";
				$cadena_sql .= "'" . $variable ['vl'] . "' ";
				$cadena_sql .= ");";
				break;
			case "actualizar_valor_sesion" :
				$cadena_sql = "UPDATE ";
				$cadena_sql .= $this->configuracion ["prefijo"] . "valor_sesion ";
				$cadena_sql .= "SET ";
				$cadena_sql .= "valor='" . $variable ['vl'] . "' ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "id_sesion='" . $variable ['ses'] . "' ";
				$cadena_sql .= "AND ";
				$cadena_sql .= "variable='" . $variable ['vr'] . "' ";
				break;
			case "actualizar_configuracion" :
				$cadena_sql = "UPDATE ";
				$cadena_sql .= $variable ['DB'] . "." . $variable ['PREF'] . "configuracion ";
				$cadena_sql .= "SET ";
				$cadena_sql .= "valor= '" . $variable ['vl'] . "' ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= $variable ['PREF'] . "configuracion.`parametro` ='" . $variable ['param'] . "'; ";
				break;
			case "busca_db" :
				$cadena_sql = "SELECT ";
				$cadena_sql .= "`nombre`, ";
				$cadena_sql .= "`tabla_sesion`, ";
				$cadena_sql .= "`prefijo` ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "bd ";
				break;
			case "verFranja" :
				$cadena_sql = "SELECT DISTINCT ";
				$cadena_sql .= "AAC_COD_FRANJA COD_FRANJA, ";
				$cadena_sql .= "AAC_FECHA_INI FEC_INI, ";
				$cadena_sql .= "AAC_FECHA_FIN FEC_FIN ";
				$cadena_sql .= "FROM ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "franjas ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= "AAC_CRA_COD=" . $variable ['proyecto'] . " ";
				$cadena_sql .= "AND AAC_DEP_COD=" . $variable ['facultad'] . " ";
				$cadena_sql .= "AND AAC_ESTADO='A' ";
				$cadena_sql .= "AND '" . $variable ['fec_actual'] . "' BETWEEN AAC_FECHA_INI AND AAC_FECHA_FIN ";
				break;
			case "consultaAccesos" :
				$cadena_sql = "SELECT  ";
				$cadena_sql .= " id_usuario , ";
				$cadena_sql .= " substr( fecha_log , 1, 10 ) fecha, ";
				$cadena_sql .= " count( accion ) AS accesos ";
				$cadena_sql .= "FROM  ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "log_acceso ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= " id_usuario = " . $variable ['id_usuario'];
				$cadena_sql .= " AND ";
				$cadena_sql .= " substr( fecha_log , 1, 10 )='" . date ( 'Y-m-d' ) . "' ";
				$cadena_sql .= "GROUP BY `id_usuario` , substr( `fecha_log` , 1, 10 ) ";
				$cadena_sql .= "ORDER BY count( `accion` ) DESC ";
				break;
			case "consultaAccesosErrados" :
				$cadena_sql = "SELECT  ";
				$cadena_sql .= " id_usuario , ";
				$cadena_sql .= " substr( fecha_log , 1, 10 ) fecha, ";
				$cadena_sql .= " count( accion ) AS accesos ";
				$cadena_sql .= "FROM  ";
				$cadena_sql .= $this->configuracion ['prefijo'] . "log_acceso ";
				$cadena_sql .= "WHERE ";
				$cadena_sql .= " id_usuario = " . $variable ['id_usuario'];
				$cadena_sql .= " AND ";
				$cadena_sql .= " tipo_registro='Acceso Errado' ";
				$cadena_sql .= " AND ";
				$cadena_sql .= " substr( fecha_log , 1, 10 )='" . date ( 'Y-m-d' ) . "' ";
				$cadena_sql .= "GROUP BY `id_usuario` , substr( `fecha_log` , 1, 10 ) ";
				$cadena_sql .= "ORDER BY count( `accion` ) DESC ";
				break;
			case 'guardar_password_hash' :
				$cadena_sql = "INSERT INTO password_hash(usuario, password_hash) ";
				$cadena_sql .= "VALUES ";
				$cadena_sql .= "('" . $variable ['usuario'] . "','" . $variable ['password_hash'] . "');";
				break;
			default :
				$cadena_sql = "";
				break;
		}
		return $cadena_sql;
	}
} // fin clase bloqueAdminUsuario
?>