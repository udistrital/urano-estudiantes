<?php
// codigo del usuario
$usuario = $_REQUEST ['usuario'];
$tokenSaraAcademica = $this->miEncriptador->codificar_sara ( 'condorSara2013!' );
$tokenSaraAdministrativa = $this->miEncriptador->codificar_sara ( 's4r44dm1n1str4t1v4C0nd0r2014!' );
$tokenSaraDocencia = $this->miEncriptador->codificar_sara ( 'condorSara2013' );
$tokenSaraServiciosAcademicos = $this->miEncriptador->codificar_sara ( 'condorSara2014' );
$tokenKyron = $this->miConfigurador->getVariableConfiguracion ( 'tokenKyron' );
$tiempo = time ();