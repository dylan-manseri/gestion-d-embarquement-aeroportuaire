INSERT INTO Utilisateur
VALUES ('USER00000001', 'hash1234', 'user'),
       ('USER00000002', 'hash5678', 'user'),
       ('USER00000003', 'hash9012', 'user'),
       ('USER00000004', 'hash3456', 'user'),
       ('USER00000005', 'hash7890', 'user'),
       ('USER00000006', 'hashabcd', 'user'),
       ('USER00000007', 'hashefgh', 'user'),
       ('USER00000008', 'hashijkl', 'user'),
       ('USER00000009', 'hashmnop', 'user'),
       ('USER00000010', 'hashmnop', 'user'),
       ('USER00000011', 'hashmnop', 'admin'),
       ('USER00000012', 'hashmnop', 'admin'),
       ('USER00000013', 'hashmnop', 'admin');

INSERT INTO Administrateur
VALUES ('ADM000000001', 'Manseri', 'Dylan', 'USER00000011'),
       ('ADM000000002', 'Bertrand', 'Alexis', 'USER00000012'),
       ('ADM000000003', 'Guerroumi', 'Boualem', 'USER00000013');

INSERT INTO Compagnie_aerienne
VALUES ('C1', 25, 'AF', 'Air France'),
       ('C2', 15, 'BA', 'British Airways'),
       ('C3', 10, 'LH', 'Lufthansa'),
       ('C4', 8, 'EK', 'Emirates'),
       ('C5', 20, 'AA', 'American Airlines');

INSERT INTO Passager
VALUES ('PASS00000001', 'FR', FALSE, 'Dylan', 'Manseri', 'M', '2004-10-27', 'USER00000001'),
       ('PASS00000002', 'FR', FALSE, 'Alexis', 'Bertrand', 'M', '2004-12-16', 'USER00000002'),
       ('PASS00000003', 'DZ', FALSE, 'Boualem', 'Guerroumi', 'M', '2002-09-25', 'USER00000003'),
       ('PASS00000004', 'US', FALSE, 'Sophie', 'Martin', 'F', '1995-07-30', 'USER00000004'),
       ('PASS00000005', 'US', TRUE, 'Liam', 'Smith', 'M', '2000-03-10', 'USER00000005'),
       ('PASS00000006', 'UK', FALSE, 'Emma', 'Johnson', 'F', '1992-11-05', 'USER00000006'),
       ('PASS00000007', 'UK', FALSE, 'Noah', 'Brown', 'M', '1988-09-14', 'USER00000007'),
       ('PASS00000008', 'ES', TRUE, 'Olivia', 'Davis', 'F', '1998-01-25', 'USER00000008'),
       ('PASS00000009', 'ES', FALSE, 'James', 'Wilson', 'M', '1975-06-18', 'USER00000009'),
       ('PASS00000010', 'IT', FALSE, 'Ava', 'Garcia', 'F', '2001-04-12', 'USER00000010');

INSERT INTO Bagage
VALUES ('BAG00000001', 15, TRUE, 'PASS00000001'),
       ('BAG00000002', 8, FALSE, 'PASS00000001'),
       ('BAG00000003', 20, TRUE, 'PASS00000002'),
       ('BAG00000004', 10, FALSE, 'PASS00000003'),
       ('BAG00000005', 25, TRUE, 'PASS00000004'),
       ('BAG00000006', 5, FALSE, 'PASS00000005'),
       ('BAG00000007', 18, TRUE, 'PASS00000006'),
       ('BAG00000008', 12, FALSE, 'PASS00000007'),
       ('BAG00000009', 30, TRUE, 'PASS00000008'),
       ('BAG00000010', 7, FALSE, 'PASS00000009');

INSERT INTO Bagage_cabine
VALUES ('BAGCAB000001', 'valise', 'armoire', 'BAG00000001'),
       ('BAGCAB000002', 'sac_dos', 'armoire', 'BAG00000002'),
       ('BAGCAB000003', 'sac_main', 'main', 'BAG00000003'),
       ('BAGCAB000004', 'valise', 'armoire', 'BAG00000004'),
       ('BAGCAB000005', 'sac_dos', 'main', 'BAG00000005');


INSERT INTO Bagage_soute
VALUES ('BAGSOU000001', '80x55x23', FALSE, 'BAG00000006'),
       ('BAGSOU000002', '90x70x35', FALSE, 'BAG00000007'),
       ('BAGSOU000003', '95x65x40', TRUE, 'BAG00000008'),
       ('BAGSOU000004', '72x43x24', FALSE, 'BAG00000009'),
       ('BAGSOU000005', '80x55x23', TRUE, 'BAG00000010');


INSERT INTO Avion
VALUES ('AVION000001', 'A320', 180, 150, 'C1'),
       ('AVION000002', 'B737', 160, 140, 'C2'),
       ('AVION000003', 'A380', 500, 400, 'C3'),
       ('AVION000004', 'B777', 300, 250, 'C4'),
       ('AVION000005', 'A350', 280, 230, 'C5');

INSERT INTO Vol
VALUES ('AF1234', '2024-07-01', '2024-07-01', '10:00', '12:00', 150, 'FR', 'US', TRUE, 'AVION000001'),
       ('BA5678', '2024-07-02', '2024-07-02', '14:00', '16:00', 140, 'UK', 'FR', FALSE, 'AVION000002'),
       ('LH9101', '2024-07-03', '2024-07-03', '09:00', '11:30', 400, 'DE', 'US', TRUE, 'AVION000003'),
       ('EK2345', '2024-07-04', '2024-07-04', '20:00', '04:00', 250, 'AE', 'FR', FALSE, 'AVION000004'),
       ('AA6789', '2024-07-05', '2024-07-05', '08:00', '10:30', 230, 'US', 'UK', TRUE, 'AVION000005');

INSERT INTO Reservation
VALUES ('RES00001', TRUE, '12A', 2, 'PASS00000001', 'AF1234'),
       ('RES00002', FALSE, '14B', 1, 'PASS00000002', 'BA5678'),
       ('RES00003', TRUE, '1C', 3, 'PASS00000003', 'LH9101'),
       ('RES00004', FALSE, '22D', 0, 'PASS00000004', 'EK2345'),
       ('RES00005', TRUE, '5E', 2, 'PASS00000005', 'AA6789'),
       ('RES00006', FALSE, '18F', 1, 'PASS00000006', 'AF1234'),
       ('RES00007', TRUE, '3A', 2, 'PASS00000007', 'BA5678'),
       ('RES00008', FALSE, '25B', 0, 'PASS00000008', 'LH9101'),
       ('RES00009', TRUE, '7C', 1, 'PASS00000009', 'EK2345'),
       ('RES00010', FALSE, '30D', 0, 'PASS00000010', 'AA6789');

INSERT INTO Scanner
VALUES ('SCAN000001', 'Rapiscan', 1),
       ('SCAN000002', 'Smiths', 2),
       ('SCAN000003', 'L3Harris', 1),
       ('SCAN000004', 'Nuctech', 3),
       ('SCAN000005', 'Astrophysics', 2);

INSERT INTO Scan
VALUES (DEFAULT, '2024-06-20', '09:00', TRUE, 'RES00001', 'SCAN000001'),
       (DEFAULT, '2024-06-20', '09:15', FALSE, 'RES00002', 'SCAN000002'),
       (DEFAULT, '2024-06-20', '09:30', TRUE, 'RES00003', 'SCAN000003'),
       (DEFAULT, '2024-06-20', '09:45', TRUE, 'RES00004', 'SCAN000004'),
       (DEFAULT, '2024-06-20', '10:00', FALSE, 'RES00005', 'SCAN000005'),
       (DEFAULT, '2024-06-20', '10:15', TRUE, 'RES00006', 'SCAN000001'),
       (DEFAULT, '2024-06-20', '10:30', TRUE, 'RES00007', 'SCAN000002'),
       (DEFAULT, '2024-06-20', '10:45', FALSE, 'RES00008', 'SCAN000003'),
       (DEFAULT, '2024-06-20', '11:00', TRUE, 'RES00009', 'SCAN000004'),
       (DEFAULT, '2024-06-20', '11:15', TRUE, 'RES00010', 'SCAN000005');

INSERT INTO Maintenance
VALUES ('MAINT00001', 'TechServ'),
       ('MAINT00002', 'ScanFix'),
       ('MAINT00003', 'SecureMaint'),
       ('MAINT00004', 'EquipCare'),
       ('MAINT00005', 'SafeScan');

INSERT INTO Intervient
VALUES ('MAINT00001', 'SCAN000001', '2024-06-15', '2024-06-20', 'Routine check completed.'),
       ('MAINT00002', 'SCAN000002', '2024-06-16', '2024-06-20', 'Replaced faulty component.'),
       ('MAINT00003', 'SCAN000003', '2024-06-17', '2024-06-20', 'Software update installed.'),
       ('MAINT00004', 'SCAN000004', '2024-06-18', '2024-06-20', 'Calibration done.'),
       ('MAINT00005', 'SCAN000005', '2024-06-19', '2024-06-20', 'General maintenance performed.');