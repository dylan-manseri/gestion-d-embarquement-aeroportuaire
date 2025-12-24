CREATE TABLE Utilisateur
(
    id_user CHAR(12) PRIMARY KEY,
    mdp_hache VARCHAR(255),
    role VARCHAR(20)
);

CREATE TABLE Passager
(
    id_passager CHAR(12) PRIMARY KEY,
    nationalite VARCHAR(5),
    handicap BOOLEAN,
    prenom VARCHAR(30),
    nom VARCHAR(30),
    genre CHAR(1),
    date_naissance DATE,
    id_user CHAR(12),
    CONSTRAINT fk_user
        FOREIGN KEY (id_user)
            REFERENCES Utilisateur(id_user)
);

CREATE TABLE Bagage
(
    id_bag CHAR(12) PRIMARY KEY,
    poid INT,
    soute BOOLEAN,
    id_passager CHAR(12),
    CONSTRAINT fk_passager_bag
        FOREIGN KEY (id_passager)
            REFERENCES Passager (id_passager)
);

CREATE TABLE Bagage_cabine
(
    id_bag_cabine CHAR(12) PRIMARY KEY,
    type_sac VARCHAR(30),
    place_rangement VARCHAR(20),
    id_bag CHAR(12),
    CONSTRAINT fk_bagage
        FOREIGN KEY (id_bag)
            REFERENCES Bagage(id_bag)
);

CREATE TABLE Bagage_soute
(
    id_bag_soute CHAR(12) PRIMARY KEY,
    dimension VARCHAR(20),
    surpoid BOOLEAN,
    id_bag CHAR(12),
    CONSTRAINT fk_bagage1
        FOREIGN KEY (id_bag)
            REFERENCES Bagage(id_bag)
);

CREATE TABLE Compagnie_aerienne
(
    id_comp CHAR(12) PRIMARY KEY,
    nb_avion INT,
    code_iata CHAR(2),
    nom_compagnie VARCHAR(30)
);

CREATE TABLE Avion
(
    id_avion CHAR(12) PRIMARY KEY,
    modele VARCHAR(10),
    nombre_place INT,
    nombre_bagage INT,
    id_comp CHAR(12),
    CONSTRAINT fk_comp
        FOREIGN KEY (id_comp)
            REFERENCES Compagnie_aerienne (id_comp)
);

CREATE TABLE Vol
(
    numero_vol VARCHAR(7) PRIMARY KEY,
    date_depart DATE,
    date_arrivee DATE,
    horaire_depart CHAR(5),
    horaire_arrivee CHAR(5),
    nombre_passager INT,
    pays_depart VARCHAR(5),
    pays_arrivee VARCHAR(5),
    business_class BOOLEAN,
    id_avion CHAR(12),
    CONSTRAINT fk_avion
        FOREIGN KEY (id_avion)
            REFERENCES Avion(id_avion)
);

CREATE TABLE Reservation
(
    num_res VARCHAR(8) PRIMARY KEY,
    business BOOL,
    siege CHAR(3),
    nb_bag INT,
    id_passager CHAR(12),
    numero_vol VARCHAR(7),
    CONSTRAINT fk_passager
        FOREIGN KEY (id_passager)
            REFERENCES Passager (id_passager),
    CONSTRAINT fk_vol
        FOREIGN KEY (numero_vol)
            REFERENCES Vol (numero_vol)
);

CREATE TABLE Scanner
(
    id_scanner CHAR(12) PRIMARY KEY,
    marque VARCHAR(20),
    etat INT
);

CREATE TABLE Scan
(
    id_scan SERIAL PRIMARY KEY,
    date_scan DATE,
    heure CHAR(5),
    resultat BOOL,
    num_res VARCHAR(8),
    id_scanner CHAR(12),
    CONSTRAINT fk_res
        FOREIGN KEY (num_res)
            REFERENCES Reservation (num_res),
    CONSTRAINT fk_scanner
        FOREIGN KEY (id_scanner)
            REFERENCES Scanner (id_scanner)
);

CREATE TABLE Maintenance
(
    id_maintenance CHAR(12) PRIMARY KEY,
    entreprise VARCHAR(30)
);

CREATE TABLE Intervient
(
    id_maintenance CHAR(12),
    id_scanner CHAR(12),
    date_intervention DATE,
    derniere_maj DATE,
    rapport VARCHAR(50),
    PRIMARY KEY (id_maintenance, id_scanner),
    CONSTRAINT fk_maintenance
        FOREIGN KEY (id_maintenance)
            REFERENCES Maintenance (id_maintenance)
);


CREATE TABLE Administrateur
(
    id_admin CHAR(12) PRIMARY KEY,
    nom VARCHAR(30),
    prenom VARCHAR(30),
    id_user CHAR(12),
    CONSTRAINT fk_id_user
        FOREIGN KEY (id_user)
            REFERENCES Utilisateur (id_user)
);

