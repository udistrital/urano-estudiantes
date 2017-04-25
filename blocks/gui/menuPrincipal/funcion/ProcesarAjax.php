<?php
namespace gui\menuPrincipal\funcion;
use gui\menuPrincipal\Sql;
use core\general\ValidadorCampos;
use gui\menuPrincipal\funcion\encriptar;
//Se configura el cross allow origin para el ambiente de producción.
$host = $this->miConfigurador->getVariableConfiguracion ( "host" );
header('Access-Control-Allow-Origin: '.$host.'');

//Se necesita para codificar o decodificar información
$_REQUEST['tiempo'] = time();
$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );

//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
switch ($_REQUEST ['funcion']) {
	case 'actualizarNotificaciones':
		$conexion = "estructura_funcionarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->sql->getCadenaSql ( 'actualizarNotificaciones', $_REQUEST ['usuario']);
		$actualizacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizacion" );
		break;
	case 'consultarEnlacesUsuario':
		// $enlace = "action=index.php";
		// $enlace .= "&bloqueNombre=menuPrincipal";
		// $enlace .= "&bloqueGrupo=gui";
		// $enlace .= "&procesarAjax=true";
		// $enlace .= "&funcion=consultarEnlacesUsuario";
		// $enlace .= "&usuario=" . $_REQUEST['usuario'];
		
		//Se crea una instancia del encriptador
		include 'encriptar.class.php';
		$this->miEncriptador = new encriptar ( $this->sql );
		
		//Se llaman las variables que necesitan los enlaces
		include $rutaBloque . 'formulario/variablesEnlaces.php';
		
		//Opcionalmente se pueden limpiar las variables
		//$_REQUEST['query'] = str_replace('\\_', '_', $_REQUEST['query']);
		
		//Se crea una instancia para validar campos
		include 'core/general/ValidadorCampos.class.php';
		$miValidador = new ValidadorCampos();
		
		//Se valida el dato como de solo letras y números más espacios
		$valido = $miValidador->validarTipo($_REQUEST['query'],'onlyLetterNumberSp');
		
		if (!$valido) {
			header('Content-Type: text/json; charset=utf-8');
			echo json_encode(array("errorType"=>"custom","errorMessage"=>"El campo observacion sólo debe contener elementos alfanuméricos y espacios."));
			exit ();
		}
		
		//Se consultan los perfiles asociados al usuario
		$conexion = 'appserv';
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
		
		$parametros = array(
        	'usuario' => $usuario,
        	'sql_tabla1' => 'geclaves'
		);
		$cadenaSql = $this->sql->getCadenaSql ( 'buscarPerfilesUsuario', $parametros );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		$perfiles = array();
		foreach ($resultado as $key => $value) {
			$perfiles[] = $value['TIP_US'];
		}
		
		//Se consultan los enlaces correspondientes a los perfiles
		$conexion = 'estructura';
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$parametros = array(
			'etiqueta' => $_REQUEST['query'],
			'perfiles' => $perfiles,
		);
		$cadenaSql = $this->sql->getCadenaSql ( 'datosMenuFiltrado', $parametros );
		$datosMenu = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		if ($datosMenu) {
			$enlaces = array ();
			$titulosMenu = array ();
			$titulosGrupoMenu = array ();
			$indice = 0;
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
				$enlacesJavascript [] = array (
					'id' => $indice,
					'name' => $item ['etiqueta_menu'] . ' ' . $item ['etiqueta_grupo_menu'] . ' ' . $item ['etiqueta_enlace'],
					'url' => $enlace
				);
				$indice++;
			}
			// var_dump($enlaces,$titulosMenu,$titulosGrupoMenu);
		} else {
			header('Content-Type: text/json; charset=utf-8');
			echo json_encode(array("errorType"=>"custom","errorMessage"=>"Sin servicios registrados para el usuario."));
			exit();
		}
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($enlacesJavascript);
		break;
	case 'guardarObservacion':
		$_REQUEST['llaves_primarias_valor'] = str_replace('\\_', '_', $_REQUEST['llaves_primarias_valor']);
		$_REQUEST['llaves_primarias_valor'] = $this->miConfigurador->fabricaConexiones->crypto->decodificar($_REQUEST['llaves_primarias_valor']);
		
		include_once ('core/general/ValidadorCampos.class.php');
		$miValidador = new ValidadorCampos();
		
		$valido = $miValidador->validarTipo($_REQUEST['observacion'],'onlyLetterNumberSpPunt');
		$valido = $valido && $miValidador->validarTipo($_REQUEST['verificado'],'boleano');
		
		if (!$valido) {
			header('Content-Type: text/json; charset=utf-8');
			echo json_encode(array("errorType"=>"custom","errorMessage"=>"El campo observacion sólo debe contener elementos alfanuméricos, espacios, comas y punto."));
			exit ();
		}
		
		$conexion = "docencia";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->sql->getCadenaSql ( 'registrar_observacion', $_REQUEST );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		if ($resultado) {
			header('Content-Type: text/json; charset=utf-8');
			echo json_encode(true);
			exit ();
		} else {
			$cadenaSql = $this->sql->getCadenaSql ( 'actualizar_observacion', $_REQUEST );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
			header('Content-Type: text/json; charset=utf-8');
			if ($resultado) {
				echo json_encode(true);
			} else {
				echo json_encode(array("errorType"=>"registry or update","errorMessage"=>"Algo anda mal, no se pudo realizar el registro de la observación."));
			}
			exit ();
		}
		return true;
        break;
    default:
        die('Asigne la variable \'funcion\'');
}
?>