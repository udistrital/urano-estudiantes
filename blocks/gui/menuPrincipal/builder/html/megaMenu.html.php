<?php
/*
 *  Sintaxis recomendada para las plantillas PHP
 */
?>

<nav id="menuPrincipal" class="navbar" role="navigation">
	<!--navbar-fixed-top-->
	<div class="container">
		<div class="row">
			<div class="col-md-1 col-lg-1 hidden-xs hidden-sm">
				<div class="left-icon">
					<a title="Retorne a la página inicial" href="<?php echo $this -> atributos['enlace_home']; ?>" target="<?php echo $this -> atributos['target']; ?>">
						<img src="<?php echo $this -> atributos['url_logo']; ?>" />
					</a>
				</div>
				<!-- /.left-icon -->
			</div>
			<div class="col-md-10 col-lg-10">
				<!--http://jsfiddle.net/apougher/ydcMQ/-->
				<div class="navbar navbar-default navbar-static-top">
					<div class="contenedor-menu">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<!--<a class="navbar-brand" href="#">Inicio</a>-->
						</div>
						<div class="navbar-collapse collapse">
							<ul class="nav navbar-nav">
								<!--<li><a href="#">GLUD</a></li>-->
								<?php if (is_array($this -> atributos ['enlaces'])):?>
									<?php foreach($this -> atributos ['enlaces'] as $keyMenu => $menu): ?>
									<li class="dropdown menu-large">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this -> atributos['titulosMenu'][$keyMenu]; ?><b class="caret"></b></a>
										<ul class="dropdown-menu megamenu row">
											<?php
											$colXMenu = 4;
											//Número de columnas por menú
											//Para 4 columnas se pone la clase (col-sm-3)
											$numSubMenu = count($menu);
											$numGMenuXCol = $numSubMenu / $colXMenu;
											//Número de grupos menú por columna
											$parteEntera = intval($numGMenuXCol);
											$numGMenuIni = array();
											for ($i = 0; $i < $colXMenu; $i++) {
												$numGMenuIni[$i] = $parteEntera;
											}
											if ($numGMenuXCol != $parteEntera) {
												$desbordeGMenu = ($numGMenuXCol * $colXMenu) - ($parteEntera * $colXMenu);
												//var_dump($numGMenuXCol, $parteEntera, $desbordeGMenu);
												$numGMenuXCol = $parteEntera;
												for ($i = 0; $i < $desbordeGMenu; $i++) {
													$numGMenuIni[$i]++;
												}
											}
											//Arreglo que guarda los índices del foreach en que inicia el menú
											$inicioGMenu[0] = 1;
											$finalGMenu[0] = $inicioGMenu[0] + $numGMenuIni[0] - 1;
											$inicioGMenu[1] = $finalGMenu[0] + 1;
											$finalGMenu[1] = $inicioGMenu[1] + $numGMenuIni[1] - 1;
											$inicioGMenu[2] = $finalGMenu[1] + 1;
											$finalGMenu[2] = $inicioGMenu[2] + $numGMenuIni[2] - 1;
											$inicioGMenu[3] = $finalGMenu[2] + 1;
											$finalGMenu[3] = $inicioGMenu[3] + $numGMenuIni[3] - 1;
											//var_dump($numGMenuIni,$inicioGMenu,$finalGMenu);//die;
											//indice inicial del grupo menú
											$indexGrupoMenu = 1;
											?>
											<?php foreach($menu as $keyGrupoMenu => $grupoMenu):?>
												<?php if(in_array($indexGrupoMenu, $inicioGMenu)):?>
												<li class="col-sm-3">
												<ul>
												<?php else: ?>
												<li class="divider"></li>
												<?php endif; ?>
													<li class="dropdown-header">
														<?php echo $this -> atributos['titulosGrupoMenu'][$keyGrupoMenu]; ?>
													</li>
													<?php foreach($grupoMenu as $keyEnlace => $enlace): ?>
													<li>
														<a href="<?php echo $enlace['url']; ?>" target="<?php echo $this -> atributos['target']; ?>"><?php echo $enlace['etiqueta']; ?></a>
													</li>
													<?php endforeach; ?>
												<?php if(in_array($indexGrupoMenu, $finalGMenu)): ?>
												</ul>
												</li>
												<?php endif; ?>
												<?php
												$indexGrupoMenu++;
												?>
											<?php endforeach; ?>
										</ul>
									</li>
									<?php endforeach; ?>
								<?php endif;?>
								<li class="hidden-lg">
									<a class="notificationLink" data-toggle="modal" data-target="#notificacionesModal" title="Notificaciones">
										<img width="25px" height="25px" class="icon-notifi" src="<?php echo $this -> atributos['iconNotificacion']; ?>" title="Notificaciones" alt="Notificaciones">
										<span>Notificaciones</span>
									</a>									
								</li>
								<li class="hidden-lg">
									<a href="<?php echo $this -> atributos['enlace_mi_cuenta']; ?>" target="<?php echo $this -> atributos['target']; ?>" >
							          <img width="25px" height="25px" src="<?php echo $this -> atributos['url_icon_account']; ?>" title="Mi Cuenta" alt="Mi Cuenta">
							          <span>Mi Cuenta</span>
							        </a>							        				        
								</li>
								<li class="hidden-lg">
									<a href="<?php echo $this -> atributos['enlace_cerrar_sesion']; ?>">
							          <img width="25px" height="25px" src="<?php echo $this -> atributos['url_icon_logout']; ?>" title="Cerrar sesi&oacute;n" alt="Cerrar sesi&oacute;n">
							          <span>Cerrar Sesión</span>
							        </a>							        				        
								</li>								
							</ul>
							<!-- /.nav navbar-nav -->
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-1 col-lg-1 hidden-xs hidden-sm hidden-md">
				<div class="right-icon">
					<div id="notification_li">
						<a class="notificationLink" href="#" title="Notificaciones">
							<img class="icon-notifi" alt="Notificaciones" title="Notificaciones" src="<?php echo $this -> atributos['iconNotificacion']; ?>" width="25px" height="25px">
						</a>
						<div id="notificationContainer">
							<div id="notificationTitle">Notificaciones del sistema</div>
							<div id="notificationsBody" class="notifications">
								<div id="contenido-notificacion">
									<?php $notificaciones = json_decode($this->atributos ['notificaciones'], TRUE); //Se decodifica JSON y con el segundo parametro se garantiza generar arreglo asociativo?>
									<?php if (count($notificaciones) == 0) :?>
										<div class="alert alert-info">
											No hay ninguna notificación registrada para usted.
										</div>
									<?php else: ?>
										<table class="table-notificacion">
										<?php if (is_array($notificaciones)): ?>
											<?php foreach ($notificaciones AS $notificacion): ?>
												<?php if ($notificacion ['estado'] == 2): ?>
													<tr class="notificacion-tr ntf-vista">
												<?php elseif ($notificacion ['estado'] == 1):?>
													<tr class="notificacion-tr ntf-pen">
												<?php endif; ?>
													<td id="td-izq-ntf">
														<img id="foto-notifi" alt="<?php echo $notificacion ['imgalt']; ?>" title="<?php echo $notificacion ['imgalt']; ?>" src="<?php echo $notificacion ['imgsrc']; ?>">
													</td>
													<td id="td-der-ntf">
														<p id="p-enlace-titulo">
															<a id="enlacetitulonotifi" href="#" title="<?php echo $notificacion ['titulo']; ?>"><?php echo $notificacion ['titulo']; ?></a>
														</p>
														<p id='textonotifi'> <?php echo $notificacion ['descripcion']; ?> </p>
														<div>
															<p id='fechanotifi'> 
																<img class="img-clock" alt="clock" src="<?php echo $this -> atributos['url_clock']; ?>">
																<?php echo $notificacion['fecha']; ?> 
															</p>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										<?php endif;?>
										</table>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<!-- /#notificationContainer -->
					</div>
					<!-- /#notification_li -->
					<?php if ($this->atributos['notificacionesPendientes'] > 0): ?>
					<div id='div_contador'>				
						<span id="notification_count"><?php echo $this -> atributos['notificacionesPendientes']; ?></span>
					</div>
					<?php endif; ?>
					<div class="juu-popup">
						<a href="#" title="Mi Cuenta">
							<img class="juu-link img-circle" data-container="contenedor1" src="<?php echo $this -> atributos['foto_perfil_thumbnail']; ?>" width="25px" height="25px" title="Mi Cuenta" alt="Mi Cuenta">
						</a>
						<div id="contenedor1" class="juu-container">
							<div class="juu-title"><?php echo $this -> atributos['nombre_usuario']; ?></div>
							<div class="juu-body">
								<div class="fuente-negra botones-separados">
									<a href="<?php echo $this -> atributos['enlace_mi_cuenta']; ?>" target="<?php echo $this -> atributos['target']; ?>" class="btn btn-default" role="button">
							          <span>Mi Cuenta</span>
							        </a>
									<a href="<?php echo $this -> atributos['enlace_cerrar_sesion']; ?>" class="btn btn-default" role="button">
							          <span>Cerrar Sesión</span>
							        </a>							        
								</div>
							</div>
							<!-- /.juu-body -->
						</div>
						<!-- /.juu-container -->
					</div>
					<!-- /.juu-popup -->
				</div>
				<!-- /.right-icon -->
			</div>
		</div>
		<!-- /.row -->
	</div>
	<!-- /.container -->
</nav>

<!-- Modal -->
<div id="notificacionesModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Mis Notificaciones</h4>
			</div>
			<div class="modal-body text-center">
				<?php if ($this->atributos ['notificaciones'] <= 0) :?>
					No hay ninguna notificación registrada para usted.
				<?php else: ?>
					<table class="table-notificacion">
						<?php for ($i = 0; $i < $this->atributos ['notificaciones']; $i++): ?>
						<?php if ($this->atributos ['estadoNotificacion'][$i] == 2): ?>
							<tr class="notificacion-tr ntf-vista">
						<?php endif; ?>
						<?php if ($this->atributos ['estadoNotificacion'][$i] == 1):?>
							<tr class="notificacion-tr ntf-pen">
						<?php endif; ?>
								<td id="td-izq-ntf">
									<img id="foto-notifi" alt="<?php echo $this -> atributos['imgaltNotificacion'][$i]; ?>" title="<?php echo $this -> atributos['imgaltNotificacion'][$i]; ?>" src="<?php echo $this -> atributos['imgsrcNotificacion'][$i]; ?>">
								</td>
								<td id="td-der-ntf">
									<p id="p-enlace-titulo">
										<a id="enlacetitulonotifi" href="#" title="<?php echo $this -> atributos['tituloNotificacion'][$i]; ?>"><?php echo $this -> atributos['tituloNotificacion'][$i]; ?></a>
									</p>
									<p id='textonotifi'> <?php echo $this -> atributos['descripNotificacion'][$i]; ?> </p>
									<div>
										<p id='fechanotifi'> 
											<img class="img-clock" alt="clock" src="<?php echo $this -> atributos['url_clock']; ?>">
											<?php echo $this -> atributos['fechasNotificacion'][$i]; ?> 
										</p>
									</div>
								</td>
							</tr>
						<?php endfor; ?>
					</table>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- /.Modal -->
