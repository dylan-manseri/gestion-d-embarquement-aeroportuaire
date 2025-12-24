package server;
import database.DatabaseManager;
import database.Enregistrement;
import exception.WrongArgumentException;
import exception.WrongDataException;

import java.time.LocalDate;
import java.util.logging.FileHandler;
import java.util.logging.Formatter;
import java.util.logging.LogRecord;
import java.util.logging.Logger;

import java.io.*;
import java.net.*;
import java.sql.ResultSet;
import java.sql.SQLException;

/**
 Nom du fichier      : server.ServeurAeroport.java
 Projet              : Gestion d'embarquement aéroportuaire
 Auteur              : Dylan Manseri
 Date                : 30-11-2025
 Description :
 Classe gérant la connexion et le dialogue entre le client et le serveur.
 Gère les differents cas d'erreurs en suivant le protocole AEROLINK
 @author Dylan Manseri
 @version 1.0
 */

public class ServeurAeroport {

    /** Port d'écoute TCP du serveur */
    private final int port;

    /** Phase du protocole, utile à la sécurité des échanges */
    private int phase;

    /** Socket représentant la connexion établie avec un client. */
    private ServerSocket server;

    /** Flux de lecture permettant de recevoir les messages du client. */
    private Socket client;

    /** Flux de lecture permettant de recevoir les messages du client. */
    private BufferedReader reader;

    /** Flux d’écriture permettant d’envoyer des messages au client. */
    private PrintWriter writer;

    /** Gestionnaire de base de données utilisé pour les opérations SQL. */
    private DatabaseManager bd;

    /** Gestionnaire d’enregistrements applicatifs (scans, logs métier, etc.). */
    private Enregistrement enr = new Enregistrement();

    /** Logger principal du serveur, utilisé pour tracer toutes les opérations. */
    private static final Logger logger = Logger.getLogger(ServeurAeroport.class.getName());

    /** Longueur maximale autorisée pour un message applicatif. */
    private static final int max_len = 20;

    /**
     * Instancie server.ServeurAeroport
     * @param port le port sur lequel le serveur sera ouvert.
     */
    public ServeurAeroport(int port, DatabaseManager bd) throws IOException {
        this.port = port;
        this.phase = 0;
        this.bd = bd;
        logger.setUseParentHandlers(false);
        FileHandler fh = new FileHandler("server.log", true);
        fh.setFormatter(new Formatter() {
            @Override
            public String format(LogRecord record) {
                return String.format(
                        "%1$tF %1$tT [%2$s] %3$s%n",
                        record.getMillis(),
                        record.getLevel().getName(),
                        record.getMessage()
                );
            }
        });
        logger.addHandler(fh);
    }

    /**
     * Ouvre le serveur sur le port définit au préalable.
     * @throws IOException si l'ouverture a échoué.
     */
    public void open() throws IOException {
        try{
            server = new ServerSocket(port); //Ouvre le port 5000 de notre machine.
        }
        catch(IOException e){
            logger.warning("Port occupé ou indisponible, utilisation du port par défaut . . .");
            server = new ServerSocket(5000);
        }

        System.out.println("Serveur prêt sur le port "+server.getLocalPort()+"...");
    }

    /**
     * Attend une connexion et crée le socket (tunnel) qui va permettre le dialogue entre le client et le serveur.
     * Crée les deux variables de dialogue.
     * @throws IOException si la connexion est brutalement intérrompu
     */
    public void accept() throws IOException {
        logger.info("----- Nouvelle session de connexion -----");
        System.out.println("- - -");
        client = server.accept();                           //Attend une connexion et renvoi le socket.
        client.setSoTimeout(10000);
        logger.info("Client connecté");
        System.out.println("Client connecté.");
        InputStream val = client.getInputStream();          //Reçois continuellement ce qu'envoi le client en binaire.
        InputStreamReader in = new InputStreamReader(val);  //Traduit le flux binaire.
        reader = new BufferedReader(in);                    //Stock le flux dans un tampon, prêt à être traité.

        OutputStream fluxSortant = client.getOutputStream();                //Tunnel vers le client
        writer = new PrintWriter(fluxSortant, true);               //Crée notre postier qui va traduire ce qu'on lui donne et l'envoyer dans le tunnel
    }                                                                       //Avant d'envoyer, il faut faire un flush, autoFlush sert à directement après l'envoi.

    /**
     * Gère le dialogue entre le client et le serveur.
     * A chaque phase la variable itère pour permettre une meilleure sécurité d'échange entre le client et le serveur
     * @throws IOException si la connexion a rompu brutalement.
     */
    public void dialog() throws IOException {
        String message;
        boolean run=true;
        int err=67;
        try{
            /* Boucle principale d’attente et de traitement des messages du client.
             * - readLine() se met en attente et renvoie la ligne reçue, ou null si le client ferme la connexion.
             * - Dans le protocole AEROLINK, la partie avant ":" indique la phase courante.
             * - On sépare donc chaque message en deux (phase, paramètres) et on délègue le traitement à la fonction correspondante.
             */
            while (run && (message = reader.readLine()) != null) {
                if(message.length() > max_len){
                    System.out.println(message);
                    throw new WrongArgumentException("Un message de taille superieur à "+max_len,phase, logger);
                }
                String[] mess = message.split(":");
                logger.info(message+" <-- Client ");
                switch(mess[0]){
                    case "HI" -> connectionPhase();
                    case "WANT" -> choicePhase(message);
                    case "QUIT" -> {
                        closeDialog();
                        run=false;
                    }
                    default -> throw new WrongArgumentException(mess[0], phase, logger);
                }
            }
        }catch(WrongArgumentException e){
            System.out.println("\u001B[31m" + e.getMessage() + "\u001B[0m");
            err=77;
        }
        catch(SocketTimeoutException e){
            logger.severe("ERREUR PHASE "+phase+"  : Le client a pris trop de temps à répondre.");
            err=17;
        }
        catch(SocketException e){
            logger.severe("ERREUR PHASE "+phase+"  : La connexion a été interrompu par le client");
            err=79;
        }
        catch(SQLException e){
            logger.severe("ERREUR PHASE "+phase+" : "+e.getMessage());
            err=63;
        }
        catch(WrongDataException d){
            logger.severe("ERREUR PHASE "+phase+" : La donnée indiqué est introuvable dans la base de donnée");
            err=d.getCode();
        }
        if(phase!=5 || err!=67){
            send("BYE "+err);
            System.out.println("Connexion interrompu, verifiez les logs.");
            if(err==67){
                logger.severe("ERREUR PHASE "+phase+" : Anomalie de protocole, l'échange ne s'est pas terminé comme prévue");
            }
        }
        else{
            System.out.println("Connexion cloturé");
        }
        phase=0;
    }

    /**
     * Phase de connexion réussite, on incrémente la phase.
     */
    private void connectionPhase() throws IOException {
        if(phase==0){
            send("HI 27");
            phase++;
        }
    }

    /**
     * Phase du choix de l'action, après WANT le client est supposé indiqué ce qu'il souhaite faire.
     * @param message le flux entrant correspondant à cette phase.
     * @throws IOException si la connexion a rompu brutalement.
     */
    private void choicePhase(String message) throws IOException, SQLException {
        String[] info = message.split(":")[1].split(" ");
        if(phase==1 && info[0].equals("scan")){
            String id_scanner = info[1];
            if(!verifString("SCAN", 12, id_scanner)){
                throw new WrongArgumentException(id_scanner, phase, logger);
            }
            searchScanner(id_scanner);      //Scanner identifié
            boardingScan();
        }
    }

    /**
     * Traite le scan d'un billet envoyé par le client.
     * @throws IOException si la connexion a rompu brutalement.
     */
    private void boardingScan() throws IOException, SQLException {
        boolean success = false;
        String message;
        /* Boucle du scan du billet (cf. methode dialog)
         * Elle ne s'arrête que lorsque l'embarquement est un succès.
         */
        while ((!success && (message = reader.readLine()) != null)){
            if(message.length() > max_len){
                throw new WrongArgumentException("Un message de taille superieur à "+max_len,phase, logger);
            }
            logger.info(message+" <-- Client");
            String[] mess = message.split(":");
            switch(mess[0]){
                case "INFO" -> infoPhase(mess[1]);
                case "PASS" -> {
                    passengerPhase(mess[1]);
                    success = true;
                }
                default -> throw new WrongArgumentException(mess[0], phase, logger);
            }
        }
    }

    /**
     * Phase d'identification du scanner via la base de donnée
     * @param idScanner l'id du scanner
     * @throws SQLException via une erreur de notre base de donnée
     * @throws IOException
     */
    private void searchScanner(String idScanner) throws SQLException, IOException {
        if (phase==1) {
            bd.connect();
            ResultSet rs = bd.selectID("Scanner", "id_scanner", idScanner);
            bd.deco();
            if(rs.next()){
                enr.setId_scanner(idScanner);
                send("16");
                phase++;
                System.out.println("Scanner identifié avec succès.");
            }
            else{
                throw new WrongDataException("Identifiant scanner introuvable", 17, logger);
            }

        }
    }

    /**
     * Phase de verification des informations fournit par le client.
     * Les informations concernent le vol et la reservation, pour respecter le protocole, il faut verifier leur présence dans la BD avant de continuer
     * @param info
     * @throws SQLException
     * @throws IOException
     */
    private void infoPhase(String info) throws SQLException, IOException {
        if (phase==2) {
            String id_vol = info.split(" ")[0];
            String id_reservation = info.split(" ")[1];
            if(verifString("RES", 8, id_reservation)){
                throw new WrongArgumentException(id_reservation, phase, logger);
            }
            bd.connect();
            ResultSet rs1 = bd.selectID("Vol", "numero_vol", id_vol);
            ResultSet rs2 = bd.selectID("Reservation", "num_res", id_reservation);
            bd.deco();
            if(rs1.next() && rs2.next()){
                LocalDate depart = rs1.getDate("date_depart").toLocalDate();
                LocalDate now = LocalDate.now();
                if(depart.isBefore(now)){
                    throw new WrongArgumentException("Une date dépassé", phase, logger);
                }
                enr.setId_vol(id_vol);
                enr.setId_reservation(id_reservation);
                send("25");
                phase++;
            }
            else{
                throw new WrongDataException("Mauvais vol ou res", 26, logger);
            }
        }
    }

    /**
     * Phase final de verification d'information fournit par le client.
     * Ici le client nous fournit les informations concernant le passager.
     * Le protocole impose la verification du passager, de l'heure de depart, de la validité de la reservation.
     * Au cas échéant une exception est levé.
     * @param id_passager
     * @throws SQLException
     * @throws IOException
     */
    private void passengerPhase(String id_passager) throws SQLException, IOException {
        if (phase==3) {
            if(verifString("PASS", 12, id_passager)){
                throw new WrongArgumentException(id_passager, phase, logger);
            }
            bd.connect();
            ResultSet rs1 = bd.selectID("Passager", "id_passager", id_passager);
            if(!rs1.next()) throw new WrongDataException("Passager introuvable", 250, logger);
            enr.setId_passager(id_passager);

            ResultSet rs2 = bd.selectID("Reservation", "id_passager", id_passager);
            if(!rs2.next()) throw new WrongDataException("Reservation pour ce passager introuvable", 252, logger);
            String id_reservation = rs2.getString("num_res");
            enr.setId_reservation(id_reservation);

            ResultSet rs3 = bd.selectID("Scan", "num_res", id_reservation);
            if(rs3.next()) throw new WrongArgumentException("Une reservation ayant déjà embarqué ", phase, logger);

            int rows = bd.insertScan(enr, true);
            if(rows!=1){
                throw new WrongDataException("Probleme de base de donnee", 667, logger);
            }
            bd.deco();
            send("270");        //Embarquement réussit
            phase++;
            System.out.println("Embarquement realisé avec succès");
        }
    }

    /**
     * Fermeture propre du dialogue entre le serveur et le client.
     * @throws IOException
     */
    private void closeDialog() throws IOException {
        send("BYE");
        writer.close();
        reader.close();
        client.close();
        phase++;
    }

    /**
     * Fonction qui gère l'envoie de message au client.
     * @param msg le message
     * @throws IOException
     */
    private void send(String msg) throws IOException {
        writer.println(msg);
        logger.info("Serveur --> "+msg);
    }

    /**
     * Verifie le format de la donnée founit par le client pour ne pas traiter une donnée erronée ou frauduleuse
     * @param prefixe
     * @param taille
     * @param data
     * @return
     */
    private boolean verifString(String prefixe, int taille, String data){
        return !data.startsWith(prefixe) || data.length() != taille;
    }

}
