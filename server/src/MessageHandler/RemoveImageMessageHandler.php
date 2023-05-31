<?php

namespace App\MessageHandler;

use App\Entity\Achievement;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveImageMessageHandler implements MessageHandlerInterface
{
    public function __invoke(Achievement $message): void
    {
        $file = $message->getImageFile();

        unlink($file->getFilename());

        // delete your file according to your mapping configuration
    }
}