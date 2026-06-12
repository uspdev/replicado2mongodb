SELECT DISTINCT 
    V.codpes, --AS NUSP,
    L.codema, --AS Email, 
    L.nompes, --AS Nome,
    V.codare 
    FROM VINCULOPESSOAUSP AS V
    INNER JOIN LOCALIZAPESSOA as L ON (V.codpes = L.codpes)
    WHERE V.tipvin = 'ALUNOPOS'
        AND V.sitatl = 'A'
        AND V.codfusclgund IN (__unidades__)