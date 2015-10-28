<?php

namespace Bajke\BookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if($options['is_api']) {
            $builder
                ->add('title', 'text')
                ->add('description', 'textarea')
                ->add('owner', 'text', [
                    'disabled' => ($options['is_edit'] || $options['is_owner_disabled'])
                ]);
        } else {
            $builder
                ->add('title', 'text')
                ->add('description', 'textarea');
                if(!$options['is_edit']) {
                    $builder->add('save', 'submit', array('label' => 'Create'));
                } else {
                    $builder->add('save', 'submit', array('label' => 'Update'));
                }
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Bajke\BookBundle\Entity\Book',
            'is_edit' => false,
            'is_owner_disabled' => false,
            'is_api' => false,
        ]);
    }

    public function getName() {
        return "book";
    }
}