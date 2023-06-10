<?php

namespace App\EventListener;

use App\Entity\Achievement;
use App\Service\FileUploader;
use Exception;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\Event\LifecycleEventArgs;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class UploadImageListener
{
    private UploaderHelper $uploaderHelper;
    private $cacheManager;
    private FileUploader $fileUploader;

    public function __construct(
        UploaderHelper $uploaderHelper,
        CacheManager $cacheManager,
        FileUploader $fileUploader
    )
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Achievement) {
            return;
        }

        $image = $this->uploaderHelper->asset($entity, 'imageFile');

        if (!$image) {
            return;
        }

        $movedImageFile = $this->fileUploader->upload($entity->getImageFile());
        $pathImage = $movedImageFile->getPathname();

        // Сжимаем изображение
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($pathImage);

        // Генерируем ссылку
        preg_match("/uploads\/[A-z]+\/[0-9A-z-]+\.(png|jpg|gif)$/", $pathImage, $matchesPath);
        $imagePath = $this->cacheManager->getBrowserPath($movedImageFile, 'thumb');
        preg_match('/^http?s?:\/\/[A-z.]+/', $imagePath, $matchesHost);
//        print_r($matchesPath);
        $imagePath = "$matchesHost[0]/api/$matchesPath[0]";
        $entity
            ->setImageSize($movedImageFile->getSize())
            ->setImageName($movedImageFile->getFilename())
            ->setImageFile($movedImageFile)
            ->setThumbnail($imagePath);

        $args->getObjectManager()->flush();

//        $args->getEntityManager()->getRepository();
    }
}