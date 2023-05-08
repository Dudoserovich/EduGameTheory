<?php

namespace App\EventListener;

use App\Entity\Achievement;
use App\Service\FileUploader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UploadImageListener
{
    private $uploaderHelper;
    private $cacheManager;
    private $fileUploader;

    public function __construct(UploaderHelper $uploaderHelper, CacheManager $cacheManager, FileUploader $fileUploader)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->fileUploader = $fileUploader;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Achievement) {
            return;
        }

        $image = $this->uploaderHelper->asset($entity, 'imageFile');

        if (!$image) {
            return;
        }

        $movedImageFile = $this->fileUploader->upload($entity->getImageFile());
        $imagePath = $this->cacheManager->getBrowserPath($movedImageFile, 'thumb');
        $entity
            ->setImageSize($movedImageFile->getSize())
            ->setImageName($movedImageFile->getFilename())
            ->setImageFile($movedImageFile)
            ->setThumbnail($imagePath);

        $args->getEntityManager()->flush();
    }
}