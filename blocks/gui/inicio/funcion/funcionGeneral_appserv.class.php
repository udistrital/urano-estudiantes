<?
/*--------------------------------------------------------------------------------------------------------------------------
@ Derechos de Autor: Vea el archivo LICENCIA.txt que viene con la distribucion
---------------------------------------------------------------------------------------------------------------------------*/
/*
THEORY REMINDER
Abstract classes are classes that contain one or more abstract methods. 
An abstract method is a method that is declared, but contains no implementation. 
Abstract classes may not be instantiated, and require subclasses to provide
implementations for the abstract methods. 

Such methods should preserve the declared list of  parameters

why not declare an abstract class as an interface?
By using abstract classes, you can inherit the implementation of other (non-abstract) methods. 
You can't do that with interfaces - an interface cannot provide any method implementations.
*/

/*if(!isset($GLOBALS["autorizado"]))
{
include("../index.php");
exit;		
}*/


namespace gui\inicio\funcion;
//use gui\inicio\funcion\funcionGeneral;

class funcionGeneral_appserv
{
    
    
    public function ejecutarSQL($configuracion, $conexion, $cadena_sql, $tipo)
    {
        $resultado = $conexion->ejecutarAcceso($cadena_sql, $tipo);
        return $resultado;
        
    }
    public function salidaSQL($configuracion, $conexion, $variable, $tam)
    {
        $resultado = $conexion->variableSQL($variable, $tam);
        return $resultado;
        
    }
    
    public function totalRegistros($configuracion, $conexion)
    {
        return $conexion->obtener_conteo_db();
    }
    
    public function totalAfectados($configuracion, $conexion) //cuenta los registros de una busqueda
    {
        return $conexion->obtener_afectadas();
    }
    
    public function datosGenerales($configuracion, $conexion, $tipo, $variable = "")
    {
        
        include_once("datosGenerales.class.php");
        $this->datoBasico = new datosGenerales();
        
        return $this->datoBasico->rescatarDatoGeneral($configuracion, $tipo, $variable, $conexion);
    }
    
    public function rescatarValorSesion($configuracion, $conexion, $tipo)
    {
        include_once("sesion.class.php");
        $this->sesion = new sesiones($configuracion);
        $this->sesion->especificar_enlace($conexion->obtener_enlace());
        $this->esta_sesion = $this->sesion->numero_sesion();
        
        //Rescatar el valor de la variable usuario de la sesion
        $this->registro = $this->sesion->rescatar_valor_sesion($configuracion, $tipo);
        if ($this->registro) {
            
            return $this->registro[0][0];
        } else {
            
            return FALSE;
        }
        
    }
    
    public function conectarDB($configuracion, $nombre = "")
    {
        include_once("dbConexion_appserv.class.php");
        
        $this->conexion = new dbConexion_appserv($configuracion);
        
        $recurso = $this->conexion->recursodb($configuracion, $nombre);
        
        $recurso->conectar_db();
        
        return $recurso;
        
    }
    
    public function revisarFormulario()
    {
        //Evitar que se ingrese codigo HTML y PHP en las peticiones
        foreach ($_REQUEST as $clave => $valor) {
            $_REQUEST[$clave] = strip_tags($valor);
        }
    }
    
    
    public function cargarArchivoServidor($configuracion, $parametro, $tipoArchivo)
    {
        @set_time_limit(0);
        //Cargar el documento en el servidor
        include_once("subir_archivo.class.php");
        
        $subir                   = new subir_archivo();
        $subir->directorio_carga = $parametro["directorio"];
        $subir->nombre_campo     = $parametro["nombreCampo"];
        $subir->tipos_permitidos = $tipoArchivo;
        
        
        
        
        // Maximo tamanno permitido
        //$subir->tamanno_maximo=5000000;
        
        $subir->especial = "[[:space:]]|[\"\*\\\'\%\$\&\@\<\>]";
        
        $subir->unico    = TRUE;
        $subir->permisos = 0777;
        
        $resultado = $subir->cargar();
        
        if ($resultado == false) {
            $this->errorCarga["noCarga"] = "El archivo no pudo ser cargado en el servidor.";
            return false;
        } else {
            //guardar datos de la carga
            if (isset($subir->log["nombre_archivo"][0])) {
                $cargar["nombreArchivo"] = $subir->log["nombre_archivo"][0];
            }
            
            if (isset($subir->log["mi_archivo"][0])) {
                $cargar["nombreInterno"] = $subir->log["mi_archivo"][0];
            }
            //Obtener direccion IP
            $fuentes_ip = array(
                "HTTP_X_FORWARDED_FOR",
                "HTTP_X_FORWARDED",
                "HTTP_FORWARDED_FOR",
                "HTTP_FORWARDED",
                "HTTP_X_COMING_FROM",
                "HTTP_COMING_FROM",
                "REMOTE_ADDR"
            );
            
            foreach ($fuentes_ip as $fuentes_ip) {
                // Si la fuente existe captura la IP
                if (isset($_SERVER[$fuentes_ip])) {
                    $proxy_ip = $_SERVER[$fuentes_ip];
                    break;
                }
            }
            
            $cargar["ip"] = (isset($proxy_ip)) ? $proxy_ip : @getenv("REMOTE_ADDR");
            return $cargar;
        }
        
        
    }
    
}


?>
