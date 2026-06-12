<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;

use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class posgradCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarPosGrad.sql',
            [       
                '__unidades__' => env('REPLICADO_CODUNDCLG')
            ]
        );

        $posgrad = ReplicadoDB::fetchAll($query);

        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($posgrad as $registro) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => $registro['codpes']],
                    [
                        '$set' => [
                            'codpes'   => $registro['codpes'],
                            'email'    => $registro['codema'],
                            'nome'     => $registro['nompes'],
                            'cod_area' => $registro['codare'],
                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('posgrad');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}