<?php

namespace gui\home\funcion;
use \DateTime;
use \DateInterval;

$conexionOracle = "academica";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexionOracle );
$rutaUrlBloque = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );

$hoy = getdate();
$diaActual=$hoy['wday'];
$horario = null;

if ($_REQUEST ['funcion'] == 'buscarHorario') {
	
	$variable['CODIGO'] = $_REQUEST['usuario'];  //Codigo estudiante
	$variable['DIA'] = $diaActual;  //Día de la semana
	$cadenaSql = $this->sql->getCadenaSql ( 'buscarHorario', $variable);
	$matrizHorario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$j = 0;
	if($matrizHorario){
		
		if($diaActual==1 ) $nombreDia='Lunes';
		if($diaActual==2 ) $nombreDia='Martes';
		if($diaActual==3 ) $nombreDia='Miércoles';
		if($diaActual==4 ) $nombreDia='Jueves';
		if($diaActual==5 ) $nombreDia='Viernes';
		if($diaActual==6 ) $nombreDia='Sábado';
		if($diaActual==0 ) $nombreDia='Domingo';
		
		foreach($matrizHorario AS $dato) {
			if(!isset($horario)){
				$horario[] = $dato;
			} else {
				if (($dato['INS_ASI_COD'] == $horario[$j]['INS_ASI_COD']) && ($dato['DIA'] == $horario[$j]['DIA']) &&($dato['ID_SALON'] == $horario[$j]['ID_SALON'])) {
					$horaInicial=$horario[$j]['HORA'];
					$horaAuxiliar=$dato['HORA']+1;
		
					$hi = explode("-",$horaInicial);
					$horario[$j]['HORA']=$hi[0].'-'.$horaAuxiliar;
				} else {
					$horario[] = $dato;
					$j++;
				}
			}
		}
	}
}

$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$arrayDias = array( 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');

echo $arrayDias[date('w')].", ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');

function diaHora($dia, $hora){
	$otro = explode("-",$hora);
	$hora=$otro[0];
	
	if($hora==6) $rta = 'ma0070';
	else if($hora==7) $rta = 'ma0080';
	else if($hora==8) $rta = 'ma0090';
	else if($hora==9) $rta = 'ma0001';
	else if($hora==10) $rta = 'ma0011';
	else if($hora==11) $rta = 'mp0021';
	else if($hora==12) $rta = 'mp0010';
	else if($hora==13) $rta = 'mp0020';
	else if($hora==14) $rta = 'mp0030';
	else if($hora==15) $rta = 'mp0040';
	else if($hora==16) $rta = 'mp0050';
	else if($hora==17) $rta = 'mp0060';
	else if($hora==18) $rta = 'mp0070';
	else if($hora==19) $rta = 'mp0080';
	else if($hora==20) $rta = 'mp0090';
	else if($hora==21) $rta = 'mp0001';
	else if($hora==22) $rta = 'mp0011';
	return $rta;
}

function diaHora2($dia, $hora){
	$otro = explode("-",$hora);
	$hora=$otro[0];

	if($hora==6) $var = '0700am'.$dia.'t';
	else if($hora==7) $var = '0800am'.$dia.'t';
	else if($hora==8) $var = '0900am'.$dia.'t';
	else if($hora==9) $var = '1000am'.$dia.'t';
	else if($hora==10) $var = '1100am'.$dia.'t';
	else if($hora==11) $var = '1200pm'.$dia.'t';
	else if($hora==12) $var = '0100pm'.$dia.'t';
	else if($hora==13) $var = '0200pm'.$dia.'t';
	else if($hora==14) $var = '0300pm'.$dia.'t';
	else if($hora==15) $var = '0400pm'.$dia.'t';
	else if($hora==16) $var = '0500pm'.$dia.'t';
	else if($hora==17) $var = '0600pm'.$dia.'t';
	else if($hora==18) $var = '0700pm'.$dia.'t';
	else if($hora==19) $var = '0800pm'.$dia.'t';
	else if($hora==20) $var = '0900pm'.$dia.'t';
	else if($hora==21) $var = '1000pm'.$dia.'t';
	else if($hora==22) $var = '1100pm'.$dia.'t';
	return $var;
}

function sumtime($in, $fin,$minutos, $dia){
	$otro = explode(":",$in);
	$in2=$otro[0];
	$ahora='';
	
	$hoy = getdate();
	$parse1 = new DateTime($in);
	$parse2 = new DateTime($fin);
	if ($parse2 <= $parse1){
		return;
	}else{
		
		$time = new DateTime($in);
		$time->add(new DateInterval('PT' . $minutos . 'M'));
		$stamp = $time->format('h:i a');
		$format24 = $time ->format('G:i');
	
		$uniq = str_replace(' ', '', str_replace(':', '', $stamp));
		
		$reverse = strrev($uniq);
		$rest = substr($uniq, -6, 4);
		
		$rest2=strrev($rest);
		
		if($in2==$hoy['hours']){
			$ahora = "<div style='margin-top: 8%'><span class='label' id='label_ahora'>Ahora >></span></div>";
		}
		
		echo '<tr id="'.$reverse.'franja'.'"><td class="td-time" id="'.$uniq.$dia."t".'">'.date('h:i a', strtotime($in)). ' - ' .$stamp.$ahora.'</td>';
		
		echo'
             <td class="td-line">
               <div class="col-sm-12 nopadding">
                  <label class="label-desc" id="'.$reverse.'" style="margin: 0 0 0 0;"></label>
               </div>
     
            </td>';
		
		echo'</tr>';

		resum($format24,$fin,$minutos, $dia);
	}
}

function resum($in,$fin,$minutos, $dia){
	
	$otro = explode(":",$in);
	$in2=$otro[0];
	$ahora='';
	
	$hoy = getdate();
	$time = new DateTime($in);
	$time->add(new DateInterval('PT' . $minutos . 'M'));
	$stamp = $time->format('h:i a');

	$format24 = $time ->format('G:i');

	$uniq = str_replace(' ', '', str_replace(':', '', $stamp));
	$reverse = strrev($uniq);
	$rest = substr($uniq, -6, 4);
	$rest2=strrev($rest);
	
	if($in2==$hoy['hours']){
		$ahora = "<div style='margin-top: 8%'><span class='label' id='label_ahora'>Ahora >></span></div>";
	}

	echo '<tr id="'.$reverse.'franja'.'"><td class="td-time" id="'.$uniq.$dia."t".'">'.date('h:i a', strtotime($in)). ' - ' .$stamp.$ahora.'</td>';
		echo'
             <td class="td-line">
               <div class="col-sm-12 nopadding">
                  <label class="label-desc" id="'.$reverse.'" style="margin: 0 0 0 0;"></label>
               </div>

            </td>
        ';
	

	echo '</tr>';
	sumtime($format24,$fin,$minutos, $dia);
}
?>

<!-- container -->
<div class="container2">

<?php 

if($horario!=null){
	$colores=array('green-label', 'blue-label', 'red-label', 'purple-label', 'blue3-label', 'pink-label',
			'red2-label', 'blue2-label', 'purple2-label', 'pink2-label', 
	);
    $materias=array();
    $franjas=array();
    $ahora='';
   
    $id=0;
    
    for($i=0; $i<count($horario); $i++){
      if(!in_array($horario[$i]['NOMBRE'], $materias)){
      	$franjaP = array($horario[$i]['NOMBRE'], $colores[$i]);
        array_push($materias, $horario[$i]['NOMBRE']);
          array_push($franjas, $franjaP);
          $id++;
      }
    }
    
    //crear arreglo para guardar todas las posibles posiciones
    $losqhay = array('ma0070', 'ma0080', 'ma0090', 'ma0001', 'ma0011', 'mp0021', 'mp0010', 'mp0020', 'mp0030', 'mp0040', 'mp0050', 'mp0060', 'mp0070', 'mp0080', 'mp0090', 'mp0001', 'mp0011');
    
    $tam=count($losqhay);
    
    //crear arreglo para guardar los tede
    $mostrados = array();

    $ahora='';
    $hora= date('6:00');
    $hora2= date('22:00');
    $min=60;
    
    for ($i=0; $i<count($horario); $i++){
    	${"espacio".$i} = $horario[$i]['NOMBRE'];
    	${"sede".$i} = $horario[$i]['NOM_SEDE']." - ".$horario[$i]['NOM_EDIFICIO'];
    	${"salon".$i} = $horario[$i]['NOM_SALON'];
     	${"tede".$i}=diaHora($horario[$i]['DIA'], $horario[$i]['HORA']);
    	
     	$otro = explode("-",$horario[$i]['HORA']);
     	$inicio=$otro[0];
     	if(isset($otro[1])){
     		$fin=$otro[1];
     	}else{
     		$fin=$inicio+1;
     	}
     	
     	$hora_franja=diaHora2($horario[$i]['DIA'], $horario[$i]['HORA']);
     	
     	$inicio2= date($inicio.":00");
     	$fin2= date($fin.":00");
     	
     	if($hoy['hours']>=$inicio2 && $hoy['hours']<$fin2){
     		$ahora = "<div style='margin-top: 8%'><span class='label' id='label_ahora'>Ahora >></span></div>";
     	}
?>
     	<script type="text/javascript">
     	
     		$("<?php echo "#".$hora_franja;?>").html("<?php echo date('h:i a', strtotime($inicio2))." - ".date('h:i a', strtotime($fin2)).$ahora; ?>");
     	</script>
<?php 
     	     	
    }
   
    for($i=0; $i<count($horario); $i++){
    	for ($k=0; $k<count($franjas); $k++){
    		if($franjas[$k][0]==${"espacio".$i} ){
    			${"color".$i} = $franjas[$k][1];
    		}
    	}
    	
    	if(isset(${"tede".$i} ) ){
    		array_push($mostrados, ${"tede".$i});
    		?>
            <script>
              $(<?php  echo '"#'.${"tede".$i}.'"'; ?>).
              html(<?php  echo '"'.${"espacio".$i}.
              "<br>".
              ${"sede".$i}."<br>".
              ${"salon".$i}.'"'; ?>)
              .addClass(<?php  echo "'". ${"color".$i}."'"; ?>).show();
            </script>
            <?php 
            
          }
        }
    
        //quitar los mostrados a los q hay
        for($i=0; $i<count($mostrados); $i++){
          if(in_array($mostrados[$i], $losqhay)){
            $quitar=array_search($mostrados[$i], $losqhay);
            unset($losqhay[$quitar]);
          }
        }
    
        //eliminar del horario
        for($i=0; $i<$tam; $i++){
          if(isset($losqhay[$i])){
            $m=$losqhay[$i].'franja';
          ?>
            <script>
              //eliminar fila vacía
              $(<?php  echo '"#'.$m.'"'; ?>).remove();
            </script>
            <?php
          }
        }   

	echo'<table id="tablaDia" class="table table-bordered">';
    echo'<thead>
    <th><i class="fa fa-clock-o"></i> Horario</th>';
    echo '<th><i class="fa fa-angle-right"></i> '.$nombreDia.'</th>';
    echo '</thead>
    <tbody>';
    sumtime($hora,$hora2,$min, $diaActual);
    echo '</tbody>';  
	echo '</table>'; 


}else{
	$imagen = "<img ";
	$imagen .= "src='" . $rutaUrlBloque . "images/no_hay_horario.png" . "'";
	$imagen .= 'class="img-responsive" style="width: 100%;"';
	?>
	
	<div style="width: 100%; height: 100%;">
		<?php echo $imagen; ?>
	</div>
	<div class="alert alert-info" style="text-align: center;">
  		<strong>No hay espacios académicos asignados para el día.</strong>
	</div>
	<?php 
	
}
?>

</div>