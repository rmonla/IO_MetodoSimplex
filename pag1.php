<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
</head>

<body>
<?php
	//Cargo los valores que traen las variables desde la página anterior.
	$vbles = $_POST["vbles"];
	$rnes = $_POST["rnes"];
?>

<div style="text-align:center;"><form action="pag2.php" method="POST">
    <p>¿Cuál es el objetivo de la función? 
	<select name="objetivo">
	  <option value="Max">Maximizar</option>
	  <option value="Min">Minimizar</option>
	</select></p>
	<p>Función Objetivo --> Z = 
	
	<input name="X1" type="text" value="0" size="5"> 
	X<sub>1</sub>  
	
	<?php //En el paso anterior contruyo la X1 y ahora contruyo y muestro los otros controles para las demas variables X.
		for ($i=2; $i<=$vbles; $i++){
			$contenido.= ' + <input name="X'. $i .'" value="0" type="text" size="5"> X<sub>'. $i .'</sub>';
		}
		echo $contenido;
	?>
	
	
	</p>
	<p>	
	Restricciones:
	</p>
	<?php
		//Oculto las variables $rnes y $vbles para que las pase a la proxima pantalla.
		$contenido = '<input type="hidden" name="rnes" value="'. $rnes .'">';
		$contenido.= '<input type="hidden" name="vbles" value="'. $vbles .'"><p>';
	
		//Contruyo y muestro los controles para las restricciones.
		for ($j=1; $j<=$rnes; $j++){
			$i = 1;
			$contenido.= '<p>Restricción '. $j .' --> <input name="X'. $j.$i .'" value="0" type="text" size="5"> X<sub>'. $i .'</sub>'; 
			for ($i=2; $i<=$vbles; $i++){
				$contenido.= ' + <input name="X'. $j.$i .'" value="0" type="text" size="5"> X<sub>'. $i .'</sub>'; 
			}
			$contenido.= ' <= ';
			$contenido.= ' <input name="Y'. $j .'" value="0" type="text" size="5"> </p>';
		}
		echo $contenido;
	?>
	<p align="center"><input type="submit" name="Submit" value="Continuar"></p></form></div>
</body>
</html>
