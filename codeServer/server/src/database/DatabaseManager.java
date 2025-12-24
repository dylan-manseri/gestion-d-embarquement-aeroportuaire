package database;
import java.sql.*;
import java.time.LocalDate;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;

/**
 Nom du fichier      : DatabaseManager.java
 Projet              : Gestion d'embarquement aéroportuaire
 Date                : 30-11-2025
 Description :
 Classe gérant les échange entre le serveur et la base de donnée.
 @author Dylan Manseri
 @version 1.0
 */

public class DatabaseManager {
    private String name;
    private String host;
    private int port = 5432;
    private String url;
    private User user;
    private Connection con;

    /**
     * Instancie nos paramètre de connexion.
     * @param name
     * @param port
     * @param host
     * @param url
     */
    public DatabaseManager(String name, int port, String host, String url) {
        this.name = name;
        this.port = port;
        this.host = host;
        this.url = url;
    }

    public void setUser(User user){
        this.user = user;
    }

    public void connect() throws SQLException {
        this.con = DriverManager.getConnection(this.url, user.getLogin(), user.getPassword());
    }

    public void deco() throws SQLException {
        this.con.close();
    }

    public ResultSet selectID(String table, String nameID, String id) throws SQLException {
        if(con!=null){
            PreparedStatement ps = con.prepareStatement("SELECT * FROM "+table+" WHERE "+nameID+" = ?");
            ps.setString(1, id);
            ResultSet rs = ps.executeQuery();
            return rs;
        }
        return null;
    }

    /**
     * Réalise l'insertion dans la table scan lors de la dernière phase du protocole.
     * @param enr classe stockant les paramètre de l'enregistrement
     * @param resultat
     * @return le resultat de l'insertion, 0 si erreur
     * @throws SQLException
     */
    public int insertScan(Enregistrement enr, boolean resultat) throws SQLException {
        if(con!=null){
            LocalDate ajd = LocalDate.now();
            DateTimeFormatter jourFormatter = DateTimeFormatter.ofPattern("yyyy-MM-dd");
            String date = ajd.format(jourFormatter);

            LocalTime now = LocalTime.now();
            DateTimeFormatter heureFormatter = DateTimeFormatter.ofPattern("HH:mm");
            String heure = now.format(heureFormatter);

            PreparedStatement ps = con.prepareStatement("INSERT INTO Scan (id_scan, date_scan, heure, resultat, num_res, id_scanner) VALUES (DEFAULT, ?, ?, ?, ?, ?);");
            ps.setDate(1, Date.valueOf(date));
            ps.setString(2, heure);
            ps.setBoolean(3, resultat);
            ps.setString(4, enr.getId_reservation());
            ps.setString(5, enr.getId_scanner());
            int rows = ps.executeUpdate();
            return rows;
        }
        return 0;
    }
}
