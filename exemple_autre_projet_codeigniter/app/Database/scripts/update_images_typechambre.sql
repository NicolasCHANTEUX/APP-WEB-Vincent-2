-- Mise à jour des images pour les types de chambres existants
UPDATE typechambre SET image = '/images/chambres/2-lits-simples.webp' WHERE typelits = 'simple';
UPDATE typechambre SET image = '/images/chambres/lit-double.webp' WHERE typelits = 'double';
UPDATE typechambre SET image = '/images/chambres/canape-lit.webp' WHERE typelits = 'canape';
