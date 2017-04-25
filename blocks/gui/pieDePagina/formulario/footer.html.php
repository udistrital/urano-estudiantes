<!-- Page Content -->
<div class="container">
	<!-- Footer -->
	<footer>
		<div class="container text-left">
			<div class="row">
				<div class="col-lg-6">
					<div class="input-group">
						<input id="searchservice" type="text" class="form-control"
							placeholder="Buscar servicios..." autocomplete="off" data-typeahead-target="client-id" /> <span
							class="input-group-btn">
							<button class="btn btn-default" type="button" id="searchservicebutton">Ir!</button>
						</span>
					</div>
					<!-- /input-group -->
				</div>
				<!-- /.col-lg-6 -->
				<div class="col-lg-6">
					<div class="row">
						<div class="col-lg-6">
							<a class="btn btn-block btn-social btn-twitter"
								data-toggle="modal" data-target="#twitterModal"> <span
								class="fa fa-twitter"></span>Twitter
							</a>
						</div>
						<div class="col-lg-6">
							<a class="btn btn-block btn-social btn-facebook"
								data-toggle="modal" data-target="#facebookModal"> <span
								class="fa fa-facebook"></span>Facebook
							</a>
						</div>
					</div>
					<!-- /.row -->
				</div>
				<!-- /.col-lg-6 -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container -->
		<br>
		<!-- /.container -->
		<div class="container text-center">
			<p>
				<a href="https://www.udistrital.edu.co/">Universidad Distrital
					Francisco José de Caldas</a> PBX: 3239300. Todos los derechos
				reservados ©. .:: <a
					href="http://condor2.udistrital.edu.co/appserv/terminos_y_condiciones.pdf">Términos,
					condiciones de uso y política de privacidad</a> ::..
			</p>
		</div>
		<!-- /.container -->
	</footer>

</div>
<!-- /.container -->

<!-- Modal -->
<div id="facebookModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Udistrital en Facebook</h4>
			</div>
			<div class="modal-body text-center">
				<div id="fb-root"></div>
				<script>
	            (function(d, s, id) {
	              var js, fjs = d.getElementsByTagName(s)[0];
	              if (d.getElementById(id)) return;
	              js = d.createElement(s);
	              js.id = id;
	              js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5 ";
	              fjs.parentNode.insertBefore(js, fjs);
	            }(document, 'script', 'facebook-jssdk'));
	          </script>
				<div style="width: 100%; overflow-x: auto;">
					<div class="fb-page"
						data-href="https://www.facebook.com/UniversidadDistrital"
						data-tabs="timeline" data-small-header="true"
						data-adapt-container-width="true" data-hide-cover="false"
						data-show-facepile="true">
						<div class="fb-xfbml-parse-ignore">
							<blockquote cite="https://www.facebook.com/UniversidadDistrital">
								<a href="https://www.facebook.com/UniversidadDistrital">Universidad
									Distrital Francisco José de Caldas</a>
							</blockquote>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- /.Modal -->

<!-- Modal -->
<div id="twitterModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Udistrital en Twitter</h4>
			</div>
			<div class="modal-body text-center">
				<a class="twitter-timeline" href="https://twitter.com/udistrital"
					data-widget-id="694252236734611456">Tweets por @udistrital.</a>
				<script>
	            ! function(d, s, id) {
	              var js, fjs = d.getElementsByTagName(s)[0],
	                p = /^http:/.test(d.location) ? 'http' : 'https';
	              if (!d.getElementById(id)) {
	                js = d.createElement(s);
	                js.id = id;
	                js.src = p + "://platform.twitter.com/widgets.js ";
	                fjs.parentNode.insertBefore(js, fjs);
	              }
	            }(document, "script", "twitter-wjs");
	          </script>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- /.Modal -->