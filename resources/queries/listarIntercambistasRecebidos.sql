SELECT DISTINCT v.nompes, v.codpes, v.dtainivin, v.dtafimvin, v.numseqpes
FROM VINCULOPESSOAUSP v
WHERE v.tipvin IN ('ALUNOICD', 'ALUNOCONVENIOINT')
AND codfusclgund in (__unidades__)