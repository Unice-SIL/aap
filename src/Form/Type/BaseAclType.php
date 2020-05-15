<?php


namespace App\Form\Type;


use App\Entity\Acl;
use App\Entity\User;
use App\Form\DataTransformer\BaseAclTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class BaseAclType extends AbstractType
{
    /**
     * @var BaseAclTransformer
     */
    private $baseAclTransformer;

    /**
     * BaseAclType constructor.
     * @param BaseAclTransformer $baseAclTransformer
     */
    public function __construct(BaseAclTransformer $baseAclTransformer)
    {
        $this->baseAclTransformer = $baseAclTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach (Acl::PERMISSION_BASES as $base) {
            $builder
                ->add($base, UserSelect2EntityType::class, [
                    'label' => 'app.acl.constant.' . $base,
                ])
            ;
        }
            $builder->addModelTransformer($this->baseAclTransformer)
        ;
    }
}