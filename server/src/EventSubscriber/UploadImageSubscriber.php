<?php

namespace App\EventSubscriber;

use App\Entity\Achievement;
use App\Service\FileUploader;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class UploadImageSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus
    )
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::POST_UPLOAD => 'postPersist',
//            Events::PRE_REMOVE => 'preRemove',
        ];
    }

    /**
     * @throws Exception
     */
    public function postPersist(Event $event): void
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        // do
    }

    /**
     * @throws Exception
     */
    public function preRemove(Event $event): void
    {
        $mapping = $event->getMapping();
        $mappingName = $mapping->getMappingName();

        if ('products' === $mappingName) {
            $this->dispatch(Achievement::class, $event);
        }
    }

    private function dispatch(string $messageClass, Event $event): void
    {
        $event->cancel();

        $object = $event->getObject();

        $mapping = $event->getMapping();
        $filename = $mapping->getFileName($object);

        $message = new $messageClass($filename);
        $this->messageBus->dispatch($message);
    }
}