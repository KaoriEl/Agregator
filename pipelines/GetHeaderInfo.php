<?php


namespace Madeline\Pipelines;

use PhoneEventHandler;

class GetHeaderInfo extends PhoneEventHandler
{
    private $message;
    public $MadelineProto;

    public function __construct($message, $Handler)
    {
        $this->message = $message;
        $this->MadelineProto = $Handler;
    }

    /**
     * Асинхронность мэделина вкл и выкл потому что иначе он не работает с пайплайнами, да - это костыль, ни в коем случае не убирать
     * @param $returnData
     * @return |null
     */
    public function __invoke($returnData)
    {
        print_r("3. Pipeline 3 - получение шапки сообщения");
        echo "\n";
        if (isset($returnData)) {
            if ($returnData["step"] == "end") {
                print_r("3.0 Конец в pipeline 1 || 2. Конец");
                echo "\n";
                return $returnData;
            } else {

                if (isset($this->message['fwd_from']['channel_id'])) {
                    $id_fwd_chanel = $this->message['fwd_from']['channel_id'];
                    $this->MadelineProto->async(false);
                    $info_fwd_chanel = $this->MadelineProto->getInfo('-100' . $id_fwd_chanel);
                    $this->MadelineProto->async(true);
                    $returnData["forward_chanel"]["chat_fwd_name"] = $info_fwd_chanel["Chat"]["title"];
                    $returnData["forward_chanel"]["chat_fwd_username"] = $info_fwd_chanel["Chat"]["username"];
                    $returnData["forward_chanel"]["chat_fwd_post_id"] = $this->message['fwd_from']["channel_post"];

                    print_r("3.1 fwd_from шапка сообщения ");
                    echo "\n";
                }

                $this->MadelineProto->async(false);
                $chat = $this->MadelineProto->getInfo("-100" . $returnData["channel_id"]);
                $this->MadelineProto->async(true);
                $returnData["chat_name"] = $chat["Chat"]["title"];
                $returnData["chat_username"] = $chat["Chat"]["username"];
                $returnData["step"] = "get_header_complete";
                print_r("3.1 Получил шапку сообщения ");
                echo "\n";
            }
            if (isset($returnData["chat_name"])) {
                if (isset($returnData["forward_chanel"])) {
                    $returnData["message"] = "from <b>" . $returnData["forward_chanel"]["chat_fwd_name"] . "</b>" . "\n" . $returnData["message"];
                    print_r("3.2 Добавление fwd_from в начало сообщения ");
                    echo "\n";
                }
                $returnData["message"] = "<b>" . $returnData["chat_name"] . "</b>" . "\n" . $returnData["message"];
                print_r("3.2 Добавление названия чата в начало сообщения ");
                echo "\n";
            }
            $returnData["message"] = $returnData["message"] . "\n" . '<a href="t.me/' . $returnData["chat_username"] . '/' . $returnData["msg_id"] . '">' . '@' . '</a>';
            print_r("3.3 вставка ссылки на пост в @ ");
            echo "\n";
//            print_r($returnData["message"]);
        }
        print_r("3. Pipeline 3 - успешно выполнен");
        echo "\n";
        return $returnData;
    }
}