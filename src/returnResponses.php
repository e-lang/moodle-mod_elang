<?php
$id_exo	 = null;
if (isset($_GET['e'])) {
	$id_exo 	= $_GET['e'];
	$texte	 	= $_GET['t'];
	returnResponses($id_exo);
}
else { return; }

function returnResponses($id_exo) // id_exo: exercice
{
	include 'connectToBdd.php';
	try {
		$requete_texte = $bdd->prepare("INSERT INTO mdl_elang_ask_correction
										VALUES (:id, :iduser, :date)");
		$requete_texte->bindValue(':id',$id_exo, PDO::PARAM_INT);
		$requete_texte->bindValue(':iduser',$USER->id(), PDO::PARAM_STR);
		$requete_texte->bindValue(':id',date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$requete_texte->execute();
	}
	catch (Exception $e) { die('Erreur : ' . $e->getMessage()); }
}