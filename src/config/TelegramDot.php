<?php

namespace app\bot\dto\config;

use app\toolkit\dto\Dto;


class TelegramDto extends Dto
{
    public $defaultLang;
    public $vacancyBotToken;
    public $vacancyBotFinaleUrl;
    public $vacancyBotFinaleText;
}