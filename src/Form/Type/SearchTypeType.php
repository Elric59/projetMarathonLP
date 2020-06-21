<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class SearchTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                "Serie" => "Serie",
                "Episode" => "Episode",
                "Categorie" => "Categorie"

            ],
            'multiple' => false,
            'expanded' => false,
        ]);
    }


    public function getParent(){
        return ChoiceType::class;
    }
}
