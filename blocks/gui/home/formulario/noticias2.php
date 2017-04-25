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
	return $meses [$fecha ['mes']] . " " . $fecha ['dia'] . ", " . $fecha ['anio'];
}

$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "buscarNoticias", $usuario );
$matrizNoticias = $esteRecursoFuncionarios->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );

$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "buscarNoticiasOracle", $usuario );
$matrizNoticiasOracle = $esteRecursoOracle->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );

$matrizNoticias = (is_array($matrizNoticias)) ? $matrizNoticias : array();
$matrizNoticiasOracle = (is_array($matrizNoticiasOracle)) ? $matrizNoticiasOracle : array();

$noticias = array_merge($matrizNoticias, $matrizNoticiasOracle);

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<span class="glyphicon glyphicon-list-alt icon-titulo"></span>
		<titu>Noticias</titu>
	</div>
	<div class="panel-body">
		<?php 
		if ($noticias) {
		?>
		<ul class="demo1">
			<?php
				foreach ( $noticias as $noticia ) {
					
					$noticia ['nombre'] = (isset($noticia ['nombre'])) ? $noticia ['nombre'] : $noticia ['NOMBRE'];
					$noticia ['descripcion'] = (isset($noticia ['descripcion'])) ? $noticia ['descripcion'] : $noticia ['DESCRIPCION'];
					$noticia ['enlace'] = (isset($noticia ['enlace'])) ? $noticia ['enlace'] : $noticia ['ENLACE'];
					$noticia ['tipo'] = (isset($noticia ['tipo'])) ? $noticia ['tipo'] : $noticia ['TIPO'];
					$noticia ['anio'] = (isset($noticia ['anio'])) ? $noticia ['anio'] : $noticia ['ANIO'];
					$noticia ['periodo'] = (isset($noticia ['periodo'])) ? $noticia ['periodo'] : $noticia ['PERIODO'];
					$noticia ['fecha_radicacion'] = (isset($noticia ['fecha_radicacion'])) ? $noticia ['fecha_radicacion'] : $noticia ['FECHA_RADICACION'];
					$noticia ['remitente'] = (isset($noticia ['remitente'])) ? $noticia ['remitente'] : $noticia ['REMITENTE'];
					$noticia ['imagen'] = (isset($noticia ['imagen'])) ? $noticia ['imagen'] : $noticia ['IMAGEN'];
					
					$pordefecto = $rutaUrlFuncionarios . "images/silueta.gif";
					$imagen = "<img id='foto-noti' ";
					if ($noticia ['imagen'] && $noticia ['imagen']!='N/A') {
						$imagen .= "src='" . $rutaUrlFuncionarios . "images/" . trim ( $noticia ['imagen'] ) . "'";
					} else {
						$imagen .= "src='" . $pordefecto . "'";
					}
					$imagen .= " alt='" . $noticia ['usr_remitente'] . "' title='" . $noticia ['usr_remitente'] . "'/>";
					
					$atributos ['id'] = 'enlacetitulo';
					if($noticia ['enlace']!=null){
						$atributos ['enlace'] = $noticia ['enlace'];
					}else{
						$atributos ['enlace'] = "#";
					}
					$atributos ['enlaceTitulo'] = "Prueba";
					$atributos ['enlaceTexto'] = $noticia ['nombre'];
					
					$descrip = trim ( $noticia ['descripcion'] );
					if ($noticia ['enlace'] && $noticia ['enlace']!='N/A') {
						$descrip = str_replace ( "[", "<a id='enlaceinterno' href='" . trim ( $noticia ['enlace'] ) . "' target='_blank'>", $descrip.' [ver más...]' );
					} else {
						$descrip = str_replace ( "[", "<a id='enlaceinterno' href=''>", $descrip );
					}
					$descrip = str_replace ( "]", "</a>", $descrip );
					
					$fecha = trim ( $noticia ['fecha_radicacion'] );
					$fecha = explode ( " ", $fecha );
					$aux = $fecha [0];
					$aux = explode ( "-", $aux );
					$f ['anio'] = $aux [0];
					$f ['mes'] = $aux [1];
					$f ['dia'] = $aux [2];
					
					?>
					<li class="news-item">
						<table>
							<td class="td-izq">	
								<?php echo $imagen; ?>
							</td>
							<td class="td-der">
								<?php echo $this->miFormulario->enlace ( $atributos ); ?>
								<p id='texto'>
									<?php echo $descrip; ?>
								</p>
								<p id='fecha'>
									<?php echo fecha_es ( $f ); ?>
								</p>
							</td>
						</table>
					</li>
				<?php
				}
			?>
		</ul>
		<?php 
		} else {
			$imagen = "<img ";
			$imagen .= "src='" . $rutaUrlBloque . "images/newspaper1.jpg" . "'";
			$imagen .= 'class="img-responsive" style="margin: 0 auto;">';
			
			?>
			<div style="width: 100%; height: 100%; text-align: center;">
				<?php echo $imagen;?>
			</div>
			<div class="alert alert-info" style="text-align: center;">
  				<strong>No hay noticias activas!</strong>
			</div>
			<?php 
		}
		?>
	</div>
	<div class="panel-footer"></div>
</div>