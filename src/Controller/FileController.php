<?php


namespace App\Controller;


use App\Entity\ProjectContent;
use App\Entity\WidgetFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FileController
 * @package App\Controller
 * @Route("file", name="file.")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/{id}/download", name="download", methods={"GET"})
     * @param ProjectContent $projectContent
     * @return BinaryFileResponse
     */
    public function download(ProjectContent $projectContent)
    {
        if (!$projectContent->getContent() instanceof WidgetFile) {
            throw new NotFoundHttpException('File not found');
        }

        $file = $projectContent->getContent();
        // This should return the file to the browser as response
        $response = new BinaryFileResponse($file->getPathName());

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isGuesserSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($file->getPathName()));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getClientOriginalName()
        );

        return $response;
    }
}