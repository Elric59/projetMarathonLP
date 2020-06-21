<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('roles')
            ->add('password')
            ->add('email')
            ->add('avatar')
            ->add('created_at')
            //initialement la description, remplacée par l'avatar
            ->add('avatar', FileType::class, [
                'label' => 'importer un avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image compatible, merci',
                    ])
                ],
            ])
            ;
        //->add('overview', null, ['label' => 'Petite description de vous']);


        if($options['style'] === 'register'){
            $builder->remove('roles')->remove('password')->remove('avatar')->remove('created_at');
            $builder->add('plainPassword', PasswordType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [new NotBlank()]
                ]);
        }

        $builder->add(
            'submit',
            SubmitType::class,
            ['label' => 'S\'inscrire', 'attr' => ['class' => 'btn btn-success']]
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );


    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire

        /** @var $entity User */

        $entity = $event->getData(); //récupération de l'entité
        if($entity->getId() != null){
            $form->remove('roles');
            $form->remove('created_at');
            $form->remove('submit');
        }
        $entity->setCreatedAt(new \DateTime());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'style' => 'null',
            ]
        );
    }
}
