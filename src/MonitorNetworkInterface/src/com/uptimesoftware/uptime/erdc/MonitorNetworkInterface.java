/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.uptimesoftware.uptime.erdc;

//import com.uptimesoftware.uptime.erdc.baseclass.Monitor;
import com.uptimesoftware.uptime.erdc.baseclass.MonitorWithMonitorVariables;
import com.uptimesoftware.uptime.erdc.custom.CustomOutputParser;
import com.uptimesoftware.uptime.erdc.custom.MonitorVariable;
import com.uptimesoftware.uptime.erdc.helper.ErdcRunResult;
import com.uptimesoftware.uptime.ranged.RangedObject;
import com.uptimesoftware.uptime.ranged.RangedObjectValue;
import com.uptimesoftware.uptime.base.util.Parameters;
import java.util.*;
import java.net.*;
import java.io.*;
import com.ireasoning.protocol.TimeoutException;
import com.ireasoning.protocol.snmp.SnmpPdu;
import com.ireasoning.protocol.snmp.SnmpVarBind;
import com.ireasoning.protocol.snmp.SnmpDataType;
import com.ireasoning.protocol.snmp.SnmpSession;
import com.uptimesoftware.uptime.erdc.communication.SnmpCommunicator;
import com.uptimesoftware.uptime.erdc.snmp.SnmpErdcParameters;
//import com.uptimesoftware.uptime.erdc.snmp.mib.SnmpSessionWrapper;
//import com.uptimesoftware.uptime.erdc.snmp.SnmpSessionFactory;
import com.uptimesoftware.persistence.PersistenceManager;

/**
 *
 * @author chris
 */
public class MonitorNetworkInterface extends MonitorWithMonitorVariables {

    private Map<String, String> oids = new HashMap<String, String>();
    private SnmpErdcParameters parameters;
    private String response = "";
    private String ifDesc = "1.3.6.1.2.1.2.2.1.2";
    private String ifStatus = "1.3.6.1.2.1.2.2.1.8";
    private Map<String, Long> values = new HashMap<String, Long>();
    private String monitorUUID = "";
    private String interfaceFilter = "";
    private List<String> interfaceList = new ArrayList<String>();
    private String message = "";
    private String counterBitness = "";

    @Override
    public void setParameters(Parameters params, Long instanceId) {
        this.setInstanceId(instanceId);
        this.parameters = new SnmpErdcParameters(params);

        String hash = "";
        interfaceFilter = parameters.getStringParameter("interfaceFilter");
        if (interfaceFilter == null) {
            hash = "0";
        } else {
            hash = Integer.toString(interfaceFilter.hashCode());
        }
        monitorUUID = params.getString("hostname") + "." + hash + ".uptm.ifstat.obj";


        counterBitness = parameters.getStringParameter("counterBitness");

        if ((interfaceFilter != null) && (!interfaceFilter.equals(""))) {
            String[] interfaces = interfaceFilter.split(",");
            for (int i = 0; i < interfaces.length; i++) {
                interfaceList.add(interfaces[i]);
            }
        }

        oids.put("inoctets", "1.3.6.1.2.1.31.1.1.1.6");
        oids.put("outoctets", "1.3.6.1.2.1.31.1.1.1.10");

        oids.put("innunicastpackets", "1.3.6.1.2.1.2.2.1.12");
        oids.put("outnunicastpackets", "1.3.6.1.2.1.2.2.1.18");

        oids.put("inmulticastpackets", "1.3.6.1.2.1.31.1.1.1.8");
        oids.put("outmulticastpackets", "1.3.6.1.2.1.31.1.1.1.12");


        oids.put("inerrors", "1.3.6.1.2.1.2.2.1.14");
        oids.put("outerrors", "1.3.6.1.2.1.2.2.1.20");

        oids.put("indiscards", "1.3.6.1.2.1.2.2.1.13");
        oids.put("outdiscards", "1.3.6.1.2.1.2.2.1.19");

        oids.put("outqueuelength", "1.3.6.1.2.1.2.2.1.21");


        if (counterBitness.equals("32")) {
            oids.put("bytesin", "1.3.6.1.2.1.2.2.1.10");
            oids.put("bytesout", "1.3.6.1.2.1.2.2.1.16");
            oids.put("inunicastpackets", "1.3.6.1.2.1.2.2.1.11");
            oids.put("outunicastpackets", "1.3.6.1.2.1.2.2.1.17");
        } else {
            oids.put("bytesin", "1.3.6.1.2.1.31.1.1.1.6");
            oids.put("bytesout", "1.3.6.1.2.1.31.1.1.1.10");
            oids.put("inunicastpackets", "1.3.6.1.2.1.31.1.1.1.7");
            oids.put("outunicastpackets", "1.3.6.1.2.1.31.1.1.1.11");
        }

    }

    @Override
    protected void monitor() throws SocketException, IOException {

        boolean interfaceDown = false;

        CustomOutputParser parser = new CustomOutputParser();
        SnmpSession session = null;
        
        //SnmpSessionFactory session = null;
        
        SnmpCommunicator communicator = new SnmpCommunicator();

        communicator.setParameters(parameters);
        communicator.openSession();
        session = communicator.getSession();



        try {
            Set oidSet = oids.keySet();
            Iterator<String> oidIterator = oidSet.iterator();

            SnmpVarBind[] ifDescBinds = session.snmpGetSubtree(ifDesc);

            if (ifDescBinds == null || ifDescBinds.length == 0) {
                SnmpPdu result = session.snmpGetRequest(ifDesc);
                if (result != null) {
                    ifDescBinds = new SnmpVarBind[]{result.getFirstVarBind()};
                }
            }

            SnmpVarBind[] ifStatusBinds = session.snmpGetSubtree(ifStatus);

            if (ifStatusBinds == null || ifDescBinds.length == 0) {
                SnmpPdu result = session.snmpGetRequest(ifStatus);
                if (result != null) {
                    ifStatusBinds = new SnmpVarBind[]{result.getFirstVarBind()};
                }
            }

            for (Integer i = 0; i < ifStatusBinds.length; i++) {
                String metricLabel = getIfLabel(ifDescBinds, i);
                String metricValue = getIfStatus(ifStatusBinds[i].getValue().toString());

                if (metricValue.equals("DOWN")) {
                            interfaceDown = true;
                        
                            String ifLabel = "";
                            String ifLabelNoId = "";
                            if (ifDescBinds != null) {
                        ifLabelNoId = getIfLabel(ifDescBinds, i);
                        ifLabel = ifLabelNoId + "-" + i;
                    } else {
                        ifLabel = i.toString();
                    }
                
                if (interfaceList.size() == 0) {
                    //addVariable(parser.parseLine(metricLabel + " " + metricValue));
                    if (metricValue.equals("DOWN")) {
                        interfaceDown = true;
                        //message += "Interface " + metricLabel + " is DOWN \r";
                         message += "Interface: " + ifLabel + " is DOWN \r";
                    }
                } else {
                    if (interfaceList.contains(getIfLabel(ifDescBinds, i))) {
                        
                            
                           // message += "Interface " + metricLabel + " is DOWN \r";
                             message += "Interface: " + ifLabel + " is DOWN \r";
                        }
                    }
                }

            }

            Map<String, Long> previousValues = (HashMap) PersistenceManager.loadObject(monitorUUID);

            while (oidIterator.hasNext()) {

                String variable = oidIterator.next();
                String oid = oids.get(variable);
                response = null;

                SnmpVarBind[] varbinds = session.snmpGetSubtree(oid);

                if (varbinds == null || varbinds.length == 0) {
                    SnmpPdu result = session.snmpGetRequest(oid);
                    if (result != null) {
                        varbinds = new SnmpVarBind[]{result.getFirstVarBind()};
                    }
                }

                for (Integer i = 0; i < varbinds.length - 1; i++) {
                    String ifLabel = "";
                    String ifLabelNoId = "";
                    if (ifDescBinds != null) {
                        ifLabelNoId = getIfLabel(ifDescBinds, i);
                        ifLabel = ifLabelNoId + "-" + i;
                    } else {
                        ifLabel = i.toString();
                    }

                    String metricLabel = ifLabel + "." + variable;
                    Long metricValue = Long.parseLong(varbinds[i].getValue().toString());

                    values.put(metricLabel, metricValue);

                    if ((previousValues != null) && (previousValues.containsKey(metricLabel))) {
                        Long previousValue = previousValues.get(metricLabel);
                        metricValue = getDeltaValue(previousValue, metricValue, varbinds[i].getValue().getType());
                    } else {
                        metricValue = 0l;
                    }

                    if (interfaceList.size() == 0) {
                        addVariable(parser.parseLine(metricLabel + " " + metricValue));
                    } else {
                        if (interfaceList.contains(ifLabel)) {
                            addVariable(parser.parseLine(metricLabel + " " + metricValue));
                        }
                    }
                }
            }

            PersistenceManager.saveObject(values, monitorUUID);

            if (interfaceDown) {
                setMessage("\r Warning** there are interfaces down. \r" + message);
                
            } 
            setState(ErdcTransientState.UNKNOWN);
           
            /* Killing this logic, this isn't well written at all and makes no sense. 
            
             else {
                setMessage("Monitor ran successfully");
                setState(ErdcTransientState.TBD);
            }*/

        } catch (TimeoutException toe) {
            setMessage("Timed out making connection - check that the community string is correct.");
            setState(ErdcTransientState.CRIT);
        } catch (Exception e) {
            setMessage("Exception running monitor: " + e.toString());
            setState(ErdcTransientState.CRIT);
        } finally {
            communicator.closeSession();
        }

    }

    private Long getDeltaValue(Long prevVal, Long currentVal, int dataType) {

        if (prevVal > currentVal) {

            if (dataType == SnmpDataType.COUNTER32) {
                Double c32max = java.lang.Math.pow(2, 31);
                currentVal = currentVal + (c32max.longValue() - prevVal);
            } else if (dataType == SnmpDataType.COUNTER64) {
                Double c64max = java.lang.Math.pow(2, 63);
                currentVal = currentVal + (c64max.longValue() - prevVal);
            }

        } else {
            currentVal = currentVal - prevVal;
        }

        return currentVal;
    }

    private String getIfLabel(SnmpVarBind[] ifDescBinds, int i) {
        String ifLabelNoId = ifDescBinds[i].getValue().toString().replace(".", "_").replace(" ", "-");
        return ifLabelNoId;
    }

    private String getIfStatus(String statusCode) {
        int status = Integer.parseInt(statusCode);
        String statusString = "";

        switch (status) {
            case 1:
                statusString = "UP";
                break;
            case 2:
                statusString = "DOWN";
                break;
            case 3:
                statusString = "TESTING";
                break;
            default:
                statusString = "UNKNOWN";
                break;
        }

        return statusString;

    }

    // kcheung 11.1.2011 - commenting this out, I don't see any reason why knowles copied this overide into this baseclass
    
    /*
    @Override
    public ErdcRunResult getResults() {
        ErdcRunResult result = super.getResults();

        List<RangedObject> rangedObjects = new ArrayList<RangedObject>();

        List<MonitorVariable> variables = getVariables();
        for (MonitorVariable variable : variables) {
            variable.setSampleTime(result.getSampleTime());
            if (variable.isRanged()) {
                RangedObject rangedObject = convertVariableToRangedObject(variable);
                if (rangedObject != null) {
                    rangedObjects.add(rangedObject);
                }
            }
        }

        if (!rangedObjects.isEmpty()) {
            result.setRangedObjects(rangedObjects);
            
            
        }

        return result;
    }*/

    public RangedObject convertVariableToRangedObject(MonitorVariable variable) {
        RangedObject object = new RangedObject();

        String objectType = variable.getObjectType();
        if (objectType != null) {
            object.setObjectType(objectType);
        }
        object.setInstanceId(getInstanceId());
        object.setObjectName(variable.getObjectName());

        RangedObjectValue value = new RangedObjectValue();
        String variableName = variable.getName();
        String variableValue = variable.getValue();

        value.setName(variableName);

        Double doubleValue = null;
        try {
            doubleValue = new Double(variableValue);
        } catch (NumberFormatException e) {
            return null;
        }
        value.setValue(doubleValue);

        value.setSampleTime(variable.getSampleTime());
        object.addValue(value);
        return object;
    }
}
