<?php

namespace gui\inicio;

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
			case 'buscarRol':
				$cadenaSql=" SELECT";
				$cadenaSql.=" `nombre`,";
				$cadenaSql.=" `servidor`,";
				$cadenaSql.=" `puerto`,";
				$cadenaSql.=" `ssl`,";
				$cadenaSql.=" `db`,";
				$cadenaSql.=" `usuario`, ";
				$cadenaSql.=" `password`,";
				$cadenaSql.=" `dbms`";
				$cadenaSql.=" FROM";
				$cadenaSql.=" dbms_dbms";
				$cadenaSql.=" WHERE";
				$cadenaSql.=" nombre='".$variable."';";
				//$cadenaSql.=" true";
				break;
			
			// case 'buscarRol':
				// $cadenaSql=" UPDATE";
				// $cadenaSql.=" ".$variable['prefijo']."valor_sesion";
				// $cadenaSql.=" SET";
				// $cadenaSql.=" valor='".$variable['valor']."'";
				// $cadenaSql.=" WHERE";
				// $cadenaSql.=" id_sesion='".$variable['id_sesion']."'";
				// $cadenaSql.=" AND";
				// $cadenaSql.=" variable='expiracion'";
				// break;
			
			case "buscarConfiguracionDBMS" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " `parametro`,";
				$cadenaSql .= " `valor`";
				$cadenaSql .= " FROM";
				$cadenaSql .= " dbms_configuracion";
				$cadenaSql .= " ;";
				break;
				
			case "buscarClaveUsuario" :
				$cadenaSql=" SELECT";
				$cadenaSql.=" cla_codigo COD,";
				$cadenaSql.=" cla_clave PWD,";
				$cadenaSql.=" cla_tipo_usu TIP_US,";
				$cadenaSql.=" cla_estado EST";
				$cadenaSql.=" FROM";
				$cadenaSql.=" " . $variable['sql_tabla1'];
				$cadenaSql.=" WHERE";
				$cadenaSql.=" cla_codigo='" . $variable['usuario'] . "'";
				$cadenaSql.=" ORDER BY cla_estado,cla_tipo_usu";
				break;
				
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
		}
		
		return $cadenaSql;
	}
}
?>
