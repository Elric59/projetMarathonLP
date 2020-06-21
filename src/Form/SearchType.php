<?php

namespace App\Form;

use App\Entity\Kind;
use App\Form\Type\SearchTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Recherche')
            ->add('Choix',SearchTypeType::class);

    }


}
