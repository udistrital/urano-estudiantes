<?
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
?><?
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
?><?
namespace gui\inicio\funcion;

class encriptar
{
    
     private $cifrado;
     private $modo;
	//Constructor
	function __construct()
	{	
	    $this->cifrado = MCRYPT_RIJNDAEL_256;
            $this->modo = MCRYPT_MODE_ECB;
	
	}
	
	function codificar_url($cadena,$configuracion)
	{       
                $cadena=mcrypt_encrypt($this->cifrado, $configuracion['verificador'], $cadena, $this->modo, mcrypt_create_iv(mcrypt_get_iv_size($this->cifrado, $this->modo), MCRYPT_RAND) );
                $cadena=rtrim(strtr(base64_encode($cadena), '+/', '-_'), '='); 

                $cadena=$configuracion["enlace"]."=".$cadena;
		return $cadena;
	}
	
	
	function decodificar_url($cadena,$configuracion)
	{       
		$cadena=base64_decode(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT)); 
                $cadena=mcrypt_decrypt($this->cifrado, $configuracion['verificador'], $cadena, $this->modo, mcrypt_create_iv(mcrypt_get_iv_size($this->cifrado, $this->modo), MCRYPT_RAND) );
                parse_str($cadena,$matriz);
		
		foreach($_REQUEST as $clave => $valor) 
		{
			unset($_REQUEST[$clave]);
		} 
		
		foreach($matriz as $clave=>$valor)
		{
			$_REQUEST[$clave]=$valor;			
		}
		
		return TRUE;
	}
	
	function codificar($cadena,$configuracion)
	{ 
		$cadena=base64_encode($cadena);
		$cadena=strrev($cadena);
		return $cadena;
	
	}
	
	
	function decodificar($cadena)
	{      
		$cadena=strrev($cadena);
		$cadena=base64_decode($cadena);
		
		return $cadena;
	
	
	}
        
    function codificar_variable($cadena,$semilla)
	{       $cadena=mcrypt_encrypt($this->cifrado, $semilla, $cadena, $this->modo, mcrypt_create_iv(mcrypt_get_iv_size($this->cifrado, $this->modo), MCRYPT_RAND) );
                $cadena=rtrim(strtr(base64_encode($cadena), '+/', '-_'), '='); 
		return $cadena;
	}
	
	function decodificar_variable($cadena,$semilla)
	{       $cadena=base64_decode(str_pad(strtr($cadena, '-_', '+/'), strlen($cadena) % 4, '=', STR_PAD_RIGHT)); 
                $cadena=mcrypt_decrypt($this->cifrado, $semilla, $cadena, $this->modo, mcrypt_create_iv(mcrypt_get_iv_size($this->cifrado, $this->modo), MCRYPT_RAND) );
                return $cadena;
	}
	
}//Fin de la clase

?>
