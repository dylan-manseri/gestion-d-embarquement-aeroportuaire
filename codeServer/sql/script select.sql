-- Gestion d'embarquement aéroportuaire

------------------- SERVER -------------------
/* Information dont nous disposons :
	- Numero de réservation : RES00003
	- L'identifiant du passager : PASS00000003
	- Le numéro du vol : LH9101
	- L'identifiant du scanner : SCAN000004
*/

-- Est-ce que le scanner utilisé est reconnu ?
SELECT * FROM Scanner WHERE id_scanner = 'SCAN000004';

-- Est ce que le vol existe ?
SELECT * FROM Vol WHERE numero_vol = 'LH9101';

-- Est-ce que le passager existe pour ce vol ?
SELECT id_passager, numero_vol, num_res FROM Passager 
	NATURAL JOIN Reservation 
	NATURAL JOIN Vol  
WHERE numero_vol = 'LH9101' AND id_passager = 'PASS00000003';

-- Est-ce que le passager a déjà embarqué ?
SELECT num_res, date_scan, numero_vol, resultat FROM Scan 
	NATURAL JOIN Reservation
WHERE num_res = 'RES00003' AND numero_vol='LH9101';

SELECT num_res, date_scan, numero_vol, resultat FROM Scan 
	NATURAL JOIN Reservation
WHERE num_res = 'RES00009' AND numero_vol='EK2345';

------------------- WEB -------------------
/* Information dont nous disposons :
	- Identifiant utilisateur : USER00000011
	- Mot de passe utilisateur : . . .
*/

-- Quel est l'utilisateur relié au login ?
SELECT * FROM Utilisateur WHERE id_user = 'USER00000011';

-- Quel est le statut de l'embarquement ?
SELECT date_depart FROM Vol WHERE numero_vol = 'LH9101';

-- Combien de passagers ont déjà embarqué ?
SELECT COUNT(*) FROM Scan
	NATURAL JOIN Reservation
	NATURAL JOIN Vol
WHERE numero_vol = 'LH9101';

-- Quelle est la place du voyageur ?
SELECT siege FROM Reservation WHERE num_res = 'RES00003';






