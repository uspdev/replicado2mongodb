<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;

use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class estagiariosCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarEstagiarios.sql');
        $estagiarios = ReplicadoDB::fetchAll($query);

        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($estagiarios as $registro) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => $registro['codpes']],
                    [
                        '$set' => [
                            'codpes'=> $registro['codpes'],
                            'nome'  => $registro['nompes'],
                            'setor' => $registro['nomset'],
                            'inicio'=> explode(' ', $registro['dtainivin'])[0],
                            'fim'   => explode(' ', $registro['dtafimvin'])[0],
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