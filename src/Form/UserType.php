<?php

namespace App\Form;

use App\Entity\User;
use App\EventSubscriber\UserTypeSubscriber;
use App\Validation\ValidationGroupResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var ValidationGroupResolver
     */
    private $groupResolver;
    /**
     * @var UserTypeSubscriber
     */
    private $userTypeSubscriber;

    /**
     * CallOfProjectInformationType constructor.
     * @param ValidationGroupResolver $groupResolver
     * @param UserTypeSubscriber $userTypeSubscriber
     */
    public function __construct(ValidationGroupResolver $groupResolver, UserTypeSubscriber $userTypeSubscriber)
    {
        $this->groupResolver = $groupResolver;
        $this->userTypeSubscriber = $userTypeSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validationGroups = $options['validation_groups']($builder->getForm());

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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'app.user.property.plain_password.label'],
                'second_options' => ['label' => 'app.user.property.plain_password_repeated.label'],
                'required' => in_array('new', $validationGroups),
                'invalid_message' => 'app.user.property.password_matching.label',
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
