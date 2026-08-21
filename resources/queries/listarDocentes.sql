SELECT DISTINCT 
V.*, S.nomset, L.idfpescpq
FROM VINCULOPESSOAUSP V
INNER JOIN SETOR S
	ON S.codset = V.codset 
LEFT JOIN DIM_PESSOA_XMLUSP  L
	ON V.codpes = L.codpes
WHERE V.codfusclgund IN (__unidades__)
AND V.tipvin = 'SERVIDOR'
AND ( V.tipfnc = 'Docente' or V.nomcaa = 'Pesquisador' )