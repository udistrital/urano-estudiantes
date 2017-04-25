<?php
/***************************************************************************
 *    Copyright (c) 2004 - 2006 :                                           *
 *    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        *
 *    Comite Institucional de Acreditacion                                  *
 *    siae@udistrital.edu.co                                                *
 *    Paulo Cesar Coronado                                                  *
 *    paulo_cesar@udistrital.edu.co                                         *
 *                                                                          *
 ****************************************************************************
 *                                                                          *
 *                                                                          *
 * SIAE es software libre. Puede redistribuirlo y/o modificarlo bajo los    *
 * términos de la Licencia Pública General GNU tal como la publica la       *
 * Free Software Foundation en la versión 2 de la Licencia ó, a su elección,*
 * cualquier versión posterior.                                             *
 *                                                                          *
 * SIAE se distribuye con la esperanza de que sea útil, pero SIN NINGUNA    *
 * GARANTÍA. Incluso sin garantía implícita de COMERCIALIZACIÓN o ADECUACIÓN*
 * PARA UN PROPÓSITO PARTICULAR. Vea la Licencia Pública General GNU para   *
 * más detalles.                                                            *
 *                                                                          *
 * Debería haber recibido una copia de la Licencia pública General GNU junto*
 * con SIAE; si esto no ocurrió, escriba a la Free Software Foundation, Inc,*
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307, Estados Unidos de     *
 * América                                                                  *
 *                                                                          *
 *                                                                          *
 ***************************************************************************/
?><?php
/****************************************************************************
 * @name          encriptar.class.php
 * @author        Paulo Cesar Coronado
 * @revision      Última revisión 28 de agosto de 2006
 *****************************************************************************
 * @subpackage
 * @package	clase
 * @copyright
 * @version      0.1
 * @author      	Paulo Cesar Coronado
 * @link
 * @description  Esta clase esta disennada para cifrar y decifrar las variables que se pasan a las paginas
 *		se recomienda que en cada distribucion el administrador del sistema use mecanismos de cifrado.
 *		diferentes a los originales
 * @author        Jairo Lavado
 * @revision      Última revisión 07 de Noviembre de 2012
 *
 ******************************************************************************/
?><?php

namespace gui\menuPrincipal\funcion;

require_once('aes_saraacademica.class.php');
require_once('aesctl_saraacademica.class.php');

class encriptar {
    var $miConfigurador;
    var $miSql;
	private $cifrado_appserv;
	private $modo_appserv;
	//Constructor
	function __construct($sql) {
	    $this->miSql = $sql;
	    $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this -> cifrado_appserv = MCRYPT_RIJNDAEL_256;
		$this -> modo_appserv = MCRYPT_MODE_ECB;
        $this -> config_appserv = $this->getConfiguracionFromDB();
	}
	
	function getConfiguracion(){
		return $this -> config_appserv;
	}

	function codificar_url_appserv($cadena) {        
		$cadena = mcrypt_encrypt(
			$this -> cifrado_appserv,
			$this -> config_appserv['verificador'],
			$cadena,
			$this -> modo_appserv,
			mcrypt_create_iv(
				mcrypt_get_iv_size($this -> cifrado_appserv, $this -> modo_appserv),
				MCRYPT_RAND
			)
		);
		$cadena = rtrim(strtr(base64_encode($cadena), '+/', '-_'), '=');
		//$cadena = $configuracion['enlace'] . '=' . $cadena;
		return $cadena;
	}

    private function getConfiguracionFromDB(){
        $conexion = 'appserv';
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        
        $cadenaSql = $this->miSql->getCadenaSql ( 'buscarConfiguracionDBMS' );
        
        $resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
        
        $configuracion = array();
        foreach($resultado as $row){
            $configuracion[$row[0]] = $row[1];
        }
        return $configuracion;
    }

	function decodificar_url_appserv($cadena, $configuracion) {
		$cadena = base64_decode(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT));
		$cadena = mcrypt_decrypt(
			$this -> cifrado,
			$configuracion['verificador'],
			$cadena, $this -> modo,
			mcrypt_create_iv(mcrypt_get_iv_size($this -> cifrado, $this -> modo), MCRYPT_RAND)
		);
		parse_str($cadena, $matriz);

		foreach ($_REQUEST as $clave => $valor) {
			unset($_REQUEST[$clave]);
		}

		foreach ($matriz as $clave => $valor) {
			$_REQUEST[$clave] = $valor;
		}
		return true;
	}
	
	function codificar_appserv($cadena, $configuracion) {
		$cadena = base64_encode($cadena);
		$cadena = strrev($cadena);
		return $cadena;

	}

	function decodificar_appserv($cadena) {
		$cadena = strrev($cadena);
		$cadena = base64_decode($cadena);
		return $cadena;
	}

	function codificar_sara($cadena){
		/*reemplaza valores + / */
		$cadena=rtrim(strtr(\AesCtr_saraacademica::encrypt($cadena,'', 256), '+/', '-_'), '='); 
		return $cadena;
	}
	
	function decodificar_sara($cadena){
		/*reemplaza valores + / */
		$cadena=\AesCtr_saraacademica::decrypt(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT),"",256);	
		return $cadena;
	}
	
	function codificar_arka($cadena, $semilla) {
		if (function_exists ( 'mcrypt_encrypt' )) {
			$cadena = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $semilla, $cadena, MCRYPT_MODE_ECB ) ;
		} else {
			$cadena = AesCtr::encrypt ( $cadena, $token, 256 ) ;
		}
		$cadena=trim($this->base64url_encode($cadena));
		return $cadena;
	}
	
	function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
	
	function codificar_kyron($cadena, $semillaKyron) {
		if (function_exists ( 'mcrypt_encrypt' )) {
			$cadena = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $semillaKyron, $cadena, MCRYPT_MODE_ECB ) ;
		} else {
			$cadena = AesCtr::encrypt ( $cadena, $semillaKyron, 256 ) ;
		}
		$cadena=trim($this->base64url_encode($cadena));
		return $cadena;
	}
	
	function codificar_urano($cadena, $semillaUrano) {		;
		if (function_exists ( 'mcrypt_encrypt' )) {
			$cadena = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $semillaUrano, $cadena, MCRYPT_MODE_ECB ) ;
		} else {
			$cadena = AesCtr::encrypt ( $cadena, $semillaUrano, 256 ) ;
		}
		$cadena=trim($this->base64url_encode($cadena));
		return $cadena;
	}
}//Fin de la clase

?>
