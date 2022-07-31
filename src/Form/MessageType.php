<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $withoutUser = $options['without'];

        $builder
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Choice recipient',
                'by_reference' => false,
                'query_builder' => function (EntityRepository $repository) use ($withoutUser) {
                    return $repository
                        ->createQueryBuilder('u')
                        ->where('u != :without')
                        ->setParameter('without', $withoutUser)
                    ;
                },
            ])
            ->add('topic', TextType::class)
            ->add('text', TextareaType::class, [
                'attr' => [
                    'class' => 'message_text',
                ],
            ])
            ->add('color', HiddenType::class, [
                'attr' => [
                    'class' => 'message_color',
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'without' => null,
        ]);
    }
}
