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
	
	//Cargo los valores que traen las variables desde la página anterior.
	$_SESSION['vbles'] = $_POST["vbles"];
	$_SESSION['rnes'] = $_POST["rnes"];
	$_SESSION['metodo'] = $_POST["objetivo"];
	//echo $_SESSION['metodo'];
	/*
	echo $_SESSION['vbles'];
	echo $_SESSION['rnes'];
	echo $_SESSION['metodo'];
	*/
	
/*	echo '<pre>';
	print_r($_SESSION['matrnes']);
	//print_r($_SESSION['matrnes']);
	echo '</pre>';
*/	
	
?>

<?php   //Funciones
	function CargarMatrisFO(){
		//Creo y cargo el array de las x y las s de la funcion objetivo.
		for($i=1; $i<=$_SESSION['vbles']; $i++){  //Cargo las x.
			$var = "lbl";
			$_SESSION['fo'][$var][$i] = "X".$i;
			$var = "X".$i;
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$_SESSION['fo'][$var] = $valor;
		}
		for($j=1; $j<=$_SESSION['rnes']; $j++){  //Inicializo las s.	
			$var = "lbl";
			$_SESSION['fo'][$var][$i] = "S".$j;
			$var = "S".$j;
			$_SESSION['fo'][$var] = "0";
			$i++;
		}
	}
	
	function CargarMatrisRnes(){
		//Creo y cargo el array de las restricciones.
		$_SESSION['matrnes'] = array();
		for($j=1; $j<=$_SESSION['rnes']; $j++){
			for($i=1; $i<=$_SESSION['vbles']; $i++){  //Cargo las a desde la POST
				$var = "X".$j.$i;
				if(isset($_POST[$var])){
					$valor = $_POST[$var];
				}
				$_SESSION['matrnes'][$var] = $valor;
			}
			for($i=1; $i<=$_SESSION['rnes']; $i++){  //Inizializo las s.
				$valor = "0";
				$var = "S".$j.$i;
				if($j == $i){
					$valor = "1";
				}
				$_SESSION['matrnes'][$var] = $valor;
			}
			$valor = "0";
			$var = "Y".$j;
			if(isset($_POST[$var])){
				$valor = $_POST[$var];
			}
			$_SESSION['matrnes'][$var] = $valor;
		}
	}

	function CargarMatrisDeCalculos(){
		for($j=1; $j<=$_SESSION['rnes']; $j++){
			$var_a = "a".$j;
			for($i=1; $i<=$_SESSION['vbles']; $i++){
				$var_rnes = "X".$j.$i;
				$_SESSION['matcalc'][$var_a][$i] = $_SESSION['matrnes'][$var_rnes];
			}
			for($m=1; $m<=$_SESSION['rnes']; $m++){
				$var_rnes = "S".$j.$m;
				$_SESSION['matcalc'][$var_a][$i] = $_SESSION['matrnes'][$var_rnes];
				$i++;
			}
			$var_rnes = "Y".$j;
			$_SESSION['matcalc'][$var_a][$i] = $_SESSION['matrnes'][$var_rnes];
		}
		$var = "a1";
		$_SESSION['mat_Fs'] = count($_SESSION['matcalc']);
		$_SESSION['mat_Cs'] = count($_SESSION['matcalc'][$var]);
	}

	function CargarMatrisBasesCBs(){
		for($i=1; $i<=$_SESSION['rnes']; $i++){
				$var = "lbl";
				$val_lbl = "S".$i;
				$_SESSION['BCBs'][$var][$i] = $val_lbl;
				//$_SESSION['BCBs'][$val_lbl] = 0;
			}
		for($j=1; $j<=$_SESSION['rnes']; $j++){
				$var = "lbl";
				$val_lbl = "CB".$j;
				$_SESSION['BCBs'][$var][$i++] = $val_lbl;
				$_SESSION['BCBs'][$val_lbl] = 0;
			}
	
	/*
	$_SESSION['BCBs']["CB1"] = 1;
	$_SESSION['BCBs']["CB2"] = 1;
	$_SESSION['BCBs']["CB3"] = 1;
	*/

	}
	
	function CalcularZtas(){
		for($j=1; $j<=$_SESSION['mat_Fs']; $j++){
			$var_cb = "CB".$j;
			$var_a = "a".$j; 
			for($i=1; $i<=$_SESSION['mat_Cs']; $i++){
				$var_z = "Z".$i;
				//echo $var_z.' ( '.$_SESSION['ztas'][$var_z].' ) + ('.$var_cb.' ( '.$_SESSION['BCBs'][$var_cb].' ) * '.$var_a.$i.'( '.$_SESSION['matcalc'][$var_a][$i].') ) = ';
				$_SESSION['ztas'][$var_z] = $_SESSION['ztas'][$var_z] + ($_SESSION['BCBs'][$var_cb] * $_SESSION['matcalc'][$var_a][$i]);
				//echo $_SESSION['ztas'][$var_z].'<br>';
			}
		}
	}

	//Creo y cargo el array de czs las diferencias Cj-Zj.
	function CalcularCjZjs(){
		for($i=1; $i<=$_SESSION['mat_Cs']; $i++){
			$var_cjzj = "CZ".$i;
			$var_z = "Z".$i;
			$var_fo = $_SESSION['fo']["lbl"][$i];
			//echo $var_fo .' ( '. $_SESSION['fo'][$var_fo] .' ) - '. $var_z .'( '.$_SESSION['ztas'][$var_z] .' ) = ';
			$_SESSION['cjzjs'][$var_cjzj] = $_SESSION['fo'][$var_fo] - $_SESSION['ztas'][$var_z];
			//echo $_SESSION['cjzjs'][$var_cjzj].'<br>';
		}
	}

	//Busco y guardo la columna del pibot.
	function BuscarPibotCol(){
		$valor_cz = $_SESSION['cjzjs']["CZ1"];
		$_SESSION['pibot_col'] = 0;
		for($i=1; $i<=count($_SESSION['cjzjs']); $i++){
			$var = "CZ".$i;
			if($_SESSION['cjzjs'][$var] >= 0){
				if($_SESSION['cjzjs'][$var] >= $valor_cz){
					$valor_cz = $_SESSION['cjzjs'][$var];
					$_SESSION['pibot_col'] = $i;	
				}
			}
		}
	}

	//Calculo los coeficientes tita.
	function CalcularTitas(){
		for($i=1; $i<=$_SESSION['mat_Fs']; $i++){
			$var = "a".$i;
			$val_vld = $_SESSION['matcalc'][$var][$_SESSION['mat_Cs']];
			$val_pb = $_SESSION['matcalc'][$var][$_SESSION['pibot_col']];
			$var = "tita".$i;
			$_SESSION['titas'][$var] = "-1";
			if($val_pb != 0){
				$_SESSION['titas'][$var] = $val_vld / $val_pb;	
			}
		}
	}

	//Busco y guardo la fila del pibot.
	function BuscarPibotFila(){
		//Busco la Fila.
		$valor_tita = $_SESSION['titas']["tita1"];
		$_SESSION['pibot_fila'] = 0;
		for($i=1; $i<=count($_SESSION['titas']); $i++){
			$var = "tita".$i;
			if($_SESSION['titas'][$var] <= $valor_tita and $_SESSION['titas'][$var] != -1){
				$valor_tita = $_SESSION['titas'][$var];
				$_SESSION['pibot_fila'] = $i;	
			}
		}
	}

	//Obtengo el valor del pibot.
	function ValorPibot(){
		$fila = $_SESSION['pibot_fila'];
		$col = $_SESSION['pibot_col'];
		$var = "a".$fila;
		$val_pibot = $_SESSION['matcalc'][$var][$col];
		//echo $val_pibot;
		return $val_pibot;
	}
	
	//Función que coloca el color a la celda segun pibot.
	function ColorCelda($fila, $col){
		$color = '';
		if($fila == $_SESSION['pibot_fila']) $color = ' bgcolor="#00CCFF"';
		if($col == $_SESSION['pibot_col']) $color = ' bgcolor="#99FF00"';
		if($fila == $_SESSION['pibot_fila'] and $col == $_SESSION['pibot_col']) $color = ' bgcolor="#009933"';
		$abro_td = '<td width="45"'. $color. '><div align="center">';
		return $abro_td;
	}
	
	function AgregarNuevaTabla($tabla, $titulo){
		$id = count($_SESSION['tablas']);
		$_SESSION['tablas'][$id] = '<h1 align="center">'. $titulo. '</h1>'. $tabla. '<br>';
	}
	
	function CrearTablaFormulas(){
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
									$formula = $_SESSION['metodo'] .' Z = ';
									for($i=1; $i<=$_SESSION['vbles']; $i++){
										$signo = ' + ';
										if ($i == 1) $signo = '';
										$var = "lbl";
										$var_lbl = $_SESSION['fo'][$var][$i];
										$formula.= $signo. $_SESSION['fo'][$var_lbl] .' X<sub>'. $i .'</sub>';
									}
									$celdas.=$formula;
								$celdas.='</td>';
								//--Formula + Holguras
								$celdas.='<td align="center">';
									for ($j=1; $j<=$_SESSION['rnes']; $j++){
										$signo = ' + ';
										$var = "lbl";
										$var_lbl = $_SESSION['fo'][$var][$i++];
										$formula.= $signo.$_SESSION['fo'][$var_lbl] .' S<sub>'. $j .'</sub>';
									}
									$celdas.=$formula;
								$celdas.='</td>';
								$tabla.=$celdas;
							$tabla.= '</tr>';	
							//Construyo y muestro las resticciones.
							$filas = '';
							$celdas = '';
							for($j=1; $j<=$_SESSION['rnes']; $j++){
								$filas.= '<tr>';
									$celdas = '<th scope="row"><div align="left">Restricci&oacute;n '. $j .' </div></th>';
									//---Formula
									$celdas.= '<td align="center">';
										$var = "X".$j."1";
										$formula = $_SESSION['matrnes'][$var] .' X<sub>1</sub>';
										for($i=2; $i<=$_SESSION['vbles']; $i++){
											$var ="X".$j.$i;
											$formula.= ' + '. $_SESSION['matrnes'][$var] .' X<sub>'. $i .'</sub>';
										}
										$celdas.= $formula;
										$celdas.= ' <= '; //sigon celdas para usar formula despues.
										$var = "Y".$j;
										$celdas.= $_SESSION['matrnes'][$var];
									$celdas.='</td>';
									//---Formula + Holguras
									$celdas.='<td align="center">';
										$formula.=' + 1 S<sub>'. $j .'</sub>';
										$var = "Y".$j;
										$formula.= ' = '. $_SESSION['matrnes'][$var];
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
								for($j=1; $j<=$_SESSION['rnes']; $j++){
									$var = "Y".$j;
									$vbles_basicas.= 'S<sub>'. $j .'</sub> = '. $_SESSION['matrnes'][$var] .'<br>';
								}
							$celdas.= $vbles_basicas .'</p></td>';
						$tabla.= $celdas;
							$celdas = '<td><p>Variables No B&aacute;sicas:';
								$vbles_No_basicas = '<br>';
								for($i=1; $i<=$_SESSION['vbles']; $i++){
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
	return $tabla;
	}

	//Función que crea y arma la tabla calculada.
	function CrearTablaCalculada(){
		//Formatos de td e imput.
		$abro_td = '<td width="45"><div align="center">';
		$cierro_td = '</div></td>';
		
		//Comienzo a armar la tabla.
		$tabla = 
			'<table  border="1" align="center" cellpadding="8">'
				.'<tr>'
					.'<td colspan="2"></td>';
					$celdas ='';
					//lbl Variables x
					for($i=1; $i<=$_SESSION['vbles']; $i++){
						$abro_td = ColorCelda(1, $i);
						$celdas.= $abro_td.' X<sub>'.$i.'</sub>'.$cierro_td;
					}
					//lbl Variables s
					for($j=1; $j<=$_SESSION['rnes']; $j++){
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
					for($i=1; $i<=$_SESSION['vbles']; $i++){
						$var = "X".$i;
						$abro_td = ColorCelda(1, $i);
						$celdas.= $abro_td .' '. $_SESSION['fo'][$var] .' '. $cierro_td;
					}
					//Variables s
					for($j=1; $j<=$_SESSION['rnes']; $j++){
						$var = "S".$j;
						$abro_td = '<td width="45"><div align="center">';
						$celdas.= $abro_td .' '. $_SESSION['fo'][$var] .' '. $cierro_td;
					}
					$tabla.= $celdas;
					$tabla.= '<th bgcolor="#999999">VLD</th>';
					$tabla.= '<th bgcolor="#999999">&#1138;</th>';
		$tabla.= '</tr>';
				//Creo y armo las filas de calculos.
				$filas = '';
				for($j=1; $j<=$_SESSION['mat_Fs']; $j++){
					$filas.= "<tr>";
					
					$abro_td = ColorCelda($j, -1);
					$var = "lbl";
					$filas.= $abro_td .' '. $_SESSION['BCBs'][$var][$j] .' '. $cierro_td; 
					
					$abro_td = ColorCelda($j, -1);
					$var = "CB".$j;
					$filas.= $abro_td .' '. $_SESSION['BCBs'][$var] .' '. $cierro_td;
					
					for($i=1; $i<=$_SESSION['mat_Cs']; $i++){
						$abro_td = ColorCelda($j, $i);
						$var = "a".$j;
						$filas.= $abro_td .' '. $_SESSION['matcalc'][$var][$i] .' '. $cierro_td;
					}

					$abro_td = ColorCelda($j, -1);
					$var = "tita".$j;
					$filas.= $abro_td .' '. $_SESSION['titas'][$var] .' '. $cierro_td;
					
					$filas.= "</tr>";
				}
				$tabla.= $filas;
		$tabla.= '<tr>'
					.'<td rowspan="2"></td>'
					.'<th bgcolor="#999999">Z<sub>j</sub></th>';
					//Creo y armo las ztas.
					$celdas ='';
					$abro_td = '<td width="45"><div align="center">';
					for($i=1; $i<=count($_SESSION['ztas']); $i++){
						$var = "Z".$i;
						$celdas.= $abro_td . $_SESSION['ztas'][$var] . $cierro_td;
					}
					$tabla.= $celdas;
		$tabla.= '</tr>';
		$tabla.= '<tr>'
					.'<th bgcolor="#999999">C<sub>j</sub> - Z<sub>j</sub> </th>';
					$celdas ='';
					$abro_td = '<td width="45"><div align="center">';
					//Creo y armo las Cj-Zj.
					for($i=1; $i<count($_SESSION['cjzjs']); $i++){
						$var = "CZ".$i;
						$celdas.= $abro_td .' '. $_SESSION['cjzjs'][$var] .' '. $cierro_td;
					}
					$tabla.= $celdas;
		$tabla.= '</tr>';
		$tabla.= '</table>';
		
		return $tabla;
	}

	//Calculo la fila del Pibot a 1.
	function CalcularValoresFilaPibot(){
		$fila = $_SESSION['pibot_fila'];
		$col = $_SESSION['pibot_col'];
		$var = "a".$fila;
		$pibot = ValorPibot();
		$cols = $_SESSION['mat_Cs'];
		if($pibot != 1){
			for($i=1; $i<=$cols; $i++){
				$_SESSION['matcalc'][$var][$i] = $_SESSION['matcalc'][$var][$i] / $pibot;
			}
		}
	}

	//Calculo los valores de las demas componentes.
	function CalcularValoresNoPibot(){
		$p_fila = $_SESSION['pibot_fila'];
		$p_col = $_SESSION['pibot_col'];
		$filas = $_SESSION['mat_Fs'];
		$cols = $_SESSION['mat_Cs'];
		$pibot = ValorPibot();
		
		$var = "a".$p_fila;
		for($j=1; $j<=$filas; $j++){
			if($j != $p_fila){
				for($i=1; $i<=$cols; $i++){
					$var = "a".$j;
					$val_comp_fila = $_SESSION['matcalc'][$var][$p_col];
					if($val_comp_fila != 0){
						if($i != $p_col){
							$val_comp = $_SESSION['matcalc'][$var][$i];
							$val_v = $_SESSION['matcalc'][$var][$p_col];
							$var_2 = "a".$p_fila;
							$val_h = $_SESSION['matcalc'][$var_2][$i];
							$_SESSION['matcalc'][$var][$i] = ($val_comp * $pibot) - ($val_v * $val_h);
						} 
					}
				}
			}
			$_SESSION['matcalc'][$var][$p_col] = 0;
		}
	}

	//Ingreso los valores de base y cb desde fo.
	function EntraSale(){
		$p_col = $_SESSION['pibot_col'];
		$p_fila = $_SESSION['pibot_fila'];
		$var = "lbl";
		$lbl_entra = $_SESSION['fo'][$var][$p_col];
		$val_entra = $_SESSION['fo'][$lbl_entra];
		$var = "lbl";
		$val_sale = "CB".$p_fila;
		$_SESSION['BCBs'][$var][$p_fila] = $lbl_entra;
		$_SESSION['BCBs'][$val_sale] = $val_entra;
	}

	//Busco si es solución.
	function EsSolucion(){
		/*
		CalcularZtas();	
		CalcularCjZjs();
		BuscarPibotCol();
		CalcularTitas();
		BuscarPibotFila();
		*/
		$essolucion = 1;
		for($i=0; $i<count($_SESSION['cjzjs']); $i++){
			$var = "CZ".$i;
			//echo $_SESSION['cjzjs'][$var]."<br>";
			if($_SESSION['cjzjs'][$var]> 0) $essolucion = 0;
		}
		//echo $essolucion." sol <br>";
		return $essolucion;
	}
?>

<?php   //Area de impresión. 
	CargarMatrisFO();
	CargarMatrisRnes();
	CargarMatrisDeCalculos();
	CargarMatrisBasesCBs();

	$tabla = CrearTablaFormulas();
	AgregarNuevaTabla($tabla, "Tabla de Formulas");

	CalcularZtas();	
	CalcularCjZjs();
	BuscarPibotCol();
	CalcularTitas();
	BuscarPibotFila();
	
	$tabla = CrearTablaCalculada();
	AgregarNuevaTabla($tabla, "Tabla inicial de c&aacute;lculos");
	//$var = count($_SESSION['tablas']);

	//while()
	$var = ValorPibot();
	
	while( !EsSolucion() and $corte < 10){
		//echo $_SESSION['pibot_col']."<-- Col Pibot<br>";
		//echo $corte."<-- Corte<br>";
		if ( ValorPibot() > 1){
			CalcularValoresFilaPibot();
			$tabla = CrearTablaCalculada();
			AgregarNuevaTabla($tabla, "Calculo valores de la fila pibot");
		}else{
			AgregarNuevaTabla('', "<h2>No se calculó fila por valor pibot = 1 </h2>");
		}
		CalcularValoresNoPibot();
		$tabla = CrearTablaCalculada();
		AgregarNuevaTabla($tabla, "Calculo componentes fuera de la fila y columna del pibot");
		EntraSale();
		$tabla = CrearTablaCalculada();
		AgregarNuevaTabla($tabla, "Ingreso en la tabla las variables que entran");
		CalcularZtas();	
		CalcularCjZjs();
		if(EsSolucion()){
			$tabla = CrearTablaCalculada();
			AgregarNuevaTabla($tabla, "Tabla final");
		}
		BuscarPibotCol();
		CalcularTitas();
		BuscarPibotFila();
		$corte++;
	}

	
	//Muestro las tablas.
	for($i=0; $i<count($_SESSION['tablas']); $i++){
		echo $_SESSION['tablas'][$i];
	}
	
	//echo $var."<br>";
	echo "<br><pre>";
	//print_r($_SESSION['matcalc']);
	print_r($_SESSION['fo']);
	//print_r($_SESSION);
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
