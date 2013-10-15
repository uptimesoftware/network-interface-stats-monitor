# Network Interface Stats Monitor
## Tags : plugin   network   deprecated  

## Category: plugin

##Version Compatibility<br/>Module Name</th><th>up.time Monitoring Station Version</th>


  
    * Network Interface Stats Monitor 1.1 - 6.0
  

  
    * Network Interface Stats Monitor 1.0 - 5.5, 5.4, 5.3
  


### Description: This monitor collects standard interface metrics from network devices such as switches and routers.

### Supported Monitoring Stations: 6.0
### Supported Agents: None; no agent required
### Installation Notes: <p>To install the plug-in: - extract the zip into the uptime base directory. - place the jar file into the “core” directory. - run the following command to import the XML monitor definition/template: > cd > scripts\erdcloader -x MonitorNetworkInterface.xml If the monitoring station is on Linux/Solaris, there is an extra step: edit the /uptime.lax file by adding the following text to the end of the long line that starts with “lax.class.path=”: :core/(JAR_FILE).jar To complete installation, restart the uptime Data Collector (Core) service.</p>

### Dependencies: <p>n/a</p>

### Input Variables: * SNMP version* 32/64 bit SNMP counters* community string* {"SNMP port (default"=>"161)"}* v3 user name* v3 authentication type (MD5 / SHA)* v3 password* v3 privacy type (DES / AES)* {"interface list"=>"comma separated list of interfaces to retrieve (empty gets all)"}* Option to alert on down interfaces
### Output Variables: * bytes in* bytes out* octets in* octets out* unicast packets in* unicast packets out* non-unicast packets in* non-unicast packets out* inbound errors* outbound errors* inbound discards* outbound discards* outbound queue length* interface status
### Languages Used: * Java

