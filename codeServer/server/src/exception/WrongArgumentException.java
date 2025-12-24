package exception;
import java.util.logging.Logger;

/**
 Nom du fichier      : WrongArgumentException.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Exception gérant les cas où l'information fournit est inutilisable ou est frauduleuse.
 @author Dylan Manseri
 @version 1.0
 */

public class WrongArgumentException extends RuntimeException {

    public WrongArgumentException(String message, int phase, Logger logger) {
      super("Erreur d'argument, verifiez les logs");
      logger.severe("ERREUR PHASE "+phase+" : "+message+" n'est pas un argument valide !");
    }
}
