<?php

namespace app\bot\models;


class IncomeMessage
{
    private $id;
    private $callbackId;
    private $chat;
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

    public function getCommand()
    {
        return $this->command;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function isCallbackQuery()
    {
        return $this->isCallbackQuery;
    }

    public function isEdited(): bool
    {
        return $this->isCallbackQuery;
    }

    public function getChat()
    {
        return $this->chat;
    }

    public function getSenderFullName(): string
    {
        return trim($this->getChat()->getFirstName() . ' ' . $this->getChat()->getLastName());
    }

    public function getSenderId(): int
    {
        return $this->getChat()->getId();
    }

    private function mapMessage($message)
    {
        $this->id = $message->getId();
        $this->chat = $message->getChat();
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