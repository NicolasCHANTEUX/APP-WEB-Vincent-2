-- Script de migration des données de typeLits vers les nouveaux champs
-- À exécuter après la migration ModifyTypeLitsToMultipleBeds

-- Mettre à jour les types de chambres existants
-- Basé sur l'ancien système où chaque type avait un seul type de lit

-- TypeChambre 1: 1 lit simple (nbplaces=1)
UPDATE typechambre 
SET nblitsimple = 1, nblitdouble = 0, nblitcanape = 0 
WHERE idtypechambre = 1;

-- TypeChambre 2: 1 lit double (nbplaces=2)
UPDATE typechambre 
SET nblitsimple = 0, nblitdouble = 1, nblitcanape = 0 
WHERE idtypechambre = 2;

-- TypeChambre 3: 1 canapé-lit (nbplaces=2)
UPDATE typechambre 
SET nblitsimple = 0, nblitdouble = 0, nblitcanape = 1 
WHERE idtypechambre = 3;

-- TypeChambre 4: 1 lit double (nbplaces=3)
UPDATE typechambre 
SET nblitsimple = 0, nblitdouble = 1, nblitcanape = 0 
WHERE idtypechambre = 4;

-- Vérifier les résultats
SELECT idtypechambre, nbplaces, nblitsimple, nblitdouble, nblitcanape, prix 
FROM typechambre 
ORDER BY idtypechambre;
