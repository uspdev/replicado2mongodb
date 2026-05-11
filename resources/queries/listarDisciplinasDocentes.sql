SELECT DISTINCT
    S.nomset AS nomset,
    V.tipmer AS meritoDocente,
    M.codpes AS codpes,
    V.nompes AS nompes,
    M.coddis AS disciplina,
    M.codtur AS turma
FROM fflch.dbo.MINISTRANTE M
INNER JOIN fflch.dbo.VINCULOPESSOAUSP V ON V.codpes = M.codpes
INNER JOIN fflch.dbo.SETOR S ON S.codset = V.codset
WHERE V.codund IN (__unidades__)