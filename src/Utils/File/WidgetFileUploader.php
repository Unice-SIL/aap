<?php


namespace App\Utils\File;

use App\Entity\ProjectContent;
use App\Entity\WidgetFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WidgetFileUploader implements FileUploaderInterface
{

    const CLIENT_ORIGINAL_NAME = 'client_original_name';
    const FILE_NAME = 'file_name';


    private $projectFileDirectory;

    /**
     * FileUploader constructor.
     * @param $projectFileDirectory
     */
    public function __construct($projectFileDirectory)
    {
        $this->projectFileDirectory = $projectFileDirectory;
    }

    public function upload(File $file)
    {
        if (!$file instanceof UploadedFile and !$file instanceof WidgetFile) {
            throw new \InvalidArgumentException(
                'The file should be an instance of ' . UploadedFile::class .
                ' or ' . WidgetFile::class . '.'
            );
        }

        $newFilename = md5_file($file->getPathname()).'.'.$file->guessExtension();

        $infoToStore = $this->getInfoToStore($file->getClientOriginalName(), $newFilename);

        if ($file instanceof UploadedFile) {
            $file->move(
                $this->getDirectory($newFilename),
                $newFilename
            );
        }

        return $infoToStore;
    }

    public function download($infos): File
    {
        if (!( isset($infos[self::CLIENT_ORIGINAL_NAME]) and isset($infos[self::FILE_NAME]) )) {
            throw new \InvalidArgumentException(
                'The infos for a file content should be an array with "' . self::CLIENT_ORIGINAL_NAME .
                '" and "' . self::FILE_NAME . '" values.'
            );
        }

        $file = new WidgetFile($this->getDirectory($infos[self::FILE_NAME]). DIRECTORY_SEPARATOR . $infos[self::FILE_NAME]);
        $file->setClientOriginalName($infos[self::CLIENT_ORIGINAL_NAME]);

        return $file;
    }

    private function getDirectory(string $fileName)
    {
        $directory = $this->projectFileDirectory;
        $directory .= DIRECTORY_SEPARATOR . substr($fileName, 0, 2);
        $directory .= DIRECTORY_SEPARATOR . substr($fileName, 2, 2);

        return $directory;
    }

    private function getInfoToStore(string $clientOriginalName, string $fileName)
    {
        return [
            self::CLIENT_ORIGINAL_NAME => $clientOriginalName,
            self::FILE_NAME => $fileName
        ];
    }
}