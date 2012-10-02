 <?php
 	include 'connexion.php';

// 3 categories de donn褳 :
// - donn褳 hors table osm et hors table statut => icone a editer
// - donn褳 hors table osm et dans table statut plus ancienne que maj => icone a editer
// - donn褳 hors table osm et dans table statut plus recente que la maj = > a masquer
// - donn褳 dans table osm => icone �isualiser
	if ($_GET['mode'] == 'postOffice'){
		$str_query = "SELECT d.latitude||','||d.longitude point,
							d.libelle_site title,
							d.identifiant description,
							'source' source,
							'lib/img/marker.png' icon,
							'0,0' offset_icon
						FROM od_postes_dept d LEFT OUTER JOIN osm_post_office p
						ON	d.identifiant = p.identifiant
						JOIN od_statut_donnees s
						ON d.identifiant = s.od_id
						CROSS JOIN osm_version_base v
						WHERE d.code_dept = '".$_GET['dept']."' AND
								p.osm_id IS NULL AND
								v.categorie = 'postOffice' AND
								v.timestamp > s.timestamp
						UNION
						SELECT d.latitude||','||d.longitude point,
							d.libelle_site title,
							d.identifiant description,
							'osm' source,
							'lib/img/marker-green.png' icon,
							'0,0' offset_icon
						FROM od_postes_dept d LEFT OUTER JOIN osm_post_office p
						ON	d.identifiant = p.identifiant
						JOIN od_statut_donnees s
						ON d.identifiant = s.od_id
						CROSS JOIN osm_version_base v
						WHERE d.code_dept = '".$_GET['dept']."' AND
								p.osm_id IS NULL AND
								v.categorie = 'postOffice' AND
								v.timestamp < s.timestamp
						UNION
						SELECT d.latitude||','||d.longitude,
							d.libelle_site,
							d.identifiant,
							'source',
							'lib/img/marker.png',
							'0,0'
						FROM od_postes_dept d LEFT OUTER JOIN osm_post_office p
						ON	d.identifiant = p.identifiant
						LEFT OUTER JOIN od_statut_donnees s
						ON d.identifiant = s.od_id
						WHERE d.code_dept = '".$_GET['dept']."' AND
								p.osm_id IS NULL AND
								s.timestamp IS NULL
						UNION
						SELECT d.latitude||','||d.longitude,
							d.identifiant,
							d.identifiant,
							'osm',
							'lib/img/marker-gold.png',
							'-10,-35'
						FROM osm_post_office d
						WHERE d.identifiant is NOT NULL AND
								d.code_dept = '".$_GET['dept']
						."' ORDER BY source DESC;";
					
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