<?php

namespace app\bot;

use TelegramBot\Api\Types\Message;


abstract class Command
{
    protected $_bot;
    protected $_message;


    abstract public function run(): void;


    public function __construct(Bot $bot, Message $message)
    {
        $this->_bot = $bot;
        $this->_message = $message;
    }


    public function getBot(): Bot
    {
        return $this->_bot;
    }


    public function getMessage(): Message
    {
        return $this->_message;
    }


    public function getUserId(): int
    {
        return $this->getMessage()->getChat()->getId();
    }
}