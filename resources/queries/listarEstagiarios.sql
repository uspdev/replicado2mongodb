SELECT V.codpes, V.nompes, S.nomset, V.dtainivin, V.dtafimvin 
    from VINCULOPESSOAUSP V
    INNER JOIN SETOR S ON V.codset = S.codset 
WHERE V.tipvin = 'ESTAGIARIORH' 
    AND V.codund IN (__unidades__)