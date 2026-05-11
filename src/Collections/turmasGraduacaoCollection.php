<?php

namespace Uspdev\Replicado2MongoDB\Collections;

use DateTime;
use Uspdev\Replicado2MongoDB\Contracts\CollectionInterface;
use MongoDB\BSON\UTCDateTime;

use Uspdev\Replicado2MongoDB\Database\MongoConnection;
use Uspdev\Replicado\DB as ReplicadoDB;

class turmasGraduacaoCollection extends Collection implements CollectionInterface
{
    public function sync(): void
    {
        
        $siglas = explode(',',env('SIGLAS_DEPARTAMENTOS'));

        $query = $this->getQuery('listarTurmasGraduacaoAtuais.sql',
        [
            '__semestre__' => date("Y") . (date("m") > 6 ? 2 : 1),

            '__siglas__' => implode("%' OR D.coddis LIKE '",$siglas)
        ]);

        $tempTurmas = ReplicadoDB::fetchAll($query);
        $turmas =[];

        //arrumar cada disciplina com um array de horários e um array de professores

        foreach ($tempTurmas as $turma) {
            $codtur = $turma['codtur'];
            $coddis = $turma['coddis'];
            $verdis = $turma['verdis'];
            $cod = $codtur. $coddis . $verdis;
            if (!isset($turmas[$cod])) {
                $turmas[$cod]=[
                    'codtur' => $turma['codtur'],
                    'nomdis' => $turma['nomdis'],
                    'coddis' => $turma['coddis'],
                    'verdis' => $turma['verdis'],
                    'nompes' => [],
                    'horario' => []
                    
                ];
            }
            if (!empty($turma['nompes']) && !in_array($turma['nompes'], $turmas[$cod]['nompes'])) {
                $turmas[$cod]['nompes'][] = $turma['nompes'];
            }

            if (!empty($turma['horario']) && !in_array($turma['horario'], $turmas[$cod]['horario'])) {
                $turmas[$cod]['horario'][] = $turma['horario'];
            }
        }
        
        $now = new UTCDateTime();
        foreach ($turmas as $cod => $turma) {
            $bulk[] = [
                'updateOne' => [
                    ['cod' => $cod],
                    [
                        '$set' => [
                            'turma' => $turma['codtur'],
                            'nomeDisciplina' => $turma['nomdis'],
                            'disciplina' => $turma['coddis'],
                            'versao' => $turma['verdis'],
                            'docentes' => $turma['nompes'],
                            'horario' => $turma['horario'],

                            'updated_at_sync' => $now
                        ]
                    ],
                    ['upsert' => true]
                ]
            ];
        }

        $collection = MongoConnection::getCollection('turmasGraduacaoAtuais');
        if (!empty($bulk)) {
            $collection->bulkWrite($bulk);
        }

        // delete antigos
        $collection->deleteMany([
            'updated_at_sync' => ['$lt' => $now]
        ]);
    }
}