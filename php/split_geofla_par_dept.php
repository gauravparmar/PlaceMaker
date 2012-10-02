<?php

include 'connexion.php';
include 'fonctions.php';

$str_query = "SELECT code_dept,nom_dept,round(ST_XMin(g)) xmin,round(ST_XMax(g)) xmax,round(ST_YMin(g)) ymin,round(ST_YMax(g)) ymax
							FROM
							(
								SELECT 	distinct d.code_dept,d.nom_dept,ST_Transform(ST_Envelope(d.wkb_geometry),900913) g
								FROM	tous_departements d
							)a
							ORDER BY substr(code_dept,1,1)::integer,substr(code_dept,2,1),substr(code_dept,3,1);";

$rq = pg_query($GLOBALS['pgc'],$str_query);
$res_emprises = pg_fetch_all($rq);

// liste des depts pour le .js
	$a_depts = array();
	$marge = 0;
	foreach ($res_emprises as $d){
		$a_depts[$d['code_dept']] = array($d['xmin']-$marge,$d['ymin']-$marge,$d['xmax']+$marge,$d['ymax']+$marge,trim(ucwords(strtolower($d['nom_dept']))));
	}
	$str_file = "../data/emprise_depts.js";
	$fp = fopen($str_file,"w");
	fwrite($fp,"liste_depts_emprise = ".json_encode($a_depts).";\n");
	fclose($fp);

?>