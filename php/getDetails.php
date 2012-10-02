 <?php
 	include 'connexion.php';
	
	$liste_champs = "identifiant,libelle_site,caracteristique_site,complement_adresse,adresse,lieu_dit,code_postal,localite,telephone,changeur_monnaie,photocopieur,dab,affranchissement_libre_service,recharge_moneo,monnaie_paris,code_dept";
	if ($_GET['mode'] == 'postOffice'){
		$str_query = "SELECT ".$liste_champs.
					"	FROM od_postes_dept
						WHERE identifiant = '".$_GET['id']."';";
			
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_array($rq,0,PGSQL_NUM);
		$str_resp = implode('#',$res);
		$str_resp = $liste_champs.';'.$str_resp;
	}
	
//	header('Content-Type: text/plain; charset=utf-8');
	echo($str_resp);

 ?>