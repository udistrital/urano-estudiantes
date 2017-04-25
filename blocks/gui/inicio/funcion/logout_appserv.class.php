<?PHP
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/***************************************************************************
 * @name          verifica.class.php 
 * @author        Jairo Lavado
 * @revision      Última revisión 22 de Diciembre de 2011
 * @author		  Jorge Useche @juusechec
 * @revision	  6 de Marzo de 2017
 ****************************************************************************
 * @subpackage   
 * @package	clase
 * @copyright    
 * @version      0.2
 * @author        Jairo Lavado
 * @link		
 * @description  
 *
 ******************************************************************************/
namespace gui\inicio\funcion;
require_once ('funcionGeneral_appserv.class.php');
require_once ('config.class.php');
require_once ('encriptar.class.php');
use gui\inicio\funcion\funcionGeneral_appserv as funcionGeneral_appserv;
use gui\inicio\funcion\config as config;
use gui\inicio\funcion\encriptar as encriptar;

class logout_appserv extends funcionGeneral_appserv
{
    private $configuracion;
    private $acceso_OCI;
    private $acceso_MY;
    private $acceso_logueo;
    private $cripto;
    private $usser;
    private $tipoUser;
    private $verificador;
    private $varIndex;
    
    public function __construct()
    {
        $esta_configuracion  = new config();
        $this->configuracion = $esta_configuracion->variable("../");
        
        $this->cripto = new encriptar();
        if (isset($_REQUEST['index'])) {
            $this->cripto->decodificar_url($_REQUEST['index'], $this->configuracion);
        }
        /*RESCATA DATOS DE USUARIO*/
        session_name($this->configuracion["usuarios_sesion"]);
        session_start();
        if (isset($_REQUEST['usuario'])) {
            $this->usser = $_REQUEST['usuario'];
        } else {
            $this->usser = $_SESSION['usuario_login'];
        }
        
        $this->acceso_MY = $this->conectarDB($this->configuracion, "logueo");
        //$this->acceso_OCI = $this->conectarDB($this->configuracion,"default");  
        
        /*variables para envio al index*/
        $this->varIndex['verificador'] = date("YmdH");
        $this->varIndex['enlace']      = $this->configuracion['enlace'];
        $this->varNombres['acceso']    = $this->cripto->codificar_variable('acceso', $this->varIndex['verificador']);
        
    }
    
    
    function action()
    //verifica el tipo de usuario
    {
        $cod_consul     = $this->cadena_sql('busca_us', $this->usser);
        //$registro=  $this->ejecutarSQL($this->configuracion,  $this->acceso_OCI, $cod_consul,"busqueda");
        $registro       = $this->ejecutarSQL($this->configuracion, $this->acceso_MY, $cod_consul, "busqueda");
        $this->tipoUser = $registro[0]['TIP_US'];
        
        $this->cerrar_sesion();
        ob_start();
        unset($_SESSION['Condorizado']);
        unset($_SESSION['usuario_login']);
        unset($_SESSION['usuario_password']);
        unset($_SESSION['usuario_nivel']);
        unset($_SESSION['carrera']);
        unset($_SESSION['codigo']);
        unset($_SESSION['tipo']);
        unset($_SESSION["A"]);
        unset($_SESSION["G"]);
        unset($_SESSION["C"]);
        unset($_SESSION['ccfun']);
        unset($_SESSION["fun_cod"]);
        unset($_SESSION['fac']);
        unset($_SESSION['u1']);
        unset($_SESSION['c2']);
        unset($_SESSION['b3']);
        session_destroy();
        $dir      = $this->configuracion['host_logueo'] . $this->configuracion['site'];
        //$variable = $this->varNombres['acceso'] . '=1';
        $variable = "";
	    if (isset($_REQUEST['error_login'])) {
            $variable = "msj=" . $_REQUEST['error_login'];
        } elseif (isset($_REQUEST['msgIndex'])) {
            $variable = "msj=" . $_REQUEST['msgIndex'];
        }
        //$variable=$this->cripto->codificar_url($variable,$this->varIndex);
        $this->direccionar($variable);
        exit;
        
    }
    
    
    function direccionar($var)
    {
        $url = '/appserv/index.html';
        echo "
                   <script type='text/javascript'>
                        window.location='$url?$var';
                   </script>";
        exit;
    } // fin funcion direccionar    
    
    
    /**
     *   
     * @param type $acceso
     * @param type $aplicacion
     * @return type 
     * @name cerrar_sesion
     * @desc Funcion que verifica datos y cierra sesiones.
     */
    function cerrar_sesion()
    //inicia la busqueda de sesiones antiguas guardadas
    {
        
        
        $user['var'] = 'id_usuario';
        $user['vl']  = $this->usser;
        
        /*actualiza al contador, registra la sesion y redireciona si es el caso*/
        
        if ($this->tipoUser == '51' || $this->tipoUser == '52') {
            if (strtoupper($this->configuracion['recibir_sesiones_estudiantexfuncionario']) == 'S') { //verifica que no se esten usando el minimo de sesiones de funcionarios, y asignarlas a estudiantes*/
                $variable     = array(
                    'DB' => 'dbms',
                    'TABLA' => $this->configuracion["prefijo"] . "valor_sesion ",
                    'USER' => $this->usser
                );
                //  var_dump($variable);
                $consulta_ses = $this->cadena_sql('rescatar_id_sesion', $variable);
                
                $conexion_db = $this->acceso_MY;
                
                //var_dump($registroSesion);
                if (isset($registroSesion)) {
                    $cod_consul = $this->cadena_sql('rescatar_tipo_sesion', $registroSesion[0]['id_sesion']);
                    $reg_ses    = $this->ejecutarSQL($this->configuracion, $conexion_db, $cod_consul, "busqueda");
                    
                    //var_dump($reg_ses);exit;
                    
                    $cerrado = $this->Db_sesiones($this->acceso_MY);
					
                } else { //si no existen datos redirecciona
                    $variable = "msj=113-1";
                    //$variable = $this->cripto->codificar_url($variable, $this->varIndex);
                    $this->direccionar($variable);
                    exit;
                }
                
            } else {
                //cierra la sesion cuando la sesion es de estudiante
                $cerrado = $this->Db_sesiones($this->acceso_MY);
                
            }
            /*fin grabar archivo*/
        } else {
            
            $cerrado = $this->Db_sesiones($this->acceso_MY);
            
            if ($cerrado == 0) { //si no existen datos redirecciona
                $variable = "msj=113-2";
                //$variable = $this->cripto->codificar_url($variable, $this->varIndex);
                $this->direccionar($variable);
                
                exit;
            }
            
            /*fin grabar archivo*/
        }
          
    } //fin funcion cerrar_sesion  
    
    function Db_sesiones($conexion)
    {
        $borrados   = 0;
        $cod_consul = $this->cadena_sql('busca_db', '');
        $registro   = $this->ejecutarSQL($this->configuracion, $conexion, $cod_consul, "busqueda");
        //var_dump($registro);
        
        if (is_array($registro)) {
            
            $i = 0;
            
            while (isset($registro[$i][0])) {
                
                $variable = array(
                    'DB' => $registro[$i][0],
                    'TABLA' => $registro[$i][1],
                    'USER' => $this->usser
                );
                
                $consulta_ses   = $this->cadena_sql('rescatar_id_sesion', $variable);
                $registroSesion = $this->ejecutarSQL($this->configuracion, $conexion, $consulta_ses, "busqueda");
                //var_dump($registroSesion);
                $j              = 0;
                while (isset($registroSesion[$j][0])) {
                    $variable['SES'] = $registroSesion[$j][0];
                    
                    $consulta_borra_ses = $this->cadena_sql('borrar_sesion', $variable);
                    $resultado          = $this->ejecutarSQL($this->configuracion, $conexion, $consulta_borra_ses, "");
                    if ($resultado) {
                        $borrados = $borrados + 1;
                    }
                    //$resultado=$acceso_db->ejecutarAcceso($cadena_sql,"");
                    
                    $j++;
                }
                
                $i++;
            }
            
        } else { //si no existen datos redirecciona
            $variable = "msgIndex=113-3";
            //$variable = $this->cripto->codificar_url($variable, $this->varIndex);
            $this->direccionar($variable);
            
            exit;
        }
        //exit;    
        return ($borrados);
    } //fin funcion Db_sesiones
     
    function expirar_sesiones()
    {
        $this->expira->verificarExpiracion('estudiante');
        $this->expira->verificarExpiracion('funcionario');
    }
    
    
    function cadena_sql($tipo, $variable)
    {
        
        switch ($tipo) {
            
            
            case "busca_us":
                
                $cadena_sql = "SELECT ";
                $cadena_sql .= "cla_codigo COD, ";
                $cadena_sql .= "cla_clave PWD, ";
                $cadena_sql .= "cla_tipo_usu TIP_US, ";
                $cadena_sql .= "cla_estado EST ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["sql_tabla1"] . " ";
                $cadena_sql .= "WHERE ";
                $cadena_sql .= "cla_codigo='" . $this->usser . "' ";
                $cadena_sql .= "ORDER BY EST";
                
                break;
            
            case "sesiones":
                
                $cadena_sql = "SELECT ";
                $cadena_sql .= "count(distinct id_sesion) NUM_SES ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["prefijo"] . "valor_sesion ";
                
                break;
            
            case "busca_db":
                
                $cadena_sql = "SELECT ";
                $cadena_sql .= "`nombre`, ";
                $cadena_sql .= "`tabla_sesion` ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["prefijo"] . "bd ";
                
                break;
            
            case "rescatar_id_sesion":
                
                $cadena_sql = "SELECT DISTINCT ";
                $cadena_sql .= "id_sesion ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $variable['DB'] . "." . $variable['TABLA'] . " ";
                $cadena_sql .= "WHERE ";
                $cadena_sql .= "variable='usuario' ";
                $cadena_sql .= "AND ";
                $cadena_sql .= "valor ='" . $variable['USER'] . "' ";
                
                break;
            
            case "rescatar_tipo_sesion":
                
                $cadena_sql = "SELECT DISTINCT ";
                $cadena_sql .= "valor tipo_ses ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $this->configuracion["prefijo"] . "valor_sesion ";
                $cadena_sql .= "WHERE ";
                $cadena_sql .= "variable='tipo_sesion' ";
                $cadena_sql .= "AND ";
                $cadena_sql .= "id_sesion='" . $variable . "' ";
                break;
            
            
            case "borrar_sesion":
                
                $cadena_sql = "DELETE  ";
                $cadena_sql .= "FROM ";
                $cadena_sql .= $variable['DB'] . "." . $variable['TABLA'] . " ";
                $cadena_sql .= "WHERE ";
                $cadena_sql .= "id_sesion='" . $variable['SES'] . "' ";
                
                break;
            
            case "guardar_valor_sesion":
                
                $cadena_sql = "INSERT INTO ";
                $cadena_sql .= $this->configuracion["prefijo"] . "valor_sesion (id_sesion,variable,valor) ";
                $cadena_sql .= "VALUES (";
                $cadena_sql .= "'" . $variable['ses'] . "', ";
                $cadena_sql .= "'" . $variable['vr'] . "', ";
                $cadena_sql .= "'" . $variable['vl'] . "' ";
                $cadena_sql .= ");";
                
                break;
            
            case "actualizar_valor_sesion":
                
                $cadena_sql = "UPDATE ";
                $cadena_sql .= $this->configuracion["prefijo"] . "valor_sesion ";
                $cadena_sql .= "SET ";
                $cadena_sql .= "valor='" . $variable['vl'] . "' ";
                $cadena_sql .= "WHERE ";
                $cadena_sql .= "id_sesion='" . $variable['ses'] . "' ";
                $cadena_sql .= "AND ";
                $cadena_sql .= "variable='" . $variable['vr'] . "' ";
                
                break;
            
            
            default:
                $cadena_sql = "";
                break;
        }
        
        return $cadena_sql;
        
    }
    
    
    
    
    
    
} // fin clase bloqueAdminUsuario

// @ Crear un objeto bloque especifico

$esteBloque = new logout_appserv();
$esteBloque->action();


?>

