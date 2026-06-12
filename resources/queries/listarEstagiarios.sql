SELECT V.codpes, V.nompes, S.nomset, V.dtainivin, V.dtafimvin 
    FROM VINCULOPESSOAUSP V
    INNER JOIN SETOR S ON V.codset = S.codset 
    WHERE V.tipvin = 'ESTAGIARIORH'
        AND V.codfusclgund IN (__unidades__)