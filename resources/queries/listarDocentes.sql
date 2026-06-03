SELECT DISTINCT 
V.codpes, V.nompes, S.nomset, V.tipmer, V.nomabvcla, V.nomabvfnc, V.sitatl, V.sitoco, V.dtafimvin, V.dtafimdctati
FROM VINCULOPESSOAUSP V
INNER JOIN SETOR S
	ON S.codset = V.codset 
WHERE V.codfusclgund = __unidades__
AND V.tipvin = 'SERVIDOR'
AND V.nomcaa = 'Docente'
AND V.codset IN (__departamentos__)
__filtros__
ORDER BY V.codpes