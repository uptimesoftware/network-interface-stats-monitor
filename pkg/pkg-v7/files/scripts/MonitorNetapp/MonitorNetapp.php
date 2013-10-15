<?php
	//error_reporting(E_ERROR | E_WARNING);
	$NetApp_Host = getenv('UPTIME_HOSTNAME');
	$NetApp_Port = getenv('UPTIME_SNMP-PORT');
	$NetApp_Community = getenv('UPTIME_READ-COMMUNITY');
	$SNMP_version = getenv('UPTIME_SNMPVERSION');
	$SNMP_v3_agent = getenv('UPTIME_AGENT-USERNAME');
	$SNMP_v3_auth_type = getenv('UPTIME_AUTH-TYPE');
	$SNMP_v3_auth_pass = getenv('UPTIME_AUTH-PASS');
	$SNMP_v3_priv_type = getenv('UPTIME_PRIVACY-TYPE');
	$SNMP_v3_priv_pass = getenv('UPTIME_PRIVACY-PASS');
	$MONITOR_TIMEOUT = getenv('UPTIME_TIMEOUT');
	
	$LAST_VALUE_FILE = "Netapp_Last_Value.".$NetApp_Host.".txt";
	$CURRENT_TIME = time();
	
	$NetApp_Connection_String = $NetApp_Host . ":" . $NetApp_Port;
	
	if (!extension_loaded("snmp")) {
		echo "PHP SNMP Extension not loaded!";
		exit(2);
	}
	
	
	if($SNMP_version == "v1") {
		if ($NetApp_Community == "") {
			echo "Please enter the SNMP community string.";
			exit(2);
		}
		$df_name = snmpwalk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.2");
		$lunName = snmpwalk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.17.15.2.1.2");
		$lunName = parseData($lunName);
		
		$dfUsedKBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.16");
		$dfUsedKBytesHigh = parseData($dfUsedKBytesHigh);
        $dfUsedKBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.7");
		$dfUsedKBytesLow = parseData($dfUsedKBytesLow);

        $fcpReadBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.3");
		$fcpReadBytesLow = parseData($fcpReadBytesLow);
        $fcpReadBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.4");
		$fcpReadBytesHigh = parseData($fcpReadBytesHigh);
        $fcpWriteBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.5");
		$fcpWriteBytesLow = parseData($fcpWriteBytesLow);
        $fcpWriteBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.6");
		$fcpWriteBytesHigh = parseData($fcpWriteBytesHigh);
		

        $iscsiReadBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.7");
		$iscsiReadBytesLow = parseData($iscsiReadBytesLow);
        $iscsiReadBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.8");
		$iscsiReadBytesHigh = parseData($iscsiReadBytesHigh);
        $iscsiWriteBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.9");
		$iscsiWriteBytesLow = parseData($iscsiWriteBytesLow);
        $iscsiWriteBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.10");
		$iscsiWriteBytesHigh = parseData($iscsiWriteBytesHigh);

        $iscsiOpsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.12");
		$iscsiOpsLow = parseData($iscsiOpsLow);
        $iscsiOpsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.11");
		$iscsiOpsHigh = parseData($iscsiOpsHigh);
        $fcpOpsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.14");
		$fcpOpsLow = parseData($fcpOpsLow);
        $fcpOpsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.13");
		$fcpOpsHigh = parseData($fcpOpsHigh);
		
        $lunReadBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.11");
		$lunReadBytesHigh = parseData($lunReadBytesHigh);
        $lunReadBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.12");
		$lunReadBytesLow = parseData($lunReadBytesLow);
        $lunWriteBytesHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.13");
		$lunWriteBytesHigh = parseData($lunWriteBytesHigh);
        $lunWriteBytesLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.14");
		$lunWriteBytesLow = parseData($lunWriteBytesLow);

        $lunErrorsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.15");
		$lunErrorsHigh = parseData($lunErrorsHigh);
        $lunErrorsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.16");
		$lunErrorsLow = parseData($lunErrorsLow);
        $lunReadOpsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.22");
		$lunReadOpsHigh = parseData($lunReadOpsHigh);
        $lunReadOpsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.23");
		$lunReadOpsLow = parseData($lunReadOpsLow);
        $lunWriteOpsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.24");
		$lunWriteOpsHigh = parseData($lunWriteOpsHigh);
        $lunWriteOpsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.25");
		$lunWriteOpsLow = parseData($lunWriteOpsLow);
        $lunOtherOpsHigh = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.26");
		$lunOtherOpsHigh = parseData($lunOtherOpsHigh);
        $lunOtherOpsLow = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.27");
		$lunOtherOpsLow = parseData($lunOtherOpsLow);

		//Syntax: INTEGER {unmounted(1), mounted(2), frozen(3), destroying(4), creating(5), mounting(6), unmounting(7), nofsinfo(8), replaying(9), replayed(10) }
        $dfStatus = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.20");
		$dfStatus = parseData($dfStatus);

		//Syntax: INTEGER {invalid(1), uninitialized(2), needcpcheck(3), cpcheckwait(4), unmirrored(5), normal(6), degraded(7), resyncing(8), failed(9), limbo(10) }
		$dfMirrorStatus = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.21");
		$dfMirrorStatus = parseData($dfMirrorStatus);

        $dfPercentInodeCapacity = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.9");
		$dfPercentInodeCapacity = parseData($dfPercentInodeCapacity);
		
		$dfPercentKBytesCapacity = snmpwalk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.6");
		$dfPercentKBytesCapacity = parseData($dfPercentKBytesCapacity);
	} elseif($SNMP_version == "v2") {
		if ($NetApp_Community == "") {
			echo "Please enter the SNMP community string.";
			exit(2);
		}
		$df_name = snmp2_walk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.2");
		$lunName = snmp2_walk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.17.15.2.1.2");
		$lunName = parseData($lunName);
		
		$dfUsedKBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.16");
		$dfUsedKBytesHigh = parseData($dfUsedKBytesHigh);
        $dfUsedKBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community,"1.3.6.1.4.1.789.1.5.4.1.7");
		$dfUsedKBytesLow = parseData($dfUsedKBytesLow);

        $fcpReadBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.3");
		$fcpReadBytesLow = parseData($fcpReadBytesLow);
        $fcpReadBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.4");
		$fcpReadBytesHigh = parseData($fcpReadBytesHigh);
        $fcpWriteBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.5");
		$fcpWriteBytesLow = parseData($fcpWriteBytesLow);
        $fcpWriteBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.6");
		$fcpWriteBytesHigh = parseData($fcpWriteBytesHigh);
		

        $iscsiReadBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.7");
		$iscsiReadBytesLow = parseData($iscsiReadBytesLow);
        $iscsiReadBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.8");
		$iscsiReadBytesHigh = parseData($iscsiReadBytesHigh);
        $iscsiWriteBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.9");
		$iscsiWriteBytesLow = parseData($iscsiWriteBytesLow);
        $iscsiWriteBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.10");
		$iscsiWriteBytesHigh = parseData($iscsiWriteBytesHigh);

        $iscsiOpsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.12");
		$iscsiOpsLow = parseData($iscsiOpsLow);
        $iscsiOpsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.11");
		$iscsiOpsHigh = parseData($iscsiOpsHigh);
        $fcpOpsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.14");
		$fcpOpsLow = parseData($fcpOpsLow);
        $fcpOpsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.13");
		$fcpOpsHigh = parseData($fcpOpsHigh);
		
        $lunReadBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.11");
		$lunReadBytesHigh = parseData($lunReadBytesHigh);
        $lunReadBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.12");
		$lunReadBytesLow = parseData($lunReadBytesLow);
        $lunWriteBytesHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.13");
		$lunWriteBytesHigh = parseData($lunWriteBytesHigh);
        $lunWriteBytesLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.14");
		$lunWriteBytesLow = parseData($lunWriteBytesLow);

        $lunErrorsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.15");
		$lunErrorsHigh = parseData($lunErrorsHigh);
        $lunErrorsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.16");
		$lunErrorsLow = parseData($lunErrorsLow);
        $lunReadOpsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.22");
		$lunReadOpsHigh = parseData($lunReadOpsHigh);
        $lunReadOpsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.23");
		$lunReadOpsLow = parseData($lunReadOpsLow);
        $lunWriteOpsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.24");
		$lunWriteOpsHigh = parseData($lunWriteOpsHigh);
        $lunWriteOpsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.25");
		$lunWriteOpsLow = parseData($lunWriteOpsLow);
        $lunOtherOpsHigh = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.26");
		$lunOtherOpsHigh = parseData($lunOtherOpsHigh);
        $lunOtherOpsLow = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.17.15.2.1.27");
		$lunOtherOpsLow = parseData($lunOtherOpsLow);

		//Syntax: INTEGER {unmounted(1), mounted(2), frozen(3), destroying(4), creating(5), mounting(6), unmounting(7), nofsinfo(8), replaying(9), replayed(10) }
        $dfStatus = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.20");
		$dfStatus = parseData($dfStatus);

		//Syntax: INTEGER {invalid(1), uninitialized(2), needcpcheck(3), cpcheckwait(4), unmirrored(5), normal(6), degraded(7), resyncing(8), failed(9), limbo(10) }
		$dfMirrorStatus = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.21");
		$dfMirrorStatus = parseData($dfMirrorStatus);

        $dfPercentInodeCapacity = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.9");
		$dfPercentInodeCapacity = parseData($dfPercentInodeCapacity);
		
		$dfPercentKBytesCapacity = snmp2_walk($NetApp_Connection_String,$NetApp_Community, "1.3.6.1.4.1.789.1.5.4.1.6");
		$dfPercentKBytesCapacity = parseData($dfPercentKBytesCapacity);
	}	elseif ($SNMP_version == "v3") {
	
		if ($SNMP_v3_agent == "") {
			echo "Please enter the SNMP v3 username";
			exit(2);
		}
	
		if ($SNMP_v3_priv_type == "") {
			if ($SNMP_v3_auth_type == "") {
				$SNMP_sec_level = "noAuthNoPriv";
			} else {
				$SNMP_sec_level = "authNoPriv";
				if ($SNMP_v3_auth_pass == "") {
					echo "Please enter the SNMP v3 authentication passphrase.";
					exit(2);
				}
			}
		} else {
			$SNMP_sec_level = "authPriv";
			if (($SNMP_v3_auth_pass == "") && ($SNMP_v3_priv_pass != "")) {
					echo "Please enter the SNMP v3 authentication passphrase.";
					exit(2);
			}
			if (($SNMP_v3_auth_pass != "") && ($SNMP_v3_priv_pass == "")) {
					echo "Please enter the SNMP v3 privacy passphrase.";
					exit(2);
			}
			if (($SNMP_v3_auth_pass == "") && ($SNMP_v3_priv_pass == "")) {
					echo "Please enter the SNMP v3 authentication & privacy passphrase.";
					exit(2);
			}
		}
		
		$df_name = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass,"1.3.6.1.4.1.789.1.5.4.1.2");
		$lunName = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass,"1.3.6.1.4.1.789.1.17.15.2.1.2");
		$lunName = parseData($lunName);
		
		$dfUsedKBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass,"1.3.6.1.4.1.789.1.5.4.1.16");
		$dfUsedKBytesHigh = parseData($dfUsedKBytesHigh);
        $dfUsedKBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass,"1.3.6.1.4.1.789.1.5.4.1.17");
		$dfUsedKBytesLow = parseData($dfUsedKBytesLow);

        $fcpReadBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.3");
		$fcpReadBytesLow = parseData($fcpReadBytesLow);
        $fcpReadBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.4");
		$fcpReadBytesHigh = parseData($fcpReadBytesHigh);
        $fcpWriteBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.5");
		$fcpWriteBytesLow = parseData($fcpWriteBytesLow);
        $fcpWriteBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.6");
		$fcpWriteBytesHigh = parseData($fcpWriteBytesHigh);
		
        $iscsiReadBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.7");
		$iscsiReadBytesLow = parseData($iscsiReadBytesLow);
        $iscsiReadBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.8");
		$iscsiReadBytesHigh = parseData($iscsiReadBytesHigh);
        $iscsiWriteBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.9");
		$iscsiWriteBytesLow = parseData($iscsiWriteBytesLow);
        $iscsiWriteBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.10");
		$iscsiWriteBytesHigh = parseData($iscsiWriteBytesHigh);

        $iscsiOpsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.12");
		$iscsiOpsLow = parseData($iscsiOpsLow);
        $iscsiOpsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.11");
		$iscsiOpsHigh = parseData($iscsiOpsHigh);
        $fcpOpsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.14");
		$fcpOpsLow = parseData($fcpOpsLow);
        $fcpOpsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.13");
		$fcpOpsHigh = parseData($fcpOpsHigh);

		
				
        $lunReadBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.11");
		$lunReadBytesHigh = parseData($lunReadBytesHigh);
        $lunReadBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.12");
		$lunReadBytesLow = parseData($lunReadBytesLow);
        $lunWriteBytesHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.13");
		$lunWriteBytesHigh = parseData($lunWriteBytesHigh);
        $lunWriteBytesLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.14");
		$lunWriteBytesLow = parseData($lunWriteBytesLow);

        $lunErrorsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.15");
		$lunErrorsHigh = parseData($lunErrorsHigh);
        $lunErrorsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.16");
		$lunErrorsLow = parseData($lunErrorsLow);
        $lunReadOpsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.22");
		$lunReadOpsHigh = parseData($lunReadOpsHigh);
        $lunReadOpsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.23");
		$lunReadOpsLow = parseData($lunReadOpsLow);
        $lunWriteOpsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.24");
		$lunWriteOpsHigh = parseData($lunWriteOpsHigh);
        $lunWriteOpsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.25");
		$lunWriteOpsLow = parseData($lunWriteOpsLow);
        $lunOtherOpsHigh = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.26");
		$lunOtherOpsHigh = parseData($lunOtherOpsHigh);
        $lunOtherOpsLow = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.17.15.2.1.27");
		$lunOtherOpsLow = parseData($lunOtherOpsLow);

		//Syntax: INTEGER {unmounted(1), mounted(2), frozen(3), destroying(4), creating(5), mounting(6), unmounting(7), nofsinfo(8), replaying(9), replayed(10) }
        $dfStatus = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.5.4.1.20");
		$dfStatus = parseData($dfStatus);

		//Syntax: INTEGER {invalid(1), uninitialized(2), needcpcheck(3), cpcheckwait(4), unmirrored(5), normal(6), degraded(7), resyncing(8), failed(9),	 limbo(10) }
		$dfMirrorStatus = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.5.4.1.21");
		$dfMirrorStatus = parseData($dfMirrorStatus);

        $dfPercentInodeCapacity = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.5.4.1.9");
		$dfPercentInodeCapacity = parseData($dfPercentInodeCapacity);
		
		$dfPercentKBytesCapacity = snmp3_walk($NetApp_Connection_String,$SNMP_v3_agent,$SNMP_sec_level,$SNMP_v3_auth_type,$SNMP_v3_auth_pass,$SNMP_v3_priv_type,$SNMP_v3_priv_pass, "1.3.6.1.4.1.789.1.5.4.1.6");
		$dfPercentKBytesCapacity = parseData($dfPercentKBytesCapacity);
	}





// Test if connection info is correct
if ($df_name == false) {
	echo "Fail to get SNMP Data! Please check credentials\n";
	exit(2);
}


// Replace . with - in FS b/c this is ranged data
$df_count = count($df_name);
$dfUsedKBytes=get64($dfUsedKBytesHigh, $dfUsedKBytesLow);

$lun_count = count($lunName);
if($lun_count == 1) {
	$lunName[0] = "default";
}
for($i=0; $i < $lun_count; $i++) {
	$lunName[$i] = substr($lunName[$i], strrpos($lunName[$i],':')+2);
	$lunName[$i] = str_replace(".","-",$lunName[$i]);
	$lunName[$i] = str_replace("\"","",$lunName[$i]);
	$lunName[$i] = str_replace(" ","_",$lunName[$i]);
	$lunNameRE[$i] = str_replace("/","_",$lunName[$i]);
}

// Counters - need to find delta & calculate the rate
$fcpReadBytes=get64($fcpReadBytesHigh, $fcpReadBytesLow);
$lastValue=getLastValue("fcpReadBytes",$LAST_VALUE_FILE);
echo "fcpReadBytes ".($fcpReadBytes[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
putLastValue("fcpReadBytes",$fcpReadBytes[0],$LAST_VALUE_FILE,$CURRENT_TIME);

$fcpWriteBytes=get64($fcpWriteBytesHigh, $fcpWriteBytesLow);
$lastValue=getLastValue("fcpWriteBytes",$LAST_VALUE_FILE);
echo "fcpWriteBytes " . ($fcpWriteBytes[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2]). "\n";
putLastValue("fcpWriteBytes",$fcpWriteBytes[0],$LAST_VALUE_FILE,$CURRENT_TIME);

$iscsiReadBytes=get64($iscsiReadBytesHigh, $iscsiReadBytesLow);
$lastValue=getLastValue("iscsiReadBytes",$LAST_VALUE_FILE);
echo "iscsiReadBytes " . ($iscsiReadBytes[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
putLastValue("iscsiReadBytes",$iscsiReadBytes[0],$LAST_VALUE_FILE,$CURRENT_TIME);

$iscsiWriteBytes=get64($iscsiWriteBytesHigh, $iscsiWriteBytesLow);
$lastValue=getLastValue("iscsiWriteBytes",$LAST_VALUE_FILE);
echo "iscsiWriteBytes " . ($iscsiWriteBytes[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
putLastValue("iscsiWriteBytes",$iscsiWriteBytes[0],$LAST_VALUE_FILE,$CURRENT_TIME);

$iscsiOps=get64($iscsiOpsHigh, $iscsiOpsLow);
$lastValue=getLastValue("iscsiOps",$LAST_VALUE_FILE);
echo "iscsiOps " . ($iscsiOps[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
putLastValue("iscsiOps",$iscsiOps[0],$LAST_VALUE_FILE,$CURRENT_TIME);

$fcpOps=get64($fcpOpsHigh, $fcpOpsLow);
$lastValue=getLastValue("fcpOps",$LAST_VALUE_FILE);
echo "fcpOps " . ($fcpOps[0]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
putLastValue("fcpOps",$fcpOps[0],$LAST_VALUE_FILE,$CURRENT_TIME);


// LUN is ranged.  Need to include name.
$lunReadBytes=get64($lunReadBytesHigh, $lunReadBytesLow);
$lunWriteBytes=get64($lunWriteBytesHigh, $lunWriteBytesLow);
$lunErrors=get64($lunErrorsHigh, $lunErrorsLow);
$lunReadOps=get64($lunReadOpsHigh, $lunReadOpsLow);
$lunWriteOps=get64($lunWriteOpsHigh, $lunWriteOpsLow);
$lunOtherOps=get64($lunOtherOpsHigh, $lunOtherOpsLow);

for($i=0; $i < $lun_count; $i++) {
	
	$lastValue=getLastValue("lunReadBytes-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunReadBytes " . ($lunReadBytes[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunReadBytes-".$lunNameRE[$i],$lunReadBytes[$i],$LAST_VALUE_FILE,$CURRENT_TIME);
	
	$lastValue=getLastValue("lunWriteBytes-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunWriteBytes " . ($lunWriteBytes[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunWriteBytes-".$lunNameRE[$i],$lunWriteBytes[$i],$LAST_VALUE_FILE,$CURRENT_TIME);

	$lastValue=getLastValue("lunErrors-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunErrors " . ($lunErrors[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunErrors-".$lunNameRE[$i],$lunErrors[$i],$LAST_VALUE_FILE,$CURRENT_TIME);
	
	$lastValue=getLastValue("lunReadOps-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunReadOps " . ($lunReadOps[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunReadOps-".$lunNameRE[$i],$lunReadOps[$i],$LAST_VALUE_FILE,$CURRENT_TIME);
	
	$lastValue=getLastValue("lunWriteOps-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunWriteOps " . ($lunWriteOps[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunWriteOps-".$lunNameRE[$i],$lunWriteOps[$i],$LAST_VALUE_FILE,$CURRENT_TIME);
	
	$lastValue=getLastValue("lunOtherOps-".$lunNameRE[$i],$LAST_VALUE_FILE);
	echo $lunName[$i].".lunOtherOps " . ($lunOtherOps[$i]-$lastValue[1])/($CURRENT_TIME-$lastValue[2])."\n";
	putLastValue("lunOtherOps-".$lunNameRE[$i],$lunOtherOps[$i],$LAST_VALUE_FILE,$CURRENT_TIME);
}


for($i=0; $i < $df_count; $i++) {
	$df_name[$i] = substr($df_name[$i], strrpos($df_name[$i],':')+2);
	$df_name[$i] = str_replace(".","-",$df_name[$i]);
	$df_name[$i] = str_replace("\"","",$df_name[$i]);
	$df_name[$i] = str_replace(" ","_",$df_name[$i]);

	echo $df_name[$i].".dfUsed ". $dfUsedKBytes[$i]/1024 ."\n";
	echo $df_name[$i].".dfPercentKBytesCapacity ".  $dfPercentKBytesCapacity[$i]."\n";
	echo $df_name[$i].".dfPercentInodeCapacity ".  $dfPercentInodeCapacity[$i]."\n";
	echo $df_name[$i].".dfStatus  ".  $dfStatus[$i]."\n";
	echo $df_name[$i].".dfMirrorStatus ".  $dfMirrorStatus[$i]."\n";
}




function getLastValue($metricName,$LAST_VALUE_FILE) {
	
	//Initialize Variable
	$data[0] = $metricName;
	$data[1] = 0;
	$data[2] = 0;
	
	if (file_exists($LAST_VALUE_FILE)) {
		$handle = fopen($LAST_VALUE_FILE,"r+") or die("Can't open last value file for read");
		if ($handle) {
			while (!feof($handle)) // Loop til end of file.
			{
				$buffer = fgets($handle, 4096); // Read a line.
				if (preg_match("/".$metricName.".*/", $buffer)) // Check for string.
				{
					$data = preg_split("/-utNetapp-/", $buffer);
				} 
			}
			fclose($handle); // Close the file.
		}
	}
	
	return $data;
}
function putLastValue($metricName, $value, $LAST_VALUE_FILE,$CURRENT_TIME) {
	
	// Look for the old value, remove it
	if (file_exists($LAST_VALUE_FILE)) {
		$contents = file_get_contents($LAST_VALUE_FILE);
		$contents = preg_replace("/".$metricName.".*/",'',$contents);
		$contents = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", '', $contents);
		file_put_contents($LAST_VALUE_FILE,$contents);
	}

	$fh = fopen($LAST_VALUE_FILE,"a+") or die("Can't open last value file to write");
	if (flock($fh, LOCK_EX)) {
	
		$stringData = $metricName."-utNetapp-".$value."-utNetapp-".	$CURRENT_TIME."\n";
		
		//echo  $metricName."-utNetapp-".$value."-utNetapp-".	$CURRENT_TIME."<BR>";
		
		fwrite($fh,$stringData);
		fflush($fh);
		flock($fh, LOCK_UN);
	} else {
	
		echo "Can't lock file!\n";
	}
	fclose($fh);
}


function parseData($data) {
	
	$data_count=  count($data);
	for($i=0; $i < $data_count; $i++) {
		$data_output[$i] = substr($data[$i], strrpos($data[$i],':')+2);
	}
	return $data_output;
}



function get64($msb, $lsb) {
	$count = count($lsb);
	for($i=0; $i < $count; $i++) {
		$value[$i] = bcadd(bcmul($msb[$i], bcpow(2, 32)), $lsb[$i] >= 0?$lsb[$i]:bcsub(bcpow(2, 32), $lsb[$i])); // $a most significant bits, $b least significant bits
	}
	return $value;
}


?>