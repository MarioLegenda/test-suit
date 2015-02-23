<?php

namespace App\ToolsBundle\Helpers;


use App\ToolsBundle\Helpers\Exceptions\JsonFormatterException;

class TestJsonFormatter
{
    private $jsonData;
    private static $instance;

    public static function initFormatter(array $jsonData) {
        self::$instance = (self::$instance instanceof TestJsonFormatter) ? self::$instance : new TestJsonFormatter($jsonData);

        return self::$instance;
    }

    private function __construct(array $jsonData) {
        $this->jsonData = $jsonData;
    }

    public function format() {
        $test = array(
            'type',
            'block_type',
            'element',
            'block_id',
            'placeholder',
            'data'
        );



        $formatted = array();
        foreach($this->jsonData as $data) {
            $this->recursiveFormatter($formatted, $data);
        }
    }

    private function recursiveFormatter(array &$dataHolder, array $dataToFormat) {
        foreach($dataToFormat as $key => $data) {
            if($data instanceof \StdClass) {

            }
        }
    }
} 