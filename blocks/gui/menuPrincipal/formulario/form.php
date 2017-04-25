<?php

namespace gui\menuPrincipal\formulario;

include_once ($this->ruta . '/funcion/encriptar.class.php');
use gui\menuPrincipal\funcion\encriptar;
// include_once ($this->ruta . "/builder/DibujarMenu.class.php");
// use gui\menuPrincipal\builder\Dibujar;
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class FormularioMenu {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $miEncriptador;
	var $configuracion_appserv;
	var $sesionUsuario;
		
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		// Se crea una instancia del objeto encriptador.
		$this->miEncriptador = new encriptar ( $this->miSql );
		
		$this->configuracion_appserv = $this->miEncriptador->getConfiguracion();
		
		$this->sesionUsuario = \Sesion::singleton ();
	}
	function formulario() {
		include 'googleAnalytics.php';
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];
		$rutaUrlBloque = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );

		$_REQUEST ['usuario'] = $this->sesionUsuario->getSesionUsuarioId();
		// $usuario = 79708124;
		// $usuario = $_SESSION ['usuario_login'];
		
		/**
		 * Comienza sección de variables necesarias para los enlaces
		 */
		require 'variablesEnlaces.php';
		/**
		 * Termina sección de variables necesarias para los enlaces
		 */

		// consultar los roles que están asignados al usuario
		// $conexion = 'academica_ac';
		// $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		// $cadenaSql = $this->miSql->getCadenaSql ( 'perfilesUsuario', $usuario );
		// $datosPerfiles = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		// $esteRecursoDB->desconectar_db();
		// $perfiles = array_column ( $datosPerfiles, 'TIP_US' );
		
		//Inicio: Se busca en la base de datos los datos de la conexión de logueo
        $conexion = 'appserv';
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        
        $cadenaSql = $this->miSql->getCadenaSql ( 'buscarRol', 'logueo' );       
        $resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' )[0];
		//Fin: Se busca en la base de datos los datos de la conexión de logueo
		
		//Se necesita una conexión al recurso de base de datos resultado de la consulta
		
		//Inicio: Crear conexión logueo
		//Se crea la conexión a base de datos recursivamente
		$semilla = 'condor';
		$conexionDB = array(
			//'inicio' => true,
			'dbsys' => $resultado['dbms'],
			'dbdns' => $resultado['servidor'],
			'dbpuerto' => $resultado['puerto'],
			'dbnombre' => $resultado['db'],
			'dbusuario' => trim($this->decodificar_variable($resultado['usuario'],$semilla)),			
			'dbclave' =>  trim($this->decodificar_variable($resultado['password'],$semilla))
		);
		//Se realiza una conexión con $conexionDB y se le llama logueo
		$conexion = 'logueo';	
		$this->miConfigurador->fabricaConexiones->setRecursoDB($conexion, 'registro', $conexionDB);
		
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
		//Fin: Crear conexión logueo
		
		//Se realiza conexión con base de datos del framework para funcionarios 
		$conexionFuncionarios = 'estructura_funcionarios';
		$esteRecursoDBFuncionarios = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionFuncionarios);
		
		$parametros = array(
        	'usuario' => $usuario,
        	'sql_tabla1' => $this->configuracion_appserv['sql_tabla1']
		);
        $cadenaSql = $this->miSql->getCadenaSql ( 'buscarPerfilesUsuario' , $parametros);
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		$perfiles = array();
		foreach ($resultado as $key => $value) {
			$perfiles[] = $value['TIP_US'];
		}
		//var_dump($perfiles);exit;
		//$perfiles = array(4, 16, 20, 24, 28, 30, 31, 32, 33, 34, 51, 52, 61, 68, 72, 75, 80, 83, 84, 87, 88, 104, 105, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125);
		
		$conexion = 'estructura';
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'datosMenu', $perfiles );
		$datosMenu = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		// Se genera un arreglo con todos los enlaces, además su título y los títulos del menú y los grupos menú
		if ($datosMenu) {
			$enlaces = array ();
			$titulosMenu = array ();
			$titulosGrupoMenu = array ();
			foreach ( $datosMenu as $menu => $item ) {
				$titulosMenu [$item ['id_menu']] = $item ['etiqueta_menu'];
				$titulosGrupoMenu [$item ['id_grupo_menu']] = $item ['etiqueta_grupo_menu'];
				$enlace = "#";
				// Se establece un enlace nulo de manera predeterminada
				if ($item ['url_host_enlace'] != '') { // Enlace completo especificado, no se arma el enlace y no se codifica nada.
					$enlace = $item ['url_host_enlace'];
				} elseif ($item ['codificado'] == 't') { // Es un enlace codificado
					if ($item ['pagina_enlace'] != '') { // Si existe el parámetro página
						$enlace = 'pagina=' . $item ['pagina_enlace'] . '&' . $item ['parametros'];
					} else { // Si no existe el parámetro página
						$enlace = $item ['parametros'];
					}
					eval ( "\$enlace = \"$enlace\";" );
					// Se evaluan las variables de los parámetros
					$enlace = $this->miEncriptador->{$item ['funcion_codificador']} ( $enlace , $item ['semilla'] );
					$enlace = $item ['host'] . $item ['ruta'] . '?' . $item ['indice_codificador'] . '=' . $enlace;
				} else { // No es un enlace codificado
					if ($item ['pagina_enlace'] != '') { // Si existe el parámetro página
						$enlace = 'pagina=' . $item ['pagina_enlace'] . '&' . $item ['parametros'];
					} else { // Si no existe el parámetro página
						$enlace = $item ['parametros'];
					}
					$enlace = $item ['host'] . $item ['ruta'] . '?' . $item ['indice_codificador'] . '=' . $enlace;
				}
				$enlaces [$item ['id_menu']] [$item ['id_grupo_menu']] [$item ['id_enlace']] = array (
						'etiqueta' => $item ['etiqueta_enlace'],
						'url' => $enlace 
				);
			}
			// var_dump($enlaces,$titulosMenu,$titulosGrupoMenu);
		} else {
			die ( 'Sin servicios registrados para el usuario.' );
		}
			
		// Se consultan las notificaciones activas
		$cadenaSql = $this->miSql->getCadenaSql ( 'buscarNotificaciones', $_REQUEST ['usuario'] );
		$matrizNotificaciones = $esteRecursoDBFuncionarios->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		// Si no hay notificaciones se genera un arreglo vacio
		$matrizNotificaciones = ($matrizNotificaciones) ? $matrizNotificaciones : array ();
		
		// Cantidad de notificaciones pendientes, inicialmente cero
		$notificacionesPendientes = 0;
		
		$infoNotificaciones = array ();
				
		if ($matrizNotificaciones) {
			foreach ( $matrizNotificaciones as $notificacion ) {
				if ($notificacion ['estado'] == 1) {
					$notificacionesPendientes ++;
				}
				
				$infoNotificacion = array ();
				$infoNotificacion ['estado'] = trim ( $notificacion ['estado'] );
				
				$pordefecto = $rutaUrlBloque . 'images/silueta.gif';
				if ($notificacion ['imagen']) {
					$imagen = $rutaUrlBloque . 'images/' . trim ( $notificacion ['imagen'] );
				} else {
					$imagen = $pordefecto;
				}
				
				$infoNotificacion ['imgsrc'] = $imagen;
				$infoNotificacion ['imgalt'] = trim ( $notificacion ['emisor'] );
				$infoNotificacion ['titulo'] = trim ( $notificacion ['titulo'] );
				$infoNotificacion ['descripcion'] = trim ( $notificacion ['contenido'] );

				$fecha = trim ( $notificacion ['fecha'] );
				$fecha = explode ( ' ', $fecha );
				
				$aux = $fecha [0];
				$aux = explode ( '-', $aux );
				
				$f ['anio'] = $aux [0];
				$f ['mes'] = $aux [1];
				$f ['dia'] = $aux [2];
				$f ['hora'] = $fecha [1];
				
				$infoNotificacion ['fecha'] = $this->fecha_es ( $f );
				
				$infoNotificaciones[] = $infoNotificacion;
				unset($infoNotificacion);
			}
		}

		$conexion = 'lamasu';
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->getCadenaSql ( 'datosFuncionario', $usuario );
		$datosPerfiles = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$enlaceLogout = 'pagina=index&action=inicio&bloque=inicio&bloqueGrupo=gui&opcion=logout';
		$enlaceLogout = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar_url($enlaceLogout, $directorio);
		
		$enlaceHome = 'pagina=home';
		$enlaceHome = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar_url($enlaceHome, $directorio);
		
		$enlaceMiCuenta = 'pagina=misDatos';
		$enlaceMiCuenta = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar_url($enlaceMiCuenta, $directorio);
		
		// $url_foto_perfil = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . '/../appserv/fun_fotos/' . $usuario . '.jpg';
		// if(!file_exists($url_foto_perfil)){//Si no existe la imagen
			// $url_foto_perfil = $rutaUrlBloque . 'images/profile.png';
		// }
		
		$url_foto_thumbnail_perfil = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . '/../appserv/esst_fotos/thumbnails/' . $usuario . '.jpg';
		if(!file_exists($url_foto_thumbnail_perfil)){//Si no existe la imagen
			$url_foto_thumbnail_perfil = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . '/../appserv/est_fotos/thumbnails/profile_thumbnail.png';
		}
		
		$nombreUsuario = 'INVITADO';
		if ($datosPerfiles) {
			$nombreUsuario = mb_strtoupper($datosPerfiles [0] [2], 'UTF-8');
		}
		
		$atributos ['id'] = 'megaMenu';
		$atributos ['target'] = 'principal';
		$atributos ['nombre_usuario'] = $nombreUsuario;
		$atributos ['foto_perfil_thumbnail'] = $this->imagenBase64($url_foto_thumbnail_perfil);
		$atributos ['enlace_cerrar_sesion'] = $enlaceLogout;
		$atributos ['enlace_mi_cuenta'] = $enlaceMiCuenta;
		$atributos ['enlace_home'] = $enlaceHome;
		$atributos ['enlaces'] = $enlaces;
		$atributos ['titulosMenu'] = $titulosMenu;
		$atributos ['titulosGrupoMenu'] = $titulosGrupoMenu;
		
		$atributos ['url_logo'] = $rutaUrlBloque . 'images/escudo_ud_blanco2-128x128.png';
		$atributos ['url_icon_logout'] = $rutaUrlBloque . 'images/logout_64x64.png';
		$atributos ['url_icon_account'] = $rutaUrlBloque . 'images/account_64x64.png';
		$atributos ['url_clock'] = $rutaUrlBloque . 'images/mini-clock.png';
		$atributos ['iconNotificacion'] = $rutaUrlBloque . 'images/notificacion-blanco-64x64.png';
		
		$atributos ['notificacionesPendientes'] = $notificacionesPendientes;
		$atributos ['notificaciones'] = json_encode($infoNotificaciones); // Se pasa arreglo de notificaciones en forma de JSON
		
		echo '<div class="freespace"></div>';
		echo '<!-- Page Content -->';
		echo $this->miFormulario->megaMenu ( $atributos );
		echo '<!-- /.container -->';
	}

	function mensaje() {
		
		// Si existe algun tipo de error en el login aparece el siguiente mensaje
		$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
		
		if ($mensaje) {
			
			$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
			
			if ($tipoMensaje == 'json') {
				
				$atributos ['mensaje'] = $mensaje;
				$atributos ['json'] = true;
			} else {
				$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
			}
			// -------------Control texto-----------------------
			$esteCampo = 'divMensaje';
			$atributos ['id'] = $esteCampo;
			$atributos ["tamanno"] = '';
			$atributos ["estilo"] = 'information';
			$atributos ['efecto'] = 'desvanecer';
			$atributos ["etiqueta"] = '';
			$atributos ["columnas"] = '';
			// El control ocupa 47% del tamaño del formulario
			echo $this->miFormulario->campoMensaje ( $atributos );
			unset ( $atributos );
		}
		
		return true;
	}
	
	private function fecha_es($fecha) {
		$meses = array (
				'01' => 'Enero',
				'02' => 'Febrero',
				'03' => 'Marzo',
				'04' => 'Abril',
				'05' => 'Mayo',
				'06' => 'Junio',
				'07' => 'Julio',
				'08' => 'Agosto',
				'09' => 'Septiembre',
				'10' => 'Octubre',
				'11' => 'Noviembre',
				'12' => 'Diciembre'
		);
		return $meses [$fecha ['mes']] . ' ' . $fecha ['dia'] . ', ' . $fecha ['anio'] . ' - ' . $fecha ['hora'];
	}
	
	private function imagenBase64($rutaImagen) {
		$imagen = file_get_contents ( $rutaImagen );
		$imagenEncriptada = base64_encode ( $imagen );
		$url = "data:image/png;base64," . $imagenEncriptada;
		return $url;
	}
	
	private function decodificar_variable($cadena,$semilla) {
		$cifrado = MCRYPT_RIJNDAEL_256;
		$modo = MCRYPT_MODE_ECB;
		$cadena=base64_decode(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT)); 
        $cadena=mcrypt_decrypt(
        	$cifrado,
        	$semilla,
        	$cadena,
        	$modo,
        	mcrypt_create_iv(
        		mcrypt_get_iv_size(
        			$cifrado,
        			$modo
				),
				MCRYPT_RAND
			)
		);
        return $cadena;
	}

}

$miFormulario = new FormularioMenu ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();
$miFormulario->mensaje ();
?>
