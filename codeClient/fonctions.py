"""
==============================================================
 Fichier : fonction.py
 Auteur  : Dylan Manseri, Boualem Guerroumi, Alexis Bertrand
 Projet  : Gestion d'embarquement aéroportuaire
 Date    : 2025-11-30

 Description :
    Fonctions utilisé pour les échange entre le client et le serveur
==============================================================
"""
from exceptions import *
import time
import json

def verifArgument(logging, data, prefix):
    """
    Vérifie le format d'un argument en fonction de plusieurs paramètres
    :param logging: variable pour écrire dans les logs
    :param data: donnée à verifier
    :param prefix: supposé préfix de la chaine de caractère
    :return:
    """
    if not data.startswith(prefix):
        logging.error(f"ERREUR : Argument invalide ({data}).")
        raise WrongArgumentException()

def envoyer(client, msg, logging):
    """
    Envoie un message au serveur
    :param client: instance du client
    :param msg: le message à envoyer
    :param logging: variable pour écrire dans les logs
    :return:
    """
    logging.info(f"CLIENT -> {msg}")

    client.sendall((msg + "\n").encode())           # Envoi un flux au serveur (encode pour le traduire en langage machine).

    response = client.recv(1024).decode().strip()   # Attend une réponse du serveur, decode traduit le langage machine
    if (not response):
        raise ConnectionError("Connexion fermée par le serveur.")

    logging.info(f"{response} <- Serveur")

    return response

#
def dialog(client, infos, logging):
    """
    Fonction principale de dialogue avec le serveur.
    :param client: instance du client
    :param infos: les infos liée au scan
    :param logging: variable pour écrire dans les logs
    :return:
    """
    print("Début du dialogue avec le serveur")

    data = envoyer(client, "HI", logging)
    run = True

    while run:
        match data:
            case "HI 27":

                scanId = infos.get("scannerId")
                verifArgument(logging, scanId, "SCAN")
                data = envoyer(client, "WANT:scan "+scanId, logging)
            case "16":
                print("\033[32mPhase d'identification terminé\033[0m")
                resId = infos.get("reservationId")
                verifArgument(logging, resId, "RES")
                data = envoyer(client, "INFO:"+infos.get("flyId")+" "+resId, logging)
            case "25":
                passId = infos.get("passengerId")
                verifArgument(logging, passId, "PASS")
                data = envoyer(client, "PASS:"+passId, logging)
            case "270":
                print("\033[32mPhase d'enregistrement terminé\033[0m")
                data = envoyer(client, "QUIT", logging)
            case "BYE":
                run = False
            case _:
                logging.error(f"ERREUR : reponse du serveur inconnue du protocole ({data}).")
                client.sendall(("BYE 78\n").encode())
                raise WrongArgumentException()

    print("Fin du dialogue avec le serveur")


def getServerConfig(logging):
    """
    Recupère les informations de connexion dans un fichier pour les renvoyer
    :param logging: variable pour écrire dans les logs
    :return:
    """
    try:
        with open("config/configServer.json", "r") as f: 		 # Ouverture du fichier de configuration
            config = json.load(f)
    except (FileNotFoundError):
        print("❌ Erreur fichier. Consultez le fichier de logs.")
        logging.error("\033[33mATTENTION : fichier configServer.json introuvable, utilisation des valeurs par défaut.\033[0m")
        config = {}
    return config