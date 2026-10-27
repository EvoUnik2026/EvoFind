<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Listing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $keys = static fn (string $prefix, array $values): array => array_combine(
            array_map(static fn (string $v): string => $prefix.$v, $values),
            $values
        );

        $builder
            ->add('title', TextType::class, ['label' => 'listing.title'])
            ->add('description', TextareaType::class, ['label' => 'listing.description', 'attr' => ['rows' => 6]])
            ->add('price', MoneyType::class, ['label' => 'listing.price', 'currency' => 'EUR', 'html5' => true, 'divisor' => 1])
            ->add('itemCondition', ChoiceType::class, [
                'label' => 'listing.condition',
                'choices' => $keys('condition.', array_values(Listing::CONDITIONS)),
            ])
            ->add('category', EntityType::class, [
                'label' => 'listing.category',
                'class' => Category::class,
                'choice_label' => static fn (Category $c): string => $c->getName($locale),
            ])
            ->add('city', EntityType::class, [
                'label' => 'listing.city',
                'class' => City::class,
                'choice_label' => 'name',
            ])
            ->add('paymentOptions', ChoiceType::class, [
                'label' => 'listing.payment',
                'choices' => $keys('payment.', array_values(Listing::PAYMENT_OPTIONS)),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('deliveryOptions', ChoiceType::class, [
                'label' => 'listing.delivery',
                'choices' => $keys('delivery.', array_values(Listing::DELIVERY_OPTIONS)),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'listing.image',
                'mapped' => false,
                'required' => false,
                'constraints' => [new File(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Listing::class, 'locale' => 'nl']);
        $resolver->setAllowedTypes('locale', 'string');
    }
}
