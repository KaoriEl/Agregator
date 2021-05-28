<?php


namespace Madeline\Pipelines;


use PhoneEventHandler;



class MediaMessage extends PhoneEventHandler
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
     * @return bool
     */
    public function __invoke($returnData)
    {
        print_r("4. Pipeline 4 - выгрузка изображений");
        echo "\n";
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__ , 2));
        $dotenv->load();
        if ($returnData["step"] == "end"){
            print_r("4.0 Конец в pipeline 1 || 2 || 3. Конец");
            echo "\n";
            return $returnData;
        }else{
            if ($returnData["validate"] == "Communication of messages and chats is established"){
                if(isset($this->message['media'])){
                    if ($this->message['media']['_'] == "messageMediaPhoto"){
                        $this->MadelineProto->async(false);
                        $output_file_name = $this->MadelineProto->downloadToDir($this->message, $_ENV['DOWNLOAD_DIRECTORY']);
                        $this->MadelineProto->async(true);
                        $returnData["media_path"] = $output_file_name;
                        $returnData["media_type"] = "photo";
                        $returnData["step"] = "download_media";
                        print_r("4.1 Выгрузка изображения");
                        echo "\n";
                    }
                    if ($this->message['media']['_'] == "messageMediaDocument"){
                        $this->MadelineProto->async(false);
                        $output_file_name = $this->MadelineProto->downloadToDir($this->message, $_ENV['DOWNLOAD_DIRECTORY']);
                        $this->MadelineProto->async(true);
                        $returnData["media_path"] = $output_file_name;
                        $returnData["media_type"] = "vide_or_document";
                        $returnData["step"] = "download_media";
                        print_r("4.1 Выгрузка видео");
                        echo "\n";
                    }
                }
            }
        }
        print_r("4. Pipeline 4 - успешно отработал");
        echo "\n";
        return $returnData;
    }
}
