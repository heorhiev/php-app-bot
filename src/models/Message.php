<?php

namespace app\bot\models;

use app\toolkit\services\RenderService;


class Message
{
    private $_viewFile;
    private $_attributes;
    private $_lang;
    private $_keyboard = [];


    public function __construct(string $viewFile, array $attributes = [], string $lang = null)
    {
        $this->_viewFile = $viewFile;
        $this->_attributes = $attributes;
        $this->_lang = $lang;
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


    public function getContent(): ?string
    {
        $path = $this->_viewFile;

        if ($this->_lang) {
            $langPath = $path . '.' . $this->_lang;

            if (RenderService::exists($langPath)) {
                $path = $langPath;
            }
        }

        return RenderService::get($path, $this->_attributes);
    }
}