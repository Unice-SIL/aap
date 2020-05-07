<?php


namespace App\Command;

use App\Entity\ProjectContent;
use App\Widget\WidgetManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CleanUpFileDirectoryCommand extends Command
{
    protected static $defaultName = 'app:file:clean-up';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var WidgetManager
     */
    private $widgetManager;

    /**
     * @var string
     */
    private $fileDirectory;

    public function __construct(EntityManagerInterface $em, WidgetManager $widgetManager, string $fileDirectory)
    {
        parent::__construct();


        $this->em = $em;
        $this->widgetManager = $widgetManager;
        $this->fileDirectory = $fileDirectory;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Clean up every file which is not longer used.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Gets every Widget class concerned by File system
        $fileWidgetClasses = $this->widgetManager->getFileWidgets();

        //Gets every ProjectContent link to a File Widget and with a content not null
        $fileProjectContents = $this->em->getRepository(ProjectContent::class)->getNotNullProjectContentByWidgetClasses($fileWidgetClasses);

        //Maps the every File to collect only pathnames
        $fileProjectContentPathNames = array_map(function ($fileProjectContent) {
                return str_replace('\\', '/', $fileProjectContent->getContent()->getPathName());

        }, $fileProjectContents);

        //For performance reasons, we remove every duplication. We only need to know if the path names are used at least once
        $fileProjectContentPathNames = array_unique($fileProjectContentPathNames);

        //Get every files in the file directory set in services.yaml
        $fileFinder = new Finder();
        $fileFinder->files()->in($this->fileDirectory);

        //Remove every not used
        foreach ($fileFinder as $file) {

            if (!in_array(str_replace('\\', '/', $file->getPathname()), $fileProjectContentPathNames)) {
                unlink($file);
            }
        }

        //Get every directories in the file directory set in services.yaml
        $directoryFinder = new Finder();
        $directoryFinder->directories()->in($this->fileDirectory);

        //Sorts by depth. From the deeper one to the less deep one
        //Important for the next step (suppression). We can't remove a directory if it contains a subdirectory even if
        //the subdirectory is empty
        //To avoid issues we remove first deeper ones
        $directoryFinder->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
            return substr_count(str_replace('\\', '/', $b->getPathname()), '/') > substr_count(str_replace('\\', '/', $a->getPathname()), '/');
        });

        //Removes every empty folder
        foreach ($directoryFinder as $directory) {

            if ($this->isDirEmpty($directory)) {
                rmdir($directory->getPathname());
            }
        }

    }

    function isDirEmpty($dir) {
        if (!is_readable($dir)) return NULL;
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return FALSE;
            }
        }
        return TRUE;
    }
}