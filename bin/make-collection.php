<?php
    #Diretórios do esqueleto e de collections
    $stubPath = __DIR__ . '/../stubs/collection.stub';
    $collectionDir = __DIR__ . '/../src/Collections/';

    #Verifica se foi passado um argumento
    if (is_null($argv[1])) {
        echo "Erro: Você precisa passar o nome da coleção.\n";
        echo "Exemplo: php gerar-collection.php Alunos\n";
        exit(1);
    }

    #Verifica se existe um arquivo de esqueleto 
    if (!file_exists($stubPath)) {
        echo "Erro: Arquivo padrão não encontrado.";
        exit(1);
    }
    #Guarda o nome o a primeira letra maiúscula e com todas as letras minúsculas
    $name_capitalize = ucfirst($argv[1]); 
    $name_lower      = strtolower($name_capitalize);

    #Caminho do arquivo da nova collection
    $collectionPath = $collectionDir . $name_lower . 'Collection.php';

    #guarda no $content o conteúdo do esqueleto com os nomes trocados
    $content = file_get_contents($stubPath);
    $content = str_replace('{{ name_capitalize }}', $name_capitalize, $content);
    $content = str_replace('{{ name_lower }}', $name_lower, $content);

    #coloca o conteúdo final no arquivo collection e verifica se teve sucesso
    if(file_put_contents($collectionPath, $content) !== false){
        echo "Arquivo criado: $collectionPath\n";
    }
    else{
        echo "Erro ao salvar arquivo.";
    }

