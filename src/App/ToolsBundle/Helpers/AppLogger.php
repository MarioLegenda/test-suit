<?php

namespace App\ToolsBundle\Helpers;

use Symfony\Component\Finder\Finder;


class AppLogger
{
    const NOTIFICATION = 0;
    const WARNING = 1;
    const EXCEPTION = 2;
    const ERROR = 3;

    private $file;
    private $writeData = array(
        'type' => null,
        'date' => null,
        'message' => null,
    );

    public function makeLog($type) {
        $this->resolveType($type);

        $finder = new Finder();
        $realpath = realpath(__DIR__ . '/../../../../app/logs/user');
        $finder->files()->in($realpath);
        foreach ($finder as $file) {
            if ($file->getFilename() === 'good.log') {
                $this->file = fopen($realpath . '/' . $file->getFilename(), 'a');
            } else if ($file->getFilename() === 'bad.log') {
                $this->file = fopen($realpath . '/' . $file->getFilename(), 'a');
            }
        }


        return $this;
    }

    public function addDate(\DateTime $date = null, $format = null) {
        if($date === null) {
            $datetime = new \DateTime();
            $this->writeData['date'] = ($format === null) ? $datetime->format('d.m.Y H:i:s') : $datetime->format($format);

            return $this;
        }

        $this->writeData['date'] = ($format === null) ? $date->format('d.m.Y H:i:s') : $date->format($format);

        return $this;
    }

    public function addMessage($message) {
        if($message === null OR $message === '') {
            $this->writeData['message'] = 'No message supplied by the user';
            return $this;
        }

        $this->writeData['message'] = $message;

        return $this;
    }

    public function log() {
        if($this->writeData['date'] === null) {
            $this->addDate();
        }

        $writeString = 'Type: ' . $this->writeData['type'] . '; Date: ' . $this->writeData['date'] . '; Message: ' . $this->writeData['message']. "\r\n";
        fwrite($this->file, $writeString);
        fclose($this->file);

        $this->writeData = array(
            'type' => null,
            'date' => null,
            'message' => null
        );
    }

    private function resolveType($type) {
        $typeStrings = array('NOTIFICATION', 'WARNING', 'EXCEPTION', 'ERROR');
        $this->writeData['type'] = $typeStrings[$type];
    }
} 