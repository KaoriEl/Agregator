<?php


namespace Madeline\Pipelines;


use PhoneEventHandler;
use PHPUnit\Runner\Exception;

class RedisInsert
{
    private $message;
    private $client;
    public $MadelineProto;

    public function __construct($message, $Handler, $client)
    {
        $this->client = $client;
        $this->message = $message;
        $this->MadelineProto = $Handler;
    }

    public function __invoke($returnData)
    {
        print_r("5. Pipeline 5 - запись в редис");
        echo "\n";
        global $cid;
        if ($returnData["step"] == "end") {
            print_r("5.0 Конец в pipeline 1 || 2 || 3 || 4. Конец");
            echo "\n";
            return $returnData;
        } else {
            if ($returnData["validate"] == "Communication of messages and chats is established") {
                if ($returnData["msg_type"] == "media" || $returnData["msg_type"] == "text") {
                    $data = json_encode($returnData, JSON_UNESCAPED_UNICODE);
                    try {
                        $this->client->rpush("agregator", $data);
                        print_r("5.1 rpush в редис");
                        echo "\n";
                    } catch (Exception $e) {
                        print_r($e->getMessage());
                        print_r("5.1 Не удалось записать пост в редис");
                        echo "\n";
                    }
                }

                if ($returnData["msg_type"] == "mediaGroup") {
                    $dataMediaGroup = json_encode($returnData, JSON_UNESCAPED_UNICODE);
                    try {
                        $this->client->rpush("agregatorMediaGroup", $dataMediaGroup);
                        print_r("5.1 rpush альбома в редис");
                        echo "\n";
                    } catch (Exception $e) {
                        print_r($e->getMessage());
                        echo "\n";
                        print_r("5.1 Не удалось записать альбом в редис");
                        echo "\n";
                    }
                }
                print_r("5. Pipeline 5 - успешно отработал");
                echo "\n";
                return $returnData;
            }
        }


    }
}