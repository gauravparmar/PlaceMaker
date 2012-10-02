<?php

function ds($content){
	print_r($content);
	if (!(defined('STDIN'))){
		echo '<br />';
	} else {
		echo " \n";
	}
}

function majFichiersPlats($dept){
	if (!$dept){
	ds("Argument 'dept' manquant");
	ds("Abandon");
	}

	if (!preg_match('/^[0-9][0-9,A-B]/',$dept)){
		ds("Argument 'dept' incorrect");
		ds("Abandon");
	}

$str_query = "SELECT distinct insee
							FROM	historique h CROSS JOIN version_base v
							WHERE	h.timestamp > v.timestamp AND
										h.insee like '".$dept."___';";
	$str_query = "SELECT distinct s.id insee
								FROM	statut_donnees s CROSS JOIN version_base v
								WHERE	s.timestamp > v.timestamp AND
										s.id like '".$dept."___' AND
										s.categorie = 'PlaceGeoFLA';";
	$rq = pg_query($GLOBALS['pgc'],$str_query);
	if (!$rq){
		ds('Requête KO');
		ds('Abandon');
		ds(pg_last_error($GLOBALS['pgc']));
		die;
	}

	$res = pg_fetch_all($rq);
	$res_insee = array();
	if ($res[0]){
		foreach ($res as $r){
			$res_insee[] = $r['insee'];
		}
	}

	if ($GLOBALS['argv']){
		$dir_root = dirname(dirname(realpath($GLOBALS['argv'][0])));
	} else {
		$dir_root = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
	}
	$dir_in = $dir_root.'/data_full';
	$dir_out = $dir_root.'/data';

//ds('dir_in');
//ds($dir_in);
//ds('dir_out');
//ds($dir_out);

	$subdirs = array("commune_avec_relation","commune_sans_relation");
	$prefixe = 'geofla_data_';
	$suffixe = '.txt';

	foreach ($subdirs as $sd){
		$f_in = $dir_in.'/'.$sd.'/'.$prefixe.$dept.$suffixe;
		$f_out = $dir_out.'/'.$sd.'/'.$prefixe.$dept.$suffixe;
		if (!file_exists($f_in)){
			ds("pas de fichier ".$f_in);
			continue;
		}		
		if (file_exists($f_out)){
//			ds("fichier ".$f_out);
		}
	
		$a_in = file($f_in);
		$a_out = array($a_in[0]);
		array_shift($a_in);
		foreach ($a_in as $l){
			$insee = explode('insee=',$l);
			$insee = substr($insee[1],1,5);
//		ds($insee);
			if (array_search($insee,$res_insee) === false){
				$a_out[] = $l;
			}
		}
//	ds($a_in);
//	ds($a_out);
//	if (count($ds_out)>1){
		$stat = ok;
		$fp = fopen($f_out,'w');
		if (!fp){
			$stat = 'pb ouverture '.$f_out;
			break;
		}
		foreach ($a_out as $l){
			fwrite($fp,$l);
		}
		$fc = fclose($fp);
		if (!$fc){
			$stat = 'pb fermeture '.$f_out;
			break;
		}
	}
//	if ($fc){
		return $stat;
//	} else {
//		return -1;
//	}
}

/*
	function majFichiersPlatsOd($dept,$root){
	if (!$dept){
	ds("Argument 'dept' manquant");
	ds("Abandon");
	}

	if (!preg_match('/^[0-9][0-9,A-B]/',$dept)){
		ds("Argument 'dept' incorrect");
		ds("Abandon");
	}

	if ($root = 'postes'){
		$str_query = "SELECT distinct s.od_id
									FROM	od_statut_donnees s CROSS JOIN version_base v
									WHERE	s.timestamp > v.timestamp AND
											s.dept='".$dept."___' AND
											s.categorie = 'postOffice';";
	$rq = pg_query($GLOBALS['pgc'],$str_query);
	if (!$rq){
		ds('Requête KO');
		ds('Abandon');
		ds(pg_last_error($GLOBALS['pgc']));
		die;
	}

	$res = pg_fetch_all($rq);
	$res_id = array();
	if ($res[0]){
		foreach ($res as $r){
			$res_id[] = $r['od_id'];
		}
	}

	if ($GLOBALS['argv']){
		$dir_root = dirname(dirname(realpath($GLOBALS['argv'][0])));
	} else {
		$dir_root = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
	}
	$dir_in = $dir_root.'/data_full';
	$dir_out = $dir_root.'/data';

//ds('dir_in');
//ds($dir_in);
//ds('dir_out');
//ds($dir_out);

	$subdirs = array($root);
	$prefixe = $root;
	$suffixe = '.txt';

	foreach ($subdirs as $sd){
		$f_in = $dir_in.'/'.$sd.'/'.$prefixe.$dept.$suffixe;
		$f_out = $dir_out.'/'.$sd.'/'.$prefixe.$dept.$suffixe;
		if (!file_exists($f_in)){
			ds("pas de fichier ".$f_in);
			continue;
		}		
		if (file_exists($f_out)){
//			ds("fichier ".$f_out);
		}
	
		$a_in = file($f_in);
		$a_out = array($a_in[0]);
		array_shift($a_in);
		foreach ($a_in as $l){
			$id = explode('identifiant=',$l);
			$id = substr($id[1],1,6);
//		ds($insee);
			if (array_search($id,$res_id) === false){
				$a_out[] = $l;
			}
		}
//	ds($a_in);
//	ds($a_out);
//	if (count($ds_out)>1){
		$stat = ok;
		$fp = fopen($f_out,'w');
		if (!fp){
			$stat = 'pb ouverture '.$f_out;
			break;
		}
		foreach ($a_out as $l){
			fwrite($fp,$l);
		}
		$fc = fclose($fp);
		if (!$fc){
			$stat = 'pb fermeture '.$f_out;
			break;
		}
	}
//	if ($fc){
		return $stat;
//	} else {
//		return -1;
//	}
}
	*/
?>
