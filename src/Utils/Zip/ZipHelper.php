<?php


namespace App\Utils\Zip;


use App\Entity\CallOfProject;
use App\Entity\ProjectContent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ZipHelper constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function createZipFromCallOfProject(CallOfProject $callOfProject, $options = [])
    {

        $options = array_merge([
            'sentHttpHeaders' => false
        ], $options);
        // enables output of HTTP headers
        $zipOptions = new Archive();
        $zipOptions->setSendHttpHeaders($options['sentHttpHeaders']);
        //Creates a zip
        $zip = new ZipStream($callOfProject->getName() . '.zip', $zipOptions);

        //Create a sheet (index.xlsx)
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Sets first header
        $sheet->setCellValueByColumnAndRow(1, 1, 'Nom du projet');

        $row = 2;
        foreach ($callOfProject->getProjects() as $project) {
            $sheet->setCellValueByColumnAndRow(1, $row, $project->getName());

            $column = 2;
            foreach ($project->getProjectContents() as $projectContent) {

                $widget = $projectContent->getProjectFormWidget()->getWidget();

                //Sets dynamic headers
                $sheet->setCellValueByColumnAndRow($column, 1, $widget->getLabel());

                //Set dynamic values depends on project and field
                $sheet->setCellValueByColumnAndRow($column, $row, $this->translator->trans($projectContent->getStringContent()));

                //If the value we add the file in a directory with project name as name and we set an url into the cell
                //to be able to open the file directly from index.xlsx
                if ($widget->isFileWidget() && $projectContent->getContent() !== null) {
                    //adds file in appropriate project directory
                    $zip->addFileFromPath($project->getName() . '/'. $projectContent->getStringContent(), $projectContent->getContent()->getPathName());
                    //sets a link on the value to open directly the file
                    $sheet->getCellByColumnAndRow($column, $row)->getHyperlink()->setUrl($project->getName() . DIRECTORY_SEPARATOR . $projectContent->getStringContent());
                }

                $column++;
            }

            $row++;
        }

        //Saves temporary the xlsx file
        $writer = new Xlsx($spreadsheet);
        $temporaryLink = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        $writer->save($temporaryLink);

        //Adds temporary xlsx file into the zip
        $zip->addFileFromPath('index.xlsx', $temporaryLink);

        //removes temporary xlsx from hard disk
        unlink($temporaryLink);

        //finish the zip
        $zip->finish();

        return $zip;
    }
}