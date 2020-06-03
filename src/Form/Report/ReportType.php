<?php


namespace App\Form\Report;


use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ReportType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * ReportType constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $urlGenerator = $this->urlGenerator;
        $builder
            ->add('comment', null, [
                'label' => 'app.report.property.comment.label'
            ])
            ->add('reportFile', VichFileType::class,[
                'required' => false,
                'download_uri' => static function (Report $report) use ($urlGenerator) {
                    return $urlGenerator->generate('app.file.download_report_file', ['id' => $report->getId()]);
                },
            ])
        ;
    }
}