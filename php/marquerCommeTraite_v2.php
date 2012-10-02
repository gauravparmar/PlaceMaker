<?php
	include 'connexion.php';
	include 'fonctions.php';
	$statut = 1;
	
	$str_query = "SELECT count(*) nb
				FROM od_statut_donnees
				WHERE od_id = '".$_GET['id']."' AND
					categorie = '".$_GET['categorie']."';";
	$rq = pg_query($GLOBALS['pgc'],$str_query);
	$res = pg_fetch_assoc($rq,0);
	$num_hist = $res['nb'];
	
	if ($num_hist == 1){
		$str_query = "UPDATE od_statut_donnees
									SET timestamp = ".time().",
										ref_statut = ".$_GET['ref_statut']."
									WHERE od_id = '".$_GET['id']."' AND categorie = '".$_GET['categorie']."';";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq){
			$statut = 2;
		}
	}

	if ($num_hist == 0){
		$str_query = "INSERT INTO od_statut_donnees (od_id,categorie,timestamp,ref_statut) values ('".$_GET['id']."','".$_GET['categorie']."',".time().",".$_GET['ref_statut'].");";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq){
			$statut = 0;
		}
	}
	
//	$maj = majFichiersPlats(substr($_GET['id'],0,2));
//	$maj = majFichiersPlatsOd(substr($_GET['id'],0,2),$_GET['dataRoot']);

	$str_xml = 	"<?xml version='1.0' encoding='UTF-8'?>";
	$str_xml = $str_xml."<reponse>";
	$str_xml = $str_xml."<statut>".$statut."</statut>";
	$str_xml = $str_xml."<maj>".$maj."</maj>";
	$str_xml = $str_xml."</reponse>";

	header('Content-Type: text/xml; charset=utf-8');
	echo($str_xml);
?>