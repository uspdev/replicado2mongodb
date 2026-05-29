<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;

use MongoDB\BSON\UTCDateTime;
use MongoDB\ClientBulkWrite;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class estagiariosCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        //pegar dados do replicado
        $query = $this->getQuery('listarEstagiarios.sql');
        $registros = ReplicadoDB::fetchAll($query);

        //passar os dados do replicado para o mongoDB
        $now = new UTCDateTime();
        $collection = MongoConnection::getCollection('estagiarios');
        $bulkWrite  = ClientBulkWrite::createWithCollection($collection);

        foreach ($registros as $registro) {  
            $bulkWrite->updateOne(
                ['codpes'     => $registro['codpes']],
                ['$set'   => [
                            'codpes'     => $registro['codpes'],
                            'nome'       => $registro['nompes'],
                            'setor'      => $registro['nomset'],
                            'data_inicio'=> explode(' ', $registro['dtainivin'])[0],
                            'data_fim'   => explode(' ', $registro['dtafimvin'])[0],
                            'updated_at_sync'  => $now
                             ] 
                ],
                ['upsert' => true]
            );
        }
            
        // deletar antigos
        $bulkWrite->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);

        //executar o bulkWrite
        MongoConnection::getClient()->bulkWrite($bulkWrite);
    }
}