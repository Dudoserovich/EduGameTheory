<?php

namespace App\Message;

use Symfony\Component\HttpFoundation\File\File;

class RemoveProductImageMessage
{
    private ?File $file;

    public function __construct(?File $file)
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }
}