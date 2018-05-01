<?php

namespace Configuration\Service;

use Zend\Config\Config;
use Zend\Config\Writer\Ini;
use Zend\Config\Writer\Json;
use Zend\Config\Writer\PhpArray;
use Zend\Config\Writer\Xml;
use Zend\Config\Writer\Yaml;

class FileGenerator
{
    /**
     * @param array $configuration
     * @param string $format
     * @return string
     */
    public function getFileContentFromArray(array $configuration, string $format): string
    {
        $config = new Config($configuration);

        switch ($format) {
            case 'php':
                $writer = new PhpArray();
                break;
            case 'json':
                $writer = new Json();
                break;
            case 'yaml':
                require_once('lib/Spyc.php');
                $writer = new Yaml(['Spyc','YAMLDump']);
                break;
            case 'xml':
                $writer = new Xml();
                break;
            case 'ini':
                $writer = new Ini();
                break;
            default:
                $writer = new Json();
                break;
        }

        return $writer->toString($config);
    }
}
