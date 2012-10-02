 <?php
 	include 'connexion.php';
	
	if ($_GET['mode'] == 'postOffice'){
		$str_whereDept = "";
		$str_resDept = "";
		if (preg_match('/^[0-9][0-9,A-B]/',$_GET['dept'])){
			$str_whereDept = " AND code_dept = '".$_GET['dept']."' ";
			$str_resDept = ','.$_GET['dept'];
		}
		$str_query = "SELECT count(*) nb,1 ref_statut
					FROM od_statut_donnees JOIN od_postes_dept
					ON od_id = identifiant
					WHERE ref_statut = 1"
					.$str_whereDept
					." UNION ALL
					SELECT count(*),2
					FROM od_statut_donnees JOIN od_postes_dept
					ON od_id = identifiant
					WHERE ref_statut = 2"
					.$str_whereDept
					." UNION ALL
					SELECT count(*),3
					FROM od_postes_dept
					WHERE code_dept = code_dept"
					.$str_whereDept
					." ORDER BY 2;";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_all($rq);
		
		$str_resp = $res[0]['nb'].",".$res[1]['nb'].",".$res[2]['nb'].$str_resDept;
	}
	
//	header('Content-Type: text/plain; charset=utf-8');
	echo($str_resp);

 ?>