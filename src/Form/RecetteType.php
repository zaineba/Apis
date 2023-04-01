<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;

class RecetteType extends AbstractType
{

    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('categorie')
            ->add('etapes')
            ->add('temps_cuis')
            ->add('temps_prep')
            ->add('ingredients')
            ->add('token', HiddenType::class, [
                'data' => $this->csrfTokenManager->getToken('form_intention')->getValue(),
            ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view['token'] = $form['token']->createView();
    }
}
