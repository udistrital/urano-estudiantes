cargarEventosPanel();

function cargarEventosPanel(){
	$(".lateral-icon").click(function (e){
		var id = $(this).attr("data-open-id");
		var actual = $("#"+id).css("display");
		cerrarEventosPanel();
		abrirEventoPanelPorId(id, actual);
		var handler = function() {
			cerrarEventosPanel();
			$("body").unbind( "click", this );
		};
		$("body").bind( "click", handler );
		return false;
	});
}

function cerrarEventosPanel(){
	$(".panel-lateral").css("display","none");
}

function abrirEventoPanelPorId(id, actual){
	if(actual=="block"){
		$("#"+id).css("display","none");
	} else {
		$("#"+id).css("display","block");
	}
}
