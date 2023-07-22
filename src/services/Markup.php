<?php

namespace app\bot\services;

use app\toolkit\services\Service;


class Markup extends Service
{
    private $command;
    private $query;

    public function __construct($command, $query = [])
    {
        $this->command = $command;
        $this->query = $query;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getQuery()
    {
        return $this->query;
    }
}