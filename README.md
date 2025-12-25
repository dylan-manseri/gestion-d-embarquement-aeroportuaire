# Système de Gestion d'Embarquement dans un Aéroport

**Projet Base de Données et Réseaux**  
Dylan Manseri, Boualem Guerroumi, Alexis Bertrand - Novembre 2025

## Description

Système de gestion automatique des embarquements aéroportuaires utilisant :
- Un protocole réseau personnalisé (AEROLINK)
- Une base de données PostgreSQL 
- Une architecture client-serveur avec un client Python et un serveur Java.

## Architecture Générale

```
[Scanner Python] ←→ TCP/IP ←→ [Serveur Java] ←→ [PostgreSQL]
                                      ↕
                              [Interface Web PHP]
```

Notre projet simule un scanner d'embarquement (un client Python) qui communique avec un serveur (en Java) pour valider les réservations en interrogeant une base de données.

## Partie Client (Python)

**Fichier principal :** `client.py`

### Fonctionnement
Le client Python simule un scanner d'embarquement qui se connecte au serveur via TCP.

```python
# Étapes principales :
1. Lire la configuration (host, port) depuis configServer.json
2. Se connecter au serveur avec socket TCP
3. Dialoguer selon le protocole AEROLINK
4. Gérer les erreurs réseau
5. Logger toutes les opérations
```

### Gestion des erreurs réseau
- `socket.gaierror` : Adresse IP/DNS invalide
- `TimeoutError` : Serveur ne répond pas
- `ConnectionRefusedError` : Port fermé
- `BrokenPipeError` : Connexion coupée

## Partie Serveur (Java)

**Fichier principal :** `ServeurAeroport.java`

### Fonctionnement
Le serveur Java écoute sur un port, accepte les connexions et valide les embarquements en interrogeant la base de données.

```java
// Étapes principales :
1. Ouvrir un ServerSocket sur le port 5000
2. Attendre une connexion (accept)
3. Créer les flux lecture/écriture (BufferedReader, PrintWriter)
4. Gérer le dialogue avec le protocole AEROLINK
5. Interroger PostgreSQL via JDBC
6. Insérer les scans valides dans la BD
```

### Gestion des phases
Le serveur suit une machine à états (5 phases) pour sécuriser les échanges :
1. Connexion initiale
2. Identification du scanner
3. Validation vol + réservation
4. Validation passager + insertion
5. Fermeture

##  Partie Base de Données (PostgreSQL)

### Structure
La base contient 12 tables interconnectées. Les principales :

**Scanner** - Équipements d'embarquement
```sql
CREATE TABLE Scanner (
    id_scanner CHAR(12) PRIMARY KEY,
    marque VARCHAR(20),
    etat INT
);
```

**Vol** - Informations des vols
```sql
CREATE TABLE Vol (
    numero_vol VARCHAR(7) PRIMARY KEY,
    date_depart DATE,
    horaire_depart CHAR(5),
    pays_depart VARCHAR(5),
    pays_arrivee VARCHAR(5),
    id_avion CHAR(12)
);
```

**Reservation** - Réservations passagers
```sql
CREATE TABLE Reservation (
    num_res VARCHAR(8) PRIMARY KEY,
    siege CHAR(3),
    id_passager CHAR(12),
    numero_vol VARCHAR(7),
    FOREIGN KEY (id_passager) REFERENCES Passager(id_passager),
    FOREIGN KEY (numero_vol) REFERENCES Vol(numero_vol)
);
```

**Scan** - Historique des embarquements
```sql
CREATE TABLE Scan (
    id_scan SERIAL PRIMARY KEY,
    date_scan DATE,
    heure CHAR(5),
    resultat BOOLEAN,
    num_res VARCHAR(8),
    id_scanner CHAR(12),
    FOREIGN KEY (num_res) REFERENCES Reservation(num_res),
    FOREIGN KEY (id_scanner) REFERENCES Scanner(id_scanner)
);
```

### Interactions avec la BD

**Java (JDBC) :**
- Connexion : `DriverManager.getConnection(url, user, password)`
- Requêtes préparées : `PreparedStatement` (protection SQL injection)
- Validation : Vérifier existence dans Scanner, Vol, Reservation, Passager
- Insertion : Ajouter un enregistrement dans Scan si tout est valide

**PHP (PDO) :**
- Interface web pour consulter et gérer les scanners
- Requêtes préparées : `$stmt->bindValue()` (sécurité)
- Authentification des utilisateurs admin

## Protocole AEROLINK

Communication simple entre client et serveur :

```
Client          Serveur         Base de données
------          -------         ---------------
HI          →
            ←   HI 27
                
WANT:scan   →                   Vérifie Scanner existe
SCAN000001
            ←   16              ✓ Scanner trouvé

INFO:AF1234 →                   Vérifie Vol + Reservation
RES00001
            ←   25              ✓ Vol et résa OK

PASS:       →                   Vérifie Passager
PASS0000001                     Insère dans Scan
            ←   270             ✓ Embarquement réussi

QUIT        →
            ←   BYE
```

### Codes réponse
- `16` : Scanner OK
- `25` : Vol + Réservation OK
- `270` : Embarquement réussi
- `17`, `26`, `250` : Erreurs (données introuvables)
- `77` : Format invalide
- `BYE` : Fin connexion

## Installation Rapide

### 1. Base de données
```bash
psql -U postgres
CREATE DATABASE aeroport;
\c aeroport
\i ddl.sql
\i dml.sql
```

### 2. Configuration

**Client Python** (`config/configServer.json`) :
```json
{
  "host": "localhost",
  "port": 5000
}
```

**Serveur Java** (`config/config.properties`) :
```properties
db.url=jdbc:postgresql://localhost:5432/aeroport
db.user=postgres
db.password=votre_mdp
```

### 3. Lancement

```bash
# Terminal 1 - Serveur
java -cp bin:lib/postgresql.jar server.Main

# Terminal 2 - Client
python3 client.py
```

## Test Simple

1. Démarrer le serveur Java
2. Modifier `config/infoToSend.json` :
```json
{
  "scannerId": "SCAN000001",
  "flyId": "AF1234",
  "reservationId": "RES00001",
  "passengerId": "PASS00000001"
}
```
3. Lancer le client Python
4. Vérifier les logs : `server.log` et `client.log`
5. Vérifier dans la BD : `SELECT * FROM Scan;`

## Concepts Mis en Œuvre

### Réseaux
- Sockets TCP/IP (client/serveur)
- Protocole applicatif personnalisé
- Gestion timeout et erreurs réseau

### Base de Données
- Modèle relationnel (12 tables)
- Contraintes d'intégrité (PK, FK)
- Requêtes SQL préparées
- Transactions

---

**Version :** 1.0
