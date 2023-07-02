<?php

namespace App\MessageHandler;

use App\Entity\Achievement;
use App\Message\RemoveProductImageMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveImageMessageHandler implements MessageHandlerInterface
{
    public function __invoke(RemoveProductImageMessage $message): void
    {
        $file = $message->getFile();

        unlink($file->getFilename());

        // delete your file according to your mapping configuration
    }
}