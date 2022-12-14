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
	//print_r($pop_info);
	$key_population = $tmp[5];
	
	
	$host = 'XXXXXX';
	$user = 'XXXXXX';
	$pass = 'XXXXXX';
	$db = 'XXXXXX';
	$immgen_data_group = 'ImmGen_data_group_20191122';
	$immgen_data_group_global = 'ImmGen_data_group_maximum_20191122';
	$key_pop_info = array('LTHSC_34-_BM','MMP2_150+48+_BM','proB_CLP_BM','proB_FrBC_BM','B_Fo_Sp','B_MZ_Sp','B_GC_CC_Sp','B_PC_Sp','B1b_PC','preT_DN1_Th','T_DP_Th','T_4_Nve_Sp','NKT_Sp','Treg_4_25hi_Sp','T_8_Nve_Sp','T8_TE_LCMV_d7_Sp','T8_Tem_LCMV_d180_Sp','Tgd_g2+d1_24a+_Th','Tgd_g2+d17_LN','Tgd_Sp','NK_27-11b+_Sp','ILC2_SI','ILC3_NKp46-CCR6-_SI','ILC3_NKp46+_SI','DC_8+_Sp','DC_pDC_Sp','GN_BM','GN_Thio_PC','MC_heparinase_PC','MF_PC','MF_Alv_Lu','Ep_MEChi_Th','LEC_SLN','IAP_SLN');
	$key_pop_info_OSMNP = array('MF_64pLYVEpIIp_Ao','EMP_E10_5_YS','DC_24p8anXCR1n_Sp','DC_XCR1nSIRPap_Th','MF_115pICAM2p226n6Cn_PC','Mo_6Cp_Bl','MF_E16_5_PC','MF_KC_Clec4FpTim4p64p_Lv','MF_11cpSigFp_BAL','Mo_6Cp_Lu','MF_CCR2p64p6ClonGFPp_Ht','MF_45lo_CNS','MF_microglia_CNS','DC_8ap_PP','MF_DRG','MF_B220n6Cn64pTim4p_PC');
	$group_info = '';
	
	$con = mysqli_connect($host, $user, $pass, $db) or die("Can not connect." . mysqli_connect_error());
	
	if($type=='rnaseq'){
		
		if($datagroup == 'ImmGen%20ULI%20RNASeq'){
			
			if($key_population == 'Reference%20populations'){
				$rnaseq_meta_query = "SELECT short_name, long_name, description FROM ".$immgen_data_group." WHERE data_group = 'ImmGen ULI RNASeq' AND population IN ('".implode("','",$key_pop_info)."') ORDER BY `order`";
			}
			else{
				$rnaseq_meta_query = "SELECT short_name, long_name, description FROM ".$immgen_data_group." WHERE data_group = 'ImmGen ULI RNASeq' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
			}
		}
		else if($datagroup == 'ImmGen%20MNP%20OpenSource'){
			
			if($key_population == 'Reference%20populations'){
				$rnaseq_meta_query = "SELECT short_name, long_name, description FROM ".$immgen_data_group." WHERE data_group = 'ImmGen MNP OpenSource' AND population IN ('".implode("','",$key_pop_info_OSMNP)."') ORDER BY `order`";
			}
			else{
				$rnaseq_meta_query = "SELECT short_name, long_name, description FROM ".$immgen_data_group." WHERE data_group = 'ImmGen MNP OpenSource' AND population IN ('".implode("','",$pop_info)."') ORDER BY `order`";
			}
		}
		else if($datagroup == 'Male/Female%20RNASeq') {
			
		}
		
		
		#echo $rnaseq_expr_query;
		$meta_result = mysqli_query($con, $rnaseq_meta_query);
		
	}
	
	
	$filename = "meta_table.csv";
	$delimiter = ",";
	
	// output headers so that the file is downloaded rather than displayed
	 //ob_clean();
	 header('Content-Type: application/csv');
	 header('Content-Disposition: attachment;filename='.$filename); 
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	
	$outnames = array('Population Name', 'Population Long Name', 'Description');
	// output the column headings
	fputcsv($output, $outnames);
	//echo $filename; 
	
	// loop over the rows, outputting them
	while ($row = mysqli_fetch_row($meta_result)) {
		fputcsv($output, $row);
	}
	exit;
	
	
?>
