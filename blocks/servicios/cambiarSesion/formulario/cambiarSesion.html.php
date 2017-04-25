<?php
$_REQUEST['ruta'] = str_replace('\\_', '_', $_REQUEST['ruta']);
//var_dump($_REQUEST);die;
$_REQUEST['ruta'] = urlencode($_REQUEST['ruta']);
$ruta = '/appserv/usuarios/conn_usuario_ruta.php?u='.$_REQUEST['tipo'].'&ruta='.$_REQUEST['ruta'];
//var_dump($_SESSION);die;

echo '<script>window.location="'.$ruta.'";</script>';
exit();

$directorio = $this -> miConfigurador -> getVariableConfiguracion("host");
$directorio .= $this -> miConfigurador -> getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this -> miConfigurador -> getVariableConfiguracion("enlace");

$enlaceHome = 'pagina=home';
$enlaceHome = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar_url($enlaceHome, $directorio);

?>
