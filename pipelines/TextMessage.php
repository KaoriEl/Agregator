<?php

namespace Madeline\Pipelines;
use Formats\Message;
class TextMessage
{

    private $message;

    public function __construct($message)
    {
        $this->message = $message;

    }

    public function __invoke($returnData)
    {
        print_r("2. Pipeline 2 - обработка текста");
        echo "\n";
        $get_pars_massage = Message::html($this->message);
        //бывает что приходит что-то совсем пустое, по этому ставим что валидация не прошла
        if ($returnData["validate"] == "Communication of messages and chats not found" || $returnData["validate"] == "Couldn't find the file with links") {
            $returnData["step"] = "end";
            print_r("2.0 Конец в pipeline 1. Конец");
            echo "\n";
            return $returnData;
        }else{
//            print_r($this->message);

            if (!isset($this->message['_']) && $this->message['_'] == "messageService") {
                print_r("2.2 Запись закреплена, по этому не вышла. Конец");
                echo "\n";
                return $returnData["step"] = "end";
            } else {
                if (isset($this->message["media"]) && $this->message["media"]["_"] != "messageMediaWebPage"){
                    $returnData["msg_type"] = "media";
                    print_r("2.1 Определил тип сообщения как - Медиа");
                    echo "\n";
                }else{
                    $returnData["msg_type"] = "text";
                    print_r("2.1 Определил тип сообщения как - Текст");
                    echo "\n";
                }
                if (isset($this->message["id"])) {
                    $returnData["msg_id"] = $this->message["id"];
                    print_r("2.2 Получил id сообщения");
                    echo "\n";
                } else {
                    print_r("2.2 Получил id сообщения. Конец");
                    echo "\n";
                    $returnData["step"] = "end";
                }
//                print_r($this->message);
//                die();
                if (isset($this->message["to_id"]["channel_id"])) {
                    $returnData["channel_id"] = $this->message["to_id"]["channel_id"];
                    print_r("2.2 Получил id канала с которого отправлено сообщение");
                    echo "\n";
                } else {
                    print_r("2.2 Получил id канала с которого отправлено сообщение. Конец");
                    echo "\n";
                    $returnData["step"] = "end";
                }
                if (isset($this->message["date"])) {
                    $returnData["date"] = $this->message["date"];
                    print_r("2.2 Получил дату сообщения");
                    echo "\n";
                } else {
                    print_r("2.2 Получил дату сообщения. Конец");
                    echo "\n";
                    $returnData["step"] = "end";
                }
                if (isset($this->message["message"])) {
                    $returnData["message"] = $get_pars_massage;
                    print_r("2.2 Получил Текст сообщения");
                    echo "\n";
                } else {
                    print_r("2.2 Получил Текст сообщения. Конец");
                    echo "\n";
                    $returnData["step"] = "end";
                }
                if(isset($this->message['grouped_id'])){
                    $returnData["msg_type"] = "mediaGroup";
                    $returnData["grouped_id"] = $this->message['grouped_id'];
                    print_r("2.1 Определил тип сообщения как - Альбом");
                    echo "\n";
                    print_r("2.2 Получил его grouped_id");
                    echo "\n";
                }
                if ($returnData["step"] != "end")
                {
                    print_r("2.3 Поставил шаг на take_text");
                    echo "\n";
                    $returnData["step"] = "take_text";
                }
            }
            print_r("2. Pipeline 2 - успешно завершен");
            echo "\n";
            return $returnData;
        }
    }

}