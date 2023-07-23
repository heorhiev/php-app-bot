<?php

namespace app\bot\models;

use app\bot\config\TelegramDto;
use app\toolkit\services\AliasService;
use app\toolkit\services\RenderService;


class Message
{
    private $_options;
    private $_keyboard = [];
    private $_lang;

    private $_messageView;
    private $_attributes;


    public function __construct(TelegramDto $options)
    {
        $this->_options = $options;
    }


    public function setReplyKeyboardMarkup(array $buttons): Message
    {
        $this->_keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($buttons, true, true, true);
        return $this;
    }


    public function setInlineKeyboardMarkup(array $buttons): Message
    {
        $this->_keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup($buttons);
        return $this;
    }


    public function getKeyboard(): array
    {
        return $this->_keyboard;
    }


    public function setLang(string $lang): Message
    {
        $this->_lang = $lang;
        return $this;
    }


    public function setMessageView(string $messageView): Message
    {
        $this->_messageView = $messageView;
        return $this;
    }


    public function setAttributes(array $attributes = []): Message
    {
        $this->_attributes = $attributes;
        return $this;
    }


    public function getRenderedContent(): ?string
    {
        $path = AliasService::getAlias($this->_options->viewDirectory . '/' . $this->_messageView);

        if ($this->_lang) {
            $langPath = $path . '.' . $this->_lang;

            if (RenderService::exists($langPath)) {
                $path = $langPath;
            }
        }

        return RenderService::get($path, $this->_attributes);
    }
}