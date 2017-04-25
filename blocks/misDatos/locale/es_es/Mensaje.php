<?php
$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

$this->idioma ['noDefinido'] = 'No definido';
$this->idioma[$esteBloque ['nombre']."Registrar"] = "Formulario de actualización de datos básicos";
$this->idioma["nombre"] = "Nombre:";
$this->idioma["apellido"] = "Apellidos:";
$this->idioma["direccion"] = "Dirección:";
$this->idioma["direccionTitulo"]="Ingrese la dirección";
$this->idioma["telefono"] = "Número de Teléfono:";
$this->idioma["telefonoTitulo"]="Ingrese el número de teléfono";
$this->idioma["celular"] = "Número de Celular:";
$this->idioma["celularTitulo"]="Ingrese el número de celular";
$this->idioma["correo_personal"] = "Correo Personal:";
$this->idioma["correo_personalTitulo"] = "Ingrese el correo personal";
$this->idioma["correo_institucional"] = "Correo Institucional:";
$this->idioma["correo_institucionalTitulo"] = "Ingrese el correo institucional";
$this->idioma["correo_funcionario"] = "Correo para información de funcionario:";
$this->idioma["imagen"] = "Imagen de perfil:";
$this->idioma["clave"] = "Contraseña:";
$this->idioma["nickname"] = "Nickname:";
$this->idioma["botonRegistrar"] = "Actualizar";
$this->idioma["botonimagen"] = "subir foto";
$this->idioma["accesoIncorrecto"] = "";
$this->idioma["continuar"]="Continuar";
?>
