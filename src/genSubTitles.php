<?php
//id: exercice
if(isset($_GET['e']))
{
	$id = $_GET['e'];
}
else
{
	return;
}

//Subtitles generation function (depending if the student
//has already done the exercise at least one time
function genSubTitles()  
{
	//Variable which will contain the the exercise to do (with the words the student has 
	//already filled if they exist)
	$gapFill = "";

	//Connection to database
	include('base_connection.php');
	
	//We check if the student has already done the exercise
	$requete_student = $bdd->prepare("SELECT idcue
								FROM mdl_elang_ask_correction
								WHERE iduser = :id");
	$requete_student->bindValue(':id',$USER->id, PDO::PARAM_INT);
	$requete_student->execute();
	$res = $requete_student->fetchAll();
	
	//If the student has already done the exercise, we use past answers he put in blanks
	//we create the subtitles file with a special tag for correct answers and an other
	//tag for wrong answers
	if($res != NULL)
	{
		//We use the real sequence to compare the real words and the words
		//the student put in the blanks
		$requete_text = $bdd->prepare("SELECT cuetext
									FROM mdl_elang_cue
									WHERE id_elang = :id");
		$requete_text->bindValue(':id',$id, PDO::PARAM_INT);
		$requete_text->execute();
		
		$text = $requete_text->fetchAll();
		
		$textExercise = "";
		//We create the whole text
		for($i=0;$i<count($text);$i++)
		{
			$textExercise .= $text[$i]['cuetext'] . " ";
		}
		
		//We get the student text
		$requete_textStudent = $bdd->prepare("SELECT usertext
									FROM mdl_elang_reponses
									WHERE iduser = :id");
		$requete_textStudent->bindValue(':id',$USER->id, PDO::PARAM_INT);
		$requete_textStudent->execute();
		
		$textStudent = $requete_textStudent->fetchAll();
		
		//We transforme the real sequence to a tab (one element = one word)
		$tabrealSequence = preg_split("/ /",$textExercise,0,PREG_SPLIT_NO_EMPTY);
		//We transforme the sequence with the student's words to a tab (one element = one word)
		$tabStudentSequence = preg_split("/ /",$textStudent,0,PREG_SPLIT_NO_EMPTY);
		
		//We check that sizes of those tabs are equals
		if(count($tabrealSequence)==count($tabStudentSequence))
		{
			//For each element of those tabs
			for($i=0; $i<count($tabrealSequence); $i++)
			{
				//If the word is surrounded by square brackets (les petits trucs comme ça "[" "]")
				//so if it's a word which must to disapear, we compare it with the one that the 
				//student put, and we add tags in function of the answer
				if($tabrealSequence[$i]{0}=="[")
				{
					$j=0;
					//We read the word caracter by caracter until the end of the word 
					while($j < strlen($tabrealSequence[$i]))
					{
						//When we find a "["
						if($tabrealSequence[$i]{$j}=="[")
						{
							//We increment the variable to pass the "["
							$j++;
							//We read the word caracter by caracter until the we find a "]"
							while($tabrealSequence[$i]{$j}!="]")
							{			
								//We write the caracter
								$motSeqJuste .= $tabrealSequence[$i]{$j};
								$j++;
							}
							$j++;
						}
						//Else we write directly the caracter
						else
						{
							$motSeqJuste .= $tabrealSequence[$i]{$j};
							$j++;
						}
					}
					//We check if the word the student put is correct or not compare to the real word
					//If they are same
					if($motSeqJuste == $tabStudentSequence[$i])
					{
						$gapFill .= "<span class=\"true\" >" . $tabStudentSequence[$i] . "</span>";
						$motSeqJuste = "";
					}
					//Else
					else
					{
						$gapFill .= "<span class=\"false\" >" . $tabStudentSequence[$i] . "</span>";
						$motSeqJuste = "";
					}
				}
				//Else we write the word form the real sequence
				else
				{
					$gapFill .= $tabrealSequence[$i];
				}
			}
			return $gapFill;
		}
	}
	//Else we create the subtitles file with underscores (1 underscore = 1 caracter)
	else
	{
		//We use the real sequence to compare the real words and the words
		//the student put in the blanks
		$requete_text = $bdd->prepare("SELECT cuetext
									FROM mdl_elang_cue
									WHERE id_elang = :id");
		$requete_text->bindValue(':id',$id, PDO::PARAM_INT);
		$requete_text->execute();
		
		$tabRealSequence = $requete_text->fetchAll();
		
		for($i=0;$i<count($tabRealSequence);$i++)
		{
			//Variable which allows to read the word caracter by caracter
			$j=0;
			//We read the sequence caracter by caracter
			while($j < strlen($tabRealSequence[$i]['cuetext']))
			{
				//When we find a "["
				if($tabRealSequence[$i]['cuetext']{$j}=="[")
				{
					//We increment the variable to pass the "["
					$j++;
					//We read the word until we find a "]"
					while($tabRealSequence[$i]['cuetext']{$j}!="]")
					{			
						$gapFill .= "_";
					}
				}
				//Else we write the word
				else
				{
					if($tabRealSequence[$i]['cuetext']{$j}!="]")
					{
						$gapFill .= $tabRealSequence[$i]['cuetext']{$j};
					}
					$j++;
				}
			}
		}	
		return $gapFill;
	}
}
