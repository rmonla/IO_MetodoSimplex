<?php	//Cabezera
	$cabezera = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
?>

<?php   //Abro html
	$html ='<html xmlns="http://www.w3.org/1999/xhtml">';
?>

<?php   //<head>
	$head ='<head>';
	$head.='<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
	$head.='<title>Documento sin t&iacute;tulo</title>';
	$head.='</head>';
?>

<?php   //Abro <body>
	$body ='<body>';
?>

<?php   //Inicializo Variables
	global $vbles, $rnes, $metodo;
	global $fo, $restricciones;
	global $mat, $mat_Fs, $mat_Cs;
	global $tablas;
	global $BCBs;
	
	$tablas = array();
	
	//Cargo los valores que traen las variables desde la página anterior.
	$vbles = $_POST["vbles"];
	$rnes = $_POST["rnes"];
	$metodo = $_POST["objetivo"];
	
	
	/*
	echo $vbles;
	echo $rnes;
	echo $metodo;
	*/
	
/*	echo '<pre>';
	print_r($restricciones);
	//print_r($restricciones);
	echo '</pre>';
*/	
	
?>

<?php   //Funciones
	function CargarMatrisFO(&$fo, $_POST, $vbles, $rnes){
		//Creo y cargo el array de las x y las s de la funcion objetivo.
		for($i=1; $i<=$vbles; $i++){  //Cargo las x.
			$var = "lbl";
			$fo[$var][$i] = "X";
			$var = "X".$i;
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$fo[$var] = $valor;
		}
		for($j=1; $j<=$rnes; $j++){  //Inicializo las s.	
			$var = "lbl";
			$fo[$var][$i] = "S";
			$var = "S".$j;
			$fo[$var] = "0";
			$i++;
		}
	}
	
	function CargarMatrisRnes(&$restricciones, $_POST, $vbles, $rnes){
		//Creo y cargo el array de las restricciones.
		$restricciones = array();
		for($j=1; $j<=$rnes; $j++){
			for($i=1; $i<=$vbles; $i++){  //Cargo las a desde la POST
				$var = "X".$j.$i;
				if(isset($_POST[$var])){
					$valor = $_POST[$var];
				}
				$restricciones[$var] = $valor;
			}
			for($i=1; $i<=$rnes; $i++){  //Inizializo las s.
				$valor = "0";
				$var = "S".$j.$i;
				if($j == $i){
					$valor = "1";
				}
				$restricciones[$var] = $valor;
			}
			$valor = "0";
			$var = "Y".$j;
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$restricciones[$var] = $valor;
		}
	}

	function CargarMatrisDeCalculos(&$mat, &$mat_Fs, &$mat_Cs, $rnes, $vbles, $restricciones){
		for($j=1; $j<=$rnes; $j++){
			$var_a = "a".$j;
			for($i=1; $i<=$vbles; $i++){
				$var_rnes = "X".$j.$i;
				$mat[$var_a][$i] = $restricciones[$var_rnes];
			}
			for($m=1; $m<=$rnes; $m++){
				$var_rnes = "S".$j.$m;
				$mat[$var_a][$i] = $restricciones[$var_rnes];
				$i++;
			}
			$var_rnes = "Y".$j;
			$mat[$var_a][$i] = $restricciones[$var_rnes];
		}
		$var = "a1";
		$mat_Fs = count($mat);
		$mat_Cs = count($mat[$var]);
	}

	function CargarMatrisBasesCBs(&$BCBs, $rnes){
		for($i=1; $i<=$rnes; $i++){
				$var = "lbl";
				$val_lbl = "S".$i;
				$BCBs[$var][$i] = $val_lbl;
				$BCBs[$val_lbl] = 0;
			}
		for($j=1; $j<=$rnes; $j++){
				$var = "lbl";
				$val_lbl = "CB".$j;
				$BCBs[$var][$i++] = $val_lbl;
				$BCBs[$val_lbl] = 0;
			}
		}
	
	function AgregarNuevaTabla(&$tablas, $tabla){
		$id = count($tablas);
		$tablas[$id] = $tabla;
		//echo $tablas[$id];
	}
	
	function CrearTablaFormulas(&$tablas, $metodo, $vbles, $rnes, $fo, $restricciones){
		$tabla = '<table border="0" align="center">';
			$tabla.= '<tr>';
				$tabla.= '<td>';
					$tabla.= '<table  border="1" align="center" cellpadding="8">';
						$tabla.= '<tbody>';
							$tabla.= '<tr>';
								$tabla.= '<th  scope="col">&nbsp;</th>';
								$tabla.= '<th  scope="col">Funciones </th>';
								$tabla.= '<th  scope="col">Asignación de Holguras</th>';
							$tabla.= '</tr>';
							$tabla.= '<tr>';
								$tabla.= '<th scope="row"><div align="left">F. Objetivo </div></th>';
								//Construyo y muestro la función objetivo.
								//--Formula
								$celdas = '<td align="center">';
									$formula = $metodo .' Z = ';
									for($i=1; $i<=$vbles; $i++){
										$signo = ' + ';
										if ($i == 1) $signo = '';
										$var = "lbl";
										$var_lbl = $fo[$var][$i];
										$var_vble = $var_lbl.$i;
										$formula.= $signo. $fo[$var_vble] .' '.$var_lbl .'<sub>'. $i .'</sub>';
									}
									$celdas.=$formula;
								$celdas.='</td>';
								//--Formula + Holguras
								$celdas.='<td align="center">';
								//$formula = $formula;
									for ($j=1; $j<=$rnes; $j++){
										$signo = ' + ';
										$var = "lbl";
										$var_lbl = $fo[$var][$i++];
										$var_vble = $var_lbl.$j;
										$formula.= $signo. $fo[$var_vble] .' '. $var_lbl .'<sub>'. $j .'</sub>';
									}
									$celdas.=$formula;
								$celdas.='</td>';
								$tabla.=$celdas;
							$tabla.= '</tr>';	
							//Construyo y muestro las resticciones.
							$filas = '';
							$celdas = '';
							for($j=1; $j<=$rnes; $j++){
								$filas.= '<tr>';
									$celdas = '<th scope="row"><div align="left">Restricci&oacute;n '. $j .' </div></th>';
									//---Formula
									$celdas.= '<td align="center">';
										$var = "X".$j."1";
										$formula = $restricciones[$var] .' X<sub>1</sub>';
										for($i=2; $i<=$vbles; $i++){
											$var ="X".$j.$i;
											$formula.= ' + '. $restricciones[$var] .' X<sub>'. $i .'</sub>';
										}
										$celdas.= $formula;
										$celdas.= ' <= '; //sigon celdas para usar formula despues.
										$var = "Y".$j;
										$celdas.= $restricciones[$var];
									$celdas.='</td>';
									//---Formula + Holguras
									$celdas.='<td align="center">';
										$formula.=' + S<sub>'. $j .'</sub>';
										$var = "Y".$j;
										$formula.= ' = '. $restricciones[$var];
										$celdas.= $formula;
									$celdas.='</td>';
								$filas.= $celdas;
								$filas.='</tr>';
							}
							$tabla.= $filas;
						$tabla.= '</tbody>';
					$tabla.= '</table>';
				$tabla.= '</td>';
				$tabla.= '<td>';
					$tabla.= '<table border="1" align="center" cellpadding="8">';
						$tabla.= '<tr>';
							$tabla.= '<th colspan="2" scope="col">Soluci&oacute;n Posible Básica</th>';
						$tabla.= '</tr>';
						$tabla.= '<tr>';
							$celdas = '<td><p>Variables B&aacute;sicas:';
								$vbles_basicas = '<br>';
								for($j=1; $j<=$rnes; $j++){
									$var = "Y".$j;
									$vbles_basicas.= 'S<sub>'. $j .'</sub> = '. $restricciones[$var] .'<br>';
								}
							$celdas.= $vbles_basicas .'</p></td>';
						$tabla.= $celdas;
							$celdas = '<td><p>Variables No B&aacute;sicas:';
								$vbles_No_basicas = '<br>';
								for($i=1; $i<=$vbles; $i++){
									$var = "x".$i;
									$vbles_No_basicas.= 'X<sub>'. $i .'</sub> = 0<br>';
								}
							$celdas.= $vbles_No_basicas .'</p></td>';
						$tabla.= $celdas;
						$tabla.= '</tr>';
					$tabla.= '</table>';
				$tabla.= '<td>';
			$tabla.= '</tr>';
		$tabla.= '</table>';
	
	AgregarNuevaTabla($tablas, $tabla);
	}

?>

<?php   //Area de impresión. 
	/*
	$id = count($tablas) + 1;
	$tablas[$id] = CrearTablaFormulas($metodo, $vbles, $rnes, $fo, $restricciones);
	
	for($i=0; $i<$id; $i++){
		echo $tablas[$i];
	}
	*/
	CargarMatrisFO($fo, $_POST, $vbles, $rnes);
	CargarMatrisRnes($restricciones, $_POST, $vbles, $rnes);
	CargarMatrisDeCalculos($mat, $mat_Fs, $mat_Cs, $rnes, $vbles, $restricciones);
	CargarMatrisBasesCBs($BCBs, $rnes);
	
	
	CrearTablaFormulas($tablas, $metodo, $vbles, $rnes, $fo, $restricciones);
	


	//Muestro las tablas.
	for($i=0; $i<count($tablas); $i++){
		echo $tablas[$i];
	}

	$var = count($tablas);
	echo "<br><pre>";
//	echo $var;
	print_r($BCBs);
	echo "</pre>";

	?>

<?php   //Cierro <body>
	$body.='</body>';
?>

<?php   //Cierro <html>
	$html.= $head;
	$html.= $body;
	$html.='</html>';
?>

<?php   //Armo y muestro la Página.
	$pag = $cabezera;
	$pag.= $html;
	echo $pag;
?>
