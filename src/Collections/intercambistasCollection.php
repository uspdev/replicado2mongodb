<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;
use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class intercambistasCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarIntercambistasRecebidos.sql',
        [
            '__unidades__' => env('REPLICADO_CODUNDCLG')
        ]);

        $data = ReplicadoDB::fetchAll($query);
        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($data as $intercambista) {
            $bulk[] = [
                'updateOne' => [
                    ['codpesseq' => ($intercambista['codpes'].' '.$intercambista['numseqpes'])],
                    [
                        '$set' => [
                            'nome' => $intercambista['nompes'],
                            'codpes' => (int)$intercambista['codpes'],
                            'numseq' => (int)$intercambista['numseqpes'],
                            'dtaInicioVinculo' => $this->DateTime($intercambista['dtainivin']),
                            'dtaFimVinculo' => $this->DateTime($intercambista['dtafimvin']),

                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('intercambistas');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}