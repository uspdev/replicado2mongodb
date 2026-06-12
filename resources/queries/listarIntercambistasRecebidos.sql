SELECT DISTINCT V.nompes, V.codpes, V.dtainivin, V.dtafimvin, V.tipvin
	FROM VINCULOPESSOAUSP AS V
	WHERE V.tipvin IN ('ALUNOICD', 'ALUNOCONVENIOINT')
		AND V.codfusclgund IN (__unidades__)