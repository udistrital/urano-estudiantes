<?php
if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
} else {
    $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
    $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
    $rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
    $rutaBloque.= $esteBloque['grupo'] . "/" . $esteBloque['nombre'];
    $directorio = $this->miConfigurador->getVariableConfiguracion("host");
    $directorio.= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
    $directorio.=$this->miConfigurador->getVariableConfiguracion("enlace");
    //$miSesion = Sesion::singleton();
    $nombreFormulario = $esteBloque["nombre"];
    include_once("core/crypto/Encriptador.class.php");
    $cripto = Encriptador::singleton();
    $directorio = $this->miConfigurador->getVariableConfiguracion("rutaUrlBloque") . "/imagen/";
    $miPaginaActual = $this->miConfigurador->getVariableConfiguracion("pagina");
    $conexion = "estructura";
    $tab = 1;
//---------------Inicio Formulario (<form>)--------------------------------
    $atributos["id"] = $nombreFormulario;
    $atributos["tipoFormulario"] = "multipart/form-data";
    $atributos["metodo"] = "POST";
    $atributos["nombreFormulario"] = $nombreFormulario;
    $atributos["tipoEtiqueta"] = 'inicio';
    $verificarFormulario = "1";
    echo $this->miFormulario->formulario($atributos);
    
    
    $atributos["id"] = "divNoEncontroEgresado";
    $atributos["estilo"] = "marcoBotones";
    //$atributos["estiloEnLinea"]="display:none"; 
    echo $this->miFormulario->division("inicio", $atributos);
    
    if ($_REQUEST['mensaje'] == 'confirma') {
        $tipo = 'success';
        $mensaje = "Datos actualizados correctamente";
        $boton = "continuar";
        $valorCodificado = "pagina=".$esteBloque['nombre'];
        $valorCodificado.="&opcion=continuar";
        $valorCodificado.="&bloque=" . $esteBloque["id_bloque"];
        $valorCodificado.="&bloqueGrupo=" . $esteBloque["grupo"];
        
        
    } else if($_REQUEST['mensaje'] == 'error') {
        $tipo = 'error';
        $mensaje = "No fue posible actualizar los datos, por favor intente de nuevo.\n Si este mensaje sigue apareciendo, contacte al administrador del sistema.";
        $boton = "continuar";
        $valorCodificado = "pagina=".$esteBloque['nombre'];
        $valorCodificado.="&opcion=continuar";
        $valorCodificado.="&bloque=" . $esteBloque["id_bloque"];
        $valorCodificado.="&bloqueGrupo=" . $esteBloque["grupo"];
       
    }else{
        $tipo = 'alert';
        $mensaje = "Oprimio el Enlace";
        $boton = "continuar";       
    }
    
    
    
    /**
     * IMPORTANTE: Este formulario esta utilizando jquery.
     * Por tanto en el archivo ready.php se delaran algunas funciones js
     * que lo complementan.
     */
    // Rescatar los datos de este bloque
    $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
    // ---------------- SECCION: Parametros Globales del Formulario ----------------------------------
    /**
     * Atributos que deben ser aplicados a todos los controles de este formulario.
     * Se utiliza un arreglo
     * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
     *
     * Si se utiliza esta tecnica es necesario realizar un mezcla entre este arreglo y el especifico en cada control:
     * $atributos= array_merge($atributos,$atributosGlobales);
     */
    $atributosGlobales ['campoSeguro'] = 'true';
    $_REQUEST['tiempo'] = time();
    // -------------------------------------------------------------------------------------------------
    // ---------------- SECCION: Parametros Generales del Formulario ----------------------------------
    $esteCampo = $esteBloque ['nombre'];
    $atributos ['id'] = $esteCampo;
    $atributos ['nombre'] = $esteCampo;
    // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
    $atributos ['tipoFormulario'] = 'multipart/form-data';
    // Si no se coloca, entonces toma el valor predeterminado 'POST'
    $atributos ['metodo'] = 'POST';
    // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
    $atributos ['action'] = 'index.php';
    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo);
    // Si no se coloca, entonces toma el valor predeterminado.
    $atributos ['estilo'] = '';
    $atributos ['marco'] = true;
    $tab = 1;
    // ---------------- FIN SECCION: de Parametros Generales del Formulario ----------------------------
    // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
    $atributos['tipoEtiqueta'] = 'inicio';
    echo $this->miFormulario->formulario($atributos);
    // ---------------- SECCION: Controles del Formulario -----------------------------------------------
    $esteCampo = 'mensaje';
    $atributos["id"] = $esteCampo; //Cambiar este nombre y el estilo si no se desea mostrar los mensajes animados
    $atributos["etiqueta"] = "";
    $atributos["estilo"] = "centrar";
    $atributos["tipo"] = $tipo;
    $atributos["mensaje"] = $mensaje;
    echo $this->miFormulario->cuadroMensaje($atributos);
    unset($atributos);
    // ------------------Division para los botones-------------------------
    $atributos ["id"] = "botones";
    $atributos ["estilo"] = "marcoBotones";
    echo $this->miFormulario->division("inicio", $atributos);
    // -----------------CONTROL: Boton ----------------------------------------------------------------
    $esteCampo = 'continuar';
    $atributos ["id"] = $esteCampo;
    $atributos ["tabIndex"] = $tab;
    $atributos ["tipo"] = 'boton';
    // submit: no se coloca si se desea un tipo button generico
    $atributos ['submit'] = true;
    $atributos ["estiloMarco"] = '';
    $atributos ["estiloBoton"] = 'jqueryui btn btn-primary';
    // verificar: true para verificar el formulario antes de pasarlo al servidor.
    $atributos ["verificar"] = '';
    $atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la funcion submit declarada en ready.js
    $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
    $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
    $tab ++;
    // Aplica atributos globales al control
    $atributos = array_merge($atributos, $atributosGlobales);
    echo $this->miFormulario->campoBoton($atributos);
    // -----------------FIN CONTROL: Boton -----------------------------------------------------------
    // ------------------Fin Division para los botones-------------------------
    echo $this->miFormulario->division("fin");
    // ------------------- SECCION: Paso de variables ------------------------------------------------
    /**
     * SARA permite que los nombres de los campos sean dinamicos.
     * Para ello utiliza la hora en que es creado el formulario para
     * codificar el nombre de cada campo. 
     */
    //$valorCodificado = "action=" . $esteBloque ["nombre"]; //Ir pagina Funcionalidad
    $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );//Frontera mostrar formulario
    $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
    $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
    $valorCodificado .= "&usuario=" . $_REQUEST['usuario'];
    $valorCodificado .= "&opcion=continuar";
    
    $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
    // Paso 2: codificar la cadena resultante
    $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);
    $atributos ["id"] = "formSaraData"; // No cambiar este nombre
    $atributos ["tipo"] = "hidden";
    $atributos ['estilo'] = '';
    $atributos ["obligatorio"] = false;
    $atributos ['marco'] = true;
    $atributos ["etiqueta"] = "";
    $atributos ["valor"] = $valorCodificado;
    echo $this->miFormulario->campoCuadroTexto($atributos);
    unset($atributos);
    // ----------------FIN SECCION: Paso de variables -------------------------------------------------
    // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
    // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
    // Se debe declarar el mismo atributo de marco con que se inicio el formulario.
    $atributos ['marco'] = true;
    $atributos ['tipoEtiqueta'] = 'fin';
    echo $this->miFormulario->formulario($atributos);
    return true;
}
?>