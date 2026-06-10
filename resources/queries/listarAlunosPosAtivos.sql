SELECT DISTINCT 
	V.codpes AS codpes,
	L.codema AS email, 
	L.nompes AS nome,
    V.codare AS codarea,
    C.codcur AS codcur,
    NC.nomcur AS nomcur,
    V.dtainivin,
    V.dtafimvin
FROM VINCULOPESSOAUSP AS V
	INNER JOIN AREA AS A 
		ON A.codare = V.codare 
    INNER JOIN CURSO AS C 
    	ON C.codcur = A.codcur
    INNER JOIN NOMECURSO AS NC 
    	ON C.codcur = NC.codcur       
 	INNER JOIN LOCALIZAPESSOA as L
    	ON (V.codpes = L.codpes)
WHERE V.tipvin = 'ALUNOPOS'
	AND V.sitatl = 'A'
	AND L.tipvin = 'ALUNOPOS'
    AND V.codfusclgund in (__unidades__)