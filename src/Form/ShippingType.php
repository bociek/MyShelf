<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices'   => array(
                'Standard Shipping'     => 'standard',
                'Expedited Shipping'    => 'expedited',
                'Priority Shipping'     => 'priority',
            ),
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}