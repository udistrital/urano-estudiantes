<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
* @name          pgsql.class.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 02 de abril de 2008
****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link			http://computo.udistrital.edu.co
* @description  Esta clase esta disennada para administrar todas las tareas 
*               relacionadas con la base de datos POSTGRESQL.
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
*		Nombre del DBMS POSTGRES
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
*TO DO    	Implementar la funcionalidad en DBMS POSTGRESQL		
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

class pgsql_appserv
{
	/*** Atributos: ***/
	/**
	 * 
	 * @access privado
	 */
	var $servidor;
	var $puerto;
	var $db;
	var $usuario;
	var $clave;
	var $enlace;
	var $dbsys;
	var $cadena_sql;
	var $error;
	var $numero;
	var $conteo;
	var $registro;
	var $campo;
	var $charset='utf8';//codificacion php

	/*** Fin de sección Atributos: ***/

	/**
     * @name db_admin
	 *	
	 */
	function __construct($registro)
	{
            $this->servidor = $registro[0][1];
            $this->db = $registro[0][4];
            $this->puerto = isset($registro[0][2])?$registro[0][2]:5432;
            $this->usuario = $registro[0][5];
            $this->clave = $registro[0][6];
            $this->dbsys = $registro[0][7];
            $this->enlace=$this->conectar_db();		
	}//Fin del método db_admin

	/**
     * @name especificar_db 
	 * @param string nombre_db 
	 * @return void
	 * @access public
	 */

	function especificar_db( $nombre_db )
	{
		$this->db = $nombre_db;
	} // Fin del método especificar_db

	/**
     * @name especificar_usuario 
	 * @param string usuario_db 
	 * @return void
	 * @access public
	 */
	function especificar_usuario( $usuario_db )
	{
		$this->usuario = $usuario_db;
	} // Fin del método especificar_usuario


	/**
     * @name especificar_clave 
	 * @param string nombre_db 
	 * @return void
	 * @access public
	 */
	function especificar_clave( $clave_db )
	{
		$this->clave = $clave_db;
	} // Fin del método especificar_clave

	/**
	 * 
     * @name especificar_servidor
	 * @param string servidor_db 
	 * @return void
	 * @access public
	 */
	function especificar_servidor( $servidor_db )
	{
		$this->servidor = $servidor_db;
	} // Fin del método especificar_servidor

	/**
	 * 
     * @name especificar_dbms
	 *@param string dbms
	 * @return void
	 * @access public
	 */
	
	function especificar_dbsys( $sistema )
	{
		$this->dbsys = $sistema;
	
	} // Fin del método especificar_dbsys

	/**
	 * 
     * @name especificar_enlace
	 *@param resource enlace
	 * @return void
	 * @access public
	 */
	
	function especificar_enlace($enlace )
	{
		if(is_resource($enlace))
		{
			$this->enlace = $enlace;
		}
	} // Fin del método especificar_enlace

	
	/**
	 * 
     * @name obtener_enlace
	 * @return void
	 * @access public
	 */
	
	function obtener_enlace()
	{
		return $this->enlace;
		
	} // Fin del método obtener_enlace
	
	
	/**
	 * 
     * @name conectar_db
	 * @return void
	 * @access public
	 */
	function conectar_db()
	{
            $this->enlace=pg_connect("host=".$this->servidor." port=".$this->puerto." dbname=".$this->db." user=".$this->usuario." password=".$this->clave);
            pg_set_client_encoding($this->enlace,$this->charset);//linea de codificacion de caracteres.
		if($this->enlace)
		{

			return $this->enlace;
						
		}
		else
		{
			$this->error = pg_last_error($this->enlace);	
		}
              /*  include_once ("conectarPostgresInvnetarios.php");
                $con = new BaseDeDato("","localhost", "5432", "inventarios", "postgres", "postgres");*/
                
	} // Fin del método conectar_db

	/**
	 * 
     * @name probar_conexion
	 * @return void
	 * @access public
	 */
	function probar_conexion()
	{
		
		if($this->enlace==TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		
		
	} // Fin del método probar_conexion
	
	function logger($configuracion,$id_usuario,$evento)
	{
		$this->cadena_sql = "INSERT INTO ";
	 	$this->cadena_sql.= "".$configuracion["prefijo"]."logger ";
	 	$this->cadena_sql.= "( ";
	 	$this->cadena_sql.= "`id_usuario` ,";
		$this->cadena_sql.= " `evento` , ";
		$this->cadena_sql.= "`fecha`  ";
		$this->cadena_sql.= ") ";
		$this->cadena_sql.= "VALUES (";
		$this->cadena_sql.= $id_usuario."," ;
		$this->cadena_sql.= "'".$evento."'," ;
		$this->cadena_sql.= "'".time()."'" ;
		$this->cadena_sql.=")";
	 	$this->ejecutar_acceso_db($this->cadena_sql); 
		unset($this->db_sel);
		return TRUE;	
	
	}
	

	/**
	 * 
     * @name desconectar_db
	 * @param resource enlace
	 * @return void
	 * @access public
	 */
	function desconectar_db()
	{
		mysql_close($this->enlace);
		
	} //Fin del método desconectar_db

	
	/**
     * @name ejecutar_acceso_db	 
	* @param string cadena_sql 
	* @param string conexion_id
	* @return boolean
	* @access public
	 */
	
	function ejecutarAcceso($cadena_sql,$tipo) 
	{       
		//@todo borrar esto

		if(!is_resource($this->enlace))
		{
			return FALSE;
		}
                
                if($tipo=="busqueda")
		{
                        $esteRegistro=$this->ejecutar_busqueda($cadena_sql);	
                        return $esteRegistro;
		}
		else
		{
                        $result = pg_query($this->enlace,$cadena_sql);
                        if(!$result) 
                        {
                                $this->error= pg_last_error($this->enlace);
                                return FALSE;
                        }else{
                                $this->afectadas = pg_affected_rows($result);
                                return $this->afectadas;
                        }
                }
                
	}

	/**
     * @name obtener_error	 
	* @param string cadena_sql 
	* @param string conexion_id
	* @return boolean
	* @access public
	 */
	
	function obtener_error()
	{
		
		return $this->error;
	
	}//Fin del método obtener_error

	/**
     * @name registro_db
	* @param string cadena_sql 
	* @param int numero
	* @return boolean
	* @access public
	 */
	function registro_db($cadena_sql,$numero) //pg_fetch_assoc
	{
            unset($this->registro);		
            if(!is_resource($this->enlace))
		{
			return FALSE;
		}
                
                $busqueda=pg_query($this->enlace,$cadena_sql);
		if($busqueda)
		{
			unset($this->registro);			
                        
            //carga una a una las filas en $this->registro
			while($row=@pg_fetch_array($busqueda,null,PGSQL_BOTH)){
				//array_walk($row, array($this,'trim_value'));
				$this->registro[]=$row;
			}

			//cuenta el numero de registros del arreglo $this->registro
			$this->conteo=count($this->obtener_registro_db());
			//@$this->afectadas=mysql_affected_rows($busqueda);

			pg_free_result($busqueda);
			return $this->conteo;
		}
		else
		{
			unset($this->registro);
			$this->error =pg_last_error($this->enlace);
			return 0;
		}             
	

	}// Fin del método registro_db
	
	/**Elimina espacios en blanco
    * @name trim_value
	* @param string $value
	* @return array
	* @access public
	 */	
	function trim_value(&$value){ 
		$value = trim($value); 
	}



	
	
	/**
     * @name obtener_registro_db	 
	* @return registro []
	* @access public
	 */

	function obtener_registro_db() 
	{
		if(isset($this->registro))
		{
			return $this->registro;
		}
	}//Fin del método obtener_registro_db
	
	
	/**
     * @name obtener_conteo_db	 
	* @return int conteo
	* @access public
	 */
	function obtener_conteo_db() 
	{
		return $this->conteo;
	
	}//Fin del método obtener_conteo_db

	function ultimo_insertado($enlace)
	{
		return mysql_insert_id($enlace);	
	}

/**
     * @name transaccion
	* @return boolean resultado
	* @access public
	 */
	function transaccion($insert,$delete) 
	{
	
		$this->instrucciones=count($insert);
		
		for($contador=0;$contador<$this->instrucciones;$contador++)
		{
			$acceso=$this->ejecutar_acceso_db($insert[$contador]);
		
			if(!$acceso)
			{
				
				for($contador_2=0;$contador_2<$this->instrucciones;$contador_2++)
				{
					@$acceso=$this->ejecutar_acceso_db($delete[$contador_2]);
					}
				return FALSE;
			
				}
			
		}
		return TRUE;
	
	}//Fin del método transaccion

	
	function ejecutar_busqueda($cadena_sql)
	{
		$this->registro_db($cadena_sql,0);
		$registro=$this->obtener_registro_db();
		return $registro;
	}
	
	
	/*function vaciar_temporales($configuracion,$sesion)
	{
		$this->esta_sesion=$sesion;
		$this->cadena_sql="DELETE ";
		$this->cadena_sql.="FROM ";
		$this->cadena_sql.=$configuracion["prefijo"]."registrado_borrador ";
		$this->cadena_sql.="WHERE ";
		$this->cadena_sql.="identificador<".(time()-3600);
		$this->ejecutar_acceso_db($this->cadena_sql);
		
	}*/
	
	//Funcion para preprocesar la creacion de clausulas sql;
	function verificar_variables($variables)
	{
		if(is_array($variables))
		{
			$dimcount = 1;
				 if (is_array(reset($variables))) 
				 {
				       $dimcount++;
				       
				   }			   
			
			
				if($dimcount==1)
				{
					
				
					foreach ($variables as $key => $value) 
					{
						$variables[$key]=pg_escape_string($value);
					}
				}
		}
		else
		{
			$variables=pg_escape_string($variables);
		}
		
		return $variables;
	}

	function obtener_afectadas()
	{
           	return $this->afectadas ;

	}//Fin del método obtener_conteo_db
	
	

	
}//Fin de la clase db_admin

?>
