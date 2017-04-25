<?php

function fecha_es($fecha) {
	$meses = array (
			'01' => 'Enero',
			'02' => 'Febrero',
			'03' => 'Marzo',
			'04' => 'Abril',
			'05' => 'Mayo',
			'06' => 'Junio',
			'07' => 'Julio',
			'08' => 'Agosto',
			'09' => 'Septiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Diciembre'
	);
	return $meses [$fecha ['mes']] . " " . $fecha ['dia'] . ", " . $fecha ['anio'] . " - " . $fecha ['hora'];
}

function imagenBase64($rutaImagen){
	$imagen=file_get_contents($rutaImagen);
	$imagenEncriptada=base64_encode($imagen);
	$url="data:image/png;base64,".$imagenEncriptada;
	return $url;
}

$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "buscarNoticias", $usuario );
$matrizNoticias = $esteRecurso->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );

$esteCampo = 'noticias';
$atributos ['id'] = $esteCampo;
$atributos ['estiloEnLinea'] = 'width: 100%; height: 100%;';
echo $this->miFormulario->division ( "inicio", $atributos );
unset ( $atributos );

$esteCampo = "noti";
$atributos ['id'] = $esteCampo;
$atributos ['estilo'] = "demo2 demof";
echo $this->miFormulario->division ( "inicio", $atributos );
unset ( $atributos );

echo "<ul id='lista-noticias'>";

// var_dump($matrizNoticias);

if ($matrizNoticias) {
		
	foreach ( $matrizNoticias as $noticia ) {

		echo "<li>";

		$pordefecto = $rutaUrlBloque . "images/silueta.gif";

		$imagen = "<img id='foto-noti' ";

		if ($noticia ['noti_img_usr_enlace']) {
			$imagen .= "src='" . imagenBase64($rutaUrlBloque . "images/" . trim ( $noticia ['noti_img_usr_enlace'] )) . "'";
		} else {
			$imagen .= "src='" . imagenBase64($pordefecto) . "'";
		}

		$imagen .= " alt='" . $noticia ['noti_usr_remi'] . "' title='" . $noticia ['noti_usr_remi'] . "'>";

		echo $imagen;

		$atributos ['id'] = 'enlacetitulo';
		$atributos ['enlace'] = "#";
		$atributos ['enlaceTitulo'] = "Prueba";
		$atributos ['enlaceTexto'] = $noticia ['noti_nombre'];
		echo $this->miFormulario->enlace ( $atributos );

		echo "<p id='texto'>";

		$descrip = trim ( $noticia ['noti_descripcion'] );

		if ($noticia ['noti_enlace']) {
			$descrip = str_replace ( "[", "<a id='enlaceinterno' href='" . trim ( $noticia ['noti_enlace'] ) . "'>", $descrip );
		} else {
			$descrip = str_replace ( "[", "<a id='enlaceinterno' href=''>", $descrip );
		}
		$descrip = str_replace ( "]", "</a>", $descrip );

		echo $descrip;

		echo "</p>";

		echo "<p id='fecha'>";

		$auxfecha = trim ( $noticia ['noti_fradicacion'] );

		$auxfecha = explode ( " ", $auxfecha );

		$auxfecha2 = $auxfecha [0];

		$auxfecha2 = explode ( "-", $auxfecha2 );

		$f ['anio'] = $auxfecha2 [0];
		$f ['mes'] = $auxfecha2 [1];
		$f ['dia'] = $auxfecha2 [2];
		$f ['hora'] = $auxfecha [1];

		echo fecha_es ( $f );

		echo "</p>";

		echo "</li>";
	}
}

echo "</ul>";

echo $this->miFormulario->division ( "fin" );


?>