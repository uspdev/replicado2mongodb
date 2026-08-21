<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
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
        ]);

        $data = ReplicadoDB::fetchAll($query);
        // Pegar dados do replicado
        $now = new UTCDateTime();
        foreach ($data as $docente) {
            $bulk[] = [
                'updateOne' => [
                    ['codpes' => (int)$docente['codpes']],
                    [
                        '$set' => [
                            'nome' => $docente['nompes'],
                            'codset' => (int)$docente['codset'],
                            'departamento' => $docente['nomset'],
                            'merito' => $docente['tipmer'],
                            'classe' => $docente['nomabvcla'],
                            'funcao' => $docente['nomabvfnc'],
                            'status' => $docente['sitatl'],
                            'ultimaOcorrencia' => $docente['sitoco'],
                            'idLattes' => $docente['idfpescpq'],
                            
                            'dtaInicioAtividade' => $this->DateTime($docente['dtainidctati']),
                            'dtaFimAtividade' => $this->DateTime($docente['dtafimdctati']),

                            'dtaInicioVinculo' => $this->DateTime($docente['dtainivin']),
                            'dtaFimVinculo' => $this->DateTime($docente['dtafimvin']),

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