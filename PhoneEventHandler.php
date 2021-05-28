<?php

use League\Pipeline\Pipeline;
use Madeline\Pipelines\MediaMessage;
use Madeline\Pipelines\TextMessage;
use Madeline\Pipelines\GetHeaderInfo;
use Madeline\Pipelines\CheckChanel;
use Madeline\Pipelines\RedisInsert;
use Madeline\Pipelines\CheckMediaGroup;

use Formats\Message;

class PhoneEventHandler extends \danog\MadelineProto\EventHandler
{
    const ADMIN = 'krivets_r';
    public $MadelineProto;
    protected $bot;

    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $this->MadelineProto = $this;
        $this->bot = new PostingFromBot();

    }

    public function getReportPeers()
    {
        return [self::ADMIN];
    }

    /**
     * @param array $update
     * @return Generator
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        echo "_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_";
        echo "\n";
        print_r("PhoneEventHandler - Получил сообщение в onUpdateNewChannelMessage");
        echo "\n";
        return yield $this->onUpdateNewMessage($update);
    }

    /**
     * @param array $update
     * @return Generator|void
     */
    public function onUpdateNewMessage(array $update)
    {
        print_r("PhoneEventHandler - Получил сообщение в onUpdateNewMessage");
        echo "\n";
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            echo "Rofl";
            print_r($update);
            return;
        }
        $returnData = \json_encode($update, JSON_PRETTY_PRINT);
        if (isset($returnData))
        {
            print_r("PhoneEventHandler - Отправляюсь в  функцию pipeLines");
            echo "\n";
            yield self::pipeLines($update);
        }
    }

    /**
     * @param $update
     */
    public function pipeLines($update)
    {
        global $cid;
        $message = $update['message'];

        ob_start();
        echo "\n";
        echo "\n";
        echo "\n";
        echo "\n";
        print_r($update);
        echo "\n";
        echo "\n";
        echo "\n";
        echo "\n";
        $debug = ob_get_contents();
        ob_end_clean();
        $fp = fopen('/var/www/madeline/logs/update.txt', 'a+');
        fwrite($fp, $debug);
        fclose($fp);

        $client = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'redis',
            'port'   => 6379,
            'parameters' => [
                'timeout' => 0,
                'read_write_timeout' => -1,
            ],
        ]);



        if (isset($message)) {
            print_r("PhoneEventHandler - запускаю pipelines");
            echo "\n";
            echo "_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_";
            echo "\n";
            $pipeline = (new \League\Pipeline\Pipeline)
                ->pipe(new CheckChanel($message, $this->MadelineProto))
                ->pipe(new TextMessage($message))
                ->pipe(new GetHeaderInfo($message, $this->MadelineProto))
                ->pipe(new MediaMessage($message, $this->MadelineProto))
                ->pipe(new RedisInsert($message, $this->MadelineProto, $client))
                ->pipe(new CheckMediaGroup($message, $this->MadelineProto, $client, $this->bot));
                $returnData = yield $pipeline->process($message);
        }
    }
}
