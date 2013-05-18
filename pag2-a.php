<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>

</head>

<body>
<?php
	//Creo y cargo el array de las x y las s de la funcion objetivo.
		$fobjetivo = array();
		for($i=1; $i<=$vbles; $i++){  //Cargo las x.
			$var = "lbl";
			$fobjetivo[$var][$i] = "x".$i;
			$var = "x".$i;
			$valor = "0";
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$fobjetivo[$var] = $valor;
		}
		for($j=1; $j<=$rnes; $j++){  //Inicializo las s.	
			$var = "lbl";
			$fobjetivo[$var][$i] = "s".$j;
			$var = "s".$j;
			$fobjetivo[$var] = "0";
			$i++;
		}
	//Creo y cargo el array de las restricciones.
		$restricciones = array();
		for($j=1; $j<=$rnes; $j++){
			for($i=1; $i<=$vbles; $i++){  //Cargo las r desde la POST
				$valor = "0";
				$var = "x".$j.$i;
				if(isset($_POST[$var])){
					$valor = $_POST[$var];
				}
				$restricciones[$var] = $valor;
				
			}
			for($i=1; $i<=$rnes; $i++){  //Inizializo las s.
				$valor = "0";
				$var = "s".$j.$i;
				if($j == $i){
					$valor = "1";
				}
				$restricciones[$var] = $valor;
			}
			$valor = "0";
			$var = "y".$j;
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$restricciones[$var] = $valor;
		}
?>

<?php                 											// -- Funciones ---//
	global $mat, $cbs, $ztas, $czs, $bases, $titas, $tablas;
	global $pibot_fila, $pibot_col;
	global $rnes, $vbles, $restricciones;
	
	$mat = $cbs = $ztas = $czs = $bases = $titas = $tablas = array();
	$pibot_fila = $pibot_col = 0;
	//Cargo los valores que traen las variables desde la página anterior.
		$vbles = $_POST["vbles"];
		$rnes = $_POST["rnes"];
		$metodo = $_POST["objetivo"];
	echo $rnes;
	
	function IniciarVblesIniciales(&$rnes, &$vbles, &$restricciones){
	//Cargo los valores que traen las variables desde la página anterior.
		$vbles = $_POST["vbles"];
		$rnes = $_POST["rnes"];
		$metodo = $_POST["objetivo"];
	}


	//Función que coloca el color a la celda segun pibot.
	function ColorCelda($fila, $col){
		$color = '';
		if($fila == $pibot_fila) $color = ' bgcolor="#00CCFF"';
		if($col == $pibot_col) $color = ' bgcolor="#99FF00"';
		if($fila == $pibot_fila and $col == $pibot_col) $color = ' bgcolor="#009933"';
		$abro_td = '<td width="45"'. $color. '><div align="center">';
		return $abro_td;
	}

	//Creo y cargo la matris de cálculos.
	function IniciarMatrisDeCalculos(&$mat, &$mat_Fs, &$mat_Cs){
	echo "<br><pre>";
	echo $rnes;
	print_r($rnes);
	echo "</pre>";
		for($j=1; $j<=$rnes; $j++){
			$var_a = "a".$j;
			for($i=1; $i<=$vbles; $i++){
				$var_rnes = "x".$j.$i;
				$mat[$var_a][$i] = $restricciones[$var_rnes];
			}
			for($m=1; $m<=$rnes; $m++){
				$var_rnes = "s".$j.$m;
				$mat[$var_a][$i] = $restricciones[$var_rnes];
				$i++;
			}
			$var_rnes = "y".$j;
			$mat[$var_a][$i] = $restricciones[$var_rnes];
		}
	}
	
	//Creo y cargo el array de Bases
	function IniciarBases(&$bases){
		for($j=1; $j<=$rnes; $j++){
			$bases[$j] = "S".$j; 
		}
	}

	//Creo y cargo el array de CBs.	
	function IniciarCBs(&$cbs){
		for($j=1; $j<=$rnes; $j++){
			$var = "s".$j;
			$var_cb = "cb".$j;
			$cbs[$var_cb] = $fobjetivo[$var]; 
		}
		$cbs["cb3"] = 40; //<--- Para hacer pruebas.
	}

	//Creo y cargo el array de ztas.
	function CalcularZtas(&$ztas){
		for($j=1; $j<=$mat_Fs; $j++){
			$var_cb = "cb".$j;
			$var_a = "a".$j; 
			for($i=1; $i<=$mat_Cs; $i++){
				$var_z = "z".$i;
				//echo $var_z.' ( '.$ztas[$var_z].' ) + ('.$var_cb.' ( '.$cbs[$var_cb].' ) * '.$var_a.$i.'( '.$mat[$var_a][$i].') ) = ';
				$ztas[$var_z] = $ztas[$var_z] + ($cbs[$var_cb] * $mat[$var_a][$i]);
				//echo $ztas[$var_z].'<br>';
			}
		}
	}
	
	//Creo y cargo el array de czs las diferencias Cj-Zj.
	function CalcularCZs(&$czs){
		$cols = count($fobjetivo["lbl"]);
		for($i=1; $i<=$cols; $i++){
			$var_cz = "cz".$i;
			$var_z = "z".$i;
			$var_fo = $fobjetivo["lbl"][$i];
			$czs[$var_cz] = $fobjetivo[$var_fo] - $ztas[$var_z];
		}
	}
	
	//Busco y guardo la columna del pibot.
	function BuscarColPibot(&$pibot_col){
		$valor_cz = $czs["cz1"];
		$pibot_col = 0;
		for($i=1; $i<=count($czs); $i++){
			$var = "cz".$i;
			if($czs[$var] >= 0){
				if($czs[$var] >= $valor_cz){
					$valor_cz = $czs[$var];
					$pibot_col = $i;	
				}
			}
		}
	}

	//Calculo los coeficientes tita.
	function CalcularTitas(&$titas){
		for($i=1; $i<=$mat_Fs; $i++){
			$var = "a".$i;
			$valor_vld = $mat[$var][$mat_Cs];
			$valor_pb = $mat[$var][$pibot_col];
			$var = "tita".$i;
			$titas[$var] = "0";
			if($valor_pb != 0){
				$titas[$var] = $valor_vld / $valor_pb;	
			}
		}
	}

	//Busco y guardo la fila del pibot.
	function BuscarFilaPibot(&$pibot_fila){
		//Busco la Fila.
		$valor_tita = $titas["tita1"];
		$pibot_fila = 0;
		for($i=1; $i<=count($titas); $i++){
			$var = "tita".$i;
			if($titas[$var] <= $valor_tita and $titas[$var] != 0){
				$valor_tita = $titas[$var];
				$pibot_fila = $i;	
			}
		}
	}
		
	//Función que crea y arma la tabla calculada.
	function CrearTabla(&$tabla){
		//Formatos de td e imput.
		$abro_td = '<td width="45"><div align="center">';
		$cierro_td = '</div></td>';
		
		$tabla = 
			'<table  border="1" align="center" cellpadding="8">'
				.'<tr>'
					.'<td colspan="2"></td>';
					$celdas ='';
					//lbl Variables x
					for($i=1; $i<=$vbles; $i++){
						$abro_td = ColorCelda(1, $i);
						$celdas.= $abro_td.' X<sub>'.$i.'</sub>'.$cierro_td;
					}
					//lbl Variables s
					for($j=1; $j<=$rnes; $j++){
						$abro_td = '<td width="45"><div align="center">';
						$celdas.= $abro_td .'S<sub>'.$j.'</sub>'.$cierro_td;
					}
					$tabla.= $celdas;
		$tabla.= '</tr>';
		$tabla.= '<tr>'
					.'<th bgcolor="#999999">Base</th>'
					.'<th bgcolor="#999999">C<sub>B</sub></th>';
					$celdas ='';
					//Variables x
					for($i=1; $i<=$vbles; $i++){
						$var = "x".$i;
						$abro_td = ColorCelda(1, $i);
						$celdas.= $abro_td .' '. $fobjetivo[$var] .' '. $cierro_td;
					}
					//Variables s
					for($j=1; $j<=$rnes; $j++){
						$var = "s".$i;
						$abro_td = '<td width="45"><div align="center">';
						$celdas.= $abro_td .' '. $fobjetivo[$var] .' '. $cierro_td;
					}
					$tabla.= $celdas;
					$tabla.= '<th bgcolor="#999999">VLD</th>';
		$tabla.= '</tr>';
				//Creo y armo las filas de calculos.
				$filas = '';
				for($j=1; $j<=$mat_Fs; $j++){
					$filas.= "<tr>";
					
					$var = "b".$j;
					$abro_td = ColorCelda($j, -1);
					$filas.= $abro_td .' '. $bases[$j] .' '. $cierro_td; 
					
					$var = "cb".$j;
					$abro_td = ColorCelda($j, -1);
					$filas.= $abro_td .' '. $cbs[$var] .' '. $cierro_td;
					
					for($i=1; $i<=$mat_Cs; $i++){
						$var = "a".$j;
						$abro_td = ColorCelda($j, $i);
						$filas.= $abro_td .' '. $mat[$var][$i] .' '. $cierro_td;
					}
					
					$filas.= "</tr>";
				}
				$tabla.= $filas;
		$tabla.= '<tr>'
					.'<td rowspan="2"></td>'
					.'<th bgcolor="#999999">Z<sub>j</sub></th>';
					//Creo y armo las ztas.
					$celdas ='';
					$abro_td = '<td width="45"><div align="center">';
					for($i=1; $i<=count($ztas); $i++){
						$var = "z".$i;
						$celdas.= $abro_td . $ztas[$var] . $cierro_td;
					}
					$tabla.= $celdas;
		$tabla.= '</tr>';
		$tabla.= '<tr>'
					.'<th bgcolor="#999999">C<sub>j</sub> - Z<sub>j</sub> </th>';
					$celdas ='';
					//Creo y armo las Cj-Zj.
					for($i=1; $i<=count($czs); $i++){
						$var = "cz".$i;
						$celdas.= $abro_td .' '. $czs[$var] .' '. $cierro_td;
					}
					$tabla.= $celdas;
		$tabla.= '</tr>';
		$tabla.= '</table>';
		
		$id = count($tabla)+ 1;
		$tabla[$id] = $tabla;
	}

	//Calculo la fila del Pibot a 1.
	function DividirFilaPibotEnPibot($mat){
		$var = "a".$pibot_fila;
		$pibot = $mat[$var][$pibot_col];
		if($pibot != 1){
			$mat_Cs = count($mat[$var]);
			for($i=1; $i<=$mat_Cs; $i++){
				$mat[$var][$i] = $mat[$var][$i] / $pibot;
			}
		}
	}

	//Calculo los valores de las demas componentes.
	function CalcularValoresNoPibot($mat){
		$var = "a".$pibot_fila;
		$mat_Fs = count($mat);
		$mat_Cs = count($mat[$var]);
		$pibot = $mat[$var][$pibot_col];
		for($j=1; $j<=$mat_Fs; $j++){
			if($j != $pibot_fila){
				for($i=1; $i<=$mat_Cs; $i++){
					$var = "a".$j;
					$val_comp_fila = $mat[$var][$pibot_col];
					if($val_comp_fila != 0){
						if($i != $pibot_col){
							$val_comp = $mat[$var][$i];
							$val_pibot = $pibot;
							$val_v = $mat[$var][$pibot_col];
							$var_2 = "a".$pibot_fila;
							$val_h = $mat[$var_2][$i];
							$mat[$var][$i] = ($val_comp * $val_pibot) - ($val_v * $val_h);
						} 
					}
				}
			}
			$mat[$var][$pibot_col] = 0;
		}
	}

	function CrearTablasResultados(&$tabla){}
?>


<table border="0" align="center">
  <tr>
    <td>
<table  border="1" align="center" cellpadding="8">
<tbody>
  <tr>
    <th  scope="col">&nbsp;</th>
    <th  scope="col">Funciones </th>
    <th  scope="col">Asignación de Holguras</th>
  </tr>
  <tr>
    <th scope="row"><div align="left">F. Objetivo </div></th>
	<?php 
	//Construyo y muestro la función objetivo.
	
	//--Funciones
	$contenido = sprintf("<td align=\"center\">");
	$formula = sprintf("$metodo Z = $fobjetivo[x1] X<sub>1</sub>");
	for ($i=2; $i<=$vbles; $i++){
		$var = "x".$i;
		$formula.= sprintf(" + $fobjetivo[$var] X<sub>$i</sub>");
	}
	$contenido.=$formula;
	$contenido.=sprintf("</td>");
	echo $contenido;	
	
	//--Asignación de Holguras
	$contenido = sprintf("<td align=\"center\">");
	$formula = $formula;
	for ($j=1; $j<=$rnes; $j++){
		$var = "s".$i;
		$formula.= sprintf(" + $fobjetivo[$var] S<sub>$j</sub>");
	}
	$contenido.=$formula;
	$contenido.=sprintf("</td>");
	
	echo $contenido;	
	?> 
  </tr>

<?php
	//Construyo y muestro las resticciones.
	for($j=1; $j<=$rnes; $j++){
		$contenido = sprintf
		("
			<tr>
			<th scope=\"row\"><div align=\"left\">Restricci&oacute;n $j </div></th>
		");
		//echo $contenido;
		
		//---Funciones
		$var = "x".$j."1";
		$contenido.=sprintf("<td align=\"center\">");
		$formula =sprintf(" $restricciones[$var] X<sub>1</sub>");
		for($i=2; $i<=$vbles; $i++){
			$var ="x".$j.$i;
			$formula.= sprintf(" + $restricciones[$var] X<sub>$i</sub>");
			$contenido.=$formula;
			$contenido.= sprintf(" <= ");
			$var = "y".$j;
			$contenido.= sprintf(" $restricciones[$var]");
		}
		$contenido.=sprintf("</td>");
		echo $contenido;
		
		//---Asignación de Holguras
		$contenido = sprintf("<td align=\"center\">");
		//$contenido.=$formula;
		$formula.=sprintf(" + S<sub>$j</sub>");
		$var = "y".$j;
		$formula.= sprintf(" = $restricciones[$var]");
		$contenido.=$formula;
		$contenido.=sprintf("</td></tr>");
		echo $contenido;
	}
	 ?>
</tbody>  
</table>	
	</td>
    <td>
<table border="1" align="center" cellpadding="8">
  <tr>
    <th colspan="2" scope="col">Soluci&oacute;n Posible Básica</th>
    </tr>
  <tr>
    <td><p>Variables B&aacute;sicas:
	<?php
	$varbas = "<br>";
	for($j=1; $j<=$rnes; $j++){
		$var = "y".$j;
		$varbas.= sprintf("S<sub>$j</sub> = $restricciones[$var]<br>");
	}
	echo $varbas;
	?>
	</p>    </td>
    <td>
	
<p>Variables No B&aacute;sicas:
	<?php
	$varnbas = "<br>";
	for($i=1; $i<=$vbles; $i++){
		$var = "x".$i;
		$varnbas.= sprintf("X<sub>$i</sub> = 0<br>");
	}
	echo $varnbas;
	?>
	</p>	</td>
  </tr>
</table>	</td>
  </tr>
</table>
<br />
<div style="text-align:center;">
<form id="form1" name="form1" method="post" action="pag3.php">
<?php                 											//---- Arrays de la Tabla ----
	//echo "<br>";
	//CrearTablasResultados($tabla);
			//Inicializo los arrays.
		IniciarVblesIniciales($rnes, $vbles, $restricciones);
		echo $rnes;
		/*
		IniciarMatrisDeCalculos($mat, $mat_Fs, $mat_Cs);
	echo "<br><pre>";
	print_r($mat);
	echo "</pre>";
		
		IniciarCBs($cbs);
		IniciarBases($bases);

		//Calculo Cooeficientes.
		CalcularZtas($ztas);
		CalcularCZs($czs);
		BuscarColPibot($pibot_col);
		CalcularTitas($titas);
		BuscarFilaPibot($pibot_fila);
		
		$tablas[1] = CrearTabla($tabla);
		DividirFilaPibotEnPibot($mat);
		$tablas[2] = CrearTabla($tabla);
		CalcularValoresNoPibot($mat);
		
		$tablas[3] = CrearTabla($tabla);
	}

	
	for($i=1; $i<=count($tablas); $i++){
		echo $tablas[$i].'<br>';
	}


	echo "<br><pre>";
	//echo $matris_Cs."<br>";
	//print_r($fobjetivo);
	print_r($mat);
	echo "</pre>";
	*/
?>

	<p align="center">
		<input type="submit" name="Submit" value="Continuar">
	</p>
</form>
</div>
<p>&nbsp;</p>

<p>&nbsp;</p>

</body>
</html>
