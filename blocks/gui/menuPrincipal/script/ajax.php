<?php

// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";

// Variables
//Se genera enlace para realizar procesamiento ajax con la opcion actualizarNotificaciones
$cadenaACodificar = "procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=actualizarNotificaciones";
$cadenaACodificar .= "&usuario=" . $_REQUEST['usuario'];
$cadenaACodificar .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
// URL definitiva
$urlFinal = $url . $cadena;

?>

<script type='text/javascript'>

$(document).ready(function() {
	var cantidad;

	$(".icon-notifi").each(function(){
	    $(this).click(function(){
	    	$("#contenedor1").hide();//no recomendado borrar
			$("#notificationContainer").fadeToggle(300);
			$("#notification_count").fadeOut("slow");
			cantidad = $("#notification_count").text();
			return false;
		});
	});
	// Document Click
	$(document).click(function() {
		if (cantidad != '') {
			$.ajax({
	            url: "<?php echo $urlFinal; ?>",
	            success: function(data){
	        		$("#notificationContainer").hide();
	        		$("#notification_count").remove();
	        		$("#div_contador").remove();
	            }
	   		});
		}
	});
	
	// Popup Click
	$("#notificationContainer").click(function() {
		return false
	});
});

</script>