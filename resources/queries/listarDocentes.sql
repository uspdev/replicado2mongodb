SELECT DISTINCT 
V.codpes, V.nompes, S.nomset, V.codset, S.nomabvset, V.tipmer, V.nomabvcla, V.nomabvfnc, V.sitatl, V.sitoco, V.dtafimvin, V.dtafimdctati
FROM VINCULOPESSOAUSP AS V
INNER JOIN SETOR AS S ON S.codset = V.codset 
WHERE V.tipvin = 'SERVIDOR'
	AND V.nomcaa = 'Docente'
	AND V.codfusclgund IN (__unidades__)