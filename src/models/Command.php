<?php

namespace app\bot\models;

use app\bot\Bot;
use app\toolkit\services\Service;
use TelegramBot\Api\Types\Message;


abstract class Command extends Service
{
    protected $_bot;


    abstract public function run(): void;


    public function __construct(Bot $bot)
    {
        $this->_bot = $bot;
    }


    public function getBot(): Bot
    {
        return $this->_bot;
    }


    public function getUserId(): int
    {
        return $this->getBot()->getMessage()->getChatId();
    }
}