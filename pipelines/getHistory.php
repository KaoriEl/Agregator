<?php


namespace Madeline\Pipelines;
use PhoneEventHandler;

class getHistory extends PhoneEventHandler
{
    private $message;
    public $MadelineProto;

    public function __construct($message, $Handler)
    {
        $this->message = $message;
        $this->MadelineProto = $Handler;
    }

    public function __invoke($message)
    {

        $returnData = [];
        $returnData["step"] = "start";

        global $cid;
        $path = '/var/www/madeline/json/' . $cid . '/link.json';

        if (file_exists($path)) {
            ob_start();
            $this->MadelineProto->async(false);
            include $path;
            $this->MadelineProto->async(true);
            $chat_channels = ob_get_contents();
            ob_end_clean();
            $chat_channels = json_decode($chat_channels, true);
            echo "\n";
            echo "\n";
            echo "|-------------------------<>------------------------|";
            echo "\n";
            print_r($message['message']);
            echo "\n";
            print_r("Pipeline 1: Проверка подписок: ");
            echo "1.0. Файл найден" . "\n";
        } else {

            $returnData["validate"] = "Couldn't find the file with links";
            $returnData["step"] = "end";
            echo "\n";
            echo "\n";
            echo "|-------------------------<>------------------------|";
            print_r($message);
            echo "\n";
            print_r("Pipeline 1: Проверка подписок: ");
            echo "1.0. Файл не найден" . "\n";
            return $this->__invoke($message);
        }


        if (isset($message["to_id"]["channel_id"])) {
            if (isset($chat_channels[$message["to_id"]["channel_id"]])) {
                foreach ($chat_channels[$message["to_id"]["channel_id"]] as $to_id) {
                    $to = str_replace("'", '"', $to_id["to"]);
                    $to = json_decode($to, true);
                    $returnData["chanel_to_id"] = $to["channel_id"];
                }

                $returnData["validate"] = "Communication of messages and chats is established";
                $returnData["cid"] = $cid;
                print_r("1.1 Чат нашел");
                echo "\n";
            } else {
                if (isset($message["message"])){
                    $returnData["error_message"] = $message["message"];
                    $returnData["validate"] = "Communication of messages and chats not found";
                    $returnData["step"] = "end";
                    print_r("1.1 Чат не нашел. Конец");
                    echo "\n";
                    return $returnData;
                }
            }
        } else {
            $returnData["error_message"] = $message["message"];
            $returnData["validate"] = "Couldn't find the file with links";
            $returnData["step"] = "end";
            print_r("1.1 У поста нету chat id. Конец");
            echo "\n";
            return $returnData;
        }
        print_r("1.2. Pipeline 1 - успешно отработал");
        echo "\n";
        return $returnData;
    }


}