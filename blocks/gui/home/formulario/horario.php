<?php 

$esteCampo = 'horario';
$atributos ['id'] = $esteCampo;
$atributos ['estiloEnLinea'] = 'width: 100%; height: 90%;';
echo $this->miFormulario->division ( "inicio", $atributos );
unset ( $atributos );

?>
	<div id="mynew">
	
	</div>
<?php
echo $this->miFormulario->division ( "fin" );
?>