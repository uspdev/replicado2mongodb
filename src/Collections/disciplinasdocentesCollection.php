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
        $query = $this->getQuery('listarDisciplinasDocentes.sql');
        $query = str_replace('__docentes__', [1963793] ,$query);
        $query = str_replace('__semestres__', env('REPLICADO_CODUNDCLG'), $query);

        $disciplinasdocentes = ReplicadoDB::fetchAll($query);

        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($disciplinasdocentes as $registro) {
            $bulk[] = [
                'updateOne' => [
                    ['codcur' => $registro['codcur']],
                    [
                        '$set' => [
                            'codcur' => $registro['codcur'],
                            'nomcur' => $registro['nomcur'],
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