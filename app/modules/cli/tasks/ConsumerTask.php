<?php
/**
 * User: Wajdi Jurry
 * Date: 12/02/19
 * Time: 10:50 م
 */

namespace app\modules\cli\tasks;


use app\common\utils\AMQPHandler;
use Phalcon\Cli\Task;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use app\common\enums\QueueNamesEnum;
use app\modules\cli\request\Handler as RequestHandler;

class ConsumerTask extends Task
{

    const SYNC_QUEUE_NAME = QueueNamesEnum::CATEGORY_SYNC_QUEUE;
    const ASYNC_QUEUE_NAME = QueueNamesEnum::CATEGORY_ASYNC_QUEUE;

    public function syncConsumerAction()
    {
        /** @var AMQPHandler $amqpHandler */
        $amqpHandler = $this->getDI()->getAmqp();
        $amqpHandler->declareSync();

        $channel = $amqpHandler->getChannel();

        try {
            $channel->basic_qos(null, 1, null);
            $channel->basic_consume(self::SYNC_QUEUE_NAME,
                '', false, false, false, false,
                function (AMQPMessage $request) use ($channel) {
                    $payload = json_decode($request->getBody(), true);
                    /** @var AMQPChannel $amqpRequest */
                    $amqpRequest = $request->delivery_info['channel'];
                    try {

                        // handle request
                        $message = RequestHandler::getInstance()->process(
                            $payload['route'],
                            $payload['method'],
                            $payload['body'],
                            $payload['query'],
                            $payload['headers']
                        );

                    } catch (\Throwable $exception) {
                        $this->di->getLogger()->logError($exception->getMessage());
                        $message = json_encode([
                            'hasError' => true,
                            'message' => $exception->getMessage(),
                            'status' => $exception->getCode() ?: 500
                        ]);
                    }
                    $amqpRequest->basic_ack($request->delivery_info['delivery_tag']);
                    $amqpRequest->basic_publish(new AMQPMessage($message, [
                        'correlation_id' => $request->get('correlation_id'),
                        'reply_to' => $request->get('reply_to')
                    ]), '', $request->get('reply_to'));
                }
            );

            while (count($channel->callbacks)) {
                $channel->wait();
            }

        } catch (\Throwable $exception) {
            $this->logger->logError($exception->getMessage());
        }
    }

    public function asyncConsumerAction()
    {
        /** @var AMQPHandler $amqpHandler */
        $amqpHandler = $this->getDI()->getAmqp();
        $amqpHandler->declareAsync();

        $channel = $amqpHandler->getChannel();

        try {
            $channel->basic_qos(null, 1, null);
            $channel->basic_consume(self::ASYNC_QUEUE_NAME, '', false, true, false, false,
                function (AMQPMessage $message) {
                    $payload = json_decode($message->getBody(), true);
                    RequestHandler::getInstance()->process(
                        $payload['route'],
                        $payload['method'],
                        $payload['body'],
                        $payload['query'],
                        $payload['headers']
                    );
                }
            );

            while (count($channel->callbacks)) {
                $channel->wait();
            }
        } catch (\Throwable $exception) {
            $this->di->getLogger()->logError($exception->getMessage());
        }
    }
}