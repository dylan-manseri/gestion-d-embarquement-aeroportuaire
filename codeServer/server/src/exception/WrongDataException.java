package exception;
import java.util.logging.Logger;

/**
 Nom du fichier      : WrongArgumentException.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Exception gérant les cas où la donnée fournit est introuvable dans la base de donnée.
 @author Dylan Manseri
 @version 1.0
 */

public class WrongDataException extends RuntimeException {

    /** Le code d'erreur */
    private int code;

    public WrongDataException(String message, int code, Logger logger) {
      super("ERREUR TYPE "+code+" : "+message);
      logger.severe(message);
      this.code = code;
    }

  public int getCode() {
    return code;
  }
}
