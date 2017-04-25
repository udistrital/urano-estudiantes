<?php
$rutaUrlBloque = $this->miConfigurador->getVariableConfiguracion ( "host" ).$this->miConfigurador->getVariableConfiguracion ( "site" ).'/blocks/gui/menuPrincipal/';

$enlace = "action=index.php";
$enlace .= "&bloqueNombre=inicio";
$enlace .= "&bloqueGrupo=gui";
$enlace .= "&procesarAjax=true";
$enlace .= "&funcion=verificarSesion";
$enlace .= "&usuario=" . $_REQUEST['usuario'];
$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$enlace = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $enlace, $directorio );

$enlaceLogout = 'pagina=index';
$enlaceLogout .= '&bloque=inicio';
$enlaceLogout .= '&bloqueGrupo=gui';
$enlaceLogout .= '&opcion=logout';
$enlaceLogout .= '&mostrarMensaje=sesionExpirada';
$enlaceLogout = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar_url($enlaceLogout, $directorio);

?>
//var i = 0;

/*
function cambiarImagen() {
   
    var imagenes = [
      "<?php echo $rutaUrlBloque.'images/header.png'?>",
      "<?php echo $rutaUrlBloque.'images/header2.png'?>",
      "<?php echo $rutaUrlBloque.'images/header3.png'?>"
    ];
    if (i < imagenes.length - 1) {
      i++;
    } else {
      i = 0;
    }
    $('#imagenfondo').fadeOut('slow', function() {

      $('#imagenfondo').css({
        'background-image': 'url("' + imagenes[i] + '")'
      });

      $('#imagenfondo').fadeIn('slow');
    });   
}
*/
//Se comenta para que no hayan slides
//setInterval(cambiarImagen, 30000);

$(".nav.navbar-nav a").each(function(){
    $(this).click(function(){
		$.ajax({
		  url: "<?php echo $enlace;?>"
		}).done(function(a) {
		  if(!a){
		  	window.location = "<?php echo $enlaceLogout;?>";
		  }
		});
	});
});

// function ajustarLogos(){
	// var alturaMenu = $(".navbar.navbar-default.navbar-static-top").innerHeight()-5;;
	// $(".left-icon").height(alturaMenu);
// }
// ajustarLogos();
// $(window).resize(ajustarLogos);

//para los popup
$(".juu-link").each(function(){	
    $(this).bind( "click",function(linkevento){
    	var contenedor = $(this).attr('data-container');
		$("#"+contenedor).fadeToggle(300);
		$("#notificationContainer").hide();//no recomendado borrar
		var link = this;
		$(document).click(function(docevent) {
			$("#"+contenedor).hide();
			//$(link).unbind(linkevento);
			$(this).unbind(docevent);
		});
		return false;
	});
});

function minimizarMenu(){
	$("#menuPrincipal").css("height","53px");
	$(".contenedor-menu").css("height","50px");
}

function maximizarMenu(){
	$("#menuPrincipal").css("height","auto");
	$(".contenedor-menu").css("height","auto");
}

setTimeout(function(){
	$("#menuPrincipal").animate({height: "53px"}, "slow");
	$(".contenedor-menu").animate({height: "50px"}, "slow");
},3000);

$(".navbar").on("mouseover",function(){
	maximizarMenu();
});

$(".navbar").on("mouseleave",function(){
	minimizarMenu();
});



