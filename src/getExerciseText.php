<?php
header('Content-type: text/json');

$id_exo	 = null;
$answers = null;
if (isset($_GET['e'])) {
	$id_exo 	= $_GET['e'];
	echo json_encode(getExerciseText($id_exo));
}
else { return; }

//Function which allows to get the text of the exercise
function getExerciseText($id_exo)
{
	include 'connectToBdd.php';
	$requete_texte = $bdd->prepare("SELECT cuetext, id
									FROM mdl_elang_cue
									WHERE id_elang = :id");
	$requete_texte->bindValue(':id',$id_exo, PDO::PARAM_INT);
	$requete_texte->execute();
	
	$texte = $requete_texte->fetchAll();
	
	//Variable which will contain the text of the exercise
	$textExercise = array();
	
	//We add each sequence to the variable
	for($i=0; $i<sizeof($texte); $i++) {
		$textExercise[$i] = array($texte[$i]['id'] => $texte[$i]['cuetext']);
	}
	return $textExercise;
}
?>