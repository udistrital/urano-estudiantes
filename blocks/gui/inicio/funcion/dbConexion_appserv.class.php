<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
* @name          dbms.class.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 26 de junio de 2005
****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.3
* @author      	Paulo Cesar Coronado
* @link		http://condor.udistrital.edu.co
* @description  Esta clase esta disennada para administrar todas las tareas 
*               relacionadas con la base de datos.
* @author        Jairo Lavado
* @revision      Última revisión 07 de Noviembre de 2012
* @description  Se modifica la clase para decodificar el usuario y la clave en dbms
*
******************************************************************************/


/*****************************************************************************
*Atributos
*
*@access private
*@param  $servidor
*		URL del servidor de bases de datos. 
*@param  $db
*		Nombre de la base de datos
*@param  $usuario
*		Usuario de la base de datos
*@param  $clave
*		Clave de acceso al servidor de bases de datos
*@param  $enlace
*		Identificador del enlace a la base de datos
*@param  $dbms
*		Nombre del DBMS, por defecto se tiene MySQL
*@param  $cadena_sql
*		Clausula SQL a ejecutar
*@param  $error
*		Mensaje de error devuelto por el DBMS
*@param  $numero
*		Número de registros a devolver en una consulta
*@param  $conteo
*		Número de registros que existen en una consulta
*@param  $registro
*		Matriz para almacenar los resultados de una búsqueda
*@param  $campo
*		Número de campos que devuelve una consulta
*TO DO    	Implementar la funcionalidad en DBMS diferentes a MySQL		
*******************************************************************************/

/*****************************************************************************
*Métodos
*
*@access public
*
     * @name db_admin
*	 Constructor. Define los valores por defecto 
     * @name especificar_db
*	 Especifica a través de código el nombre de la base de datos
     * @name especificar_usuario
*	 Especifica a través de código el nombre del usuario de la DB
     * @name especificar_clave
*	 Especifica a través de código la clave de acceso al servidor de DB
     * @name especificar_servidor
*	 Especificar a través de código la URL del servidor de DB
     * @name especificar_dbms
*	 Especificar a través de código el nombre del DBMS
     * @name especificar_enlace
*	 Especificar el recurso de enlace a la DBMS
     * @name conectar_db
*	 Conecta a un DBMS
     * @name probar_conexion
*	 Con la cual se realizan acciones que prueban la validez de la conexión
     * @name desconectar_db
*	 Libera la conexion al DBMS
     * @name ejecutar_acceso_db
*	 Ejecuta clausulas SQL de tipo INSERT, UPDATE, DELETE
     * @name obtener_error
*	 Devuelve el mensaje de error generado por el DBMS
     * @name obtener_conteo_dbregistro_db
*	 Devuelve el número de registros que tiene una consulta
     * @name registro_db
*	 Ejecuta clausulas SQL de tipo SELECT
     * @name obtener_registro_db
*	 Devuelve el resultado de una consulta como una matriz bidimensional
     * @name obtener_error
*	 Realiza una consulta SQL y la guarda en una matriz bidimensional
*
******************************************************************************/
namespace gui\inicio\funcion;
include_once('mysql_appserv.class.php');
include_once('oci8_appserv.class.php');
include_once('pgsql_appserv.class.php');
use gui\inicio\funcion\mysql_appserv as mysql_appserv;
use gui\inicio\funcion\oci8_appserv as oci8_appserv;
use gui\inicio\funcion\pgsql_appserv as pgsql_appserv;

class dbConexion_appserv
{
    private $cripto;
    private $Semilla;
            
	public function __construct()
	{
            require_once("encriptar.class.php");
            include_once("dbms_appserv.class.php");
            $this->cripto=new encriptar();
            $this->Semilla="condor";
		
	}
	
	public function recursodb($configuracion,$nombre="")
	{
		$acceso_db=new dbms_appserv($configuracion);
		$enlace=$acceso_db->conectar_db();

		if ($enlace)
		{
			if($nombre!="")
			{
				$cadena_sql="SELECT "; 
				$cadena_sql.="`nombre`, ";			 
				$cadena_sql.="`servidor`, "; 
				$cadena_sql.="`puerto`, "; 
				$cadena_sql.="`ssl`, "; 
				$cadena_sql.="`db`, "; 
				$cadena_sql.="`usuario`, "; 
				$cadena_sql.="`password`, ";
				$cadena_sql.="`dbms` "; 
				$cadena_sql.="FROM "; 
				$cadena_sql.=$configuracion["prefijo"]."dbms ";
				$cadena_sql.="WHERE "; 
				$cadena_sql.="nombre='".$nombre."'";  
				                               
				$acceso_db->registro_db($cadena_sql,0);
				$registro=$acceso_db->obtener_registro_db();                                

                                if(is_array($registro))
				{	
                                    $dbms_appserv='gui\inicio\funcion\\'.$registro[0][7].'_appserv';                                                                           
                                    //var_dump($dbms_appserv);exit;
                                    /*lineas actualizadas para la decodificacion de las claves y usuarios */
                                    $registro[0][5]=trim($this->cripto->decodificar_variable($registro[0][5],$this->Semilla));
                                    //$registro[0]['usuario']=trim($this->cripto->decodificar_variable($registro[0]['usuario'],$this->Semilla));
                                    $registro[0][6]=trim($this->cripto->decodificar_variable($registro[0][6],$this->Semilla));
                                    //$registro[0]['password']=trim($this->cripto->decodificar_variable($registro[0]['password'],$this->Semilla));                                    
                                    
                                return new $dbms_appserv($registro);                                                              
                                
				}
				else
				{	
					
					throw new Exception('Lamentablemente esta instancia no se encuentra registrada en el sistema.');
				}
			}
			else
			{
				return $acceso_db;
			}
		}
			
		
		
	}
	
}//Fin de la clase db_admin

?>
