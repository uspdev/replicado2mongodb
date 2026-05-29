SELECT V.codpes, V.nompes, S.nomset, V.dtainivin, V.dtafimvin 
    from VINCULOPESSOAUSP V
    INNER JOIN dbo.SETOR S ON V.codset = S.codset 
    AND tipvin = 'ESTAGIARIORH' 
    AND V.dtainivin LIKE '%2026%'
    ORDER BY V.nompes