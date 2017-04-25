<?php

/* --------------------------------------------------------------------------------------------------------------------------
@ Derechos de Autor: Vea el archivo LICENCIA.txt que viene con la distribucion
--------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------
|				Control Versiones 				    	|
|bloque: admin_proyecto							    	|
----------------------------------------------------------------------------------------
| fecha      |        Autor            | version     |              Detalle            |
----------------------------------------------------------------------------------------
| 13/01/2012 | Jairo Lavado      	 | 0.0.0.1     |                                 |
----------------------------------------------------------------------------------------
| 19/07/2012 | Jairo Lavado      	 | 0.0.0.2     |                                 | 
----------------------------------------------------------------------------------------
*/

namespace gui\inicio\funcion;
include_once("funcionGeneral_appserv.class.php");
use gui\inicio\funcion\funcionGeneral_appserv as funcionGeneral_appserv;


//date_default_timezone_set("America/Bogota");

class expira_sesion extends funcionGeneral_appserv
{
    
    private $configuracion;
    private $acceso_MY;
    private $acceso_Est;
    private $acceso_Est2;
    private $acceso_Est3;
    private $acceso_Fun;
    
    private $CarpetaSesion;
    private $NumSesionEst;
    private $NumSesionEst2;
    private $NumSesionEst3;
    private $NumSesionFun;
    private $TimeSesionEst;
    private $TimeSesionFun;
    
    
    function __construct()
    {
        //[ TO DO ]En futuras implementaciones cada usuario debe tener un estilo
        //include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
        
        require_once("config.class.php");
        $esta_configuracion = new config();
        
        $this->configuracion = $esta_configuracion->variable("../");
        
        /*variables para controlar las sesiones*/
	$this->CarpetaSesion = $this->configuracion['raiz_documento'] . "/clase/sesiones/";	
        $this->NumSesionEst  = "sesionesEstudiante.txt";
        $this->NumSesionEst2 = "sesionesEstudiante2.txt";
        $this->NumSesionEst3 = "sesionesEstudiante3.txt";
        $this->NumSesionFun  = "sesionesFuncionario.txt";
        $this->TimeSesionEst = "tiempoSesionesEstudiante.txt";
        $this->TimeSesionFun = "tiempoSesionesFuncionario.txt";
        
    }
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Funcion:     verificarExpiracion                                                                            //
    // Descripción:                                                                                                //
    // Parametros de entrada:                                                                                      //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    /*******************Eliminar expiracion********************/
    function verificarExpiracion($tipo_ses)
    {
        
        //crea la conexion a la base de datos
        $this->acceso_MY = $this->conectarDB($this->configuracion, "logueo");
        
        if (strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S') {
            /*realiza la conexion a la bd del servidor de Estudiantes*/
            $this->acceso_Est = $this->conectarDB($this->configuracion, "sesiones_estudiante");
        }
        //nuevo                
        if (strtoupper($this->configuracion['activar_redireccion_estudiante2']) == 'S') {
            /*realiza la conexion a la bd del servidor de Estudiantes*/
            $this->acceso_Est2 = $this->conectarDB($this->configuracion, "sesiones_estudiante2");
        }
        if (strtoupper($this->configuracion['activar_redireccion_estudiante3']) == 'S') {
            /*realiza la conexion a la bd del servidor de Estudiantes*/
            $this->acceso_Est3 = $this->conectarDB($this->configuracion, "sesiones_estudiante3");
        }
        
        if (strtoupper($this->configuracion['activar_redireccion_funcionario']) == 'S') {
            /*realiza la conexion a la bd del servidor de Funcionarios*/
            $this->acceso_Fun = $this->conectarDB($this->configuracion, "sesiones_funcionario");
        }
        
        $tiempo_actual = time();
        /*verifica que tablas manejan las sesiones en el servidor de aplicacion*/
        $consulta_bd   = $this->cadena_sql("busca_db", "");
        $registro      = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $consulta_bd, "busqueda");
        //var_dump($registro);echo "<br>";exit;
        if (is_array($registro)) {
            $nro_db = count($registro);
            
            for ($key = 0; $key < $nro_db; $key++) { //echo "<br>".$registro[$key]['DB'];//exit;
                
                $variables = array(
                    'DB' => $registro[$key]['DB'],
                    'TABLA' => $registro[$key]['TABLA'],
                    'EXPIRA' => $tiempo_actual
                );
                
                $consulta_ses = $this->cadena_sql("sesiones", $variables);
                
                if ($tipo_ses == 'estudiante' && (strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S' || strtoupper($this->configuracion['activar_redireccion_estudiante2']) == 'S' || strtoupper($this->configuracion['activar_redireccion_estudiante3']) == 'S')) {
                    $reg_sesiones = $this->ejecutarSQL($this->configuracion, $this->acceso_Est, $consulta_ses, "busqueda");
                    if (strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S') {
                        $reg_sesiones = $this->ejecutarSQL($this->configuracion, $this->acceso_Est, $consulta_ses, "busqueda");
                    }
                    if (strtoupper($this->configuracion['activar_redireccion_estudiante2']) == 'S') {
                        $reg_sesiones2 = $this->ejecutarSQL($this->configuracion, $this->acceso_Est2, $consulta_ses, "busqueda");
                    }
                    if (strtoupper($this->configuracion['activar_redireccion_estudiante3']) == 'S') {
                        $reg_sesiones3 = $this->ejecutarSQL($this->configuracion, $this->acceso_Est3, $consulta_ses, "busqueda");
                    }
                } elseif ($tipo_ses == 'funcionario' && strtoupper($this->configuracion['activar_redireccion_funcionario']) == 'S') {
                    $reg_sesiones = $this->ejecutarSQL($this->configuracion, $this->acceso_Fun, $consulta_ses, "busqueda");
                } else {
                    $reg_sesiones = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $consulta_ses, "busqueda");
                }
                
                if (isset($reg_sesiones) && is_array($reg_sesiones)) {
                    $nro_ses = count($reg_sesiones);
                    for ($aux = 0; $aux < $nro_ses; $aux++) {
                        $variables['SESION'] = $reg_sesiones[$aux]['SESION'];
                        $consulta_exp        = $this->cadena_sql("expirar", $variables);
                        if ($tipo_ses == 'estudiante' && strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S') {
                            $this->ejecutarSQL($this->configuracion, $this->acceso_Est, $consulta_exp, "");
                        } elseif ($tipo_ses == 'funcionario' && strtoupper($this->configuracion['activar_redireccion_funcionario']) == 'S') {
                            $this->ejecutarSQL($this->configuracion, $this->acceso_Fun, $consulta_exp, "");
                        } else {
                            $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $consulta_exp, "");
                        }
                        unset($consulta_exp);
                    }
                }
                if (isset($reg_sesiones2) && is_array($reg_sesiones2)) {
                    $nro_ses = count($reg_sesiones2);
                    for ($aux = 0; $aux < $nro_ses; $aux++) {
                        $variables['SESION'] = $reg_sesiones2[$aux]['SESION'];
                        $consulta_exp        = $this->cadena_sql("expirar", $variables);
                        $this->ejecutarSQL($this->configuracion, $this->acceso_Est2, $consulta_exp, "");
                        unset($consulta_exp);
                    }
                }
                if (isset($reg_sesiones3) && is_array($reg_sesiones3)) {
                    $nro_ses = count($reg_sesiones3);
                    for ($aux = 0; $aux < $nro_ses; $aux++) {
                        $variables['SESION'] = $reg_sesiones3[$aux]['SESION'];
                        $consulta_exp        = $this->cadena_sql("expirar", $variables);
                        $this->ejecutarSQL($this->configuracion, $this->acceso_Est3, $consulta_exp, "");
                        unset($consulta_exp);
                    }
                }
                unset($consulta_ses);
                unset($variables);
            }
        }
        
        /*actualiza el archivo de sesiones */
        //var_dump($this->CarpetaSesion);exit;
        if ($tipo_ses == 'estudiante') {
            if (strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S' || strtoupper($this->configuracion['activar_redireccion_estudiante2']) == 'S' || strtoupper($this->configuracion['activar_redireccion_estudiante3']) == 'S') {
                $consulta_act = $this->cadena_sql("sesiones_activas", $tipo_ses);
                //echo $consulta_act;exit;
                if (strtoupper($this->configuracion['activar_redireccion_estudiante']) == 'S') {
                    $reg_activas = $this->ejecutarSQL($this->configuracion, $this->acceso_Est, $consulta_act, "busqueda");
                    $visitas     = $reg_activas[0][0];
                    $fd          = fopen($this->CarpetaSesion . $this->NumSesionEst, "w");
                    fwrite($fd, $visitas);
                    fclose($fd);
                }
                if (strtoupper($this->configuracion['activar_redireccion_estudiante2']) == 'S') {
                    $reg_activas2 = $this->ejecutarSQL($this->configuracion, $this->acceso_Est2, $consulta_act, "busqueda");
                    $visitas2     = $reg_activas2[0][0];
                    $fd2          = fopen($this->CarpetaSesion . $this->NumSesionEst2, "w");
                    fwrite($fd2, $visitas2);
                    fclose($fd2);
                }
                if (strtoupper($this->configuracion['activar_redireccion_estudiante3']) == 'S') {
                    $reg_activas3 = $this->ejecutarSQL($this->configuracion, $this->acceso_Est3, $consulta_act, "busqueda");
                    $visitas3     = $reg_activas3[0][0];
                    $fd3          = fopen($this->CarpetaSesion . $this->NumSesionEst3, "w");
                    fwrite($fd3, $visitas3);
                    fclose($fd3);
                }
            } else {
                $consulta_act = $this->cadena_sql("sesiones_activas", $tipo_ses);
                $reg_activas  = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $consulta_act, "busqueda");
                $visitas      = $reg_activas[0][0];
                $fd           = fopen($this->CarpetaSesion . $this->NumSesionEst, "w");
                fwrite($fd, $visitas);
                fclose($fd);
            }
        } elseif ($tipo_ses == 'funcionario') {
            
            if (strtoupper($this->configuracion['activar_redireccion_funcionario']) == 'S') {
                $consulta_act = $this->cadena_sql("sesiones_activas", $tipo_ses);
                $reg_activas  = $this->ejecutarSQL($this->configuracion, $this->acceso_Fun, $consulta_act, "busqueda");
                $visitas      = $reg_activas[0][0];
                
                $fd = fopen($this->CarpetaSesion . $this->NumSesionFun, "w");
                fwrite($fd, $visitas);
                fclose($fd);
            } else {
                $consulta_act = $this->cadena_sql("sesiones_activas", $tipo_ses);
                $reg_activas  = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $consulta_act, "busqueda");
                $visitas      = $reg_activas[0][0];
                
                $fd = fopen($this->CarpetaSesion . $this->NumSesionFun, "w");
                fwrite($fd, $visitas);
                fclose($fd);
            }
            
        }
        unset($visitas);
        /*termina la actualizacion*/
        
    }
    
    function cadena_sql($tipo, $variable)
    {
        
        switch ($tipo) {
            
            case "busca_db":
                
                $cadena_sql = "SELECT ";
                $cadena_sql .= "nombre DB, ";
                $cadena_sql .= "tabla_sesion TABLA  ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["prefijo"] . "bd ";
                
                break;
            
            case "sesiones":
                
                $cadena_sql = "SELECT DISTINCT ";
                $cadena_sql .= "DISTINCT(id_sesion) SESION ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $variable['DB'] . "." . $variable['TABLA'] . " ";
                $cadena_sql .= "WHERE variable='expiracion' ";
                $cadena_sql .= "AND valor<='" . $variable['EXPIRA'] . "' ";
                
                break;
            
            case "expirar":
                
                $cadena_sql = "DELETE ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $variable['DB'] . "." . $variable['TABLA'] . " ";
                $cadena_sql .= "WHERE id_sesion='" . $variable['SESION'] . "'";
                
                break;
            
            case "sesiones_activas":
                
                $cadena_sql = "SELECT ";
                $cadena_sql .= "count(distinct id_sesion) NUM_SES ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["prefijo"] . "valor_sesion ";
                if ($variable) {
                    $cadena_sql .= "WHERE variable='tipo_sesion' ";
                    $cadena_sql .= "AND valor='" . $variable . "'";
                }
                break;
            
            default:
                $cadena_sql = "";
                break;
        }
        
        return $cadena_sql;
        
    }
    
    
}

// fin de la clase
?>
