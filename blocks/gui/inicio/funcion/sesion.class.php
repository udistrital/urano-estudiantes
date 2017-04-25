<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                                                       #
#    Paulo Cesar Coronado 2004 - 2005                                      #
#    paulo_cesar@berosa.com                                                #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/

/***************************************************************************

sesion.class.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 6 de Marzo de 2006

****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		http://acreditacion.udistrital.edu.co
* @description  Clase para el manejo de sesiones
*****************************************************************************
*Atributos
*
*@access private
*@param  $conexion_id
*		Identificador del enlace a la base de datos 
*@param  $sesion_id
*		Identificador de la sesion
*@param  $expiracion
*		Tiempo que dura la sesion en segundos
*@param  $usuario
*		Nombre de usuario
*@param  $nivel
*		Nivel de acceso que tiene el usuario.
*@param  $registro_sesion
*		Registro que almacena el valor de las variables de sesión.		
*******************************************************************************
*/
/******************************************************************************
 *Métodos
 *
 *@access public
 *
 * @name sesiones
 *		 Constructor. Define los valores por defecto 
 * @name sesion
 *		 Determina la existencia de una sesión, actualizando su expiración en caso de existir
 * @name abrir_sesion
 *		 Método auxiliar de sesión que realiza la búsqueda de la sesión.
 * @name caracteres_validos
 *		 Verifica que los caracteres de la sesión_id sean alfanuméricos
 * @name especificar_sesion
 *		 Especifica el sesion_id
 * @name especificar_enlace
 *		 Especifica el enlace a la db que ha de usarse.
 * @name especificar_usuario
 *		 Define el usuario propietario de la sesión
 * @name especificar_nivel
 *		 Define el nivel de acceso
 * @name especificar_expiracion
 *		 Define el tiempo de expiración de las nuevas sesiones que se crean.
 * @name usuario_ip
 *		 Rescata la dirección IP del usuario, clave para crear sesion_id
 * @name crear_sesion
 *		 Inserta un registro de sesión en el sistema
 * @name rescatar_valor_sesión
 *		 Obtiene el valor de una variable de sesión determinada
 * @name guardar_valor_sesion
 *		 Guarda el valor de una variable de sesión determinada
 * @name borrar_valor_sesion
 *		 Borra la entrada de una variable de sesión determinada
 * @name borrar_sesion_expirada
 *		 Remueve de la db las sesiones que esten expiradas
 * @name encriptar_identificador
 *		 Para futuro uso. Encripta el sesion_id para evitar creaciones de sesión no autorizadas
 * @name terminar_sesion
 *		 Borra una sesión específica
 *
 *******************************************************************************************
 */

/*
 * @USAGE
 * El uso de la clase es bastante simple. Inicia con la creación de un objeto de tipo sesiones:
 * 		$nueva_sesion=new sesiones();
 * El constructor define por defecto:
 * 		$this->usuario='ANONIMOUS'
 * 		$this->nivel=0 (Sin ningún privilegio de acceso)
 * 		$this->expiracion=1800 (30 minutos)
 *Si se quieren sobreescribir estos valores se debe invocar los métodos:
 *		$nueva_sesion->especificar_usuario('nuevo_usuario')
 *		$nueva_sesion->especificar_nivel(nuevo_nivel) (Donde nuevo_nivel es un entero)
 *		$nueva_sesion->especificar_expiracion(tiempo_segundos)
 *Luego de estos pasos se verifica la existencia de una sesión válida para la máquina del cliente
 *		$nueva_sesion->sesion($configuracion)
 *Este método retorna TRUE en el caso de que exista una sesión válida, al mismo tiempo que
 *actualiza el valor de la fecha de creación de la sesión. Si por el contrario no encuentra una sesión
 *devuelve FALSE.
 *Para crear una nueva sesión se debe llamar al método:
 *		$nueva_sesion->crear_sesion
 * 
 * 
 */
namespace gui\inicio\funcion;

class sesiones
{
    /** Aggregations: */
    
    /** Compositions: */
    
    /*** Attributes: ***/
    
    /**
     * Miembros privados de la clase 
     * @access private
     */
    var $conexion_id;
    var $sesion_id;
    var $expiracion;
    var $usuario;
    var $nivel;
    var $aplicacion;
    var $registro_sesion;
    
    
    
    /**
     * @name sesiones
     * constructor
     */
    function sesiones($aplicativo)
    {
        $this->usuario    = 'ANONIMO';
        $this->expiracion = $aplicativo['expiracion'];
        $this->nivel      = 0;
        $this->aplicativo = $aplicativo;
    } //Fin del método session
    
    /**
     * @name sesiones Verifica la existencia de una sesion válida en la máquina del cliente
     * @param string nombre_db 
     * @return void
     * @access public
     */
    function sesion($configuracion, $id_sesion)
    {
        
        //Verificar si en el cliente existe registrada una cookie que tenga el identificador de la sesion
        
        if (!isset($id_sesion)) {
            $this->sesion_id = $this->numero_sesion();
        } else {
            $this->sesion_id = $id_sesion;
        }
        
        //echo $this->sesion_id;exit;      
        //Se eliminan las sesiones expiradas
        $borrar    = $this->borrar_sesion_expirada($configuracion);
        $resultado = $this->abrir_sesion($configuracion, $this->sesion_id);
        
        /* Detecta errores*/
        if ($resultado == FALSE) {
            return FALSE;
        } else {
            // Si no hubo errores se puede actualizar los valores
            // Update, porque se tiene un identificador
            /*Crear una nueva cookie*/
            setcookie("aplicativo", $this->sesion_id, (time() + $this->expiracion), "/");
            $cadena_sql = "UPDATE ";
            $cadena_sql .= $configuracion["prefijo"] . "valor_sesion ";
            $cadena_sql .= "SET ";
            $cadena_sql .= "valor=" . (time() + $this->expiracion) . " ";
            $cadena_sql .= "WHERE ";
            $cadena_sql .= "id_sesion='" . ($this->sesion_id) . "' ";
            $cadena_sql .= "AND ";
            $cadena_sql .= "variable='expiracion'";
            //echo $cadena_sql;
            $db_sel = new dbms($this->aplicativo);
            $db_sel->especificar_enlace($this->conexion_id);
            $resultado = $db_sel->ejecutar_acceso_db($cadena_sql);
            return $resultado;
        }
    } //Fin del método sesion
    
    
    /**
     *@METHOD numero_sesion
     *
     * Rescata el número de sesion correspondiente a la máquina
     * @PARAM sesion
     * @return valor
     * @access public
     */
    
    function numero_sesion()
    {
        
        if (isset($_COOKIE["aplicativo"])) {
            $this->id_sesion = $_COOKIE["aplicativo"];
        } else {
            if (isset($_REQUEST["aplicativo"])) {
                $this->id_sesion = $_REQUEST["aplicativo"];
            } else {
                $this->id_sesion = '0';
            }
            
        }
        return $this->id_sesion;
        
    }
    /*Fin de la función numero_sesion*/
    
    
    /**
     *@METHOD abrir_sesion
     *
     * Busca la sesión en la base de datos
     * @PARAM sesion
     * @return valor
     * @access public
     */
    
    function abrir_sesion($configuracion, $sesion)
    {
        // Primero se verifica la longitud del parámetro
        
        if (strlen($sesion) != 32) {
            return FALSE;
        } else {
            // Verifica la validez del id de sesion
            
            if ($this->caracteres_validos($sesion) != strlen($sesion)) {
                return FALSE;
            }
            
            $this->especificar_sesion($sesion);
            // Busca una sesión que coincida con el id del computador y el nivel de acceso de la página
            $this->el_resultado = $this->rescatar_valor_sesion($configuracion, 'acceso' . $this->nivel, $this->sesion_id);
            if ($this->el_resultado) {
                if ($this->el_resultado[0][0] >= $this->nivel) {
                    $this->el_resultado = $this->rescatar_valor_sesion($configuracion, 'expiracion', $this->sesion_id);
                    if ($this->el_resultado[0][0] > time()) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
            
        }
        
        
        
    } //Final del método abrir_sesion
    
    
    /**
     *@METHOD caracteres_validos
     *
     * Verifica que los caracteres en el identificador de sesión sean válidos
     * @PARAM cadena
     * @return valor
     * @access public//Realizar un barrido por la matriz de resultados para comprobar que se tiene los privilegios para la pagina
     $this->validacion=0;
     for($this->i=0;$this->i<$this->count;$this->i++)
     {
     */
    
    
    function caracteres_validos($cadena)
    {
        // Retorna el número de elementos que coinciden con la lista de caracteres
        return strspn($cadena, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
    }
    
    /**
     *@METHOD especificar_sesion
     *
     * Obtiene los valores guardados para una determinad	return FALSE;a sesión de usuario
     * @return valor
     * @access public
     */
    
    function especificar_sesion($sesion_id)
    {
        
        $this->sesion_id       = $sesion_id;
        $_COOKIE["aplicativo"] = $sesion_id;
    } // end of member function especificar_sesion
    
    
    /**
     *@METHOD especificar_enlace
     *
     * @param conexion_id
     * @return valor
     * @access public
     */
    
    function especificar_enlace($conexion_id)
    {
        
        if (is_resource($conexion_id)) {
            $this->conexion_id = $conexion_id;
        }
        
    } //Fin de la función especificar_enlace
    
    
    /**
     *@METHOD especificar_expiracion
     * @return valor
     * @access public
     */
    
    function especificar_expiracion($expiracion)
    {
        
        $this->expiracion = $expiracion;
        
    } //Fin del mètodo especificar_expiracion
    
    
    /**
     *@METHOD especificar_nivel
     *
     * @param nivel
     * @access public
     */
    
    function especificar_nivel($nivel)
    {
        $this->nivel = $nivel;
        
    } //Fin de la función especificar_enlace
    
    
    /**
     *@METHOD especificar_usuario
     * @return valor
     * @access public
     */
    
    function especificar_usuario($usuario)
    {
        
        $this->usuario = $usuario;
        
    } //Fin del mètodo especificar_usuario
    
    /**
     *@METHOD especificar_aplicacion
     * @return valor
     * @access public
     */
    
    function especificar_aplicacion($aplica)
    {
        
        $this->aplicacion = $aplica;
        
    } //Fin del mètodo especificar_usuario        
    
    /**
     *@METHOD crear_sesion
     *
     * Crea una nueva sesión en la base de datos.
     * @PARAM usuario_aplicativo
     * @PARAM nivel_acceso
     * @PARAM expiracion
     * @PARAM conexion_id
     * @return boolean
     * @access public
     */
    
    function crear_sesion($configuracion, $usuario_aplicativo, $nivel_acceso)
    {
        
        
        if ($usuario_aplicativo != "") {
            $this->usuario = $usuario_aplicativo;
        }
        
        if ($nivel_acceso != "") {
            $this->nivel = $nivel_acceso;
        }
        
        //Identificador de sesion
        $this->fecha     = explode(" ", microtime());
        $this->sesion_id = md5($this->fecha[1] . substr($this->fecha[0], 2) . rand());
        //secho $this->sesion_id;
        if (strlen($this->sesion_id) != 32) {
            return FALSE;
        } else {
            // Verifica la validez del id de sesion
            if ($this->caracteres_validos($this->sesion_id) != strlen($this->sesion_id)) {
                return FALSE;
            }
            /**Borra todas las sesiones que existan con el id del computador*/
            
            if (isset($_COOKIE["aplicativo"])) {
                $this->la_sesion = $_COOKIE["aplicativo"];
                $this->terminar_sesion($configuracion, $this->la_sesion);
                
            }
            /*Actualizar la cookie*/
            $tiempo = time() + $this->expiracion;
            //echo $tiempo;exit;
            //echo "La expiracion de las cookies es: ".$this->expiracion."<br>";
            setcookie("aplicativo", $this->sesion_id, (time() + $this->expiracion), "/");
            /*Borrar cookies anteriores
            setcookie("aplicativo","",time()+60*60*2,"/");*/
            //$cadena_sql = "INSERT INTO ".$configuracion["prefijo"]."valor_sesion ( id_usuario , id_sesion , expiracion , nivel_acceso ) VALUES ('".$this->usuario."', '".$this->sesion_id."',".(time()+$this->expiracion).",".$this->nivel.")";
            //Insertar usuario
            $this->resultado = $this->guardar_valor_sesion($configuracion, 'usuario', $this->usuario, $this->sesion_id);
            if ($this->resultado) {
                //Insertar expiracion
                $this->resultado = $this->guardar_valor_sesion($configuracion, 'expiracion', (time() + $this->expiracion), $this->sesion_id);
                if ($this->resultado) {
                    //Insertar nivel de acceso
                    $this->resultado = $this->guardar_valor_sesion($configuracion, 'acceso', $this->nivel, $this->sesion_id);
                    if ($this->resultado) { //Insertar alpicacion
                        $this->resultado = $this->guardar_valor_sesion($configuracion, 'aplicacion', $this->aplicacion, $this->sesion_id);
                        if ($this->resultado) {
                            return $this->sesion_id;
                        }
                    }
                }
                
            }
            
            return FALSE;
            
        }
        
        
    } //Fin del método crear_sesion
    
    
    
    
    /**
     *@METHOD rescatar_valor_sesion
     * @PARAM variable
     * @PARAM usuario_aplicativo ??
     * @return boolean
     * @access public
     */
    
    
    function rescatar_valor_sesion($configuracion, $variable)
    {
        
        
        if (isset($_COOKIE["aplicativo"])) {
            $this->sesion_id = $_COOKIE["aplicativo"];
        } else {
            return FALSE;
        }
        
        // Busca la sesión
        $this->cadena_sql = "SELECT ";
        $this->cadena_sql .= "valor ";
        $this->cadena_sql .= "FROM ";
        $this->cadena_sql .= $configuracion["prefijo"] . "valor_sesion ";
        $this->cadena_sql .= "WHERE ";
        $this->cadena_sql .= "id_sesion ='" . ($this->sesion_id) . "' ";
        $this->cadena_sql .= "AND ";
        $this->cadena_sql .= "variable='" . $variable . "' ";
        //echo "consulta ".$this->cadena_sql;
        $this->db_sel = new dbms($this->aplicativo);
        $this->db_sel->especificar_enlace($this->conexion_id);
        $this->resultado = $this->db_sel->registro_db($this->cadena_sql, 0);
        $this->count     = $this->db_sel->obtener_conteo_db();
        
        if ($this->count > 0) {
            $this->registro_sesion = $this->db_sel->obtener_registro_db();
            unset($this->db_sel);
            return $this->registro_sesion;
        } else {
            unset($this->db_sel);
            return FALSE;
        }
        
        
    } //Fin del método rescatar_valor_sesion	return FALSE;
    
    
    /**
     *@METHOD rescatar_id_sesion
     * @PARAM variable
     * @PARAM usuario_aplicativo ??
     * @return boolean
     * @access public
     * rescata sesiones de un usuario que quedaron anteriormente, para ser borradas
     */
    
    function rescatar_id_sesion($configuracion, $variable)
    {
        
        // Busca la sesión
        $this->cadena_sql = "SELECT DISTINCT ";
        $this->cadena_sql .= "id_sesion ";
        $this->cadena_sql .= "FROM ";
        $this->cadena_sql .= $configuracion["prefijo"] . "valor_sesion ";
        $this->cadena_sql .= "WHERE ";
        $this->cadena_sql .= "variable='" . $variable['var'] . "' ";
        $this->cadena_sql .= "AND ";
        $this->cadena_sql .= "valor ='" . $variable['vl'] . "' ";
        //echo $this->cadena_sql;
        $this->db_sel = new dbms($this->aplicativo);
        $this->db_sel->especificar_enlace($this->conexion_id);
        $this->resultado = $this->db_sel->registro_db($this->cadena_sql, 0);
        $this->count     = $this->db_sel->obtener_conteo_db();
        
        if ($this->count > 0) {
            $this->registro_sesion = $this->db_sel->obtener_registro_db();
            unset($this->db_sel);
            return $this->registro_sesion;
        } else {
            unset($this->db_sel);
            return FALSE;
        }
        
        
    } //Fin del método rescatar_valor_sesion	return FALSE;
    
    /**
     *@METHOD guardar_valor_sesion
     * @PARAM variable
     * @PARAM valor
     * @return boolean
     * @access public
     */
    
    function guardar_valor_sesion($configuracion, $variable, $valor, $sesion)
    {
        $num_args = func_num_args();
        if ($num_args == 0) {
            return FALSE;
        } else {
            
            if (strlen($sesion) != 32) {
                if (isset($_COOKIE["aplicativo"])) {
                    $this->sesion_id = $_COOKIE["aplicativo"];
                } else {
                    return FALSE;
                }
            } else {
                $this->sesion_id = $sesion;
                
            }
            $cadena_sql = "SELECT * FROM " . $configuracion["prefijo"] . "valor_sesion WHERE id_sesion = '" . ($this->sesion_id) . "'  AND variable='" . $variable . "'";
            $db_sel     = new dbms($this->aplicativo);
            $db_sel->especificar_enlace($this->conexion_id);
            $resultado = $db_sel->registro_db($cadena_sql, 1);
            /*echo $resultado; importante para depurar*/
            $count     = $db_sel->obtener_conteo_db();
            
            if ($count > 0) {
                //Si la variable ya existe en la sesión entonces UPDATE	
                $cadena_sql = "UPDATE " . $configuracion["prefijo"] . "valor_sesion SET valor='" . $valor . "' WHERE id_sesion='" . ($this->sesion_id) . "' AND variable='" . $variable . "'";
                /*echo $cadena_sql;*/
                $resultado  = $db_sel->ejecutar_acceso_db($cadena_sql);
                unset($db_sel);
                
                return $resultado;
                
                
            } else {
                //Si la variable no existe entonces INSERT		
                $cadena_sql = "INSERT INTO " . $configuracion["prefijo"] . "valor_sesion ( id_sesion,variable,valor) VALUES ('" . $this->sesion_id . "', '" . $variable . "','" . $valor . "' )";
                /*echo $cadena_sql;*/
                $resultado  = $db_sel->ejecutar_acceso_db($cadena_sql);
                unset($db_sel);
                return $resultado;
            }
            
            
        }
    } //Fin del método guardar_valor_sesion
    
    
    
    /**
     *@METHOD borrar_valor_sesion
     * @PARAM variable
     * @PARAM valor
     * @return boolean
     * @access public
     */
    
    function borrar_valor_sesion($configuracion, $variable, $sesion)
    {
        $num_args = func_num_args();
        
        if ($num_args == 0) {
            return FALSE;
        } else {
            
            
            if (strlen($sesion) != 32) {
                if (isset($_COOKIE["aplicativo"])) {
                    $this->sesion_id = $_COOKIE["aplicativo"];
                } else {
                    return FALSE;
                }
            } else {
                $this->sesion_id = $sesion;
                
            }
            if ($variable != 'TODOS') {
                $cadena_sql = "DELETE FROM " . $configuracion["prefijo"] . "valor_sesion WHERE id_sesion='" . $this->sesion_id . "' AND variable='" . $variable . "'";
            } else {
                $cadena_sql = "DELETE FROM " . $configuracion["prefijo"] . "valor_sesion WHERE id_sesion='" . $this->sesion_id . "'";
                
            }
            $db_sel = new dbms($this->aplicativo);
            $db_sel->especificar_enlace($this->conexion_id);
            $resultado = $db_sel->ejecutar_acceso_db($cadena_sql);
            //echo $resultado; importante para depurar
            return $resultado;
        }
        
    } //Fin del método borrar_valor_sesion
    
    /**
     * @name borrar_sesion_expirada
     * @return void
     * @access public
     */
    function borrar_sesion_expirada($configuracion)
    {
        
        /*Comprobamos que exista*/
        
        //SELECT para borrar valores de sesión expirados y garantiza integridad referencial	
        $this->tiempo = time();
        $cadena_sql   = "SELECT ";
        $cadena_sql .= "id_sesion, ";
        $cadena_sql .= "valor ";
        $cadena_sql .= "FROM ";
        $cadena_sql .= $configuracion["prefijo"] . "valor_sesion ";
        $cadena_sql .= "WHERE ";
        $cadena_sql .= "variable='expiracion'";
        //echo $cadena_sql;
        $db_sel = new dbms($this->aplicativo);
        $db_sel->especificar_enlace($this->conexion_id);
        $db_sel->registro_db($cadena_sql, 0);
        $conteo = $db_sel->obtener_conteo_db();
        if ($conteo > 0) {
            $registro = $db_sel->obtener_registro_db();
            for ($i = 0; $i < $conteo; $i++) {
                if (($registro[$i][1] + (24 * 60 * 60)) < time()) {
                    $this->sql = "DELETE ";
                    $this->sql .= "FROM ";
                    $this->sql .= $configuracion["prefijo"] . "valor_sesion ";
                    $this->sql .= "WHERE ";
                    $this->sql .= "id_sesion='" . $registro[$i][0] . "'";
                    //echo $this->sql."<br>";
                    if (!$db_sel->ejecutar_acceso_db($this->sql)) {
                        //Si hay un error al borrar el registro retorna FALSE
                        return FALSE;
                    }
                }
            }
        }
        
        
        
    } //Fin del método borrar_sesion_expirada
    
    
    
    /**
     * 
     * @name terminar_sesion
     * @return boolean
     * @access public
     */
    
    function terminar_sesion($configuracion, $sesion)
    {
        if (strlen($sesion) != 32) {
            return FALSE;
        } else {
            $this->esta_sesion = $sesion;
        }
        
        $cadena_sql = "DELETE FROM " . $configuracion["prefijo"] . "valor_sesion WHERE id_sesion = '" . ($this->esta_sesion) . "'";
        $db_sel     = new dbms($this->aplicativo);
        $db_sel->especificar_enlace($this->conexion_id);
        $resultado = $db_sel->ejecutar_acceso_db($cadena_sql);
        //Borrar las cookies del equipo remoto
        @setcookie("aplicativo", "", $this->expiracion, "/");
        unset($db_sel);
        return $resultado;
        
        
    } //Fin del método terminar_sesion
    
    
    function borrar_sesion($configuracion, $sesion)
    {
        if (strlen($sesion) != 32) {
            return FALSE;
        } else {
            $this->esta_sesion = $sesion;
        }
        
        $cadena_sql = "DELETE FROM " . $configuracion["prefijo"] . "valor_sesion WHERE id_sesion = '" . ($this->esta_sesion) . "'";
        $db_sel     = new dbms($this->aplicativo);
        $db_sel->especificar_enlace($this->conexion_id);
        $resultado = $db_sel->ejecutar_acceso_db($cadena_sql);
        unset($db_sel);
        return $resultado;
        
        
    } //Fin del método terminar_sesion
    
    
    function especificar_clave($clave_aplicativo)
    {
        $this->clave_aplicativo = $clave_aplicativo;
    }
    
    
    
    
    
    
} //Fin de la clase sesion

?>
