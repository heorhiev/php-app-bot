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


    public function setKeyboard(array $keyboard): Message
    {
        $this->_keyboard = $keyboard;
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


    public function setContent(string $messageView, array $attributes = []): Message
    {
        $this->_messageView = $messageView;
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