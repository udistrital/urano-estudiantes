<?php
require_once ("core/builder/HtmlBase.class.php");


class RecaptchaHtml extends HtmlBase{

    function recaptcha($atributos) {
    
        require_once ($this->configuracion ["raiz_documento"] . $this->configuracion ["clases"] . "/recaptcha/recaptchalib.php");
        $publickey = $this->configuracion ["captcha_llavePublica"];
    
        if (isset ( $atributos [self::ESTILO] ) && $atributos [self::ESTILO] != "") {
            $this->cadenaHTML = "<div class='" . $atributos [self::ESTILO] . "'>\n";
        } else {
            $this->cadenaHTML = "<div class='recaptcha'>\n";
        }
        $this->cadenaHTML .= recaptcha_get_html ( $publickey );
        $this->cadenaHTML .= "</div>\n";
        return $this->cadenaHTML;
    
    }
    
    
}