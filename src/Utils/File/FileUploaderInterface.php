<?php


namespace App\Utils\File;

use Symfony\Component\HttpFoundation\File\File;

interface FileUploaderInterface
{
    public function upload(File $file);

    public function download($infos): File;
}