<?php

$regisztracio = 1446161536;


$perc = 60;

if((time() - $regisztracio) > $perc){
	echo "eltelt egy perc";
}else{
	echo "nem telt el egy perc";
}


echo "<br/>".time();
	



?>