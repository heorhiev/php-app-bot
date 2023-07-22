<?php

namespace app\bot\services;

use app\toolkit\services\Service;


class Message extends Service
{
    private $id;
    private $callbackId;
    private $chatId;
    private $command;
    private $params;
    private $userName;
    private $isCallbackQuery;


    public function __construct($update)
    {
        if ($update->getMessage()) {
            $this->mapMessage($update->getMessage());
        } elseif ($update->getCallbackQuery()) {
            $this->mapCallbackQuery($update->getCallbackQuery());
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCallbackId()
    {
        return $this->callbackId;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getUsername()
    {
        return $this->userName;
    }

    public function isCallbackQuery()
    {
        return $this->isCallbackQuery;
    }

    public function isEdited(): bool
    {
        return $this->isCallbackQuery;
    }

    private function mapMessage($message)
    {
        $this->id = $message->getId();
        $this->chatId = $message->getChat()->getId();
        $this->userName = $message->getChat()->getUsername();
        $this->command = $message->getCommand();
        $this->params = $message->getParams();
    }

    private function mapCallbackQuery($callbackQuery)
    {
        $this->mapMessage($callbackQuery->getMessage());

        $this->isCallbackQuery = true;
        $this->callbackId = $callbackQuery->getId();
        $this->command = $callbackQuery->getCommand();
        $this->params = $callbackQuery->getParams();
    }
}