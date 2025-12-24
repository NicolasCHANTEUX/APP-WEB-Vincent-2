-- Nettoyage préalable
DROP TABLE IF EXISTS Reservation CASCADE;
DROP TABLE IF EXISTS Chambre CASCADE;
DROP TABLE IF EXISTS TypeChambre CASCADE;
DROP TABLE IF EXISTS Client CASCADE;

-----------------------------------------------
-- Création des tables
-----------------------------------------------

CREATE TABLE Client (
    idClient SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    mail VARCHAR(255) NOT NULL,
    telephone VARCHAR(30)
);

CREATE TABLE TypeChambre (
    idTypeChambre SERIAL PRIMARY KEY,
    nbPlaces INTEGER NOT NULL,
    nbLitSimple INTEGER DEFAULT 0 NOT NULL,
    nbLitDouble INTEGER DEFAULT 0 NOT NULL,
    nbLitCanape INTEGER DEFAULT 0 NOT NULL,
    prix NUMERIC(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT '/images/chambres/default.webp'
);

CREATE TABLE Chambre (
    idChambre SERIAL PRIMARY KEY,
    typeChambre INTEGER NOT NULL REFERENCES TypeChambre(idTypeChambre),
    pmr BOOLEAN NOT NULL
);

CREATE TABLE Reservation (
    idRes SERIAL PRIMARY KEY,
    idClient INTEGER NOT NULL REFERENCES Client(idClient),
    idChambre INTEGER[] NOT NULL,
    dateDebut DATE NOT NULL,
    dateFin DATE NOT NULL,
    nbPersonnes INTEGER,
    note TEXT,
    statut VARCHAR(50) DEFAULT 'en_attente' NOT NULL
);

------------------------------------------------
-- Insertion du jeu de test
------------------------------------------------

INSERT INTO TypeChambre (nbPlaces, nbLitSimple, nbLitDouble, nbLitCanape, prix, image) VALUES 
(1, 1, 0, 0, 55.00, '/images/chambres/2-lits-simples.webp'),
(2, 0, 1, 0, 65.00, '/images/chambres/lit-double.webp'),
(2, 0, 0, 1, 45.00, '/images/chambres/canape-lit.webp'),
(3, 0, 1, 0, 70.00, '/images/chambres/lit-double.webp');


INSERT INTO Chambre (typeChambre, pmr) VALUES
(1,false),(1,false),(1,false),(1,false),(1,true),(1,false),
(2,false),(2,true),(2,false),(2,false),
(3,false),(3,false),
(4,false),(4,false);


INSERT INTO Client (nom, prenom, mail, telephone) VALUES
('Dupont','Jean','jean.dupont@email.com','0123456789'),
('Lefevre','Marie','marie.lefevre@email.com','0987654321'),
('Le Blanc','Paul','paul.le.blanc@email.com','0112233445'),
('Bois','Sophie','sophie.bois@email.com','0555666777'),
('Dubois','Luc','luc.dubois@email.com','0888999000'),
('Chanteux','Claire','claire.chanteux@email.com','0333444555'),
('Petit','Pierre','pierre.petit@email.com','0666777888'),
('Prout','Quentin','quentin.prout@email.com','0999000111'),
('Durand','Marc','marc.durand@email.com','0222333444'),
('Leroy','Anne','anne.leroy@email.com','0777888999'),
('Moreau','David','david.moreau@email.com','0444555666'),
('Varenel','Elise','elise.varenel@email.com','0111222333'),
('Odamne','Franck','franck.odamne@email.com','0666777888'),
('Lefebvre','Nathalie','nathalie.lefebvre@email.com','0999000111'),
('Dos Santos','Olivier','olivier.dos.santos@email.com','0222333444'),
('Roux','Patricia','patricia.roux@email.com','0777888999'),
('Ming','Sebastien','sebastien.ming@email.com','0444555666'),
('Thauvier','Valerie','valerie.thauvier@email.com','0111222333'),
('Rousseau','Xavier','xavier.rousseau@email.com','0555666777'),
('Lambert','Yves','yves.lambert@email.com','0888999000');

INSERT INTO Reservation (idClient, idChambre, dateDebut, dateFin, statut) VALUES
(12, ARRAY[8,9],'2024-12-05','2024-12-10','confirmee'),
(11, ARRAY[8,9],'2024-12-15','2024-12-17','en_attente'),
(13, ARRAY[10,11,12],'2024-11-15','2024-11-20','confirmee'),
(14, ARRAY[13,14],'2024-10-20','2024-10-25','confirmee'),
(4, ARRAY[10],'2024-08-20','2024-08-22','confirmee'),
(16, ARRAY[6],'2024-08-25','2024-08-27','confirmee'),
(5, ARRAY[12],'2024-07-20','2024-07-22','confirmee'),
(17, ARRAY[6,7,8],'2024-07-20','2024-07-25','confirmee'),
(3, ARRAY[8],'2024-07-10','2024-07-12','confirmee'),
(19, ARRAY[11,12,13,14,1],'2024-05-15','2024-05-20','confirmee'),
(18, ARRAY[10],'2024-06-20','2024-06-22','confirmee'),
(8, ARRAY[3],'2024-04-20','2024-04-22','confirmee'),
(20, ARRAY[2,3],'2024-04-20','2024-04-25','confirmee'),
(9, ARRAY[12],'2024-05-25','2024-05-27','confirmee'),
(20, ARRAY[14],'2024-04-10','2024-04-12','confirmee'),
(1, ARRAY[1],'2024-03-15','2024-03-17','confirmee'),
(2, ARRAY[3],'2024-02-20','2024-02-22','confirmee'),
(11, ARRAY[9],'2025-01-20','2025-01-22','confirmee'),
(10, ARRAY[14,1,2],'2025-02-20','2025-02-25','confirmee'),
(9, ARRAY[5],'2025-03-25','2025-03-27','en_attente'),
(8, ARRAY[8,9,10,11],'2025-04-10','2025-04-15','confirmee'),
(7, ARRAY[1],'2025-05-10','2025-05-12','confirmee'),
(7, ARRAY[5,7],'2025-05-20','2025-05-25','en_attente'),
(6, ARRAY[2,3,4,6],'2025-06-15','2025-06-20','confirmee'),
(6, ARRAY[14],'2025-06-25','2025-06-27','en_attente'),
(1, ARRAY[4],'2025-11-10','2025-11-12','confirmee'),
(1, ARRAY[1,3,5],'2025-11-01','2025-11-05','confirmee'),
(2, ARRAY[2,4,7],'2025-10-15','2025-10-20','confirmee'),
(2, ARRAY[6],'2025-10-25','2025-10-27','en_attente'),
(3, ARRAY[6,8,10],'2025-09-20','2025-09-25','confirmee'),
(3, ARRAY[8],'2025-09-10','2025-09-12','confirmee'),
(15, ARRAY[1,2,3,4],'2024-09-10','2024-09-15','confirmee'),
(14, ARRAY[2],'2024-10-10','2024-10-12','confirmee'),
(1, ARRAY[3,4,5,6,7],'2025-01-10','2025-01-15','confirmee'),
(13, ARRAY[13],'2024-11-25','2024-11-27','confirmee'),
(10, ARRAY[7],'2025-02-15','2025-02-17','en_attente'),
(16, ARRAY[6],'2024-08-15','2024-08-20','confirmee'),
(19, ARRAY[12],'2024-05-25','2024-05-27','confirmee'),
(18, ARRAY[9,10],'2024-06-25','2024-06-30','confirmee'),
(20, ARRAY[14],'2024-04-10','2024-04-12','confirmee'),
(4, ARRAY[9,11,13,14],'2025-08-10','2025-08-15','confirmee'),
(15, ARRAY[4],'2024-09-20','2024-09-22','confirmee'),
(17, ARRAY[8],'2024-07-10','2024-07-12','confirmee'),
(19, ARRAY[11,12,13,14,1],'2024-05-15','2024-05-20','confirmee'),
(5, ARRAY[1,12],'2025-07-05','2025-07-10','en_attente'),
(1, ARRAY[1],'2025-03-25','2025-03-27','confirmee'),
(13, ARRAY[10,11,12],'2024-11-15','2024-11-20','confirmee'),
(3, ARRAY[5,7,9],'2025-12-10','2025-12-15','en_attente'),
(4, ARRAY[11,13],'2025-12-15','2025-12-20','en_attente'),
(5, ARRAY[2,4,6,8],'2025-12-20','2025-12-25','en_attente'),
(6, ARRAY[10],'2025-12-25','2025-12-27','en_attente'),
(7, ARRAY[12],'2025-12-28','2025-12-30','en_attente'),
(8, ARRAY[14],'2026-01-05','2026-01-07','en_attente'),
(9, ARRAY[1,3],'2026-01-10','2026-01-15','en_attente'),
(10, ARRAY[5],'2026-01-20','2026-01-22','en_attente');
