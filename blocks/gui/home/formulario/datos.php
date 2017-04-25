<?php

$cadenaSql = $this -> miSql -> getCadenaSql('pruebaDatos', $usuario);
$datos = $esteRecursoDB -> ejecutarAcceso($cadenaSql, "busqueda");


// $profesion = "Msc. Teleinformática";
$info_nombre = ($atributos['nombre_usuario']) ? $atributos['nombre_usuario'] : 'Usuario inv&aacute;lido.';
$info_usuario = ($datos[0]['cta_nombre_usuario']) ? $datos[0]['cta_nombre_usuario'] : 'Usuario desconocido';
$info_correo = ($datos[0]['usu_correo']) ? $datos[0]['usu_correo'] : 'Ning&uacute;n correo registrado';
$info_correo_inst = ($datos[0]['usu_correo_institucional']) ? $datos[0]['usu_correo_institucional'] : 'Ning&uacute;n correo registrado';
$info_num_documento = ($datos[0]['usu_nro_doc_actual']) ? $datos[0]['usu_nro_doc_actual'] : 'Indeterminado';
$info_tipo_doc = ($datos[0]['usu_tipo_doc_actual']) ? $datos[0]['usu_tipo_doc_actual'] : 'Indeterminado';
$info_direccion = ($datos[0]['usu_direccion']) ? $datos[0]['usu_direccion'] : 'No hay registrada ninguna direcci&oacute;n';
$info_telefono = ($datos[0]['usu_telefono']) ? $datos[0]['usu_telefono'] : 'No hay registrado ning&uacute;n tel&eacute;fono';
$info_telefono_celular = ($datos[0]['usu_celular']) ? $datos[0]['usu_celular'] : 'No hay registrado ning&uacute;n tel&eacute;fono celular';
$info_fecha_registro = ($datos[0]['usu_fecha_registro']) ? $datos[0]['usu_fecha_registro'] : 'Fecha desconocida';
$info_estado = ($datos[0]['cta_estado']) ? 'Activo' : 'Inactivo';


function imagenBase64($rutaImagen) {
	$imagen = file_get_contents($rutaImagen);
	$imagenEncriptada = base64_encode($imagen);
	$url = "data:image/png;base64," . $imagenEncriptada;
	return $url;
}
?>

<div class="col-lg-2">
</div>
<div class="row">
	<div class="col-xs-12 col-sm-10 col-lg-8">
		<table id="table-datos">
			<caption><h4 class="nombre"><?php echo $info_nombre; ?></h4></caption>
			<tbody>
				<tr>
					<td class="usuario" rowspan="9">
						<img id="foto-perfil" src="<?php echo imagenBase64($url_foto_perfil); ?>" alt="Perfil" style="width: 170px; height: 170px;" class="foto-user img-responsive img-rounded profilepicture" />
					</td>
				</tr>
				<tr>
					<td class="tipo-info">Código:</td>
					<td class="info-user"><?php echo $info_usuario; ?></td>
				</tr>
				<tr>
					<td class="tipo-info">Documento:</td>
					<td class="info-user"><?php echo $info_tipo_doc . " " . $info_num_documento; ?></td>
				</tr>	
				<tr>
					<td class="tipo-info">Tel&eacute;fono:</td>
					<td class="info-user"><?php echo $info_telefono; ?></td>
				</tr>
							<tr>
					<td class="tipo-info">Celular:</td>
					<td class="info-user"><?php echo $info_telefono_celular; ?></td>
				</tr>
								<tr>
					<td class="tipo-info">Direcci&oacute;n:</td>
					<td class="info-user"><?php echo $info_direccion; ?></td>
				</tr>
												<tr>
					<td class="tipo-info">Correo Institucional:</td>
					<td class="info-user"><?php echo $info_correo_inst; ?></td>
				</tr>	
				<tr>
					<td class="tipo-info">Correo Personal:</td>
					<td class="info-user"><?php echo $info_correo; ?></td>
				</tr>	
				<tr>
					<td class="tipo-info">Estado:</td>
					<td class="info-user"><?php echo $info_estado; ?></td>
				</tr>
			</tbody>
		</table>
		<br>
	</div>
</div>