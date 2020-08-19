<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Team;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Collections\ArrayCollection;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label'=> "Nom du groupe",  'attr' => ['class'=>'form-control', 'placeholder' => 'Entrez le nom de votre projet']])
            ->add('description', TextareaType::class, ['label'=> "Description",  'attr' => ['class'=>'form-control']])
            ->add('team', EntityType::class, [
                'class' => Team::class,
                'choice_label' => function(Team $team){
                    return $team->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u');
                },
                'expanded' => true,
                'multiple' => true, 
            ]);
            $builder->add('save', SubmitType::class, ['attr' => ['class' => "btn btn-success"]]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
