<?php


namespace App\Controller\Front;


use App\Entity\CallOfProject;
use App\Entity\ProjectContent;
use App\Entity\Report;
use App\Entity\WidgetFile;
use App\Utils\Zip\ZipHelper;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

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
    public function downloadProjectContentFile(ProjectContent $projectContent)
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

    /**
     * @param Report $report
     * @param DownloadHandler $downloadHandler
     * @return Response
     * @Route("/{id}/download-report-file", name="download_report_file", methods={"GET"})
     * @IsGranted(App\Security\ReportVoter::EDIT, subject="report")
     */
    public function downloadReportFileAction(Report $report, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($report, $fileField = 'reportFile');
    }

    /**
     * @param CallOfProject $callOfProject
     * @param ZipHelper $zipHelper
     * @Route("/{id}/get-zip-from-call-of-project", name="get_zip_from_call_of_project", methods={"GET"})
     * @Entity("callOfProject", expr="repository.getCallOfProjectForZip(id)")
     * @IsGranted(App\Security\CallOfProjectVoter::EDIT, subject="callOfProject")
     * @return Response
     */
    public function getZipFromCallOfProject(CallOfProject $callOfProject, ZipHelper $zipHelper)
    {
        $zipHelper->createZipFromCallOfProject($callOfProject, [
            'sentHttpHeaders' => true
        ]);

        return new Response();
    }
}