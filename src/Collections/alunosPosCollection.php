<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;
use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class alunosPosCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        $query = $this->getQuery('listarAlunosPosAtivos.sql',
        [
            '__unidades__' => env('REPLICADO_CODUNDCLG')
        ]);

        $data = ReplicadoDB::fetchAll($query);
        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($data as $aluno) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => (int)$aluno['codpes']],
                    [
                        '$set' => [
                            'nome' => $aluno['nome'],
                            'email' => $aluno['email'],
                            'codarea' => $aluno['codarea'],
                            'codPrograma' => $aluno['codcur'],
                            'programa' => $aluno['nomcur'],

                            'dtaInicioVinculo' => $this->DateTime($aluno['dtainivin']),

                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('alunosPos');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}