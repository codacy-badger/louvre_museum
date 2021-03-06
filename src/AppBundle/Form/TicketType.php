<?php

namespace AppBundle\Form;

use AppBundle\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname', TextType::class, [
            'label' => 'ticket.lastname'])

            ->add('firstname', TextType::class, [
                'label' => 'ticket.firstname'])

            ->add('dateOfBirth', DateType::class, [
                'label' => 'ticket.birth_date',
                'widget' => 'single_text',
                'years' => range(date('Y'), date('Y') - 100)])

            ->add('country', CountryType::class, ['label' => 'ticket.country', 'preferred_choices' => ['FR']])

            ->add('reducedPrice', CheckboxType::class, ['label'=> 'ticket.reduce_rate', 'required' => false]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Ticket::class,
            'validation_groups' => ['ticketStep']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ticket';
    }


}
