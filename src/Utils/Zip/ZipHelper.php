<?php


namespace App\Utils\Zip;


use App\Entity\CallOfProject;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Soundasleep\Html2Text;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Entity\File;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use ZipStream\Exception\FileNotFoundException;
use ZipStream\Exception\FileNotReadableException;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    private $projectDir;

    /**
     * ZipHelper constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, UploaderHelper $uploaderHelper, string $projectDir)
    {
        $this->translator = $translator;
        $this->uploaderHelper = $uploaderHelper;
        $this->projectDir = $projectDir;
    }

    /**
     * @throws Exception
     * @throws FileNotFoundException
     * @throws OverflowException
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws FileNotReadableException
     */
    public function createZipFromCallOfProject(CallOfProject $callOfProject, $options = [])
    {
        libxml_use_internal_errors(true);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $options = array_merge([
            'sentHttpHeaders' => false
        ], $options);
        // enables output of HTTP headers
        $zipOptions = new Archive();
        $zipOptions->setSendHttpHeaders($options['sentHttpHeaders']);
        //Creates a zip
        $zip = new ZipStream($callOfProject->getName() . '.zip', $zipOptions);

        //Create a sheet (projet.xlsx)
        $spreadsheetProject = new Spreadsheet();
        $sheetProject = $spreadsheetProject->getActiveSheet();

        $rowProject = 2;
        $spreadsheetReport = new Spreadsheet();
        $sheetReport = $spreadsheetReport->getActiveSheet();
        $sheetReport->setCellValueByColumnAndRow(1, 1, 'Projet');
        $sheetReport->setCellValueByColumnAndRow(2, 1, 'Rapporteur');
        $sheetReport->setCellValueByColumnAndRow(3, 1, 'Commentaire');
        $sheetReport->setCellValueByColumnAndRow(4, 1, 'Rapport');
        $rowReport = 2;

        foreach ($callOfProject->getProjects() as $project) {
            $projectDir = $this->cleanFileName($project->getName());
            $sheetProject->setCellValueByColumnAndRow(1, $rowProject, $project->getName());
            $columnProject = 2;
            foreach ($project->getProjectContents() as $projectContent) {
                $widget = $projectContent->getProjectFormWidget()->getWidget();
                //Sets dynamic headers
                $sheetProject->setCellValueByColumnAndRow($columnProject, 1, $widget->getLabel());
                //Set dynamic values depends on project and field
                $sheetProject->setCellValueByColumnAndRow(
                    $columnProject,
                    $rowProject,
                    Html2Text::convert($this->translator->trans($projectContent->getStringContent()))
                );

                //If the value we add the file in a directory with project name as name and we set an url into the cell
                //to be able to open the file directly from index.xlsx
                if ($widget->isFileWidget() && $projectContent->getContent() !== null) {
                    $path = $projectContent->getContent()->getPathName();
                    if (!is_file($path) || !is_readable($path)) {
                        throw new \RuntimeException("Fichier introuvable ou illisible : ".$path);
                    }
                    //adds file in appropriate project directory
                    $fileName   = $this->cleanFileName($projectContent->getStringContent());
                    $zip->addFileFromPath($projectDir . '/Fichiers/' . $fileName, $path);
                    //sets a link on the value to open directly the file
                    $sheetProject->getCellByColumnAndRow($columnProject, $rowProject)->getHyperlink()->setUrl($projectDir . DIRECTORY_SEPARATOR . 'Fichiers' . DIRECTORY_SEPARATOR . $projectContent->getStringContent());
                }
                $columnProject++;
            }
            $rowProject++;

            if (!$project->getReports()->isEmpty()) {
                foreach ($project->getReports() as $report) {
                    $reporterName = $report->getReporter()->getFirstname() . ' ' . $report->getReporter()->getLastname();
                    $sheetReport->setCellValueByColumnAndRow(1, $rowReport, $project->getName());
                    $sheetReport->setCellValueByColumnAndRow(2, $rowReport, $reporterName);
                    $sheetReport->setCellValueByColumnAndRow(3, $rowReport, $report->getComment());
                    $sheetReport->setCellValueByColumnAndRow(4, $rowReport, $report->getReport()->getOriginalName());
                    if ($report->getReport() instanceof File and $report->getReport()->getOriginalName() !== null) {
                        $path = $this->projectDir . $this->uploaderHelper->asset($report, 'reportFile');
                        if (!is_file($path) || !is_readable($path)) {
                            throw new \RuntimeException("Rapport introuvable : ".$path);
                        }

                        $reportName = 'Rapport ' . $reporterName . '.' . $this->getFileExtention($report->getReport()->getName());
                        $zip->addFileFromPath($projectDir . '/Rapports/' . $reportName, $path);
                        //sets a link on the value to open directly the file
                        $sheetReport->getCellByColumnAndRow(4, $rowReport)->getHyperlink()->setUrl($projectDir . '/Rapports/' . $reportName);
                    }
                    $rowReport++;
                }
            }
        }

        //Saves temporary the xlsx file
        $writerReport = new Xlsx($spreadsheetReport);
        $temporaryLinkReport = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $writerReport->save($temporaryLinkReport);
        $zip->addFileFromPath('rapports.xlsx', $temporaryLinkReport);


        //Saves temporary the xlsx file
        $writerProject = new Xlsx($spreadsheetProject);
        $temporaryLink = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $writerProject->save($temporaryLink);

        //Adds temporary xlsx file into the zip
        $zip->addFileFromPath('projets.xlsx', $temporaryLink);



        //finish the zip
        $zip->finish();

        //removes temporary files
        @unlink($temporaryLinkReport);
        @unlink($temporaryLink);

        return $zip;

    }

    private function cleanFileName(string $name, int $maxLength = 80): string
    {
        if (class_exists(\Normalizer::class)) {
            $name = \Normalizer::normalize($name, \Normalizer::FORM_C);
        }

        // Supprime caractères de contrôle (0x00–0x1F, 0x7F)
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name);

        // Trim espaces et points (interdits en fin de nom sous Windows)
        $name = trim($name);
        $name = rtrim($name, '. ');

        // Remplace les séparateurs interdits Windows
        $name = preg_replace('/[\/\\\\:\*\?"<>\|]/u', '_', $name);

        // Réduit les espaces multiples
        $name = preg_replace('/\s+/u', ' ', $name);

        // Limite la longueur
        if (mb_strlen($name, 'UTF-8') > $maxLength) {
            $name = mb_substr($name, 0, $maxLength, 'UTF-8');
        }

        // Sécurité ultime
        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'fichier';
        }

        return $name;
    }

    public function getFileExtention(string $fileName): ?string
    {
        if (preg_match("/^.*\.([\d\w]*)$/", $fileName, $match) === 1) {
            return $match[1];
        }
        return null;
    }
}
