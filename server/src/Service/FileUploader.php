<?php

namespace App\Service;

use Exception;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validation;

class FileUploader
{
    private string $globalDirectory;
    private SluggerInterface $slugger;

    public function __construct(
        string           $globalDirectory,
        SluggerInterface $slugger
    )
    {
        $this->globalDirectory = $globalDirectory;
        $this->slugger = $slugger;
    }

    public function optimize(
        string $targetDirectory,
        string $fileName
    ): void
    {
        // Сжимаем изображение
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($targetDirectory . $fileName);
    }

    /**
     * @throws Exception
     */
    public function upload(
        File   $file,
        string $targetDirectory = 'achievement'
    ): File|null
    {
        $file->getType();

        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $safeFilenameWithoutSalt = preg_replace("/(-[A-z0-9]+)+$/", "", $safeFilename);
        $fileName = $safeFilenameWithoutSalt . '-' . uniqid() . '.' . $file->guessExtension();

        $newFile = null;

        try {
            $newFile = $file->move(
                $this->getGlobalDirectory() . $targetDirectory,
                $fileName
            );
        } catch (FileException $e) {
            throw new Exception($e->getMessage());
        }

        return $newFile;
    }

    /**
     * Получение картинки в формате base64
     * @param string $dir
     * @param string $name
     * @return string
     */
    public function getImageBase64(string $dir, string $name): string
    {
        $files = scandir($dir);
        $files = array_diff($files, array('.', '..'));

        $imageSrc = null;
        if (in_array($name, $files)) {
            $file = new File($dir . "/$name");

            $imageSize = getimagesize($file);
            $imageData = base64_encode(file_get_contents($file));
            $imageSrc = "data:{$imageSize['mime']};base64,{$imageData}";
        }

        return $imageSrc;
    }

    /**
     * Получение файла из директории
     *
     * @param string $targetDirectory Путь к директории для получения
     * @param string $fileName Название файла
     * @return ?File Загруженный файл
     *
     * @throws Exception
     */
    public function load(
        string $targetDirectory,
        string $fileName
    ): ?File
    {
        $file = null;
        try {
            $file = new File(
                $this->getGlobalDirectory()
                . $targetDirectory
                . "/$fileName"
            );
        } catch (FileException $e) {
            throw new Exception($e->getMessage());
        }

        return $file;
    }

    /**
     * Проверяем, что файл - изображение
     *
     * @param File $file
     * @return bool
     */
    public function isImage(File $file): bool
    {
        $validator = Validation::createValidator();
        // Создаем ограничение на тип файла
        $constraint = new Image([
            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
            'mimeTypesMessage' => 'File not image',
        ]);

        // Проверяем сущность на соответствие ограничению
        $errors = $validator->validate($file, $constraint);

        // Если есть ошибки, значит не изображение
        if (count($errors) > 0) {
            return false;
        }

        return true;
    }

    public function delete(
        string $fileName,
        string $targetDirectory = 'achievement'
    ): void
    {
        unlink(
            $this->getGlobalDirectory()
            . $targetDirectory
            . "/$fileName"
        );
    }

    private function getGlobalDirectory(): string
    {
        return $this->globalDirectory;
    }
}