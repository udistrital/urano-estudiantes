<?php
$rutaUrlBloque = $this->miConfigurador->getVariableConfiguracion ( 'host' ).$this->miConfigurador->getVariableConfiguracion ( 'site' ).'/blocks/gui/menuPrincipal/';

$esteBloque = $this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );

$enlace = "action=index.php";
$enlace .= "&bloqueNombre=menuPrincipal";
$enlace .= "&bloqueGrupo=gui";
$enlace .= "&procesarAjax=true";
$enlace .= "&funcion=consultarEnlacesUsuario";
$enlace .= "&usuario=".$_REQUEST['usuario'];
$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$enlace = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $enlace, $directorio );
//Se usa el plugin http://plugins.upbootstrap.com/bootstrap-ajax-typeahead/
?>

$('#searchservice').typeahead({
    ajax: { 
	    url: '<?php echo $enlace; ?>',
        triggerLength: 3,
        valueField: 'url'
	},
	onSelect: function (a){
        if(a){
        	//window.open(a.id, '_blank');
        	window.open(a.value, 'principal');
        }
  	},
  	display: 'name',
    val: 'url',
    //item: '<li><a href="{{value}}" target="principal"></a></li>',
});

$('#searchservice').click(function(){
	$('.typeahead.dropdown-menu>li.active').click();
});
