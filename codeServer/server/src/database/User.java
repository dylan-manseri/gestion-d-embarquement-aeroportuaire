package database;

/**
 Nom du fichier      : WrongArgumentException.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Classe stockant les données relative à l'utilisateur de la base de donnée.
 @author Dylan Manseri
 @version 1.0
 */

public class User {
    private String login;
    private String password;

    public User(String login, String password) {
        this.login = login;
        this.password = password;
    }

    public String getLogin() {
        return login;
    }

    public String getPassword() {
        return password;
    }
}
