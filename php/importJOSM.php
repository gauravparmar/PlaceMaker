<?php
	include 'connexion.php';
	
	if ($_GET['mode'] == 'postOffice' && $_GET['id']){
		$str_query = "SELECT *
						FROM od_postes_dept
						WHERE identifiant = '".$_GET['id']."';";
			
		$rq = pg_query($GLOBALS['pgc'],$str_query);
		if (!$rq) die;
		if (pg_num_rows($rq) == 0) die;
		
		$res = pg_fetch_assoc($rq,0);
	
		$x = $res['longitude'];
		$y = $res['latitude'];
		$ref = $res['identifiant'];
		$a_lib = explode(' ',$res['libelle_site']);
		if (count($a_lib) > 1){
			if ($a_lib[count($a_lib)-1] == 'A'){
				$a_lib[count($a_lib)-1] = 'ANNEXE';
			}
			if ($a_lib[count($a_lib)-1] == 'PAL'){
				$a_lib[count($a_lib)-1] = 'PRINCIPAL';
			}
		}
		$lib = implode(' ',$a_lib);
		$pOname = ucwords(strtolower($lib));

		$a_note = array();
		/*
		if ($res['complement_adresse'] != 'null'){
			$a_note[] = $res['complement_adresse'];
		}
		if ($res['adresse'] != 'null'){
			$a_note[] = $res['adresse'];
		}
		if ($res['lieu_dit'] != 'null'){
			$a_note[] = $res['lieu_dit'];
		}
		*/
		if ($res['complement_adresse'] && strtolower($res['complement_adresse']) != 'null'){
			$a_note[] = $res['complement_adresse'];
		}
		if ($res['adresse'] && strtolower($res['adresse']) != 'null'){
			$a_note[] = $res['adresse'];
		}
		if ($res['lieu_dit'] && strtolower($res['lieu_dit']) != 'null'){
			$a_note[] = $res['lieu_dit'];
		}
		$str_josm = "<?xml version='1.0' encoding='UTF-8'?>";
		$str_josm = $str_josm."<osm version='0.6' upload='true'>";
		$str_josm = $str_josm."<node id='-1' action='modify' visible='true' lat='".$y."' lon='".$x."'>";
		$str_josm = $str_josm."<tag k='addr:postcode' v='".$res['code_postal']."' />";
		$str_josm = $str_josm."<tag k='amenity' v='post_office' />";
// atm
		if ($res['dab'] == 'O'){
			$str_josm = $str_josm."<tag k='atm' v='yes' />";
		}
// change_machine
		if ($res['changeur_monnaie'] == 'O'){
			$str_josm = $str_josm."<tag k='change_machine' v='yes' />";
		}
// copy_facility
		if ($res['photocopieur '] == 'O'){
			$str_josm = $str_josm."<tag k='copy_facility' v='yes' />";
		}
// moneo:loading
		if ($res['recharge_moneo'] == 'O'){
			$str_josm = $str_josm."<tag k='moneo:loading' v='yes' />";
		}
// note
		if (count($a_note) > 0){
			$str_josm = $str_josm."<tag k='note' v='situation : ".implode(' - ',$a_note)."' />";
		}
// operator
		$str_josm = $str_josm."<tag k='operator' v='La Poste' />";
// phone
		if ($res['telephone'] != 'null'){
			$str_josm = $str_josm."<tag k='phone' v='".$res['telephone']."' />";
		}
// post_office:name
		$str_josm = $str_josm."<tag k='name' v=\"".stripslashes($pOname)."\" />";
// post_office:type
		if ($res['caracteristique_site'] == 'AGENCE POSTALE COMMUNALE'){
			$str_josm = $str_josm."<tag k='post_office:type' v='post_annex' />";
		}
		if ($res['caracteristique_site'] == 'RELAIS POSTE COMMERCANT'){
			$str_josm = $str_josm."<tag k='post_office:type' v='post_partner' />";
		}
// ref
		$str_josm = $str_josm."<tag k='ref:FR:LaPoste' v='".$ref."' />";
// source
		$str_josm = $str_josm."<tag k='source' v='data.gouv.fr:LaPoste - 04/2012' />";

// stamping_machine
		if ($res['affranchissement_libre_service'] == 'O'){
			$str_josm = $str_josm."<tag k='stamping_machine' v='yes' />";
		}

		$str_josm = $str_josm."</node>";
		$str_josm = $str_josm."</osm>";
	}

	if ($str_josm){
		header('Content-Type: text/xml; charset=utf-8');
		echo($str_josm);
	}
?>