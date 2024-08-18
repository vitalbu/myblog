<?php

namespace Myblog\libs;

class FileUploader
{
    protected $src = TMP . '/';

    public function upload(File $file)
    {
        $uploadDir = $this->src;

        $uploadFile = $uploadDir . basename($file->getName());

        if (move_uploaded_file($file->getTmpName(), $uploadFile)) {
            return $uploadFile;
        }

        return false;
    }
}