<?php
	include 'connexion.php';

	$url_tmp = "";

	if ($_GET['mode'] == 'insee'){
		$insee = "'".$_GET['insee']."'";
	
		$str_query = "SELECT count(*) nb
									FROM	historique h CROSS JOIN version_base v
									WHERE	h.insee = ".$insee." AND 
												h.timestamp > v.timestamp;";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		$res = pg_fetch_assoc($rq,0);
		$num_hist = $res['nb'];

		$str_query = "INSERT INTO	historique(insee,timestamp)
								VALUES(".$insee.",".time().");";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
	
		//Pop
		$place = 'village';
		if ($_GET['pop'] < 100) $place = 'hamlet';
		if ($_GET['pop'] == 0) $place = 'locality';
		if ($_GET['pop'] >= 10000) $place = 'town';
		if ($_GET['pop'] >= 100000) $place = 'city';
	
		//XML
		$tmpdir = dirname(dirname(__FILE__))."/tmp/";
		$tmpfile = $tmpdir.basename(tempnam(".","")).".osm";
		
		$fp = fopen($tmpfile,'w');
		fwrite($fp,"<?xml version='1.0' encoding='UTF-8'?>\n");
		fwrite($fp,"<osm version='0.6' upload='true'>\n");
		fwrite($fp,"\t<node id='-1' action='modify' visible='true' lat='".$_GET['y']."' lon='".$_GET['x']."'>\n");
		fwrite($fp,"\t\t<tag k='name' v=\"".stripslashes($_GET['name'])."\" />\n");
		fwrite($fp,"\t\t<tag k='place' v='".$place."' />\n");
		fwrite($fp,"\t\t<tag k='population' v='".$_GET['pop']."' />\n");
		fwrite($fp,"\t\t<tag k='ref:INSEE' v=".$insee." />\n");
		fwrite($fp,"\t</node>\n");
		fwrite($fp,"</osm>");
		fclose($fp);
		
		$url_tmp = "http://".$_SERVER['SERVER_NAME']."/geofla/tmp/".basename($tmpfile,'');
	}
	if ($_GET['mode'] == 'postOffice'){
		$id = "'".$_GET['id']."'";
	
		$str_query = "SELECT count(*) nb
									FROM	od_historique h CROSS JOIN version_base v
									WHERE	h.od_id = ".$id." AND 
												h.timestamp > v.timestamp;";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		$res = pg_fetch_assoc($rq,0);
		$num_hist = $res['nb'];

		$str_query = "INSERT INTO	od_historique(od_id,categorie,timestamp)
								VALUES(".$id.",'postOffice',".time().");";
		$rq = pg_query($GLOBALS['pgc'],$str_query);
	}
		

	$str_xml = 	"<?xml version='1.0' encoding='UTF-8'?>";
	$str_xml = $str_xml."<reponse>";
	$str_xml = $str_xml."<nbh>".$num_hist."</nbh>";
	$str_xml = $str_xml."<url>".$url_tmp."</url>";
	$str_xml = $str_xml."</reponse>";

	header('Content-Type: text/xml; charset=utf-8');
	echo($str_xml);
?>