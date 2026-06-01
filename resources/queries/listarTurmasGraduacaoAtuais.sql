SELECT DISTINCT TR.coddis, TR.codtur, TR.verdis, D.nomdis, P.nompes, horario = (O.diasmnocp + ' - ' + PH.horent + ' - ' + PH.horsai) FROM TURMAGR TR
					LEFT JOIN DISCIPLINAGR D ON 
						TR.coddis = D.coddis AND TR.verdis = D.verdis
					LEFT JOIN MINISTRANTE M ON
						TR.codtur = M.codtur AND TR.verdis = M.verdis AND TR.coddis = M.coddis
					LEFT JOIN PESSOA P ON
						M.codpes = P.codpes
					LEFT JOIN OCUPTURMA O ON
						TR.codtur = O.codtur AND TR.verdis = O.verdis AND TR.coddis = O.coddis
					LEFT JOIN PERIODOHORARIO PH ON
						O.codperhor = PH.codperhor
                    WHERE 
                        TR.codtur LIKE '__semestre__%' 
                    AND TR.verdis = (SELECT MAX(DI.verdis) FROM DISCIPLINAGR AS DI WHERE (DI.coddis = TR.coddis) AND dtaatvdis IS NOT NULL)
					AND (D.coddis LIKE '__siglas__%')
					