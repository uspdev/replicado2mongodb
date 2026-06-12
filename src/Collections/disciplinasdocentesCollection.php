<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;

use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class disciplinasdocentesCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarDisciplinasDocentes.sql',
            [       
                '__unidades__' => env('REPLICADO_CODUNDCLG')
            ]
        );
        $disciplinasdocentes = ReplicadoDB::fetchAll($query);
        $qtd = count($disciplinasdocentes);

        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($disciplinasdocentes as $registro) {
            $bulk[] = [
                'updateOne' => [
                    ['nusp_docente' => $registro['codpes'],
                     'disciplina' => $registro['coddis'],
                     'turma'      => $registro['codtur']
                     ],
                    [
                        '$set' => [
                            'departamento' => $registro['nomset'],
                            'merito_docente' => $registro['tipmer'],
                            'nusp_docente'   => $registro['codpes'],
                            'nome_docente'   => $registro['nompes'],
                            'disciplina'    => $registro['coddis'],
                            'turma'         => $registro['codtur'],
                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('disciplinasdocentes');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}