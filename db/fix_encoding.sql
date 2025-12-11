-- Correction de l'encodage des statuts de livraison dans la table charts
-- Mise à jour des valeurs avec les bons caractères UTF-8

UPDATE charts 
SET delivery_status = 'En cours de préparation' 
WHERE delivery_status LIKE '%pr%paration%' OR delivery_status LIKE 'En cours%';

UPDATE charts 
SET delivery_status = 'Expédiée' 
WHERE delivery_status LIKE 'Exp%di%' OR delivery_status LIKE '%xp%di%';

UPDATE charts 
SET delivery_status = 'Livrée' 
WHERE delivery_status LIKE 'Livr%' OR delivery_status LIKE '%ivr%';

-- Modifier la valeur par défaut de la colonne
ALTER TABLE charts 
MODIFY COLUMN delivery_status VARCHAR(100) DEFAULT 'En cours de préparation';

-- Vérification
SELECT id, delivery_status FROM charts;
