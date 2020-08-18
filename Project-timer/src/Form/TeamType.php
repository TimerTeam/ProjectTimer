<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Collections\ArrayCollection;

class TeamType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $option)
    {

        $builder->add('name', TextType::class, ['label'=> "Nom du groupe",  'attr' => ['class'=>'form-control', 'placeholder' => 'Entrez le nom de votre groupe']]);
        $builder->add('users', EntityType::class, [
            'class' => User::class,
            'choice_label' => function(User $user){
                return $user->getFirstName();
            },
            'query_builder' => function (EntityRepository $er) {
                $curentUser = $this->security->getUser();
                $curentUserId = $curentUser->getId();
                return $er->createQueryBuilder('u')
                          ->where('u.id !='.$curentUserId );
            },
            'expanded' => true,
            'multiple' => true, 
            'label' => "Membres",
        ]);
        $builder->add('save', SubmitType::class, ['attr' => ['class' => "btn btn-success"]]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
