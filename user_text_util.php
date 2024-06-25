<?php

require 'FilesService.php';
require 'CsvFileReader.php';
require 'Parser.php';

use test\Parser;

$options = $argv;

unset($options[0]);
$parser = new Parser('texts', 'people.csv', 'output_texts');

    $parser->setOptions($options);

    try {
        $output = $parser->getResultOfParsing();
        print_r($output);

    } catch (Exception $e) {
        printf("%s", $e->getMessage());
    }

