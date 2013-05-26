<?php
header('Content-type: text/json');
//Generation of a tab which will contain true/false element
//in function of the student's answers
$id_exo	 = null;
$answers = null;
if (isset($_GET['e']) && isset($_GET['a'])) {
	$id_exo 	= $_GET['e'];
	$answers 	= $_GET['a'];
	echo json_encode(tabCompareAnswers($id_exo, $answers));
}
else { return; }

function tabCompareAnswers($id_exo,$answers) // id_exo: exercice ,answers: student's answers
{
	include 'connectToBdd.php';
	try {

		$requete_texte = $bdd->prepare("SELECT cuetext
				FROM mdl_elang_cue
				WHERE id_elang = :id");
		$requete_texte->bindValue(':id',$id_exo, PDO::PARAM_INT);
		$requete_texte->execute();

		$texte = $requete_texte->fetchAll();

		//We transforme the answers sequence to a tab
		$tabStudentWords = preg_split("/[_]+/",$answers,0,PREG_SPLIT_NO_EMPTY);


		//We create the global text of the exercise
		$exercice = "";
		for($i=0; $i<sizeof($texte); $i++)
		{
			$exercice .= $texte[$i]['cuetext'] . " ";
		}

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
		$tabCompareAnswers = array();
		//Words comparaison and tab creation

		if(count($tabStudentWords) == count($tabCorrectWords))
		{
			for($i=0; $i<count($tabCorrectWords); $i++)
			{
				if(strtolower ($tabCorrectWords[$i])==strtolower ($tabStudentWords[$i]))
				{
					$tabCompareAnswers[$i] = "true";
				}
				else
				{
					$tabCompareAnswers[$i] = "false";
				}
			}
		}
		else
		{
			echo "Arrays have different size";
		}

		return $tabCompareAnswers;
	}
	catch (Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}
}
?>
