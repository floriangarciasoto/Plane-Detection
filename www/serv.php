<?php

// Fonction permettant de remettre correctement en forme les coordonnées venant de la base de donnée.
// On précise simplement la requête, la connexion sera prise comme variable globale.
function getSQL($req){
	global $connexion;
	$data = array();
	$resultat = mysqli_query($connexion,$req) or die('Erreur');
	$i=0;
	while($dat = mysqli_fetch_assoc($resultat)){
		$i++;
		$data[$i]=$dat;
	}
	return $data;
}

$data = array();

$connexion = mysqli_connect('localhost', 'client', 'JLVtwYjsmEAc3LEf', 'avions');

if ($_POST['type'] == 0) file_put_contents('new.txt',1);

if (file_get_contents('new.txt') == '1') {
	// Initialisation de la connexion avec la base de donnée.
	$data[0] = 1;
	file_put_contents('new.txt',0);

	// Renvoit du retour de la fonction en JSON.
	if ($_POST['all'] == 1) $data[1] = getSQL("SELECT `positions`.*,color FROM `positions`,colors WHERE positions.hex=colors.hex");
	else $data[1] = getSQL("SELECT `positions`.*,color FROM `positions`,colors WHERE positions.hex=colors.hex AND TIMESTAMPDIFF(SECOND, date, NOW())<3600");
	$dat=getSQL("SELECT * FROM `positions` GROUP BY hex");
	$data[2] = array();
	foreach ($dat as $key => $value) {
		$dat2=getSQL("SELECT * FROM `positions` WHERE hex='".$value['hex']."' GROUP BY date");
		if (sizeof($dat2) > 1) {
			array_push($data[2],array($value['hex'],array($dat2[sizeof($dat2)-1]['lat'],$dat2[sizeof($dat2)-1]['lng']),array($dat2[sizeof($dat2)]['lat'],$dat2[sizeof($dat2)]['lng'])));
			// array_push($data[2], array(sizeof($dat2)-2,sizeof($dat2)-1));
		}
	}
}
else $data[0] = 0;

echo json_encode($data);

// Fermeture de la connexion avec la base de donnée.
mysqli_close($connexion);

?>
