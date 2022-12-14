<?php
	session_start ();    
	
	$url = $_SERVER['REQUEST_URI'];
	$tmp = explode("=", $url);
	$tmp1 = explode("&", $tmp[1]);
	$type = $tmp1[0];
	$tmp2 = explode("&", $tmp[2]);
	$probeset_id = $tmp2[0];
	$tmp3 = explode("&", $tmp[3]);
	$datagroup = $tmp3[0];
	$tmp4 = explode("&", $tmp[4]);
	$population = $tmp4[0];
	$pop_info=explode(",", $population);
	$key_population = $tmp[5];
	
	
	$host = 'XXXXXX';
	$user = 'XXXXXX';
	$pass = 'XXXXXX';
	$db = 'XXXXXX';
	$immgen_data_group = 'ImmGen_data_group_20191122';
	$immgen_data_group_global = 'ImmGen_data_group_maximum_20191122';
	$rnaseq_meta_table = 'ImmGen_RNAseq_meta_info_20200610';
	$key_pop_info = array('LTHSC_34-_BM','MMP2_150+48+_BM','proB_CLP_BM','proB_FrBC_BM','B_Fo_Sp','B_MZ_Sp','B_GC_CC_Sp','B_PC_Sp','B1b_PC','preT_DN1_Th','T_DP_Th','T_4_Nve_Sp','NKT_Sp','Treg_4_25hi_Sp','T_8_Nve_Sp','T8_TE_LCMV_d7_Sp','T8_Tem_LCMV_d180_Sp','Tgd_g2+d1_24a+_Th','Tgd_g2+d17_LN','Tgd_Sp','NK_27-11b+_Sp','ILC2_SI','ILC3_NKp46-CCR6-_SI','ILC3_NKp46+_SI','DC_8+_Sp','DC_pDC_Sp','GN_BM','GN_Thio_PC','MC_heparinase_PC','MF_PC','MF_Alv_Lu','Ep_MEChi_Th','LEC_SLN','IAP_SLN');
	$key_pop_info_OSMNP = array('MF_64pLYVEpIIp_Ao','MF_E10_5_YS','DC_24p8anXCR1n_Sp','DC_XCR1nSIRPap_Th','MF_115pICAM2p226n6Cn_PC','Mo_6Cp_Bl','MF_E16_5_PC','MF_KC_Clec4FpTim4p64p_Lv','MF_11cpSigFp_BAL','Mo_6Cp_Lu','MF_CCR2p64p6ClonGFPp_Ht','MF_45lo_CNS','MF_microglia_CNS','DC_8ap_PP','MF_DRG','MF_B220n6Cn64pTim4p_PC');
	$group_info = '';
	
	$con = mysqli_connect($host, $user, $pass, $db) or die("Can not connect." . mysqli_connect_error());
	
	if($type=='rnaseq'){
		
		if($datagroup == 'ImmGen%20ULI%20RNASeq'){
			$rnaseq_table = 'ImmGen_ATAC_ULI_RNAseq_expr_20200610';
			
			if($key_population == 'Reference%20populations'){
				$rnaseq_expr_query = "SELECT m.Gene_Symbol_MGI, `".implode("`, r.`",$key_pop_info)."` FROM ".$rnaseq_table." AS r, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Gene_Symbol = r.Gene_Symbol";
				$group_info = $key_pop_info;
			}
			else{
				$rnaseq_expr_query = "SELECT m.Gene_Symbol_MGI, `".implode("`, r.`",$pop_info)."` FROM ".$rnaseq_table." AS r, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Gene_Symbol = r.Gene_Symbol";
				$group_info = $pop_info;
			}
		}
		else if($datagroup == 'ImmGen%20MNP%20OpenSource'){
			$rnaseq_table = 'ImmGen_OSMNP_ULI_RNAseq_expr_20200610';
			
			if($key_population == 'Reference%20populations'){
				$rnaseq_expr_query = "SELECT m.Gene_Symbol_MGI, `".implode("`, r.`",$key_pop_info_OSMNP)."` FROM ".$rnaseq_table." AS r, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Gene_Symbol = r.Gene_Symbol";
				$group_info = $key_pop_info_OSMNP;
			}
			else{
				$rnaseq_expr_query = "SELECT m.Gene_Symbol_MGI, `".implode("`, r.`",$pop_info)."` FROM ".$rnaseq_table." AS r, ".$rnaseq_meta_table." AS m WHERE m.Gene_Symbol_MGI = '".$probeset_id."' AND m.Gene_Symbol = r.Gene_Symbol";
				$group_info = $pop_info;
			}
		}
		else if($datagroup == 'Male/Female%20RNASeq') {
			$rnaseq_table = 'ImmGen_RNAseq_expr';
			
			$rnaseq_population_query = "SELECT population FROM ".$immgen_data_group." WHERE data_group = 'Male/Female RNASeq' ORDER BY `order`";
			$rnaseq_population_result = mysql_query($rnaseq_population_query,$con);
			$rnaseq_populations = array();
			while ($r2 = mysql_fetch_row($rnaseq_population_result)) {
				array_push($rnaseq_populations, $r2[0]);
			}
			
			$rnaseq_expr_query = "SELECT Gene_Symbol, `".implode("`, `",$rnaseq_populations)."` FROM ".$rnaseq_table." WHERE Gene_Symbol = '".$probeset_id."'";
			$group_info = $rnaseq_populations;
		}
		
		
		$expr_result = mysqli_query($con, $rnaseq_expr_query);
		
		$headers = array('Gene_Symbol');
	}
	
	
	$filename = "expr_table.csv";
	$delimiter = ",";
	
	// output headers so that the file is downloaded rather than displayed
	 //ob_clean();
	 header('Content-Type: application/csv');
	 header('Content-Disposition: attachment;filename='.$filename); 
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	
	$outnames = array_merge($headers, $group_info);
	// output the column headings
	fputcsv($output, $outnames);
	
	// loop over the rows, outputting them
	while ($row = mysqli_fetch_row($expr_result)) {
		fputcsv($output, $row);
	}
	exit;
	
	
?>
