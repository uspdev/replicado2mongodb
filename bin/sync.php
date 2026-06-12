#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Uspdev\Replicado2MongoDB\Bootstrap;
use Uspdev\Replicado2MongoDB\Console\SyncRunner;

Bootstrap::init();

// Roda uma collection em particular

use Uspdev\Replicado2MongoDB\Collections\docentesCollection;
$sync = new docentesCollection();
$sync->sync();


// Roda todas collections
/* $runner = new SyncRunner();
$runner->run(); */



