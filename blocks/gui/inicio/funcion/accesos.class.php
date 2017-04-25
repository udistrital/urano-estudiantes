<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
 * @name          accesos.class.php 
 * @author        Jairo Lavado
 * @revision      Última revisión 22 de Diciembre de 2011
 ****************************************************************************
 * @subpackage   
 * @package	clase
 * @copyright    
 * @version      0.2
 * @author       Jairo Lavado
 * @link		
 * @description  
 *
 ******************************************************************************/

namespace gui\inicio\funcion;
include_once("funcionGeneral_appserv.class.php");
use gui\inicio\funcion\funcionGeneral_appserv as funcionGeneral_appserv;


class acceso extends funcionGeneral_appserv
{
    private $configuracion;
    private $acceso_OCI;
    
    public function __construct($configuracion)
    {
        $this->configuracion = $configuracion;
        
    }
    
    /**
     * @name registrar
     * @param type $user
     * @param type $apli 
     * @descripcion Registra en el log, los accesos de los usuarios a las diferentes aplicaciones.
     */
    function registrar($user, $apli)
    {
        $this->acceso_OCI = $this->conectarDB($this->configuracion, "default");
        $fec_consul       = $this->cadena_sql('fecha', '');
        $rs_fecha         = $this->ejecutarSQL($this->configuracion, $this->acceso_OCI, $fec_consul, "busqueda");
        
        $registro = array(
            "usuario" => $user,
            "fecha" => $rs_fecha[0][0],
            "hora" => $rs_fecha[0][1],
            "host" => $_SERVER['REMOTE_ADDR'],
            "aplicacion" => $apli
        );
        
        $reg_consul = $this->cadena_sql('registro', $registro);
        $rs_reg     = $this->ejecutarSQL($this->configuracion, $this->acceso_OCI, $reg_consul, "");
    }
    
    function cadena_sql($tipo, $variable)
    {
        
        switch ($tipo) {
            
            case "fecha":
                $cadena_sql = "SELECT ";
                $cadena_sql .= "SYSDATE FEC, ";
                $cadena_sql .= "to_char(SYSDATE,'hh24:mi:ss') HORA ";
                $cadena_sql .= "FROM dual";
                break;
            
            
            case "registro":
                
                $cadena_sql = "INSERT ";
                $cadena_sql .= "INTO ";
                $cadena_sql .= "geconexlog ";
                $cadena_sql .= "(";
                $cadena_sql .= "CNX_USUARIO, ";
                $cadena_sql .= "CNX_MAQUINA, ";
                $cadena_sql .= "CNX_FECHA, ";
                $cadena_sql .= "CNX_HORA,";
                $cadena_sql .= "CNX_APP";
                $cadena_sql .= ") ";
                $cadena_sql .= "VALUES ";
                $cadena_sql .= "(";
                $cadena_sql .= "'" . $variable['usuario'] . "', ";
                $cadena_sql .= "'" . $variable['host'] . "', ";
                $cadena_sql .= "'" . $variable['fecha'] . "', ";
                $cadena_sql .= "'" . $variable['hora'] . "', ";
                $cadena_sql .= "'" . $variable['aplicacion'] . "' ";
                $cadena_sql .= ")";
                
                break;
            
            default:
                $cadena_sql = "";
                break;
                
        }
        
        
        
        return $cadena_sql;
        
    }
    
} //Fin de la clase db_admin

?>
