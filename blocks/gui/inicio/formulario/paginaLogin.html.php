<?php

use gui\menuPrincipal\funcion\encriptar as encriptar;

// Rescatar los datos de este bloque
$_REQUEST['tiempo'] = time();
$atributosGlobales = array();
$esteBloque = $this->miConfigurador->getVariableConfiguracion('esteBloque');
$rutaUrlBloque = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );
$atributosGlobales ['campoSeguro'] = 'true';

//Enlace para recuperación de contraseña
// Se crea una instancia del objeto encriptador.
include_once ($this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/blocks/gui/menuPrincipal/funcion/encriptar.class.php');
$miEncriptador = new encriptar ( $this->sql );
$usuario ="159357645";
$tipo=1;
$indiceSaraPassword = $this->miConfigurador->getVariableConfiguracion ( 'host' )."/lamasu/index.php?";
$tokenCondor = "l4v3rn42013!r3cup3raci0ncl4v3s2013";
$tokenCondor = $miEncriptador->codificar_sara($tokenCondor);
$opcion="temasys=";
$variable="gestionPassword&pagina=claves";                                                        
$variable.="&usuario=".$usuario;
$variable.="&tipo=".$tipo;
$variable.="&token=".$tokenCondor;
$variable.="&opcionPagina=gestionPassword";
$variable=$miEncriptador->codificar_sara($variable);
$enlacePassword = $indiceSaraPassword.$opcion.$variable;

$indiceMoodle=$this->miConfigurador->getVariableConfiguracion ("host") . "/moodle/index.php?";
$variable="";
$enlaceMoodle=$indiceMoodle.$variable;

?>

<header>
	<div id="tabudistrital">
		<a href="http://www.udistrital.edu.co">Udistrital</a>
	</div>
</header>
<div style="opacity: 1;" class="fade-in-forward" id="stage">
	<div class="sign-in">
		<div id="main-content" class="card">

			<div style="opacity: 1;" class="fade-in-forward" id="udistrital-logo"></div>

			<header>
				<h1 id="fxa-signin-header">
					<!-- L10N: For languages structured like English, the second phrase can read "to continue to %(serviceName)s" -->
					Sistema de Gestión Académica
					<!--<span class="service">Identificarse-->
					</span>
				</h1>
			</header>

			<section>

				<?php if(isset($_REQUEST['msgIndex'])):?>
				<div class="error">
					<?php echo $this->lenguaje->getCadena ( 'ERROR'.$_REQUEST['msgIndex'] );?>
				</div>
				<?php endif; ?>
				
				<?php if(isset($_REQUEST['mostrarMensaje']) && $_REQUEST['mostrarMensaje'] == 'sesionExpirada'):?>
				<div class="error">
					¡¡¡Sesión Expirada!!!
				</div>
				<?php endif; ?>
				
				<?php if(isset($_REQUEST['mensaje'])):?>
				<div class="success">
					<?php echo $this->lenguaje->getCadena ( $_REQUEST['mensaje'] );?>
				</div>
				<?php endif; ?>

        		<?php
				// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
				// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
				$esteCampo = $esteBloque ['nombre'];
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				/**
                 * Nuevo a partir de la versión 1.0.0.2, se utiliza para crear de manera rápida el js asociado a
                 * validationEngine.
                 */
                //$atributos ['validar'] = true;
				// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
				$atributos ['tipoFormulario'] = '';
				// Si no se coloca, entonces toma el valor predeterminado 'POST'
				$atributos ['metodo'] = 'POST';
				// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
				$atributos ['action'] = 'index.php';
				$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
				// Si no se coloca, entonces toma el valor predeterminado.
				$atributos ['estilo'] = '';
				$atributos ['marco'] = true;
				$tab = 1;
				// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
				// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
				$atributos ['tipoEtiqueta'] = 'inicio';
                $atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->formulario ( $atributos );
                unset($atributos);
				?>

				<div class="input-row">
					<?php
						$esteCampo = 'usuario';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'login jqueryui';
						$atributos ['marco'] = false;
						$atributos ['columnas'] = 1;
						$atributos ['dobleLinea'] = false;
						$atributos ['tabIndex'] = $tab;
						$atributos ['textoFondo'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['placeholder'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required,custom[integer]';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '15';
						$atributos ['evento'] = 'required';
						$tab ++;
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					?>
				</div>

				<div class="input-row password-row">
					<?php
						$esteCampo = 'clave';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'password';
						$atributos ['estilo'] = 'login jqueryui';
						$atributos ['marco'] = false;
						$atributos ['columnas'] = 1;
						$atributos ['dobleLinea'] = false;
						$atributos ['tabIndex'] = $tab;
						$atributos ['textoFondo'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required';
						$atributos ['evento'] = 'required';
						if (isset ( $_REQUEST [$esteCampo] )) {
							//$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '50';
						$tab ++;
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					?>
				</div>

				<div class="button-row">
					<?php					
		                // -----------------CONTROL: Botón ----------------------------------------------------------------
		                $esteCampo = 'botonIngresar';
		                $atributos ["id"] = $esteCampo;
		                $atributos ["tabIndex"] = $tab;
		                $atributos ["tipo"] = 'boton';
		                // submit: no se coloca si se desea un tipo button genérico
		                $atributos ['submit'] = true;
		                $atributos ["estiloMarco"] = '';
		                $atributos ["estiloBoton"] = '';
		                // verificar: true para verificar el formulario antes de pasarlo al servidor.
		                $atributos ["verificar"] = true;
		                $atributos ["tipoSubmit"] = ''; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		                $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
		                $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		                $tab ++;
		                // Aplica atributos globales al control
		                $atributos = array_merge($atributos, $atributosGlobales);
		                echo $this->miFormulario->campoBoton($atributos);
		                unset($atributos);
					?>
				</div>
				<?php
				// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
				// Paso 1: crear el listado de variables
				$valorCodificado = 'action=' . $esteBloque ['nombre'];
				$valorCodificado .= '&pagina=' . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$valorCodificado .= '&bloque=' . $esteBloque ['nombre'];
				$valorCodificado .= '&bloqueGrupo=' . $esteBloque ['grupo'];
				$valorCodificado .= '&opcion=login';
				/**
				 * SARA permite que los nombres de los campos sean dinámicos.
				 * Para ello utiliza la hora en que es creado el formulario para
				 * codificar el nombre de cada campo.
				 */
				$valorCodificado .= '&campoSeguro=' . $_REQUEST ['tiempo'];
				$valorCodificado .= "&tiempo=" . time();
				 /*
                 * Sara permite validar los campos en el formulario o funcion destino.
                 * Para ello se envía los datos atributos["validadar"] de los componentes del formulario
                 * Estos se pueden obtener en el atributo $this->miFormulario->validadorCampos del formulario
                 * La función $this->miFormulario->codificarCampos() codifica automáticamente el atributo validadorCampos
                 */
                $valorCodificado .= "&validadorCampos=" . $this->miFormulario->codificarCampos();
				
				// Paso 2: codificar la cadena resultante
				$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
				$atributos ['id'] = 'formSaraData'; // No cambiar este nombre
				$atributos ['tipo'] = 'hidden';
				$atributos ['estilo'] = '';
				$atributos ['obligatorio'] = false;
				$atributos ['marco'] = true;
				$atributos ['etiqueta'] = '';
				$atributos ['valor'] = $valorCodificado;
				echo $this->miFormulario->campoCuadroTexto ( $atributos );
				unset ( $atributos );
				// ----------------FIN SECCION: Paso de variables -------------------------------------------------
				// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
				// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
				// Se debe declarar el mismo atributo de marco con que se inició el formulario.
				$atributos ['marco'] = true;
				$atributos ['tipoEtiqueta'] = 'fin';
				echo $this->miFormulario->formulario ( $atributos );
				
				?>

        <div class="links">
					<a href="/appserv" class="reset-password">¿Volver a la interfaz antigua?</a>
					<br>
					<a href="<?php echo $enlacePassword;?>" class="/*left*/ reset-password">¿Olvidaste
						la contraseña?</a>

					<!--<a href="/oauth/signup" class="right sign-up">Crear una cuenta</a>-->
				</div>

				<div class="privacy-links">
					Al continuar, estás de acuerdo con los <a id="fxa-tos"
						href="http://condor2.udistrital.edu.co/appserv/terminos_y_condiciones.pdf"
						target="_blank">Términos del servicio</a> del Sistema de Gestión Académica.
				</div>

			</section>
		</div>
	</div>
	<aside>
		<div>
			<!-- <div class="lateral-icon news" data-open-id="noticias"></div> -->
			<div class="lateral-icon help" data-open-id="ayuda"></div>
			<div class="lateral-icon others" data-open-id="otros"></div>
		</div>
	</aside>
	<section id="noticias" class="panel-lateral" style="display: none;">
		<span class="callout section1"></span>
		<p>Noticias</p>
		<hr>
		<div class="noticia_index">
			<font color="RED">Modificación Calendario Académico<br>
			</font> Consulte la <a href="/descargas/circular_001_2016.pdf"
				target="_blank" :="">Circular</a> de Vicerrectoría Académica
			en la que se modifican fechas del Calendario Académico.
		</div>
		<hr>
		<div class="noticia_index">
			<b>IMPORTANTE, CAMBIO DE CLAVES!</b> <br>
		</div>
		<hr>
	</section>
	<section id="ayuda" class="panel-lateral" style="display: none;">
		<span class="callout section1"></span>
		<p>Ayuda</p>
		<div>
			<a href="<?php echo $enlacePassword; ?>">Recuperación contraseña de Sistema de Gestión Académica</a> <br> <br>
			<a href="https://docs.google.com/a/correo.udistrital.edu.co/file/d/0BzG7rdBcnWhoUWNsRzlTNmJoZVU/preview">Video recuperación de clave</a> <br> <br>
			<a href="https://www.udistrital.edu.co/novedades/particularNews.php?idNovedad=3985&amp;Type=N">Recuperación contraseña del Correo Electrónico</a> <br>
		</div>
	</section>
	<section id="otros" class="panel-lateral" style="display: none;">
		<span class="callout section2"></span>
		<p>Otros e información general</p>
		<div>
			<a href="<?php echo $enlaceMoodle; ?>">Moodle</a> <br> <br>
			<a href="https://portalws.udistrital.edu.co/soporte/">Manuales y Videotutoriales de Ayuda</a> <br> <br>
			<a href="https://sgral.udistrital.edu.co/sgral/index.php">Calendario Académico</a> <br> <br>
			<a href="http://sgral.udistrital.edu.co/xdata/sgral/Derechos_Pecuniarios2015.pdf">Derechos Pecuniarios</a> <br>
		</div>
	</section>
</div>
<footer>
	<div class="define" id="pie">
		<!--Based on template https://github.com/jennifervpacheco-->
		<p style="text-align: center;">
			<a href="http://autoevaluacion.udistrital.edu.co"> <img
				src="<?php echo $rutaUrlBloque.'img/acreditacion.png'?>" />
			</a> <a href="http://autoevaluacion.udistrital.edu.co"> <img
				src="<?php echo $rutaUrlBloque.'img/autoevaluacion.png'?>" />
			</a>
		</p>
		<p style="text-align: center;">
			<a href="https://www.udistrital.edu.co/">Universidad Distrital
				Francisco José de Caldas</a> PBX: 3239300. Todos los derechos
			reservados &copy;. .:: <a
				href="http://condor2.udistrital.edu.co/appserv/terminos_y_condiciones.pdf">Términos,
				condiciones de uso y política de privacidad</a> ::..
		</p>
	</div>
</footer>
<!--[if !(IE) | (gte IE 10)]><!-->
<noscript>SGA necesita Javascript.</noscript>
