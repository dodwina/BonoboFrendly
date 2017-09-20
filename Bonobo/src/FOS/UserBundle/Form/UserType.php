<?php
namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', TextType::class ,array('attr' => array(
                'placeholder' =>'Your username',
                'class'       =>'errors')))

            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field', 'placeholder' =>'Your password',)),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))

            ->add('age', DateType::class ,array(
                'widget'      => 'choice',
                'format'      => 'yyyy-MM-dd',
                'years'       => range(1970, date('Y') ),
                 ))

            ->add('family', ChoiceType::class ,
                array(
                    'placeholder' => 'Your family',
                    'required'    => true,
                    'multiple'    => false,
                    'choices'     => [
                        'Hominini'=> 'Hominini',
                        'Panines '=> 'Panines',
                        'Hominines'=>'Hominines',
                    ]))

            ->add('race', ChoiceType::class,
                array(
                'placeholder' => 'Your race',
                'multiple'    => false,
                'required'    => true,
                'choices'     => [
                    'Chimpanzé commun'=>'Chimpanzé commun',
                    'Bonobo'          =>'Bonobo',
                    'Chimpanzé nain'  =>'Chimpanzé nain',
                 ]))

            ->add('foods', TextType::class ,array('attr' => array(
                'placeholder' => 'Your favorite food',
                'class'       =>'errors')))

            ->add('Register', SubmitType::class, array ('attr' => array(
                'class'       =>'btn btn_success',
                'label'       =>'register')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FOS\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fos_userbundle_user';
    }


}
