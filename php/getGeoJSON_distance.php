<?php

	include 'fonctions.php';
 	include 'connexion.php';

	if ($_GET['mode'] == 'postOffice'){
		$str_whereDept = "";
		$str_resDept = "99"; // valeur par defaut = France
		if (preg_match('/^[0-9][0-9,A-B]/',$_GET['dept'])){
			$str_whereDept = " AND o.code_dept = '".$_GET['dept']."' ";
			$str_resDept = $_GET['dept'];
		}
		
		$str_query = "SELECT 	o.latitude lat0,
													o.longitude lon0,
													p.latitude lat1,
													p.longitude lon1
									FROM		osm_post_office o
									JOIN		od_postes_dept p
									ON			p.identifiant = o.identifiant"
									.$str_whereDept
									.";";

		$rq = pg_query($GLOBALS['pgc'],$str_query);
		$res_distance = pg_fetch_all($rq);

		$str_resp = '{ "type": "FeatureCollection","features": [';
		for ($i = 0;$i < count($res_distance);$i++){
			$str_resp = $str_resp.'{'
													.'"type": "Feature",'
													.'"geometry": {"type": "LineString","coordinates": [['.$res_distance[$i]['lon0'].','.$res_distance[$i]['lat0'].'],['.$res_distance[$i]['lon1'].','.$res_distance[$i]['lat1'].']]},'
													.'"properties": {}'
													.'}';
			if ($i + 1 < count($res_distance)){
				$str_resp = $str_resp.',';
			}
		}
		$str_resp = $str_resp.']}';
	}
	echo($str_resp);
?>