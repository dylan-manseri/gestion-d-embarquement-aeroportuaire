package database;

/**
 Nom du fichier      : Enregistrement.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Classe stockant les informations de l'enregistrement en cours.
 @author Dylan Manseri
 @version 1.0
 */

public class Enregistrement {

    private String id_scanner;
    private String id_vol;
    private String id_reservation;
    private String id_passager;

    public Enregistrement() {
        this.id_scanner = null;
        this.id_vol = null;
        this.id_reservation = null;
        this.id_passager = null;
    }

    public String getId_scanner() {
        return id_scanner;
    }

    public String getId_passager() {
        return id_passager;
    }

    public String getId_reservation() {
        return id_reservation;
    }

    public String getId_vol() {
        return id_vol;
    }

    public void setId_scanner(String id_scanner) {
        this.id_scanner = id_scanner;
    }

    public void setId_vol(String id_vol) {
        this.id_vol = id_vol;
    }

    public void setId_reservation(String id_reservation) {
        this.id_reservation = id_reservation;
    }

    public void setId_passager(String id_passager) {
        this.id_passager = id_passager;
    }
}
