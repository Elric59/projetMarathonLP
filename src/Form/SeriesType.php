<?php

namespace App\Form;

use App\Entity\Series;
use function Sodium\crypto_box_keypair_from_secretkey_and_publickey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\File;

class SeriesType extends AbstractType
{
    private $tokenStorage;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage; // le token utilisateur
        $this->authorizationChecker = $authorizationChecker; // le service de controle d'utilisateur
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('summary')
            ->add('language')
            ->add('score')
            ->add('status')
            ->add('premiere')
            ->add('url')
            ->add('image')
            ->add('created_at')
            ->add('Kinds')
            ->add('movie',FileType::class, [
                'label' => 'importer Avis Video',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '50M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/avi',
                            'video/mkv',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une video compatible, merci',
                    ])
            ],
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            );
    }
        public function onPreSetData(FormEvent $event){
            $form = $event->getForm();

            $form = $event->getForm(); //récupération du formulaire

            /** @var $entity Series */
            $entity = $event->getData(); //récupération de l'entité

            if($this->authorizationChecker->isGranted('ROLE_ADMIN') === true && $entity->getId() != null)//si je suis en édition
            {
                $form->remove('name');
                $form->remove('summary');
                $form->remove('language');
                $form->remove('score');
                $form->remove('status');
                $form->remove('premiere');
                $form->remove('url');
                $form->remove('image');
                $form->remove('created_at');
                $form->remove('Kinds');

            }

            $form->add('avis');


        }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Series::class,
        ]);
    }
}
