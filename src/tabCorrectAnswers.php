<?php
header('Content-type: text/json');
//Generation of a tab which will contain the correct
//words from the real sequence

$id_exo = null;
if (isset($_GET['e'])) {
	$id_exo = $_GET['e'];
	echo json_encode(tabCorrectAnswers($id_exo));
}
else { return; }

function tabCorrectAnswers($id_exo) // id_exo: exercice ,answers: student's answers 
{
	include 'connectToBdd.php';

	//We collect all the sequences of the exercise

	$requete_texte = $bdd->prepare("SELECT cuetext
									FROM mdl_elang_cue
									WHERE id_elang = :id");
	$requete_texte->bindValue(':id',$id_exo, PDO::PARAM_INT);
	$requete_texte->execute();
	
	$texte = $requete_texte->fetchAll();
	
	//We create the global text of the exercise
	$exercice = "";
	for($i=0; $i<count($texte); $i++)
	{
		$exercice .= $texte[$i]['cuetext'] . " ";
	}
	
	//Variable in which will be stocked the correct words (into a string form)
	$correctWords = "";
	
	$j =0;
	//Get words surrounded by "[" "]"
	while($j < strlen($exercice))
	{
		//When we find a "["
		if($exercice{$j}=="[")
		{
			//We increment the variable to pass the "["
			$j++;
			//We read the word caracter by caracter until the we find a "]"
			while($exercice{$j}!="]")
			{
				//We write the caracter
				$correctWords .= $exercice{$j};
				$j++;
			}
		}
		$correctWords .= " ";
		$j++;
	}		

	//Transform the list of correct words to a tab
	$tabCorrectWords = preg_split("/ /",$correctWords,0,PREG_SPLIT_NO_EMPTY);
	
	return $tabCorrectWords;
}
?>
