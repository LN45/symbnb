<?php

namespace App\Form;

use App\Entity\Ad;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champs
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = []) {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration('Titre', 'Titre de l\'annonce')
            )
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration('Chaine URL', 'Adresse web automatique', [
                    'required' => false
                ])
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration('URL de l\'image principale', 'Image de couverture de l\'annonce')
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration('Introduction', 'Donner une description globale de l\'annonce')
            )
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration('Description détaillée', 'Tapez une super description')
            )
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfiguration('Nombre de chambres', 'Le nombre de chambre disponibles')
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration('Prix par nuit', 'Indiquer le prix par nuit')
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true, //autorise le fait de pouvoir rajouter des éléments sur le formulaire (prototype)
                    'allow_delete' => true //autorise la suppression des images dans l'édition
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
