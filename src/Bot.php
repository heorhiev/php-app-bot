<?php

namespace app\bot;

use app\bot\config\TelegramDto;
use app\toolkit\services\SettingsService;
use app\toolkit\services\RenderService;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;


/**
 * @property Client $_bot
 */
abstract class Bot
{
    private $_options;
    private $_bot;
    private $_inlineKeyboardMarkup;
    private $_replyKeyboardMarkup;


    abstract public function handler();

    abstract public static function getVewPath(string $fileName): string;

    public function __construct(string $configFile)
    {
        /** @var TelegramDto $options */
        $this->_options = SettingsService::load($configFile, TelegramDto::class);
        $this->_bot = new Client($this->_options->token);
    }

    public function getOptions(): TelegramDto
    {
        return $this->_options;
    }


    public function getBot(): Client
    {
        return $this->_bot;
    }


    public function setInlineKeyboardMarkup($inlineKeyboardMarkup): self
    {
        $this->_inlineKeyboardMarkup = $inlineKeyboardMarkup;
        return $this;
    }


    public function setReplyKeyboardMarkup($replyKeyboardMarkup): self
    {
        $this->_replyKeyboardMarkup = $replyKeyboardMarkup;
        return $this;
    }


    public function sendMessage($userId, $messageKey, $message = null, array $attributes = []): void
    {
        if (empty($message)) {
            $userLang = null;
            $message = $this->getViewContent($messageKey, $attributes, $userLang);
        }

        $keyboard = null;

        if ($this->_inlineKeyboardMarkup) {
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup($this->_inlineKeyboardMarkup);
        }

        if ($this->_replyKeyboardMarkup) {
            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($this->_replyKeyboardMarkup, true, false, true);
        }

        $this->_bot->sendMessage($userId, $message, 'html', false, null, $keyboard);
    }


    private function getViewContent($messageKey, $attributes, $lang = null): ?string
    {
        $path = self::getVewPath($messageKey);

        if ($lang) {
            $langPath = $path . '.' . $lang;

            if (RenderService::exists($langPath)) {
                $path = $langPath;
            }
        }

        return RenderService::get($path, $attributes);
    }
}