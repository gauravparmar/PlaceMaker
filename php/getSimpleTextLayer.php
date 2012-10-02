 <?php
 	include 'connexion.php';

// 3 categories de donn褳 :
// - donn褳 hors table osm et hors table statut => icone a editer
// - donn褳 hors table osm et dans table statut plus ancienne que maj => icone a editer
// - donn褳 hors table osm et dans table statut plus recente que la maj = > a masquer
// - donn褳 dans table osm => icone �isualiser
	if ($_GET['mode'] == 'postOffice'){
		$str_query = "SELECT p.latitude||','||p.longitude point,
							'foo' title,
							'foo' description,
							'osm_only' source,
							'lib/img/marker-blue-reverse.png' icon,
							'-10,0' offset_icon
						FROM osm_post_office p
						WHERE p.code_dept = '".$_GET['dept']."' AND
								p.identifiant IS NULL;";
					
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		$res = pg_fetch_all($rq);
	}
	
	$str_resp = "point\ttitle\tdescription\ticon\ticonOffset\n";
	foreach ($res as $r){
		$str_resp = $str_resp.$r['point']."\t".$r['title']."\t<div class=\"desc_main\" id=\"".$r['description']."\" source=\"".$r['source']."\"></div>\t".$r['icon']."\t".$r['offset_icon']."\n";
	}
 
//	header('Content-Type: text/plain; charset=utf-8');
	echo($str_resp);

 ?>