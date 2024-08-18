<?php

namespace Myblog\libs;

class FileResize
{
    protected $thumbWidth;
    protected $thumbHeight;
    protected $width;
    protected $height;
    protected $quality;
    protected $types = ['image/gif', 'image/png', 'image/jpeg'];
    protected $path = ALBUMS . '/';
    protected $pathThumb = ALBUMS . '/thumb/';

    public function __construct($thumb_width, $thumb_height, $width, $height, $quality)
    {
        $this->thumbWidth = $thumb_width;
        $this->thumbHeight = $thumb_height;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
    }

    public function resize($uploadFile, $file)
    {
        // Cоздаём исходное изображение на основе исходного файла
        if ($file->getType() == 'image/jpeg')
            $source = imagecreatefromjpeg($uploadFile);
        elseif ($file->getType() == 'image/png')
            $source = imagecreatefrompng($uploadFile);
        elseif ($file->getType() == 'image/gif')
            $source = imagecreatefromgif($uploadFile);
        else
            return false;

        // Определяем ширину и высоту изображения
        $w_src = imagesx($source);
        $h_src = imagesy($source);

        $getMime = explode('.', $file->getName());
        $mime = strtolower(end($getMime));
        $file_name = uniqid() . '.' . $mime;

        $this->save($source, $this->path . $file_name, $w_src, $h_src, $this->width, $this->height, $this->quality);
        $this->save($source, $this->pathThumb . $file_name, $w_src, $h_src, $this->thumbWidth, $this->thumbHeight, $this->quality);
        imagedestroy($source);
        @unlink($uploadFile);

        return $file_name;
    }

    public function save($source, $pathFile, $w_src, $h_src, $w, $h, $quality)
    {
        // Если ширина больше заданной
        if ($w_src > $w) {
            // Вычисление пропорций
            $ratio = $w_src / $w;
            $w_dest = round($w_src / $ratio);
            $h_dest = round($h_src / $ratio);

            // Создаём пустую картинку
            $dest = imagecreatetruecolor($w_dest, $h_dest);

            // Копируем старое изображение в новое с изменением параметров
            imagecopyresampled($dest, $source, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);

            // Вывод картинки и очистка памяти
            imagejpeg($dest, $pathFile, $quality);
            imagedestroy($dest);
        } else {
            imagejpeg($source, $pathFile, $quality);
        }
    }

}