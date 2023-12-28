<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'attr'=> [
                'class' => 'form-control',
                'minlength' =>  '2',
                'maxlength' =>  '50'
            ],
            'label' => 'Nom ',
            'label_attr' => [
                'class'=> 'form-label mt-4'
            ],
            'constraints' => [
                new Assert\Length(min: 2, max:50),
                new Assert\NotBlank(),
            ]
        ])
            ->add('time', IntegerType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 1440
                ],
                'label' => 'Temps en (minutes)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ], 'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1441)
                ]
            ])
            ->add('nbPeople', IntegerType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 50
                ],
                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ], 'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(51)
                ]
            ])
            ->add('difficulty', RangeType::class, [
                'attr'=> [
                    'class' => 'form-range',
                    'min' => 1,
                    'max' => 5
                ],
                'label' => 'Difficulté',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ], 'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(5)
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 1440
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ], 'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('price', MoneyType::class, [
                'attr'=> [
                    'class' => 'form-control',
                ],
                'label'=> 'Prix ',
                'label_attr'=> [
                    'class'=> 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(200),
                ]
            ])
            ->add('isFavorite', CheckboxType::class, [
                'attr'=> [
                    'class' => 'form-check-input',
                    'min' => 1,
                    'max' => 1440
                ],
                'label' => 'Favoris ? ',
                'label_attr' => [
                    'class' => 'form-check-label'
                ], 'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.name', 'ASC');
                },
                'label' => 'Ingrédients :',
                'label_attr'=> [
                    'class'=> 'form-label mt-4'
                ],
                'attr' => [
                    'class' => 'form-select mt-4'
                ],
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {
            $recipe = $event->getData();
            $form = $event->getForm();

            if(!$recipe || null === $recipe->getId()) {
                $form->add('submit', SubmitType::class, [
                    'attr'=> [
                        'class' => 'btn btn-primary mt-4',
                    ],
                    'label'=> 'Créer une recette',
                ]);
            } else {
                $form->add('submit', SubmitType::class, [
                    'attr'=> [
                        'class' => 'btn btn-primary mt-4',
                    ],
                    'label'=> 'Modifier la recette',
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
