<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;

use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class docentesCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarDocentes.sql',
            [       
                '__unidades__' => env('REPLICADO_CODUNDCLG')
            ]
        );

        $docentes = ReplicadoDB::fetchAll($query);

        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($docentes as $registro) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => $registro['codpes']],
                    [
                        '$set' => [
                            'codpes'            => $registro['codpes'],
                            'nome_docente'      => $registro['nompes'],
                            'nome_setor'        => $registro['nomset'],
                            'cod_setor'         => $registro['codset'],
                            'clg_setor'         => $registro['nomabvset'],
                            'merito'            => $registro['tipmer'],
                            'classe'            => $registro['nomabvcla'],
                            'funcao'            => $registro['nomabvfnc'],
                            'status'            => $registro['sitatl'],
                            'ultima_ocorrencia' => $registro['sitoco'],
                            'fim_vinculo'       => explode(' ',$registro['dtafimvin'] ?? '')[0],
                            'fim_atividade'     => explode(' ',$registro['dtafimdctati'] ?? '')[0],
                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('docentes');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}