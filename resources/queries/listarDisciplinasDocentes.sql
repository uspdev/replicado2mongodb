SELECT DISTINCT
    S.nomset AS nomset,
    V.tipmer AS meritoDocente,
    M.codpes AS codpes,
    V.nompes AS nompes,
    M.coddis AS disciplina,
    M.codtur AS turma
FROM MINISTRANTE M
INNER JOIN VINCULOPESSOAUSP V ON V.codpes = M.codpes
INNER JOIN SETOR S ON S.codset = V.codset
WHERE V.codund IN (__unidades__)