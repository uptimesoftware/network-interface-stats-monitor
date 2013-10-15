/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.uptimesoftware.uptime.erdc;

import com.uptimesoftware.uptime.erdc.baseclass.MonitorWithMonitorVariables;
import com.uptimesoftware.uptime.erdc.custom.CustomOutputParser;
//import com.uptimesoftware.uptime.erdc.custom.MonitorVariable;
//import com.uptimesoftware.uptime.erdc.helper.ErdcRunResult;
//import com.uptimesoftware.uptime.ranged.RangedObject;
//import com.uptimesoftware.uptime.ranged.RangedObjectValue;
import com.uptimesoftware.uptime.base.util.Parameters;
import java.util.*;
import java.net.*;
import java.io.*;
import com.ireasoning.protocol.TimeoutException;
import com.ireasoning.protocol.snmp.SnmpPdu;
import com.ireasoning.protocol.snmp.SnmpVarBind;
import com.uptimesoftware.uptime.erdc.communication.SnmpCommunicator;
import com.uptimesoftware.uptime.erdc.snmp.SnmpErdcParameters;
import com.ireasoning.protocol.snmp.SnmpSession;
//import com.uptimesoftware.uptime.erdc.snmp.mib.SnmpSessionWrapper;
import com.ireasoning.protocol.snmp.SnmpDataType;
import com.uptimesoftware.persistence.PersistenceManager;

/**
 *
 * @author chris
 */
public class MonitorNetappFilerSnmp extends MonitorWithMonitorVariables {

    private Map<String, String> oidsHigh = new HashMap<String, String>();
    private Map<String, String> oidsLow = new HashMap<String, String>();
    private Map<String, String> controllerOidsHigh = new HashMap<String, String>();
    private Map<String, String> controllerOidsLow = new HashMap<String, String>();
    private Map<String, String> lunOidsHigh = new HashMap<String, String>();
    private Map<String, String> lunOidsLow = new HashMap<String, String>();
    private Map<String, String> oids = new HashMap<String, String>();
    private SnmpErdcParameters parameters;
    private String dfFilesystemName = "1.3.6.1.4.1.789.1.5.4.1.2";
    private String lunNameOid = "1.3.6.1.4.1.789.1.17.15.2.1.2";
    private String message = "";
    private String monitorUUID = "";
    private Map<String, Long> values = new HashMap<String, Long>();
    private SnmpSession session = null;
    private CustomOutputParser parser = new CustomOutputParser();

    @Override
    public void setParameters(Parameters params, Long instanceId) {
        this.setInstanceId(instanceId);
        this.parameters = new SnmpErdcParameters(params);
        monitorUUID = params.getString("hostname") + "." + ".uptm.ifstat.obj";

        oidsHigh.put("dfUsedKBytes", "1.3.6.1.4.1.789.1.5.4.1.16");
        oidsLow.put("dfUsedKBytes", "1.3.6.1.4.1.789.1.5.4.1.7");

        controllerOidsLow.put("fcpReadBytes", "1.3.6.1.4.1.789.1.17.3");
        controllerOidsHigh.put("fcpReadBytes", "1.3.6.1.4.1.789.1.17.4");
        controllerOidsLow.put("fcpWriteBytes", "1.3.6.1.4.1.789.1.17.5");
        controllerOidsHigh.put("fcpWriteBytes", "1.3.6.1.4.1.789.1.17.6");

        controllerOidsLow.put("iscsiReadBytes", "1.3.6.1.4.1.789.1.17.7");
        controllerOidsHigh.put("iscsiReadBytes", "1.3.6.1.4.1.789.1.17.8");
        controllerOidsLow.put("iscsiWriteBytes", "1.3.6.1.4.1.789.1.17.9");
        controllerOidsHigh.put("iscsiWriteBytes", "1.3.6.1.4.1.789.1.17.10");

        controllerOidsLow.put("iscsiOps", "1.3.6.1.4.1.789.1.17.12");
        controllerOidsHigh.put("iscsiOps", "1.3.6.1.4.1.789.1.17.11");
        controllerOidsLow.put("fcpOps", "1.3.6.1.4.1.789.1.17.14");
        controllerOidsHigh.put("fcpOps", "1.3.6.1.4.1.789.1.17.13");

        lunOidsHigh.put("lunReadBytes", "1.3.6.1.4.1.789.1.17.15.2.1.11");
        lunOidsLow.put("lunReadBytes", "1.3.6.1.4.1.789.1.17.15.2.1.12");
        lunOidsHigh.put("lunWriteBytes", "1.3.6.1.4.1.789.1.17.15.2.1.13");
        lunOidsLow.put("lunWriteBytes", "1.3.6.1.4.1.789.1.17.15.2.1.14");
        lunOidsHigh.put("lunErrors", "1.3.6.1.4.1.789.1.17.15.2.1.15");
        lunOidsLow.put("lunErrors", "1.3.6.1.4.1.789.1.17.15.2.1.16");
        lunOidsHigh.put("lunReadOps", "1.3.6.1.4.1.789.1.17.15.2.1.22");
        lunOidsLow.put("lunReadOps", "1.3.6.1.4.1.789.1.17.15.2.1.23");
        lunOidsHigh.put("lunWriteOps", "1.3.6.1.4.1.789.1.17.15.2.1.24");
        lunOidsLow.put("lunWriteOps", "1.3.6.1.4.1.789.1.17.15.2.1.25");
        lunOidsHigh.put("lunOtherOps", "1.3.6.1.4.1.789.1.17.15.2.1.26");
        lunOidsLow.put("lunOtherOps", "1.3.6.1.4.1.789.1.17.15.2.1.27");


        oids.put("dfStatus", "1.3.6.1.4.1.789.1.5.4.1.20");
        oids.put("dfMirrorStatus", "1.3.6.1.4.1.789.1.5.4.1.21");
        oids.put("dfPercentInodeCapacity", "1.3.6.1.4.1.789.1.5.4.1.9");
        oids.put("dfPercentKBytesCapacity", "1.3.6.1.4.1.789.1.5.4.1.6");
    }

    @Override
    protected void monitor() throws SocketException, IOException {



        SnmpCommunicator communicator = new SnmpCommunicator();

        communicator.setParameters(parameters);
        communicator.openSession();
        session = communicator.getSession();

        try {
            SnmpVarBind[] filesystemNameBinds = getVarBinds(session, dfFilesystemName);

            Set oidSet = oids.keySet();
            Iterator<String> oidIterator = oidSet.iterator();


            //Iterate over 32 bit metrics

            while (oidIterator.hasNext()) {
                String varName = oidIterator.next();

                try {
                    SnmpVarBind[] varBinds = getVarBinds(session, oids.get(varName));

                    for (int i = 0; i < filesystemNameBinds.length; i++) {

                        String metricLabel = filesystemNameBinds[i].getValue().toString().replace("/.", "-");
                        String metricValue = varBinds[i].getValue().toString();

                        String metricData = metricLabel + "." + varName + " " + metricValue;
                        addVariable(parser.parseLine(metricData));
                    }
                } catch (Exception e) {
                    message += "Failed to collect " + varName + "\r";
                }

            }


            processCounterOids(controllerOidsLow, controllerOidsHigh, null);
            processCounterOids(lunOidsLow, lunOidsHigh, lunNameOid);
            processCounterOids(oidsLow, oidsHigh, dfFilesystemName);

            setMessage("Monitor ran successfully\r" + message);
            
            //setStatus("TBD");
            setState(ErdcTransientState.OK);
        } catch (TimeoutException toe) {
            setMessage("Timed out making connection - check that the community string is correct. ");
            setState(ErdcTransientState.CRIT);
        } catch (Exception e) {
            setMessage(message + " " + e.toString());
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

    private long get64BitValue(long msb, long lsb) {
        long value = 0l;

        value = 0xFFFFFFFF & msb;
        value <<= 32;
        value |= (0xFFFFFFFF & lsb);

        return value;


    }

    private SnmpVarBind[] getVarBinds(SnmpSession session, String oid) throws IOException {

        SnmpVarBind[] varBind = session.snmpGetSubtree(oid);

        if (varBind == null || varBind.length == 0) {
            SnmpPdu result = session.snmpGetRequest(oid);
            if (result != null) {
                varBind = new SnmpVarBind[]{result.getFirstVarBind()};
            }
        }

        return varBind;
    }

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
    }

    @Override
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
*/

    private void processCounterOids(Map<String, String> oidsLow, Map<String, String> oidsHigh, String nameOid) {



        Set oidSet = oidsHigh.keySet();
        Iterator<String> oidIterator = oidSet.iterator();

        Map<String, Long> previousValues = (HashMap) PersistenceManager.loadObject(monitorUUID);
        SnmpVarBind[] nameBinds = null;
        try {
            if (nameOid != null) {
                nameBinds = getVarBinds(session, nameOid);
            }
        } catch (Exception e) {
            message += "Failed to get binds for " + nameOid + "\r";
        }


        while (oidIterator.hasNext()) {
            String varName = oidIterator.next();

            try {
                SnmpVarBind[] highVarBinds = getVarBinds(session, oidsHigh.get(varName));
                SnmpVarBind[] lowVarBinds = getVarBinds(session, oidsLow.get(varName));
                for (int i = 0; i < highVarBinds.length; i++) {


                    String metricLabel = "";

                    if (nameOid != null) {

                        metricLabel = nameBinds[i].getValue().toString().replace("/.", "-");
                    } else {
                        metricLabel = varName;
                    }


                    //Long metricValue = get64BitValue(Integer.parseInt(highVarBinds[i].getValue().toString()),
                    //      Integer.parseInt(lowVarBinds[i].getValue().toString()));

                    Long metricValue = get64BitValue(Long.parseLong(highVarBinds[i].getValue().toString()),
                            Long.parseLong(lowVarBinds[i].getValue().toString()));



                    values.put(metricLabel, metricValue);

                    if ((previousValues != null) && (previousValues.containsKey(metricLabel)) && ((highVarBinds[i].getValue().getType() == SnmpDataType.COUNTER32) || (highVarBinds[i].getValue().getType() == SnmpDataType.COUNTER64))) {
                        Long previousValue = previousValues.get(metricLabel);
                        metricValue = getDeltaValue(previousValue, metricValue, highVarBinds[i].getValue().getType());
                    } else {
                        metricValue = 0l;
                    }

                    String metricData = metricLabel + "." + varName + " " + metricValue;
                    addVariable(parser.parseLine(metricData));
                }
            } catch (Exception e) {
                message += "Failed to collect " + varName;
            }

        }

        PersistenceManager.saveObject(values, monitorUUID);
    }
}
