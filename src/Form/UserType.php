<?php

namespace App\Form;

use App\Entity\User;
use App\EventSubscriber\UserTypeSubscriber;
use App\Validation\ValidationGroupResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * @var UserTypeSubscriber
     */
    private $userTypeSubscriber;
    /**
     * @var ValidationGroupResolver
     */
    private $groupResolver;

    /**
     * CallOfProjectInformationType constructor.
     * @param UserTypeSubscriber $userTypeSubscriber
     * @param ValidationGroupResolver $groupResolver
     */
    public function __construct(UserTypeSubscriber $userTypeSubscriber, ValidationGroupResolver $groupResolver)
    {
        $this->userTypeSubscriber = $userTypeSubscriber;
        $this->groupResolver = $groupResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'app.user.property.username.label'
            ])
            ->add('email', null, [
                'label' => 'app.user.property.email.label'
            ])
            ->add('firstname', null, [
                'label' => 'app.user.property.firstname.label'
            ])
            ->add('lastname', null, [
                'label' => 'app.user.property.lastname.label'
            ])
            ->addEventSubscriber($this->userTypeSubscriber)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => $this->groupResolver,
        ]);
    }


}
