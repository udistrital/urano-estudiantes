<?php

namespace misDatos;

if (!isset($GLOBALS["autorizado"])) {
	include ("../index.php");
	exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this -> miConfigurador = \Configurador::singleton();
	}

	function getCadenaSql($tipo, $variable = "") {

		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this -> miConfigurador -> getVariableConfiguracion("prefijo");
		$idSesion = $this -> miConfigurador -> getVariableConfiguracion("id_sesion");

		switch ($tipo) {

			/**
			 * Clausulas gen�ricas.
			 * se espera que est�n en todos los formularios
			 * que utilicen esta plantilla
			 */
			case "iniciarTransaccion" :
				$cadenaSql = "START TRANSACTION";
				break;

			case "finalizarTransaccion" :
				$cadenaSql = "COMMIT";
				break;

			case "cancelarTransaccion" :
				$cadenaSql = "ROLLBACK";
				break;

			case "eliminarTemp" :
				$cadenaSql = "DELETE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion = '" . $variable . "' ";
				break;

			case "insertarTemp" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "( ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";

				foreach ($_REQUEST as $clave => $valor) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable['fecha'] . "' ";
					$cadenaSql .= "),";
				}

				$cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
				break;

			case "rescatarTemp" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion='" . $idSesion . "'";
				break;

			/* Consultas del desarrollo */
			case "facultad" :
				$cadenaSql = "SELECT";
				$cadenaSql .= " id_facultad,";
				$cadenaSql .= "	nombre";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " docencia.facultad";
				break;

			case "consultarDatosBasicosEstudiante" :
				$cadenaSql = " SELECT acest.EST_NOMBRE,";
				$cadenaSql .= " acest.EST_COD,";
				$cadenaSql .= " acest.EST_TIPO_IDEN,";
				$cadenaSql .= " acest.EST_NRO_IDEN,";
				$cadenaSql .= " acest.EST_CRA_COD,";
				$cadenaSql .= " acest.EST_DIRECCION,";
				$cadenaSql .= " acest.EST_TELEFONO,";
				$cadenaSql .= " acestotr.EOT_TEL_CEL,";
				$cadenaSql .= " acestotr.EOT_EMAIL,";
				$cadenaSql .= " acestotr.EOT_EMAIL_INS";
				$cadenaSql .= " FROM acest";
				$cadenaSql .= " INNER JOIN acestotr";
				$cadenaSql .= " ON acestotr.EOT_COD = acest.EST_COD";
				$cadenaSql .= " WHERE est_cod ='$variable'";

				break;

			case 'actualizarEstudiante' :
				$cadenaSql = " UPDATE acest";
				$cadenaSql .= " SET EST_DIRECCION='" . $variable['direccion'] . "',";
				$cadenaSql .= " EST_TELEFONO='" . $variable['telefono'] . "'";
				$cadenaSql .= " WHERE EST_COD='" . $variable['usuario'] . "'";

				break;

			case 'actualizarEstudianteOtros' :
				$cadenaSql = " UPDATE acestotr";
				$cadenaSql .= " SET ";
				$cadenaSql .= " EOT_TEL_CEL='" . $variable['celular'] . "',";
				$cadenaSql .= " EOT_EMAIL='" . $variable['correo_personal'] . "',";
				$cadenaSql .= " EOT_EMAIL_INS='" . $variable['correo_institucional'] . "'";
				$cadenaSql .= " WHERE EOT_COD='" . $variable['usuario'] . "'";

				break;

			case 'actualizarDatos' :
				$cadenaSql = " UPDATE administracion.admin_usuario";
				$cadenaSql .= " SET usu_direccion='" . $variable['direccion'] . "',";
				$cadenaSql .= " usu_telefono='" . $variable['telefono'] . "',";
				$cadenaSql .= " usu_celular='" . $variable['celular'] . "',";
				$cadenaSql .= " usu_correo='" . $variable['correo_personal'] . "',";
				$cadenaSql .= " usu_correo_institucional='" . $variable['correo_institucional'] . "'";
				$cadenaSql .= " WHERE usu_nro_doc_actual='" . $variable['documento'] . "'";
				
				break;

			case "buscarConfiguracionDBMS" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " `parametro`,";
				$cadenaSql .= " `valor`";
				$cadenaSql .= " FROM";
				$cadenaSql .= " dbms_configuracion";
				$cadenaSql .= " ;";
				break;

			case 'consultaDatosLamasu' :
				$cadenaSql = " SELECT cta_usu_id,";
				$cadenaSql .= " cta_nombre_usuario,";
				$cadenaSql .= " cta_estado,";
				$cadenaSql .= " TRIM(usu_apellido) apellido,";
				$cadenaSql .= " TRIM(usu_nombre) nombre,";
				$cadenaSql .= " usu_tipo_doc_actual,";
				$cadenaSql .= " usu_correo,";
				$cadenaSql .= " usu_correo_institucional,";
				$cadenaSql .= " usu_direccion,";
				$cadenaSql .= " usu_telefono,";
				$cadenaSql .= " usu_celular,";
				$cadenaSql .= " usu_estado,";
				$cadenaSql .= " usu_fecha_registro";
				$cadenaSql .= " FROM administracion.admin_cuenta";
				$cadenaSql .= " INNER JOIN administracion.admin_usuario ON usu_id=cta_usu_id";
				$cadenaSql .= " WHERE cta_nombre_usuario='" . $variable . "';";
				break;

			case "buscarEnlace" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " enl.etiqueta AS etiqueta_enlace,";
				$cadenaSql .= " enl.url_host_enlace AS url_host_enlace,";
				$cadenaSql .= " enl.pagina_enlace AS pagina_enlace,";
				$cadenaSql .= " enl.parametros AS parametros,";
				$cadenaSql .= " subs.id_subsistema_sga AS id_subsistema_sga,";
				$cadenaSql .= " subs.host AS host,";
				$cadenaSql .= " subs.ruta AS ruta,";
				$cadenaSql .= " subs.codificado AS codificado,";
				$cadenaSql .= " subs.indice_codificador AS indice_codificador,";
				$cadenaSql .= " subs.funcion_codificador AS funcion_codificador,";
				$cadenaSql .= " subs.semilla AS semilla ";
				$cadenaSql .= " FROM public.urano_enlace AS enl ";
				$cadenaSql .= " LEFT JOIN public.urano_subsistema_sga AS subs";
				$cadenaSql .= " ON subs.id_subsistema_sga=enl.id_subsistema_sga";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " enl.id_enlace=2";
				$cadenaSql .= " ;";

				break;
		}

		return $cadenaSql;
	}

}
?>
