<?php
/**
 * $atributos['id']
 * $atributos['enlace']
 * $atributos['tabIndex']
 * $atributos['estilo']
 * $atributos['enlaceTexto']
 * $atributos['enlaceImagen']
 * $atributos['posicionImagen']
 */
require_once ("core/builder/HtmlBase.class.php");
class Link extends HtmlBase {
	function enlace($atributos) {
		$this->setAtributos ( $atributos );
		$this->campoSeguro ();
		
		$this->cadenaHTML = "";
		$this->cadenaHTML .= "<a ";
		
		if (isset ( $atributos ["id"] )) {
			$this->cadenaHTML .= "id='" . $atributos ["id"] . "' ";
		}
		
		if (isset ( $atributos [self::ENLACE] ) && $atributos [self::ENLACE] != "") {
			$this->cadenaHTML .= "href='" . $atributos [self::ENLACE]."' " ;
		}
		
		if (isset ( $atributos [self::REDIRLUGAR] ) && $atributos [self::REDIRLUGAR] ==true) {
			$this->cadenaHTML .= " " ;
		}else{
			$this->cadenaHTML .= " target='_blank' ";
		}
		
		
		
		
		
		
		if (isset ( $atributos [self::ENLACECODIFICAR] ) && $atributos [self::ENLACECODIFICAR] != "") {
			$this->cadenaHTML .= "href='" . $this->miConfigurador->fabricaConexiones->crypto->$atributos [self::ENLACE] . "' ";
		}
		
		if (isset ( $atributos ["tabIndex"] )) {
			$this->cadenaHTML .= "tabindex='" . $atributos ["tabIndex"] . "' ";
		}
		
		if (isset ( $atributos [self::ESTILO] ) && $atributos [self::ESTILO] != "") {
			
			if ($atributos [self::ESTILO] == self::JQUERYUI) {
				$this->cadenaHTML .= " class='botonEnlace ui-widget ui-widget-content ui-state-default ui-corner-all' ";
			} else {
				
				$this->cadenaHTML .= "class='" . $atributos [self::ESTILO] . "' ";
			}
		}
		$this->cadenaHTML .= ">\n";

		if (isset ( $atributos ["enlaceImagen"] )) {
			$imagen = "<img src=" . $atributos ["enlaceImagen"] . " ";
			
			if (isset ( $atributos [self::ANCHO] )) {
				if ($atributos [self::ANCHO] != "") {
					$imagen .= "width='" . $atributos [self::ANCHO] . "' ";
				}
			} else {
				$imagen .= "width='10px' ";
			}
			
			if (isset ( $atributos ["alto"] )) {
				if ($atributos ["alto"] != "") {
					$imagen .= "height='" . $atributos [self::ALTO] . "' ";
				}
			} else {
				$imagen .= "height='10px' ";
			}
			
			$imagen .= " />";
		}
                
                if (isset ( $atributos ["enlaceImagen"] ) && isset ( $atributos [self::POSICIONIMAGEN]) && $atributos [self::POSICIONIMAGEN]=='atras' )
                    {$this->cadenaHTML .= $imagen; }
                
		if (isset ( $atributos ["enlaceTexto"] )) {
			$this->cadenaHTML .= "<span>" . $atributos ["enlaceTexto"] . "</span>";
		}
		
                if (isset ( $atributos ["enlaceImagen"] ) && isset ( $atributos [self::POSICIONIMAGEN]) && $atributos [self::POSICIONIMAGEN]=='adelante' )
                    {$this->cadenaHTML .= $imagen; }
		 elseif (isset ( $atributos ["enlaceImagen"] ) && !isset ( $atributos [self::POSICIONIMAGEN] ))
                    {$this->cadenaHTML .= $imagen; }
                    
		$this->cadenaHTML .= "</a>";
		
		if (isset ( $atributos [self::SALTOLINEA] )) {
			if ($atributos [self::SALTOLINEA] == true) {
				$this->cadenaHTML .= " <br>";
			}
		}
		return $this->cadenaHTML;
	}
	function enlaceWiki($cadena, $titulo = "", $datoConfiguracion, $elEnlace = "") {
		if ($elEnlace != "") {
			$enlaceWiki = "<a class='wiki' href='" . $datoConfiguracion ["wikipedia"] . $cadena . "' title='" . $titulo . "'>" . $elEnlace . "</a>";
		} else {
			$enlaceWiki = "<a class='wiki' href='" . $datoConfiguracion ["wikipedia"] . $cadena . "' title='" . $titulo . "'>" . $cadena . "</a>";
		}
		return $enlaceWiki;
	}
}