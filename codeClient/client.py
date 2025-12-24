"""
==============================================================
 Fichier : client.py
 Auteur  : Dylan Manseri, Boualem Guerroumi, Alexis Bertrand
 Projet  : Gestion d'embarquement aéroportuaire
 Date    : 2025-11-30

 Description :
    Client TCP chargé de se connecter au serveur, d'envoyer les
    informations de réservation et de gérer le protocole applicatif.

 Notes :
    - Le host et le port sont paramétrables dans configServer.json
    - Les erreurs réseau sont journalisées dans client.log
==============================================================
"""

import socket
import json
from exceptions import WrongArgumentException
from fonctions import *
import logging

logging.basicConfig(
	level=logging.DEBUG,   # mets INFO si tu veux moins de détails
	format="%(asctime)s [%(levelname)s] %(message)s",
	filename="client.log",    # écrit tout ici
	filemode="a"              # append au lieu d'écraser
)

run = True
while(run):
	logging.info("----- Nouvelle session de connexion -----")
	TIMEOUT = 9
	DEFAULT_PORT = 5000


	# – Récuperation des informations –
	try:
		with open("config/infoToSend.json", "r") as i:
			infos = json.load(i)
	except (FileNotFoundError):
		print("❌ \033[33mErreur fichier. Consultez le fichier de logs.\033[0m")
		logging.error("ATTENTION : fichier infoToSend.json introuvable, utilisation des valeurs par défaut.")
		infos = {}

	# – Configuration de la connexion –
	config = getServerConfig(logging)

	HOST = config.get("host")  # Adresse du serveur Java
	if (not HOST):
		print("❌ \033[31mErreur de connexion. Consultez le fichier de logs.\033[0m")
		logging.error("ERREUR : aucun 'host' défini dans configServer.json.")
		raise SystemExit(1)

	try:
		PORT = int(config.get("port", DEFAULT_PORT))  # Le port à mettre (5000 par défaut)
	except (ValueError):
		logging.error("ERREUR : le port est invalide, utilisation de la valeur par défaut.")
		PORT = DEFAULT_PORT

	logging.info(f"Configuration : HOST={HOST}, PORT={PORT}")

	client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
	client.settimeout(TIMEOUT)  # délai max de 10 secondes pour connect / send / recv

	established = True

	try:
		client.connect((HOST, PORT))  # Connexion au serveur (via le port et l'hôte renseigné).
	except (socket.gaierror) as e:  # Erreur de résolution DNS ou IP invalide
		print("❌ \033[31mErreur de connexion. Consultez le fichier de logs.\033[0m")
		logging.error(f"ERREUR : nom d'hôte ou adresse IP invalide ({e})")
		established = False
	except (TimeoutError) as e:
		print("❌ \033[31mErreur de connexion. Consultez le fichier de logs.\033[0m")
		logging.error(f"ERREUR : délai dépassé lors de la tentative de connexion ({e})")
		established = False
	except (ConnectionRefusedError) as e:  # Rien n'écoute sur ce port (port non ouvert) ou refus explicite
		print("❌ \033[31mErreur de connexion. Consultez le fichier de logs.\033[0m")
		logging.critical(f"ERREUR : connexion refusée (port non ouvert ou refus) ({e})")
		established = False
	except (OSError) as e:  # Autres erreurs réseau : IP non atteignable etc...
		print("❌ \033[31mErreur de connexion. Consultez le fichier de logs.\033[0m")
		logging.critical(f"ERREUR réseau lors de la connexion : {e}")
		established = False

	print("Connexion au serveur réussite")

	if established:
		logging.info("Connexion établie avec succès")
		try:
			dialog(client, infos, logging)
		except (WrongArgumentException) as e:
			print("❌ \033[31mErreur lors des échanges. Consultez le fichier de logs.\033[0m")
		except (BrokenPipeError, ConnectionResetError, ConnectionAbortedError) as e:
			print("❌ \033[31mErreur lors des échanges. Consultez le fichier de logs.\033[0m")
			logging.critical(f"\ERREUR : connexion interrompue par le serveur ({e}).")
		except (OSError) as e:
			print("❌ \033[31mErreur lors des échanges. Consultez le fichier de logs.\033[0m")
			logging.critical(f"ERREUR réseau lors de l'échange avec le serveur : {e}")

	# On ferme la connexion dans tous les cas
	client.close()

	print("Connexion fermée.")
	want=3
	while want!='0' and want!='1':
		try:
			want=input("Appuyer sur 1 pour recommencer l'echange ou 0 pour l'arreter\n")
		except KeyboardInterrupt:
			raise SystemExit(1)
	if int(want)==0:
		run = False
