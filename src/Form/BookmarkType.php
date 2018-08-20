<?php
/**
 * Bookmark type.
 */
namespace Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Form\TagsDataTransformer;
/**
 * Class BookmarkType.
 */
class BookmarkType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['bookmark-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['bookmark-default'],
                            'min' => 3,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );
        $builder->add(
            'url',
            UrlType::class,
            [
                'label' => 'label.url',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['bookmark-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['bookmark-default'],
                            'min' => 3,
                            'max' => 128,
                        ]
                    ),
                    new Assert\Url(
                        ['groups' => ['bookmark-default']]
                    ),
                ],
            ]
        );
        $builder->add(
            'tags',
            TextType::class,
            [
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        );
//        $builder->add(
//            'tags',
//            ChoiceType::class,
//            [
//                'label' => 'label.tags',
//                'required' => true,
//                'placeholder' => 'label.none',
//                'choices' => $this->prepareTagsForChoices($options['tags_repository']),
//                'expanded' => true,
//                'multiple' => true,
//            ]
//        );
        $builder->add(
            'is_public',
            ChoiceType::class,
            [
                'label' => 'label.is_public',
                'choices'  => [
                    'label.no' => 0,
                    'label.yes' => 1,
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['bookmark-default']]
                    ),
                    new Assert\Choice(
                        [
                            'groups' => ['bookmark-default'],
                            'choices' => [0, 1],
                        ]
                    ),
                ],
            ]
        );
        $builder->get('tags')->addModelTransformer(
            new TagsDataTransformer($options['tags_repository'])
        );
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'bookmark-default',
                'tags_repository' => null,
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bookmark_type';
    }
    /**
     * Prepare data for choices.
     *
     * @param Repository\TagsRepository $tagsRepository Tags repository
     *
     * @return array Result
     */
    protected function prepareTagsForChoices($tagsRepository)
    {
        $tags = $tagsRepository->findAll();
        $choices = [];
        foreach ($tags as $tag) {
            $choices[$tag['name']] = $tag['id'];
        }
        return $choices;
    }
}