<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;
use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class disciplinasDocentesCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarDisciplinasDocentes.sql',
        [
            '__unidades__' => env('REPLICADO_CODUNDCLG')
        ]);

        $data = ReplicadoDB::fetchAll($query);
        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($data as $row) {
            $bulk[] = [
                'updateOne' => [
                    ['cod' => $row['codpes'].$row['turma'].$row['disciplina']],
                    [
                        '$set' => [
                            'nomeDocente' => $row['nompes'],
                            'codpes' => (int)$row['codpes'],
                            'nomeDepartamento' => $row['nomset'],
                            'meritoDocente' => $row['meritoDocente'],
                            'turma' => $row['turma'],
                            'disciplina' => $row['disciplina'],
                            'semestre' => (int)substr($row['turma'],0,5),

                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('disciplinasDocentes');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}