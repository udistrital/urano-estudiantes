<?php
$usuario = $_REQUEST ['usuario'];
$conexion = 'academica';
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

$cadenaSql = $this->sql->getCadenaSql ('carrera', $usuario );
$registroCarrera = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

$carrera = $registroCarrera[0][0];
$plan = '/appserv/plandeestudio/pe_' . $carrera . '.pdf';
if (! file_exists ( $plan )) {
	$plan = '/appserv/plandeestudio/sin_plan.pdf';
}
echo '<script>window.location="' . $plan . '";</script>';
exit ();

$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );

$enlaceHome = 'pagina=home';
$enlaceHome = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $enlaceHome, $directorio );

?>
