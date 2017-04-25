<?php

namespace gui\menuPrincipal;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
			
			case "buscarConfiguracionDBMS" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " `parametro`,";
				$cadenaSql .= " `valor`";
				$cadenaSql .= " FROM";
				$cadenaSql .= " dbms_configuracion";
				$cadenaSql .= " ;";
				break;
			
			case "insertarRegistro" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "registradoConferencia ";
				$cadenaSql .= "( ";
				$cadenaSql .= "`idRegistrado`, ";
				$cadenaSql .= "`nombre`, ";
				$cadenaSql .= "`apellido`, ";
				$cadenaSql .= "`identificacion`, ";
				$cadenaSql .= "`codigo`, ";
				$cadenaSql .= "`correo`, ";
				$cadenaSql .= "`tipo`, ";
				$cadenaSql .= "`fecha` ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "NULL, ";
				$cadenaSql .= "'" . $variable ['nombre'] . "', ";
				$cadenaSql .= "'" . $variable ['apellido'] . "', ";
				$cadenaSql .= "'" . $variable ['identificacion'] . "', ";
				$cadenaSql .= "'" . $variable ['codigo'] . "', ";
				$cadenaSql .= "'" . $variable ['correo'] . "', ";
				$cadenaSql .= "'0', ";
				$cadenaSql .= "'" . time () . "' ";
				$cadenaSql .= ")";
				break;
			
			case "actualizarRegistro" :
				$cadenaSql = "UPDATE ";
				$cadenaSql .= $prefijo . "conductor ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "`nombre` = '" . $variable ["nombre"] . "', ";
				$cadenaSql .= "`apellido` = '" . $variable ["apellido"] . "', ";
				$cadenaSql .= "`identificacion` = '" . $variable ["identificacion"] . "', ";
				$cadenaSql .= "`telefono` = '" . $variable ["telefono"] . "' ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "`idConductor` = " . $_REQUEST ["registro"] . " ";
				break;
			
			/**
			 * Clausulas genéricas.
			 * se espera que estén en todos los formularios
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
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
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
				$cadenaSql .= "id_sesion = '" . $idSesion . "'";
				break;
			
			/**
			 * Clausulas Menú.
			 * Mediante estas sentencias se generan los diferentes menus del aplicativo
			 */
			case "datosMenu" :
				$cadenaSql = "/*qc=on*//*qc_ttl=600*/";
				// $cadenaSql = "/*qc=on*//*qc_ttl=1200*/"; //20 min
				$cadenaSql .= " SELECT DISTINCT";
				$cadenaSql .= " mn.id_menu AS id_menu,";
				$cadenaSql .= " mn.etiqueta AS etiqueta_menu,";
				$cadenaSql .= " mn.peso AS menu_grupo,";
				$cadenaSql .= " gru.id_grupo_menu AS id_grupo_menu,";
				$cadenaSql .= " gru.etiqueta AS etiqueta_grupo_menu,";
				$cadenaSql .= " gru.id_grupo_padre AS id_grupo_padre,";
				$cadenaSql .= " gru.peso AS peso_grupo,";
				$cadenaSql .= " serv.id_enlace AS id_enlace,";
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
				$cadenaSql .= " subs.semilla AS semilla";
				$cadenaSql .= " FROM public.urano_menu AS mn";
				$cadenaSql .= " INNER JOIN public.urano_grupo_menu AS gru";
				$cadenaSql .= " ON mn.id_menu=gru.id_menu";
				$cadenaSql .= " AND gru.estado=true";
				$cadenaSql .= " AND mn.estado=true";
				$cadenaSql .= " INNER JOIN public.urano_servicio AS serv";
				$cadenaSql .= " ON serv.id_grupo_menu=gru.id_grupo_menu";
				$cadenaSql .= " AND serv.estado=true";
				$cadenaSql .= " INNER JOIN public.urano_enlace AS enl";
				$cadenaSql .= " ON enl.id_enlace=serv.id_enlace";
				$cadenaSql .= " AND enl.estado=true";
				$cadenaSql .= " LEFT JOIN public.urano_subsistema_sga AS subs";
				$cadenaSql .= " ON subs.id_subsistema_sga=enl.id_subsistema_sga";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " serv.id_rol IN (" . implode ( ", ", $variable ) . ")";
				$cadenaSql .= " ORDER BY";
				$cadenaSql .= " mn.peso,";
				$cadenaSql .= " gru.peso,";
				$cadenaSql .= " enl.etiqueta";
				$cadenaSql .= " ;";
				break;
			
			case "datosMenuFiltrado" :
				$cadenaSql = "/*qc=on*//*qc_ttl=600*/";
				$cadenaSql .= " SELECT DISTINCT";
				$cadenaSql .= " mn.id_menu AS id_menu,";
				$cadenaSql .= " mn.etiqueta AS etiqueta_menu,";
				$cadenaSql .= " mn.peso AS menu_grupo,";
				$cadenaSql .= " gru.id_grupo_menu AS id_grupo_menu,";
				$cadenaSql .= " gru.etiqueta AS etiqueta_grupo_menu,";
				$cadenaSql .= " gru.id_grupo_padre AS id_grupo_padre,";
				$cadenaSql .= " gru.peso AS peso_grupo,";
				$cadenaSql .= " serv.id_enlace AS id_enlace,";
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
				$cadenaSql .= " subs.semilla AS semilla";
				$cadenaSql .= " FROM public.urano_menu AS mn";
				$cadenaSql .= " INNER JOIN public.urano_grupo_menu AS gru";
				$cadenaSql .= " ON mn.id_menu=gru.id_menu";
				$cadenaSql .= " AND gru.estado=true";
				$cadenaSql .= " AND mn.estado=true";
				$cadenaSql .= " INNER JOIN public.urano_servicio AS serv";
				$cadenaSql .= " ON serv.id_grupo_menu=gru.id_grupo_menu";
				$cadenaSql .= " AND serv.estado=true";
				$cadenaSql .= " INNER JOIN public.urano_enlace AS enl";
				$cadenaSql .= " ON enl.id_enlace=serv.id_enlace";
				$cadenaSql .= " AND enl.estado=true";
				$cadenaSql .= " LEFT JOIN public.urano_subsistema_sga AS subs";
				$cadenaSql .= " ON subs.id_subsistema_sga=enl.id_subsistema_sga";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " serv.id_rol IN (" . implode ( ", ", $variable ['perfiles'] ) . ")";
				$cadenaSql .= " AND UPPER(mn.etiqueta||' '||gru.etiqueta||' '||enl.etiqueta) LIKE '%'||UPPER('" . $variable ['etiqueta'] . "')||'%'";
				$cadenaSql .= " ORDER BY";
				$cadenaSql .= " mn.peso,";
				$cadenaSql .= " gru.peso,";
				$cadenaSql .= " enl.etiqueta";
				$cadenaSql .= " LIMIT 5";
				$cadenaSql .= " ;";
				break;
			
			// Se consultan todas las notificaciones registradas para el usuario activo
			case 'buscarNotificaciones' :
				$cadenaSql = "/*qc=on*//*qc_ttl=60*/";
				$cadenaSql .= " SELECT ";
				$cadenaSql .= "notificacion.id AS id, ";
				$cadenaSql .= "titulo AS titulo, ";
				$cadenaSql .= "contenido AS contenido, ";
				$cadenaSql .= "remitente AS remitente, ";
				$cadenaSql .= "fecha_registro AS fecha, ";
				$cadenaSql .= "estado AS estado, ";
				$cadenaSql .= "imagen AS imagen ";
				$cadenaSql .= 'FROM "co_udistrital_academica-general".notificacion ';
				$cadenaSql .= 'JOIN "co_udistrital_academica-general".tipo_notificacion ON tipo_notificacion=tipo_notificacion.id ';
				$cadenaSql .= "WHERE estado<>'0' AND estado<>'-1' ";
				$cadenaSql .= "AND usuario='" . $variable . "' ";
				$cadenaSql .= "ORDER BY fecha_registro DESC; ";
				break;
			
			// Se marca cmom leida la notificación
			case 'actualizarNotificaciones' :
				$cadenaSql = "UPDATE ";
				$cadenaSql .= '"co_udistrital_academica-general".notificacion ';
				$cadenaSql .= "SET estado=2 ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "usuario='" . $variable . "'; ";
				break;
			
			case 'perfilesUsuario' :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "cla_codigo COD, ";
				$cadenaSql .= "cla_clave PWD, ";
				$cadenaSql .= "cla_tipo_usu TIP_US, ";
				$cadenaSql .= "cla_estado EST ";
				$cadenaSql .= "FROM geclaves ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "cla_codigo = '" . $variable . "' ";
				$cadenaSql .= "and cla_estado = 'A' ";
				$cadenaSql .= "ORDER BY cla_estado, cla_tipo_usu";
				break;
			
			case 'buscarRol' :
				$cadenaSql = " SELECT";
				$cadenaSql .= " `nombre`,";
				$cadenaSql .= " `servidor`,";
				$cadenaSql .= " `puerto`,";
				$cadenaSql .= " `ssl`,";
				$cadenaSql .= " `db`,";
				$cadenaSql .= " `usuario`, ";
				$cadenaSql .= " `password`,";
				$cadenaSql .= " `dbms`";
				$cadenaSql .= " FROM";
				$cadenaSql .= " dbms_dbms";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " nombre = '" . $variable . "';";
				// $cadenaSql.=" true";
				break;
			
			case "buscarPerfilesUsuario" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " cla_codigo COD,";
				$cadenaSql .= " cla_clave PWD,";
				$cadenaSql .= " cla_tipo_usu TIP_US,";
				$cadenaSql .= " cla_estado EST";
				$cadenaSql .= " FROM";
				$cadenaSql .= " " . $variable ['sql_tabla1'];
				$cadenaSql .= " WHERE";
				$cadenaSql .= " cla_codigo = '" . $variable ['usuario'] . "'";
				$cadenaSql .= " and cla_estado = 'A' ";
				$cadenaSql .= " ORDER BY cla_estado, cla_tipo_usu";
				break;
			
			case 'datosFuncionario' :
				$cadenaSql = " SELECT cta_usu_id,";
				$cadenaSql .= " cta_nombre_usuario,";
				$cadenaSql .= " TRIM(usu_apellido) || ' ' || TRIM(usu_nombre) nombre";
				$cadenaSql .= " FROM administracion.admin_cuenta";
				$cadenaSql .= " INNER JOIN administracion.admin_usuario ON usu_id = cta_usu_id";
				$cadenaSql .= " WHERE cta_nombre_usuario = '" . $variable . "'";
				break;
		}
		
		return $cadenaSql;
	}
}
?>
