<?php
namespace gui\inicio\funcion;
// use gui\menuPrincipal\Sql;
// use core\general\ValidadorCampos;
// use gui\menuPrincipal\funcion\encriptar;
//Se configura el cross allow origin para el ambiente de producción.
$host = $this->miConfigurador->getVariableConfiguracion ( "host" );
header('Access-Control-Allow-Origin: '.$host.'');

//Se necesita para codificar o decodificar información
$_REQUEST['tiempo'] = time();
$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );

//Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php
switch ($_REQUEST ['funcion']) {
	case 'verificarSesion':
		include 'VerificarSesion.php';		
		$respuesta=($respuesta==false)?false:true;
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($respuesta);
		exit ();
		break;
    default:
        die('Asigne la variable \'funcion\'');
}
?>