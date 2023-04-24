<?php
// src/Service/FileUploader.php
namespace App\Service;

use App\Entity\Achievement;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

//#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Achievement::class)]
class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(File $file): File | null
    {
        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $newFile = null;

        try {
            $newFile = $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $newFile;
    }

    public function load(string $fileName): ?File
    {
        $file = null;
        try {
            $file = new File($this->getTargetDirectory() . "/$fileName");
//            dd($this->getTargetDirectory() . "\{$fileName}");
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $file;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}