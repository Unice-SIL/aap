<?php


namespace App\Widget\FormWidget;


use App\Entity\WidgetFile;
use App\Form\Type\FileWidgetType;
use App\Form\Widget\FormWidget\FormFileWidgetType;
use App\Utils\File\FileUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileWidget extends AbstractFormWidget implements FormWidgetInterface
{

    public function getFormType(): string
    {
        return FormFileWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return FileWidgetType::class;
    }

    public function getPosition(): int
    {
        return 10;
    }

    public function transformData($value, array $options = [])
    {
        if (null === $value) {
            return $value;
        }

        if (!isset($options['file_uploader']) or !$options['file_uploader'] instanceof FileUploaderInterface) {
            throw new \Exception('The option "file_uploader" is needed when using the "' . __FUNCTION__ . '" method 
            in the ' . self::class . ' widget. It should implement ' . FileUploaderInterface::class . ' interface.');
        }

        /** @var FileUploaderInterface $fileUploader */
        $fileUploader = $options['file_uploader'];

        $value = unserialize($value);

        return $fileUploader->download($value);

    }


    public function reverseTransformData($value, array $options = [])
    {
        if (null === $value) {
            return $value;
        }

        if (!isset($options['file_uploader']) or !$options['file_uploader'] instanceof FileUploaderInterface) {
            throw new \Exception('The option "file_uploader" is needed when using the "' . __FUNCTION__ . '" method 
            in the ' . self::class . ' widget. It should implement ' . FileUploaderInterface::class . ' interface.');
        }

        /** @var FileUploaderInterface $fileUploader */
        $fileUploader = $options['file_uploader'];

        $value = $fileUploader->upload($value);

        return serialize($value);
    }

    public function isFileWidget(): bool
    {
        return true;
    }


}