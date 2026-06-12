SELECT DISTINCT
    S.nomset, --AS NomeDepartamento,
    V.tipmer, --AS MeritoDocente,
    M.codpes, --AS NUSP,
    V.nompes, --AS NomeDocente,
    M.coddis, --AS Disciplina,
    M.codtur --AS Turma
FROM MINISTRANTE M
INNER JOIN VINCULOPESSOAUSP V ON V.codpes = M.codpes
INNER JOIN SETOR S ON S.codset = V.codset
WHERE V.codfusclgund IN (__unidades__)