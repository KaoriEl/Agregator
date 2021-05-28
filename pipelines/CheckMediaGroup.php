<?php


namespace Madeline\Pipelines;

use PhoneEventHandler;
use PHPUnit\Runner\Exception;

class CheckMediaGroup
{
    private $message;
    private $client;
    public $MadelineProto;
    protected $bot;

    public function __construct($message, $Handler, $client, $bot)
    {
        $this->client = $client;
        $this->message = $message;
        $this->MadelineProto = $Handler;
        $this->bot = $bot;
    }

    public function __invoke($returnData)
    {
        print_r("6. Pipeline 6 - Проверка альбомов");
        echo "\n";
        global $cid;
        if ($returnData["step"] == "end") {
            print_r("6.0 Конец в pipeline 1 || 2 || 3 || 4 || 5. Конец");
            echo "\n";
            return $returnData;
        } else {
            if ($returnData["msg_type"] != "mediaGroup") {
                print_r("6.1 Отправка альбома в канал");
                echo "\n";
                $this->bot->sendMediaGroup($cid);
            }
        }
        print_r("6. Pipeline 6 - Успешно отработал");
        echo "\n";
        echo "|-------------------------<>------------------------|";
        echo "\n";
        echo "\n";
        echo "\n";
        return $returnData;
    }
}