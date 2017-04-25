<?php

namespace misDatos\formulario;

use gui\menuPrincipal\funcion\encriptar as encriptar;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class FormularioRegistro {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $miEncriptador;
	var $miSesion;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();

		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

		$this->lenguaje = $lenguaje;

		$this->miFormulario = $formulario;

		$this->miSql = $sql;

		// Se crea una instancia del objeto encriptador.
		include_once ($this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/blocks/gui/menuPrincipal/funcion/encriptar.class.php');
		$this->miEncriptador = new encriptar ( $this->miSql );

		$this->miSesion = \Sesion::singleton ();
	}
	function formulario() {

		$conexionLamasu = 'lamasu';
		$esteRecurso = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexionLamasu );

		$conexion = "academica_ac";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		// $usuario = $_REQUEST['usuario'];
		$usuario = $this->miSesion->getSesionUsuarioId ();

		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarDatosBasicosEstudiante', $usuario );
		$datosEstudiante = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );	
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaDatosLamasu', $usuario );
		$datosLamasu = $esteRecurso->ejecutarAcceso ( $cadenaSql, "busqueda" );			

		$_REQUEST ['usuario'] = $usuario;

		$conexionUrano = 'estructura';
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexionUrano );

		// buscar el enlace de cambio de contraseña
		$cadenaSqlEnlace = $this->miSql->getCadenaSql ( 'buscarEnlace' );
		$enlaceCambio = $esteRecursoDB->ejecutarAcceso ( $cadenaSqlEnlace, 'busqueda' );
		
		// armar el enlace
		$enlace = "#";

		$tokenSaraAcademica = $this->miEncriptador->codificar_sara ( 'condorSara2013!' );

		if ($enlaceCambio) {

			if ($enlaceCambio [0] ['codificado'] == 't') { // Es un enlace codificado
				if ($enlaceCambio [0] ['pagina_enlace'] != '') { // Si existe el parámetro página
					$enlace = 'pagina=' . $enlaceCambio [0] ['pagina_enlace'] . '&' . $enlaceCambio [0] ['parametros'];
				} else { // Si no existe el parámetro página
					$enlace = $enlaceCambio [0] ['parametros'];
				}

				eval ( "\$enlace = \"$enlace\";" );
				// Se evaluan las variables de los parámetros
				$enlace = $this->miEncriptador->{$enlaceCambio [0] ['funcion_codificador']} ( $enlace, $enlaceCambio [0] ['semilla'] );
				$enlace = $enlaceCambio [0] ['host'] . $enlaceCambio [0] ['ruta'] . '?' . $enlaceCambio [0] ['indice_codificador'] . '=' . $enlace;
			}
		}

		// Nombre, lo toma de la tabla ACEST

		if (isset ( $datosEstudiante ) and $datosEstudiante != false) {
			$datos ['NOMBRE'] = $datosEstudiante [0] ['EST_NOMBRE'];
		} elseif (isset ( $datosLamasu ) and $datosLamasu != false) {
			$datos ['NOMBRE'] = $datosLamasu [0] ['apellido']. ' ' . $datosLamasu [0] ['nombre'] ;
		}

		// TIPO DE DOCUMENTO

		if (isset ( $datosEstudiante ) and $datosEstudiante != false) {
			switch ($datosEstudiante [0] ['EST_TIPO_IDEN']) {
				case 'C' :
					$datos ['TIPO_DOCUMENTO'] = 'CC';
					break;

				case 'T' :
					$datos ['TIPO_DOCUMENTO'] = 'TI';
					break;

				case 'E' :
					$datos ['TIPO_DOCUMENTO'] = 'CE';
					break;

				default :
					$datos ['TIPO_DOCUMENTO'] = '';
					break;
			}
		} elseif (isset ( $datosLamasu ) and $datosLamasu != false) {
			switch ($datosLamasu [0] ['usu_tipo_doc_actual']) {
				case 'CC' :
					$datos ['TIPO_DOCUMENTO'] = 'CC';
					break;

				case 'TI' :
					$datos ['TIPO_DOCUMENTO'] = 'TI';
					break;

				case 'CE' :
					$datos ['TIPO_DOCUMENTO'] = 'CE';
					break;

				default :
					$datos ['TIPO_DOCUMENTO'] = '';
					break;
			}
		}

		// NUMERO DE DOCUMENTO
		if (isset ( $datosEstudiante ) and $datosEstudiante != false) {
			$datos ['DOCUMENTO'] = $datosEstudiante [0] ['EST_NRO_IDEN'];
		} elseif (isset ( $datosLamasu ) and $datosLamasu != false) {
			$datos ['DOCUMENTO'] = $datosLamasu [0] ['cta_nombre_usuario'];
		}

		$url_foto_perfil = $this->miConfigurador->getVariableConfiguracion ( "host_funcionarios" ) . '/appserv/est_fotos/' . $usuario . '.jpg';

		if (! file_exists ( $url_foto_perfil )) { // Si no existe la imagen
			$url_foto_perfil = $this->miConfigurador->getVariableConfiguracion ( "host_funcionarios" ) . '/appserv/est_fotos/profile.png';
		}

		?>

<br>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="glyphicon glyphicon-user icon-titulo"></span>
			<titu>Usuario</titu>
		</div>
		<div class="panel-body">
			<div class="row">
				<div align="center">
					<caption>
						<h4 class="nombre"><?php echo $datos ['NOMBRE'] ?></h4>
					</caption>
					<h5 class="nombre"><?php echo $datos['TIPO_DOCUMENTO'] . ' ' . $datos['DOCUMENTO']; ?></h5>
				</div>
			</div>

			<div class="row" style="margin-left: 35%; margin-right: 35%; margin-bottom: 1%;">
				<div align="center" style="background-color: #f2f2f2;">
					<span class="glyphicon glyphicon-lock" style="margin: 10px;"></span>
					<a href="<?php echo $enlace; ?>"><?php echo 'Cambiar la contraseña'; ?></a>

				</div>
			</div>

			<div align="center">
				<img id="foto-perfil"
					src="<?php echo $this -> imagenBase64($url_foto_perfil); ?>"
					alt="Perfil" style="width: 200 px; height: 200px;"
					class="foto-user img-responsive img-rounded profilepicture" />
			</div>
		</div>
		<div class="panel-footer"></div>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="glyphicon glyphicon-user icon-titulo"></span>
			<titu>Datos de contacto</titu>
		</div>
		<div class="panel-body">
			<div class="row">
		<?

		/**
		 * IMPORTANTE: Este formulario está utilizando jquery.
		 * Por tanto en el archivo ready.php se delaran algunas funciones js
		 * que lo complementan.
		 */

		if (isset($_REQUEST['error'])) {
			echo '<h1>' . $_REQUEST['error'] . '</h1>';
		}

		// Rescatar los datos de este bloque
		$esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");

		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo
		 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		$atributosGlobales['campoSeguro'] = 'true';
		$_REQUEST['tiempo'] = time();

		// -------------------------------------------------------------------------------------------------
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque['nombre'] . "Registrar";
		$atributos['id'] = $esteCampo;
		$atributos['nombre'] = $esteCampo;

		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos['tipoFormulario'] = 'multipart/form-data';

		// Si no se coloca, entonces toma el valor predeterminado 'POST'
		$atributos['metodo'] = 'POST';

		// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		$atributos['action'] = 'index.php';
		$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo);

		// Si no se coloca, entonces toma el valor predeterminado.
		$atributos['estilo'] = '';
		$atributos['marco'] = true;
		$tab = 1;

		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------

		$atributos['tipoEtiqueta'] = 'inicio';
		// Aplica atributos globales al control
		echo $this -> miFormulario -> formulario($atributos);

		// ----------------INICIO CONTROL: Campo de Texto direccion--------------------------------------------------------

		// DIRECCION

		if (isset($datosEstudiante) or isset($datosLamasu)) {
			if (isset($datosEstudiante[0]['EST_DIRECCION'])) {
				$datos['DIRECCION'] = $datosEstudiante[0]['EST_DIRECCION'];
			} elseif (isset($datosLamasu[0]['usu_direccion'])) {
				$datos['DIRECCION'] = $datosLamasu[0]['usu_direccion'];
			} else {
				$datos['DIRECCION'] = '';
			}

			$esteCampo = 'direccion';
			$atributos['id'] = $esteCampo;
			$atributos['nombre'] = $esteCampo;
			$atributos['tipo'] = 'text';
			$atributos['estilo'] = 'jqueryui borde';
			$atributos['marco'] = true;
			$atributos['estiloMarco'] = '';
			$atributos["etiquetaObligatorio"] = true;
			$atributos['columnas'] = 1;
			$atributos['dobleLinea'] = 0;
			$atributos['tabIndex'] = $tab;
			$atributos['etiqueta'] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['validar'] = 'required';
			$atributos['valor'] = $datos['DIRECCION'];
			$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo . 'Titulo');
			$atributos['deshabilitado'] = false;
			$atributos['tamanno'] = 57;
			$atributos['maximoTamanno'] = '320';
			$atributos['anchoEtiqueta'] = 280;
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoCuadroTexto($atributos);
			unset($atributos);
			// ----------------FIN CONTROL: Campo de Texto direccion--------------------------------------------------------
		}// fin direccion

		// ----------------INICIO CONTROL: Campo de Texto telefono--------------------------------------------------------

		// TELEFONO FIJO
		if (isset($datosEstudiante) or isset($datosLamasu)) {

			if (isset($datosEstudiante[0]['EST_TELEFONO'])) {
				$datos['TELEFONO'] = $datosEstudiante[0]['EST_TELEFONO'];
			} elseif (isset($datosLamasu) and $datosLamasu != false) {
				$datos['TELEFONO'] = $datosLamasu[0]['usu_telefono'];
			} else {
				$datos['TELEFONO'] = '';
			}

			$esteCampo = 'telefono';
			$atributos['id'] = $esteCampo;
			$atributos['nombre'] = $esteCampo;
			$atributos['tipo'] = 'number';
			$atributos['estilo'] = 'jqueryui borde';
			$atributos['marco'] = true;
			$atributos['estiloMarco'] = '';
			$atributos["etiquetaObligatorio"] = true;
			$atributos['columnas'] = 1;
			$atributos['dobleLinea'] = 0;
			$atributos['tabIndex'] = $tab;
			$atributos['etiqueta'] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['validar'] = 'required, custom[onlyNumberSp], minSize[7], maxSize[10]';
			$atributos['valor'] = $datos['TELEFONO'];
			$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo . 'Titulo');
			$atributos['deshabilitado'] = false;
			$atributos['tamanno'] = 8;
			$atributos['maximoTamanno'] = '10';
			$atributos['anchoEtiqueta'] = 280;
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoCuadroTexto($atributos);
			unset($atributos);
			// ----------------FIN CONTROL: Campo de Texto telefono--------------------------------------------------------
		}// fin telefomo fijo

		// ----------------INICIO CONTROL: Campo de Texto telefono celular--------------------------------------------------------

		// TELEFONO CELULAR
		if (isset($datosEstudiante) or isset($datosLamasu)) {

			if (isset($datosEstudiante[0]['EOT_TEL_CEL'])) {
				$datos['CELULAR'] = $datosEstudiante[0]['EOT_TEL_CEL'];
			} elseif (isset($datosLamasu[0]['usu_celular'])) {
				$datos['CELULAR'] = str_replace(" ", "", $datosLamasu[0]['usu_celular']);
			} else {
				$datos['CELULAR'] = '';
			}

			$esteCampo = 'celular';
			$atributos['id'] = $esteCampo;
			$atributos['nombre'] = $esteCampo;
			$atributos['tipo'] = 'number';
			$atributos['estilo'] = 'jqueryui borde';
			$atributos['marco'] = true;
			$atributos['estiloMarco'] = '';
			$atributos["etiquetaObligatorio"] = true;
			$atributos['columnas'] = 1;
			$atributos['dobleLinea'] = 0;
			$atributos['tabIndex'] = $tab;
			$atributos['etiqueta'] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['validar'] = 'required, custom[onlyNumberSp], minSize[10], maxSize[15]';
			$atributos['valor'] = $datos['CELULAR'];
			$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo . 'Titulo');
			$atributos['deshabilitado'] = false;
			$atributos['tamanno'] = 10;
			$atributos['maximoTamanno'] = '10';
			$atributos['anchoEtiqueta'] = 280;
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoCuadroTexto($atributos);
			unset($atributos);
			// ----------------FIN CONTROL: Campo de Texto telefono celular--------------------------------------------------------
		}// fin celular

		// ----------------INICIO CONTROL: Campo de Texto correo personal--------------------------------------------------------
		if (isset($datosEstudiante) or isset($datosLamasu)) {
			if (isset($datosEstudiante[0]['EOT_EMAIL'])) {
				$datos['CORREO_PERSONAL'] = $datosEstudiante[0]['EOT_EMAIL'];
			} elseif (isset($datosLamasu[0]['usu_correo'])) {
				$datos['CORREO_PERSONAL'] = $datosLamasu[0]['usu_correo'];
			} else {
				$datos['CORREO_PERSONAL'] = '';
			}

			$esteCampo = 'correo_personal';
			$atributos['id'] = $esteCampo;
			$atributos['nombre'] = $esteCampo;
			$atributos['tipo'] = 'text';
			$atributos['estilo'] = 'jqueryui borde';
			$atributos['marco'] = true;
			$atributos['estiloMarco'] = '';
			$atributos["etiquetaObligatorio"] = true;
			$atributos['columnas'] = 1;
			$atributos['dobleLinea'] = 0;
			$atributos['tabIndex'] = $tab;
			$atributos['etiqueta'] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['validar'] = 'required, custom[email]';
			$atributos['valor'] = strtolower($datos['CORREO_PERSONAL']);
			$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo . 'Titulo');
			$atributos['deshabilitado'] = false;
			$atributos['tamanno'] = 57;
			$atributos['maximoTamanno'] = '320';
			$atributos['anchoEtiqueta'] = 280;
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoCuadroTexto($atributos);
			unset($atributos);
			// ----------------FIN CONTROL: Campo de Texto correo--------------------------------------------------------
		}// fin correo personal

		// ----------------INICIO CONTROL: Campo de Texto correo institucional--------------------------------------------------------
		// EMAIL INSTITUCIONAL

		if (isset($datosEstudiante) or isset($docente) or isset($datosLamasu)) {
			if (isset($datosEstudiante[0]['EOT_EMAIL_INS'])) {
				$datos['CORREO_INSTITUCIONAL'] = $datosEstudiante[0]['EOT_EMAIL_INS'];
			} elseif (isset($datosLamasu[0]['usu_correo_institucional'])) {
				$datos['CORREO_INSTITUCIONAL'] = $datosLamasu[0]['usu_correo_institucional'];
			} else {
				$datos['CORREO_INSTITUCIONAL'] = '';
			}

			$esteCampo = 'correo_institucional';
			$atributos['id'] = $esteCampo;
			$atributos['nombre'] = $esteCampo;
			$atributos['tipo'] = 'text';
			$atributos['estilo'] = 'jqueryui borde';
			$atributos['marco'] = true;
			$atributos['estiloMarco'] = '';
			$atributos["etiquetaObligatorio"] = true;
			$atributos['columnas'] = 1;
			$atributos['dobleLinea'] = 0;
			$atributos['tabIndex'] = $tab;
			$atributos['etiqueta'] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['validar'] = 'required, custom[email]';
			$atributos['valor'] = strtolower($datos['CORREO_INSTITUCIONAL']);
			$atributos['titulo'] = $this -> lenguaje -> getCadena($esteCampo . 'Titulo');
			$atributos['deshabilitado'] = false;
			$atributos['tamanno'] = 57;
			$atributos['maximoTamanno'] = '320';
			$atributos['anchoEtiqueta'] = 280;
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoCuadroTexto($atributos);
			unset($atributos);
			// ----------------FIN CONTROL: Campo de Texto correo institucionañ--------------------------------------------------------
		}// fin correo institucional

		// ------------------Division para los botones-------------------------
		$atributos["id"] = "botones";
		$atributos["estilo"] = "marcoBotones";
		echo $this -> miFormulario -> division("inicio", $atributos);
		{
			// -----------------CONTROL: Botón ----------------------------------------------------------------
			$esteCampo = 'botonRegistrar';
			$atributos["id"] = $esteCampo;
			$atributos["tabIndex"] = $tab;
			$atributos["tipo"] = 'boton';
			// submit: no se coloca si se desea un tipo button genérico
			$atributos['submit'] = 'true';
			$atributos["estiloMarco"] = '';
			$atributos["estiloBoton"] = 'jqueryui btn btn-primary';
			// verificar: true para verificar el formulario antes de pasarlo al servidor.
			$atributos["verificar"] = '';
			$atributos["tipoSubmit"] = 'jquery';
			// Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
			$atributos["valor"] = $this -> lenguaje -> getCadena($esteCampo);
			$atributos['nombreFormulario'] = $esteBloque['nombre'] . "Registrar";
			$tab++;

			// Aplica atributos globales al control
			$atributos = array_merge($atributos, $atributosGlobales);
			echo $this -> miFormulario -> campoBoton($atributos);

			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		}
		// ------------------Fin Division para los botones-------------------------
		echo $this -> miFormulario -> division("fin");

		// ------------------- SECCION: Paso de variables ------------------------------------------------

		/**
		 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
		 * SARA permite realizar esto a través de tres
		 * mecanismos:
		 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
		 * la base de datos.
		 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
		 * formsara, cuyo valor será una cadena codificada que contiene las variables.
		 * (c) a través de campos ocultos en los formularios. (deprecated)
		 */
		// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
		// Paso 1: crear el listado de variables

		$valorCodificado = "action=" . $esteBloque["nombre"];
		$valorCodificado .= "&pagina=" . $this -> miConfigurador -> getVariableConfiguracion('pagina');
		$valorCodificado .= "&bloque=" . $esteBloque['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
		$valorCodificado .= "&opcion=actualizar";
		$valorCodificado .= "&usuario=" . $usuario;
		$valorCodificado .= "&documento=" . $datos ['DOCUMENTO'];

		/**
		 * SARA permite que los nombres de los campos sean dinámicos.
		 * Para ello utiliza la hora en que es creado el formulario para
		 * codificar el nombre de cada campo.
		 */
		$valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
		$valorCodificado .= "&tiempo=" . time();
		/*
		 * Sara permite validar los campos en el formulario o funcion destino.
		 * Para ello se envía los datos atributos["validadar"] de los componentes del formulario
		 * Estos se pueden obtener en el atributo $this->miFormulario->validadorCampos del formulario
		 * La función $this->miFormulario->codificarCampos() codifica automáticamente el atributo validadorCampos
		 */
		$valorCodificado .= "&validadorCampos=" . $this -> miFormulario -> codificarCampos();

		// Paso 2: codificar la cadena resultante
		$valorCodificado = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar($valorCodificado);

		$atributos["id"] = "formSaraData";
		// No cambiar este nombre
		$atributos["tipo"] = "hidden";
		$atributos['estilo'] = '';
		$atributos["obligatorio"] = false;
		$atributos['marco'] = true;
		$atributos["etiqueta"] = "";
		$atributos["valor"] = $valorCodificado;
		echo $this -> miFormulario -> campoCuadroTexto($atributos);
		unset($atributos);

		$atributos['marco'] = true;
		$atributos['tipoEtiqueta'] = 'fin';
		echo $this -> miFormulario -> formulario($atributos);

		// ----------------FIN SECCION: Paso de variables -------------------------------------------------
		// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		?>
</div>
		</div>
		<div class="panel-footer"></div>
	</div>

				<?if(isset($datosFuncionario) AND $datosFuncionario!=false){?>

				<div class="panel panel-default">
		<div class="panel-heading">
			<span class="glyphicon glyphicon-user icon-titulo"></span>
			<titu>Información de Funcionario</titu>
		</div>
		<div class="panel-body">
			<div class="row">
				<div style="text-indent: 20px">
					<div>
						<?echo 'Código de Empleado: '.$datosFuncionario[0]['EMP_COD']?>
					</div>
					<br>
					<div>
						<?echo 'Fecha de ingreso: '.$datosFuncionario[0]['EMP_DESDE']?>
					</div>
					<br>
					<div>
						<?

						if (isset($funcionario[0]['EMP_HASTA'])) {
							echo 'Fecha de terminación: ' . $datosFuncionario[0]['EMP_HASTA'];
						} else
							echo 'Fecha de terminación:';
			?>
					</div>
					<br>
					<div>
						<?

						if (isset($datosFuncionario[0]['EMP_REGIMEN'])) {
							switch ($datosFuncionario [0] ['EMP_REGIMEN']) {
								case 'A' :
									echo 'Régimen: Antiguo';
									break;

								case 'N' :
									echo 'Régimen: Nuevo';
									break;
								default :
									echo '';
									break;
							}
						} else
							echo 'Régimen:';
			?>
					</div>
					<br>
					<div>
						<?

						if (isset($datosFuncionario[0]['DEP_NOMBRE'])) {
							echo 'Dependencia: ' . $datosFuncionario[0]['DEP_NOMBRE'];
						} else
							echo 'Dependencia:';
			?>
					</div>


				</div>
			</div>
		</div>
		<div class="panel-footer"></div>
	</div>
	<?}//fin del panes de funcionario ?>

	<?if(isset($docente) AND $docente!=false){?>

				<div class="panel panel-default">
		<div class="panel-heading">
			<span class="glyphicon glyphicon-user icon-titulo"></span>
			<titu>Información del Docente</titu>
		</div>
		<div class="panel-body">
			<div class="row">
				<div style="text-indent: 20px">
					<div>
						<?echo 'Estado del Docente: '.$docente[0]['DOC_ESTADO']?>
					</div>
					<br>
					<div>
						<?

						if (isset($docente[0]['DOC_NIVEL_ESTUDIO'])) {
							echo 'Nivel de Estudios: ' . $docente[0]['DOC_NIVEL_ESTUDIO'];
						} else
							echo 'Nivel de Estudios: ';
			?>

					</div>
					<br>
					<div></div>
				</div>
			</div>
		</div>
		<div class="panel-footer"></div>
	</div>
	<?}//fin del panes de funcionario ?>



		</div>



</div><?
// cierre container
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
$atributos ["etiqueta"] = '';
$atributos ["columnas"] = '';
// El control ocupa 47% del tamaño del formulario
echo $this->miFormulario->campoMensaje ( $atributos );
unset ( $atributos );
}

return true;
}

/**
* convierte imagen a base 64
* para presentarla en la página
*/
function imagenBase64($rutaImagen) {
$imagen = file_get_contents ( $rutaImagen );
$imagenEncriptada = base64_encode ( $imagen );
$url = "data:image/png;base64," . $imagenEncriptada;
return $url;
}
}

$miFormulario = new FormularioRegistro ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();
$miFormulario->mensaje ();
?>
