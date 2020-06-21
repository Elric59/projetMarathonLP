<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class CommentsType extends AbstractType
{
    private $tokenStorage;
    private $authorizationChecker;
    private $series;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->series = $options["series"];
        $builder
            ->add('content')
            ->add('positive')
            ->add('note')
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )

        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var $weapon Weapon */
        $comments = $event->getData();

        if($this->authorizationChecker->isGranted('ROLE_USER') === true){
            $comments->setUser($this->tokenStorage->getToken()->getUser());
            $comments->setSeries($this->series);
            $comments->setValidated(0);
            $comments->setCreatedAt(new \DateTime("now"));
        }

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
            'series' => null,
        ]);
    }
}
