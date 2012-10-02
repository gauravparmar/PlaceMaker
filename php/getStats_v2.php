 <?php
	//format de reponse :
	//date_en_clair,nb de points importes,nb de points dispo,departement (si France alors 99),distnace moyenne, distance max

 	include 'connexion.php';
	
	if ($_GET['mode'] == 'postOffice'){
		$str_whereDept = "";
		$str_resDept = "99"; // valeur par defaut = France
		if (preg_match('/^[0-9][0-9,A-B]/',$_GET['dept'])){
			$str_whereDept = " AND p.code_dept = '".$_GET['dept']."' ";
			$str_resDept = $_GET['dept'];
		}
		$str_query = "SELECT count(*) nb,'integres' statut
					FROM 
					(SELECT s.od_id
					FROM	od_statut_donnees s JOIN od_postes_dept p
					ON 		s.od_id = p.identifiant
					CROSS JOIN osm_version_base v
					WHERE 	v.categorie = 'postOffice' AND
							s.timestamp > v.timestamp"
					.$str_whereDept
					." UNION
					SELECT  o.identifiant
					FROM 	od_postes_dept o JOIN osm_post_office p
					ON		o.identifiant = p.identifiant
					WHERE 1=1"
					.$str_whereDept
					.")a
					UNION ALL
					SELECT count(*),'total'
					FROM (SELECT 1 un
								FROM od_postes_dept p
								LEFT OUTER JOIN osm_post_office o
								ON o.identifiant = p.identifiant
								WHERE o.identifiant IS NULL"
								.$str_whereDept
								." UNION ALL
								SELECT 1
								FROM osm_post_office p
								WHERE p.identifiant IS NOT NULL "
								.$str_whereDept
								.")a "
								."ORDER BY 2;";
		//echo $str_query;
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_all($rq);
		
		$str_resp = $res[0]['nb'].",".$res[1]['nb'].",".$str_resDept;
		
		$str_query = "SELECT date_en_clair
					FROM osm_version_base v
					WHERE categorie = 'postOffice';"; 
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_all($rq);
		$str_resp = $res[0]['date_en_clair'].",".$str_resp;

		$str_query = "SELECT round(avg(p.distance)) moy,max(p.distance) max
									FROM osm_post_office p
									WHERE distance is NOT NULL"
									.$str_whereDept
									.";";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_all($rq);
		$str_resp = $str_resp.",".$res[0]['moy'].",".$res[0]['max'];
	}
	
//	header('Content-Type: text/plain; charset=utf-8');
	echo($str_resp);

 ?>