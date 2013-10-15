/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.uptimesoftware.persistence;

import java.io.IOException;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileInputStream;
import java.io.ObjectOutputStream;
import java.io.ObjectInputStream;

/**
 *
 * @author chris
 */
public class PersistenceManager {

    public static void saveObject(Object o, String filename) {

        try {
            File saveFile = new File(System.getProperty("java.io.tmpdir"), filename);

            FileOutputStream f_out = new FileOutputStream(saveFile);
            ObjectOutputStream o_out = new ObjectOutputStream(f_out);
            o_out.writeObject(o);
            o_out.close();
            f_out.close();

        } catch (Exception e) {
            System.err.println("Exception in PersistenceManager.saveObject() " + e);

        }
    }

    public static Object loadObject(String filename) {

        Object o = null;

        try {
            File loadFile = new File(System.getProperty("java.io.tmpdir"), filename);

            if (loadFile.exists()) {
                FileInputStream f_in = new FileInputStream(loadFile);
                ObjectInputStream o_in = new ObjectInputStream(f_in);
                o = o_in.readObject();
                o_in.close();
                f_in.close();
            }


        } catch (Exception e) {
            System.err.println("Exception in PersistenceManager.loadObject() " + e);
        }
        return o;
    }
}
