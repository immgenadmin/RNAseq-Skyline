<?php
	session_start ();    
	
	$probeset_id = $_POST['probeset_id'];
	$datagroup = $_POST['datagroup'];
	if(isset($_POST['population']) ) { $pop_info=explode(",", $_POST['population']); }
	$key_population = $_POST['key_population'];
	
	$host = 'XXXXXX';
	$user = 'XXXXXX';
	$pass = 'XXXXXX';
	$db = 'XXXXXX';
	$immgen_data_group = 'ImmGen_data_group_20191122';
	$immgen_data_group_global = 'ImmGen_data_group_maximum_20191122';
	$key_pop_info = array('LTHSC_34-_BM','MMP2_150+48+_BM','proB_CLP_BM','proB_FrBC_BM','B_Fo_Sp','B_MZ_Sp','B_GC_CC_Sp','B_PC_Sp','B1b_PC','preT_DN1_Th','T_DP_Th','T_4_Nve_Sp','NKT_Sp','Treg_4_25hi_Sp','T_8_Nve_Sp','T8_TE_LCMV_d7_Sp','T8_Tem_LCMV_d180_Sp','Tgd_g2+d1_24a+_Th','Tgd_g2+d17_LN','Tgd_Sp','NK_27-11b+_Sp','ILC2_SI','ILC3_NKp46-CCR6-_SI','ILC3_NKp46+_SI','Mo_6C+II-_Bl','DC_8+_Sp','DC_pDC_Sp','GN_BM','GN_Thio_PC','MC_heparinase_PC','MF_PC','MF_RP_Sp','MF_microglia_CNS','MF_Alv_Lu','Ep_MEChi_Th','LEC_SLN','IAP_SLN');
	$key_pop_info_OSMNP = array('MF_64pLYVEpIIp_Ao','MF_E10_5_YS','DC_24p8anXCR1n_Sp','DC_XCR1nSIRPap_Th','MF_115pICAM2p226n6Cn_PC','Mo_6Cp_Bl','MF_E16_5_PC','MF_KC_Clec4FpTim4p64p_Lv','MF_11cpSigFp_BAL','Mo_6Cp_Lu','MF_CCR2p64p6ClonGFPp_Ht','MF_45lo_CNS','MF_microglia_CNS','DC_8ap_PP','MF_DRG','MF_B220n6Cn64pTim4p_PC');
	

	$con = mysqli_connect($host, $user, $pass, $db) or die("Can not connect." . mysqli_connect_error());
	
		if($datagroup == 'ImmGen ULI RNASeq'){
			$rnaseq_table = 'ImmGen_ATAC_ULI_RNAseq_expr_20200610';
			
			if($key_population == 'Reference populations'){
				$rnaseq_population_query = "SELECT population, color, short_name, long_name, description, author, number_of_sample, sorting_info, color_unique_set FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$key_pop_info)."') ORDER BY `order`";
				$rnaseq_population_group_query = "SELECT count(distinct(`population_group`)) FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$key_pop_info)."') ORDER BY `order`";
			}
			else{
				$rnaseq_population_query = "SELECT population, color, short_name, long_name, description, author, number_of_sample, sorting_info, color_unique_set FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
				$rnaseq_population_group_query = "SELECT count(distinct(`population_group`)) FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
			}
		}
		else if($datagroup == 'ImmGen MNP OpenSource'){
			$rnaseq_table = 'ImmGen_OSMNP_ULI_RNAseq_expr_20200610';
			
			if($key_population == 'Reference populations'){
				$rnaseq_population_query = "SELECT population, color, short_name, long_name, description, author, number_of_sample, sorting_info, color_unique_set FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$key_pop_info_OSMNP)."') ORDER BY `order`";
				$rnaseq_population_group_query = "SELECT count(distinct(`population_group`)) FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$key_pop_info_OSMNP)."') ORDER BY `order`";
			}
			else{
				$rnaseq_population_query = "SELECT population, color, short_name, long_name, description, author, number_of_sample, sorting_info, color_unique_set FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
				$rnaseq_population_group_query = "SELECT count(distinct(`population_group`)) FROM ".$immgen_data_group." WHERE data_group = '".$datagroup."' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
			}
		}
		
		$rnaseq_meta_table = 'ImmGen_RNAseq_meta_info_20200610';
		$rnaseq_kegg_table = 'ImmGen_RNAseq_meta_info_Entrez2KEGG';
		$rnaseq_orthology_table = 'ImmGen_RNAseq_meta_info_Human_Ortholog_20220121';		
		$rnaseq_go_table = 'ImmGen_RNAseq_meta_info_GO_20200610';
		$rnaseq_biolegend_table = 'ImmGen_RNAseq_meta_info_BioLegend';
		
		$rnaseq_expr_query = "SELECT * FROM ".$rnaseq_table." AS r, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Gene_Symbol_MGI = r.Gene_Symbol";
		$rnaseq_expr_global_query = "SELECT population, value FROM ".$immgen_data_group_global." WHERE data_group = '".$datagroup."'";
		$rnaseq_column_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$rnaseq_table."'";
		$rnaseq_meta_query = "SELECT * FROM ".$rnaseq_meta_table." WHERE Gene_Symbol_MGI = '".$probeset_id."'";
		$rnaseq_kegg_query = "SELECT k.* FROM ".$rnaseq_kegg_table." AS k, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Entrez_Gene_ID = k.Entrez_Gene_ID";
		$rnaseq_go_query = "SELECT distinct(g.GO_ID), g.Category, g.GO_Name FROM ".$rnaseq_go_table." AS g, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.MGI_ID = g.MGI_ID ORDER BY g.Category DESC, g.GO_ID ASC";
		$rnaseq_orthology_query = "SELECT o.* FROM ".$rnaseq_orthology_table." AS o, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.MGI_ID = o.MGI_ID";
		$rnaseq_biolegend_query = "SELECT b.* FROM ".$rnaseq_biolegend_table." AS b, ".$rnaseq_orthology_table." AS o, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.MGI_ID = o.MGI_ID AND o.Human_EntrezID = b.Human_EntrezID";
		
		$rnaseq_population_result = mysqli_query($con, $rnaseq_population_query);
		$rnaseq_population_group_result = mysqli_query($con, $rnaseq_population_group_query);
		$rnaseq_expr_result = mysqli_query($con, $rnaseq_expr_query);
		$rnaseq_expr_global_result = mysqli_query($con, $rnaseq_expr_global_query);
		$rnaseq_column_result = mysqli_query($con, $rnaseq_column_query);
		$rnaseq_meta_result = mysqli_query($con, $rnaseq_meta_query);
		$rnaseq_kegg_result = mysqli_query($con, $rnaseq_kegg_query);
		$rnaseq_go_result = mysqli_query($con, $rnaseq_go_query);
		$rnaseq_orthology_result = mysqli_query($con, $rnaseq_orthology_query);
		$rnaseq_biolegend_result = mysqli_query($con, $rnaseq_biolegend_query);
		
		$rnaseq_column_index = array();
		$rnaseq_index = 0;
		while ($r1 = mysqli_fetch_row($rnaseq_column_result)) {
			$rnaseq_column_index[$r1[0]] = $rnaseq_index;
			$rnaseq_index = $rnaseq_index + 1;
		}
		
		$rnaseq_expr = mysqli_fetch_array($rnaseq_expr_result);
		
		$rnaseq_max = 0;
		while ($r2 = mysqli_fetch_row($rnaseq_expr_global_result)) {
			if(floatval($r2[1]) > $rnaseq_max){
				$rnaseq_max = floatval($r2[1]);	
			}
		}
		
		$rnaseq_meta = mysqli_fetch_assoc($rnaseq_meta_result);
		
		$kegg = array();
		while ($r3 = mysqli_fetch_row($rnaseq_kegg_result)) {
			array_push($kegg, array('kegg_id' => $r3[1], 'kegg_name' => $r3[2]));	
		}
				
		$go = array();
		while ($r4 = mysqli_fetch_row($rnaseq_go_result)) {
			array_push($go, array('go_id' => $r4[0], 'go_category' => $r4[1], 'go_name' => $r4[2]));	
		}
		
		if(mysqli_num_rows($rnaseq_orthology_result) > 0 ) { $orthology = mysqli_fetch_assoc($rnaseq_orthology_result); } else { $orthology = 'None'; }
		if(mysqli_num_rows($rnaseq_biolegend_result) > 0 ) { $biolegend = mysqli_fetch_assoc($rnaseq_biolegend_result); } else { $biolegend = 'None'; }
		
		$r5 = mysqli_fetch_row($rnaseq_population_group_result);
		$ranseq_population_group_num = $r5[0];
		
		$json = array();
		while ($r = mysqli_fetch_row($rnaseq_population_result)) {
			array_push($json, array('population' => $r[0], 'start' => 0, 'value' => floatval($rnaseq_expr[$rnaseq_column_index[$r[0]]]), 'color' => $r[1], 'short_name' => $r[2], 'long_name' => $r[3], 'description' => $r[4], 'author' => $r[5], 'number_of_sample' => $r[6], 'sorting_info' => $r[7], 'color_unique_set' => $r[8]));	
		}
		$output = array('data'=>$json, 'meta'=>$rnaseq_meta, 'kegg'=>$kegg, 'go'=>$go, 'orthology'=>$orthology, 'biolegend'=>$biolegend,  'max'=>$rnaseq_max, 'pop_group_num'=>$ranseq_population_group_num);
		echo json_encode($output);

?>
