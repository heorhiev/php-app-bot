<?php

namespace app\bot;

use app\bot\config\TelegramDto;
use app\bot\services\Command;
use app\toolkit\services\RenderService;
use app\toolkit\services\SettingsService;
use TelegramBot\Api\Client;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;
use app\toolkit\services\http\RequestService;
use app\bot\services\Message;


abstract class Bot
{
    private $_options;
    private $_botApi;
    private $_dataFromRequest;
    private $_message;


    abstract public static function getCommands(): array;

    abstract public static function getVewPath(string $fileName): string;


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


    public function handler(): void
    {
        $command = $this->getMessage()->getCommand();

        $class = static::getCommandHandler($command);

        if ($class) {
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


    public function getMessage(): Message
    {
        if (!$this->_message) {
            $this->_message = new Message($this->_dataFromRequest);
        }

        return $this->_message;
    }


    public function sendMessage($messageKey, array $attributes = [], $keyboard = null, $acceptEdit = true)
    {
        $message = static::getViewContent($messageKey, $attributes);

        if ($acceptEdit && $this->getMessage()->isEdited()) {
            return $this->getBotApi()->editMessageText(
                $this->getMessage()->getChatid(),
                $this->getMessage()->getId(),
                $message,
                'HTML',
                true,
                $keyboard
            );
        } else {
            return $this->getBotApi()->sendMessage(
                $this->getMessage()->getChatid(),
                $message,
                'HTML',
                true,
                null,
                $keyboard
            );
        }
    }


    public static function getViewContent($messageKey, $attributes, $lang = null): ?string
    {
        $path = COMMON_PATH . '/bots/vacancy/views/' . $messageKey;

        if ($lang) {
            $langPath = $path . '.' . $lang;

            if (RenderService::exists($langPath)) {
                $path = $langPath;
            }
        }

        return RenderService::get($path, $attributes);
    }
}