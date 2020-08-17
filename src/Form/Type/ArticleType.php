<?php
/**
 * Copyright (c) Diffco US, Inc
 */

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * App\Form\Type\ArticleType
 */
class ArticleType extends AbstractType
{
    /**
     * @throws InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('body', TextType::class);
    }

    /**
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false, 'data_class' => Article::class]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
