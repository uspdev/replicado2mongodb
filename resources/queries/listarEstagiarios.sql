SELECT V.codpes, V.nompes, S.nomset, V.dtainivin, V.dtafimvin 
    FROM VINCULOPESSOAUSP V
    INNER JOIN SETOR S ON V.codset = S.codset 
    AND tipvin = 'ESTAGIARIORH' 
    AND V.dtainivin LIKE '%__ano__%'
    ORDER BY V.nompes