<?php

namespace gui\home;

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
	function getCadenaSql($tipo, $variable = '') {
		
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
			case 'insertarPagina' :
				$cadenaSql = 'INSERT INTO ';
				$cadenaSql .= $prefijo . 'pagina ';
				$cadenaSql .= '( ';
				$cadenaSql .= 'nombre,';
				$cadenaSql .= 'descripcion,';
				$cadenaSql .= 'modulo,';
				$cadenaSql .= 'nivel,';
				$cadenaSql .= 'parametro';
				$cadenaSql .= ') ';
				$cadenaSql .= 'VALUES ';
				$cadenaSql .= '( ';
				$cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
				$cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
				$cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
				$cadenaSql .= ') ';
				break;
			
			case 'buscarPagina' :
				
				$cadenaSql = 'SELECT ';
				$cadenaSql .= 'id_pagina as PAGINA, ';
				$cadenaSql .= 'nombre as NOMBRE, ';
				$cadenaSql .= 'descripcion as DESCRIPCION,';
				$cadenaSql .= 'modulo as MODULO,';
				$cadenaSql .= 'nivel as NIVEL,';
				$cadenaSql .= 'parametro as PARAMETRO ';
				$cadenaSql .= 'FROM ';
				$cadenaSql .= $prefijo . 'pagina ';
				$cadenaSql .= 'WHERE ';
				$cadenaSql .= 'nombre=\'' . $_REQUEST ['nombrePagina'] . '\' ';
				break;
			
			case 'insertarBloque' :
				$cadenaSql = 'INSERT INTO ';
				$cadenaSql .= $prefijo . 'bloque ';
				$cadenaSql .= '( ';
				$cadenaSql .= 'nombre,';
				$cadenaSql .= 'descripcion,';
				$cadenaSql .= 'grupo';
				$cadenaSql .= ') ';
				$cadenaSql .= 'VALUES ';
				$cadenaSql .= '( ';
				$cadenaSql .= '\'' . $_REQUEST ['nombreBloque'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['descripcionBloque'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['grupoBloque'] . '\' ';
				$cadenaSql .= ') ';
				break;
			
			case 'buscarBloque' :
				
				$cadenaSql = 'SELECT ';
				$cadenaSql .= 'id_bloque as BLOQUE, ';
				$cadenaSql .= 'nombre as NOMBRE, ';
				$cadenaSql .= 'descripcion as DESCRIPCION,';
				$cadenaSql .= 'grupo as GRUPO ';
				$cadenaSql .= 'FROM ';
				$cadenaSql .= $prefijo . 'bloque ';
				$cadenaSql .= 'WHERE ';
				$cadenaSql .= 'nombre=\'' . $_REQUEST ['nombreBloque'] . '\' ';
				break;
			
			case 'buscarBloques' :
				
				$cadenaSql = 'SELECT ';
				$cadenaSql .= 'id_bloque as BLOQUE, ';
				$cadenaSql .= 'nombre as NOMBRE, ';
				$cadenaSql .= 'descripcion as DESCRIPCION,';
				$cadenaSql .= 'grupo as GRUPO ';
				$cadenaSql .= 'FROM ';
				$cadenaSql .= $prefijo . 'bloque ';
				$cadenaSql .= 'WHERE ';
				$cadenaSql .= 'id_bloque>0';
				break;
			
			case 'buscarNoticias' :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "nombre, ";
				$cadenaSql .= "descripcion, ";
				$cadenaSql .= "enlace, ";
				$cadenaSql .= "tipo_noticia, ";
				$cadenaSql .= "anio, ";
				$cadenaSql .= "periodo, ";
				$cadenaSql .= "fecha_radicacion, ";
				$cadenaSql .= "remitente, ";
				$cadenaSql .= "imagen ";
				$cadenaSql .= 'FROM "co_udistrital_academica-general".noticia ';
				$cadenaSql .= "WHERE estado=1 ";
				$cadenaSql .= "AND now()::date BETWEEN fecha_inicio AND fecha_fin ";
				$cadenaSql .= "ORDER BY fecha_radicacion DESC;";
				break;
			
			case 'buscarNoticiasOracle' :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " CME_TITULO nombre,";
				$cadenaSql .= " CME_MENSAJE descripcion,";
				$cadenaSql .= " 'N/A' enlace,";
				$cadenaSql .= " CME_CRA_COD tipo,";
				$cadenaSql .= " TO_CHAR(CME_FECHA_INI,'yyyy') anio,";
				$cadenaSql .= " 'N/A' periodo,";
				$cadenaSql .= " TO_CHAR(CME_FECHA_INI,'yyyy-mm-dd') fecha_radicacion,";
				$cadenaSql .= " CME_AUTOR remitente,";
				$cadenaSql .= " 'N/A' imagen";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " MNTAC.ACCOORMENSAJE";
				$cadenaSql .= " INNER JOIN ACEST ON CME_CRA_COD = EST_CRA_COD";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " TO_CHAR(CURRENT_TIMESTAMP,'dd/mm/yyyy') BETWEEN TO_CHAR(CME_FECHA_INI,'dd/mm/yyyy') AND TO_CHAR(CME_FECHA_FIN,'dd/mm/yyyy') ";
				$cadenaSql .= " AND EST_COD=" . $variable;
				break;
			
			// //Asignaturas inscritas
			case 'buscarAsignaturasInscritas' :
				$cadenaSql = " SELECT ins_asi_cod CODIGO,";
				$cadenaSql .= " (lpad(cur_cra_cod,3,0)||'-'||cur_grupo) GRUPO,";
				$cadenaSql .= " asi_nombre NOMBRE,";
				$cadenaSql .= " ins_cred CREDITOS, ";
				$cadenaSql .= " cea_abr CLASIFICACION, ";
				$cadenaSql .= " ins_ano ANIO,";
				$cadenaSql .= " ins_per PERIODO,";
				$cadenaSql .= " ins_gr CURSO";
				$cadenaSql .= " FROM acasi, acins, accursos, geclasificaespac";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " acasi.asi_cod=acins.INS_ASI_COD";
				$cadenaSql .= " AND accursos.CUR_ASI_COD=acins.INS_ASI_COD";
				$cadenaSql .= " AND ins_gr=cur_id";
				$cadenaSql .= " AND geclasificaespac.CEA_COD=acins.INS_CEA_COD";
				$cadenaSql .= " AND ins_est_cod=" . $variable;
				$cadenaSql .= " AND ins_ano=(SELECT APE_ANO FROM ACASPERI WHERE APE_ESTADO LIKE '%A%') ";
				$cadenaSql .= " AND ins_per=(SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO LIKE '%A%')";
				$cadenaSql .= " AND ins_estado LIKE '%A%'";
				$cadenaSql .= " ORDER BY CODIGO";
				// echo $cadenaSql;
				break;
			
			// Consulta horario grupo
			case 'buscarHorarioGrupo' :
				$cadenaSql = " SELECT DISTINCT";
				$cadenaSql .= " HOR_DIA_NRO DIA,";
				$cadenaSql .= " HOR_HORA HORA,";
				$cadenaSql .= " SED_ID COD_SEDE,";
				$cadenaSql .= " SED_ID NOM_SEDE,";
				$cadenaSql .= " SAL_EDIFICIO ID_EDIFICIO,";
				$cadenaSql .= " EDI_NOMBRE NOM_EDIFICIO,";
				$cadenaSql .= " HOR_SAL_ID_ESPACIO ID_SALON,";
				$cadenaSql .= " SAL_NOMBRE NOM_SALON";
				$cadenaSql .= " FROM ACHORARIOS";
				$cadenaSql .= " INNER JOIN ACCURSOS ON hor_id_curso=cur_id";
				$cadenaSql .= " Left Outer Join Gesalones ON hor_sal_id_espacio = sal_id_espacio AND sal_estado='A'";
				$cadenaSql .= " LEFT OUTER JOIN gesede ON sal_sed_id=sed_id";
				$cadenaSql .= " LEFT OUTER JOIN geedificio On Sal_Edificio=Edi_Cod";
				$cadenaSql .= " WHERE CUR_ASI_COD=" . $variable ['CODIGO'];
				$cadenaSql .= " AND cur_ape_ano=(SELECT APE_ANO FROM ACASPERI WHERE APE_ESTADO LIKE '%A%') ";
				$cadenaSql .= " And Cur_Ape_Per=(Select Ape_Per From Acasperi Where Ape_Estado Like '%A%')";
				$cadenaSql .= " AND Hor_id_curso=" . $variable ['CURSO'];
				// $cadenaSql.=" AND HOR_DIA_NRO=".$variable['DIA'];
				$cadenaSql .= " ORDER BY 1,2,3";
				// echo $cadenaSql;
				break;
			
			case 'buscarHorario' :
				
				$cadenaSql = " SELECT ins_asi_cod CODIGO,";
				$cadenaSql .= " INS_ASI_COD,";
				
				$cadenaSql .= " (lpad(cur_cra_cod,3,0)||'-'||cur_grupo) GRUPO,";
				$cadenaSql .= " asi_nombre NOMBRE,";
				$cadenaSql .= " ins_cred CREDITOS,";
				$cadenaSql .= " cea_abr CLASIFICACION,";
				$cadenaSql .= " ins_ano ANIO,";
				$cadenaSql .= " ins_per PERIODO,";
				$cadenaSql .= " ins_gr CURSO,";
				$cadenaSql .= " HOR_DIA_NRO DIA,";
				$cadenaSql .= " HOR_HORA HORA,";
				$cadenaSql .= " SED_ID COD_SEDE,";
				$cadenaSql .= " SED_NOMBRE NOM_SEDE,";
				$cadenaSql .= " SAL_EDIFICIO ID_EDIFICIO,";
				$cadenaSql .= " EDI_NOMBRE NOM_EDIFICIO,";
				$cadenaSql .= " HOR_SAL_ID_ESPACIO ID_SALON,";
				$cadenaSql .= " SAL_NOMBRE NOM_SALON";
				$cadenaSql .= " FROM acasi, acins, accursos, geclasificaespac, ACHORARIOS, Gesalones, gesede, geedificio";
				$cadenaSql .= " WHERE";
				$cadenaSql .= " acasi.asi_cod=acins.INS_ASI_COD";
				$cadenaSql .= " AND accursos.CUR_ASI_COD=acins.INS_ASI_COD";
				$cadenaSql .= " AND ins_gr=ACCURSOS.cur_id";
				$cadenaSql .= " AND geclasificaespac.CEA_COD=acins.INS_CEA_COD";
				$cadenaSql .= " AND ins_est_cod=" . $variable ['CODIGO'];
				$cadenaSql .= " AND ins_ano=(SELECT APE_ANO FROM ACASPERI WHERE APE_ESTADO LIKE '%A%')";
				$cadenaSql .= " AND ins_per=(SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO LIKE '%A%')";
				$cadenaSql .= " AND ins_estado LIKE '%A%'";
				$cadenaSql .= " AND hor_id_curso=ACCURSOS.cur_id";
				$cadenaSql .= " AND hor_sal_id_espacio = sal_id_espacio AND sal_estado='A'";
				$cadenaSql .= " AND sal_sed_id=sed_id";
				$cadenaSql .= " AND Sal_Edificio=Edi_Cod";
				$cadenaSql .= " AND HOR_DIA_NRO=" . $variable ['DIA'];
				$cadenaSql .= " ORDER BY DIA, HORA";
				
				// echo $cadenaSql;
				break;
			
			case 'datosFuncionario' :
				$cadenaSql = " SELECT cta_usu_id,";
				$cadenaSql .= " cta_nombre_usuario,";
				$cadenaSql .= " TRIM(usu_apellido)||' '||TRIM(usu_nombre) nombre";
				$cadenaSql .= " FROM administracion.admin_cuenta";
				$cadenaSql .= " INNER JOIN administracion.admin_usuario ON usu_id=cta_usu_id";
				$cadenaSql .= " WHERE cta_nombre_usuario='" . $variable . "'";
				break;
			
			case 'pruebaDatos' :
				$cadenaSql = " SELECT cta_usu_id,";
				$cadenaSql .= " cta_nombre_usuario,";
				$cadenaSql .= " cta_estado,";
				$cadenaSql .= " TRIM(usu_apellido)||' '||TRIM(usu_nombre) nombre,";
				$cadenaSql .= " usu_tipo_doc_actual,";
				$cadenaSql .= " usu_nro_doc_actual,";
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
		}
		
		if (! isset ( $cadenaSql )) {
			echo "No se encontr&oacute; la sentencia: '" . $tipo . "'";
		}
		
		return $cadenaSql;
	}
}
?>
