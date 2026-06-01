<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;
use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class estagiariosCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarEstagiarios.sql',
        [
            '__unidades__' => env('REPLICADO_CODUNDCLG')
        ]);

        $data = ReplicadoDB::fetchAll($query);
        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($data as $estagiario) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => (int)$estagiario['codpes']],
                    [
                        '$set' => [
                            'nome' => $estagiario['nompes'],
                            'setor' => $estagiario['nomset'],
                            'dtaInicio' => $this->DateTime($estagiario['dtainivin']),
                            'dtaFim' => $this->DateTime($estagiario['dtafimvin']),

                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('estagiarios');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}