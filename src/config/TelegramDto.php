<?php

namespace app\bot\config;

use app\toolkit\dto\Dto;


class TelegramDto extends Dto
{
    public $defaultLang;
    public $token;
    public $buttons;
    public $viewDirectory;
    public $menu;
    public $data;
}