package server;
import database.DatabaseManager;
import database.User;

import java.io.FileInputStream;
import java.io.IOException;
import java.util.Properties;

/**
 Nom du fichier      : server.Main.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Classe principale, elle récupère les paramètre de la base de donnée et initie le dialogue.
 @author Dylan Manseri
 @version 1.0
 */

public class Main{
    /**
     * Récupère les paramètres de la base de donnée et initie le dialogue
     * @param args
     * @throws IOException
     */
    public static void main(String[] args) throws IOException {
        Properties props1 = new Properties();
        Properties props2 = new Properties();

        try {
            props1.load(new FileInputStream("config/config.properties"));

            String url = props1.getProperty("db.url");
            String user = props1.getProperty("db.user");
            String password = props1.getProperty("db.password");
            String port = props1.getProperty("db.port");
            String name = props1.getProperty("name");
            String host = props1.getProperty("host");
            DatabaseManager bd = new DatabaseManager(name, Integer.parseInt(port), host, url);
            bd.setUser(new User(user, password));

            props2.load(new FileInputStream("config/configServer.properties"));

            int portServer = Integer.parseInt(props2.getProperty("server.port"));
        ServeurAeroport server = new ServeurAeroport(portServer, bd);
            server.open();
            while (true) {
                server.accept();
                server.dialog();
            }
        } catch (IOException | NumberFormatException e) {
            throw new RuntimeException(e);
        }
    }
}
