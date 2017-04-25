<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                                                       #
#    
#    Jairo Lavado 		 2004 - 2008                                      #
#                                                   #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/***************************************************************************
  
log.class.php 

Jairo Lavado
Copyright (C) 2016

Última revisión 19 de Enero de 2016 

*****************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.1
* @author      	Jairo Lavado 
* @link		N/D
* @description  Script para guarda el historico de los accesos de los usuarios.
* @usage        Toda pagina tiene un id_pagina que es propagado por cualquier metodo GET, POST.
******************************************************************************/
namespace gui\inicio\funcion;


include_once("funcionGeneral_appserv.class.php");

//date_default_timezone_set("America/Bogota");

class log extends funcionGeneral_appserv {

    private $configuracion;
    private $acceso_MY;
       
    
    function __construct() {
        //[ TO DO ]En futuras implementaciones cada usuario debe tener un estilo
        //include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
        
        require_once("config.class.php");
        $esta_configuracion=new config();
      
        $this->configuracion=$esta_configuracion->variable("../");
        //crea la conexion a la base de datos
        $this->acceso_MY = $this->conectarDB($this->configuracion, "logueo");

        
    }

		
	function log_acceso($acceso)
	{       $acceso['host']=$this->obtenerIP();
                $acceso['fecha']=date("Y-m-d H:i:s");
                $cadena_acceso = $this->cadena_sql('registro_acceso',$acceso);
                $registro = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $cadena_acceso,""); 
	}
        
        
        

        function obtenerIP() {
            if (!empty($_SERVER['HTTP_CLIENT_IP']))
                return $_SERVER['HTTP_CLIENT_IP'];

            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];

            return $_SERVER['REMOTE_ADDR'];
        }
        
        function cadena_sql($tipo,$variable)
		{  switch($tipo)
		      { case "registro_acceso":
                                $cadena_sql="INSERT INTO ";
                                $cadena_sql.=$this->configuracion["prefijo"]."log_acceso "; 
                                $cadena_sql.="( "; 
                                $cadena_sql.="`id_usuario`, "; 
                                $cadena_sql.="`accion`, ";
                                $cadena_sql.="`id_registro`, ";
                                $cadena_sql.="`tipo_registro`, "; 
                                $cadena_sql.="`nombre_registro`, ";
                                $cadena_sql.="`fecha_log`, "; 
                                $cadena_sql.="`descripcion` ,";
                                $cadena_sql.="`host` ";
                                $cadena_sql.=") "; 
                                $cadena_sql.="VALUES ";
                                $cadena_sql.="( "; 
                                $cadena_sql.="'".$variable['id_usuario']."', "; 
                                $cadena_sql.="'".$variable['accion']."', "; 
                                $cadena_sql.="'".$variable['id_registro']."', "; 
                                $cadena_sql.="'".$variable['tipo_registro']."', ";
                                $cadena_sql.="'".$variable['nombre_registro']."', ";
                                $cadena_sql.="'".$variable['fecha']."', ";
                                $cadena_sql.="'".$variable['descripcion']."', "; 
                                $cadena_sql.="'".$variable['host']."' "; 
                                $cadena_sql.=")"; 
                        break;             
                    
                        default    :
                            $cadena_sql= ""; 
                        break;        
			}
		return $cadena_sql;
		}   



}
?>
