/*
 * MonitorNetapp.java
 *
 * Created: Dec 5, 2012
 * Author: Joel Pereira
 * Company: uptime software inc.
 *
 * Description:
 * To retrieve metrics from a Netapp device via SNMP.
 * 
 */
package com.uptimesoftware.uptimelabs;

import java.util.HashMap;
import java.util.Map;
import com.ireasoning.protocol.*;
import com.ireasoning.protocol.snmp.*;

/**
 *
 * @author jpereira
 */
public class MonitorNetapp {

    private Map<String, String> oidsHigh = new HashMap<String, String>();
    private Map<String, String> oidsLow = new HashMap<String, String>();
    private Map<String, String> controllerOidsHigh = new HashMap<String, String>();
    private Map<String, String> controllerOidsLow = new HashMap<String, String>();
    private Map<String, String> lunOidsHigh = new HashMap<String, String>();
    private Map<String, String> lunOidsLow = new HashMap<String, String>();
    private Map<String, String> oids = new HashMap<String, String>();
    private String dfFilesystemName = "1.3.6.1.4.1.789.1.5.4.1.2";
    private String lunNameOid = "1.3.6.1.4.1.789.1.17.15.2.1.2";
    private String message = "";
    private String monitorUUID = "";
    private Map<String, Long> values = new HashMap<String, Long>();
    
    private SnmpSession session = null;
    
    
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
    }
}
