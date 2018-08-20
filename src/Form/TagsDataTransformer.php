<?php
/**
 * Tags data transformer.
 */
namespace Form;
use Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;
/**
 * Class TagsDataTransformer.
 */
class TagsDataTransformer implements DataTransformerInterface
{
    /**
     * Tags repository.
     *
     * @var TagsRepository|null $tagsRepository
     */
    protected $tagsRepository = null;
    /**
     * TagsDataTransformer constructor.
     *
     * @param TagsRepository $tagsRepository Tags repository
     */
    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }
    /**
     * Transform array of tags Ids to string of names.
     *
     * @param array $tags Tags ids
     *
     * @return string Result
     */
    public function transform($tags)
    {
        if (null == $tags) {
            return '';
        }
        return implode(',', $tags);
    }
    /**
     * Transform string of tag names into array of Tags Ids.
     *
     * @param string $string String of tag names
     *
     * @return array Result
     */
    public function reverseTransform($string)
    {
        return explode(',', $string);
    }
}