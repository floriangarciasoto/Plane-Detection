<meta http-equiv="refresh" content="1;URL=./simul.php">
<?php
function random_float ($min,$max) {
   return ($min+lcg_value()*(abs($max-$min)));
}

$connexion = mysqli_connect('localhost', 'client2', 'rH6LP9WdBrflOf5h', 'avions');

function getonceSQL($req,$connexion){return mysqli_fetch_assoc(mysqli_query($connexion,$req));}
function getSQL($req,$connexion){
	$data = array();
	$resultat = mysqli_query($connexion,$req) or die('Erreur');
	$i=0;
	while($dat = mysqli_fetch_assoc($resultat)){
		$i += 1;
		$data[$i]=$dat;
	}
	return $data;
}
$maxdeb=3;

$minlgn=10;
$maxlgn=20;

$minlat=42;
$maxlat=45;

$minlong=2;
$maxlong=5;

$n1 = rand($minlgn,$maxlgn);
// echo 'N: '.$n1.'<br/><br/>';

// Ouvre le fichier en réduisant sa taille à zéro puis referme le fichier
fclose(fopen('/etc/mysql/coordonnees.txt', 'w'));

$monfichier = fopen('/etc/mysql/coordonnees.txt', 'r+');

$data=getonceSQL("SELECT COUNT(*) AS n FROM positions",$connexion);
if ($data['n']==0){
	for ($i=0; $i < 3; $i++) {
		echo 'FIRST !!! '.'Av0'.rand(1000000, 9999999).','.random_float($minlat,$maxlat).','.random_float($minlong,$maxlong).'<br/>';
		fwrite($monfichier, 'Av0'.rand(1000000, 9999999).','.random_float($minlat,$maxlat).','.random_float($minlong,$maxlong)."\n");
		// fwrite($monfichier, 'Av0'.rand(1000000, 9999999).','.random_float($minlat,$maxlat).','.random_float($minlong,$maxlong).','.random_float(1,2)*(1-2*rand(0,1)).','.random_float(1,2)*(1-2*rand(0,1)).',');
	}
}
else{
	for ($i=0; $i < $n1; $i++){
		$n2 = rand(1,10);
		if ($n2==10) {
			fwrite($monfichier, 'Av0'.rand(1000000, 9999999).','.random_float($minlat,$maxlat).','.random_float($minlong,$maxlong)."\n");
			echo 'NEW !!! '.'Av0'.rand(1000000, 9999999).','.random_float($minlat,$maxlat).','.random_float($minlong,$maxlong);
		}
		else {
			$data=getonceSQL("SELECT COUNT(*) AS n FROM positions",$connexion);
			$n3 = rand(1,$data['n']);
			$i2=0;
			$data=getSQL("SELECT idAvion FROM positions GROUP BY idAvion",$connexion);
			foreach ($data as $key => $value) {
				$i2++;
				if ($i2 == $n3) {
					fwrite($monfichier, $value['idAvion'].','.random_float($minlat,$maxlat).','.random_float($minlat,$maxlat)."\n");
					echo 'RENEW !!! '.$value['idAvion'].','.random_float($minlat,$maxlat).','.random_float($minlat,$maxlat);
				}
			}
		}
		echo '<br/>';
	}
}

fclose($monfichier);


mysqli_query($connexion,"LOAD DATA INFILE '/etc/mysql/coordonnees.txt' INTO TABLE positions FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' (`idAvion`, `latitude`, `longitude`) set `date`=NOW();");

mysqli_close($connexion);
?>
