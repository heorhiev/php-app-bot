<?php

namespace app\bot;

use app\bot\config\TelegramDto;
use app\toolkit\services\SettingsService;
use app\toolkit\services\http\RequestService;
use app\bot\models\{IncomeMessage, Message, Command};
use TelegramBot\Api\{Client, BotApi, Types\Update};


abstract class Bot
{
    private $_options;
    private $_botApi;
    private $_dataFromRequest;
    private $_incomeMessage;


    abstract public static function getCommands(): array;


    public function __construct(string $configFile, $data = null)
    {
        /** @var TelegramDto $options */
        $this->_options = SettingsService::load($configFile, TelegramDto::class);
        $this->_botApi = new BotApi($this->_options->token);

        if (!$data) {
            $data = BotApi::jsonValidate(RequestService::raw(), true);
        }

        $this->_dataFromRequest = Update::fromResponse($data);
    }


    public function run(): void
    {
        $command = $this->getIncomeMessage()->getCommand();

        $class = static::getCommandHandler($command);

        if ($class instanceof Command) {
            (new $class($this))->run();
        }
    }


    public static function getCommandHandler($command): ?Command
    {
        $commands = static::getCommands();

        $command = trim($command);

        if ($pos = strpos($command, " ")) {
            $command = substr($command, 0, $pos);
        }

        return $commands[$command] ?? null;
    }


    public function getOptions(): TelegramDto
    {
        return $this->_options;
    }


    public function getBotApi(): BotApi
    {
        return $this->_botApi;
    }


    public function getMenu(): ?array
    {
        return $this->getOptions()->menu;
    }


    public function getIncomeMessage(): IncomeMessage
    {
        if (!$this->_incomeMessage) {
            $this->_incomeMessage = new IncomeMessage($this->_dataFromRequest);
        }

        return $this->_incomeMessage;
    }


    public function sendMessage(Message $message, $acceptEdit = true)
    {
        if ($acceptEdit && $this->getIncomeMessage()->isEdited()) {
            return $this->getBotApi()->editMessageText(
                $this->getIncomeMessage()->getSenderId(),
                $this->getIncomeMessage()->getId(),
                $message->getRenderedContent(),
                'HTML',
                true,
                $message->getKeyboard()
            );
        } else {
            return $this->getBotApi()->sendMessage(
                $this->getIncomeMessage()->getSenderId(),
                $message->getRenderedContent(),
                'HTML',
                true,
                null,
                $message->getKeyboard()
            );
        }
    }
}