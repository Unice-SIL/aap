<?php


namespace App\Form\DataTransformer;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BatchActionTransformer implements DataTransformerInterface
{

    private $className;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BatchActionTransformer constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param mixed $className
     */
    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function transform($value)
    {
        // TODO: Implement transform() method.
    }

    public function reverseTransform($value)
    {

        if (isset($value['entities']) and is_array($value['entities'])) {

            try {
                $value['entities'] = $this->entityManager->getRepository($this->className)->findBy(['id' => $value['entities']]);
            } catch (\Exception $e) {
                throw new TransformationFailedException('An error occured');
            }
            if (count($value['entities']) === 0) {
                throw new TransformationFailedException('An error occured');
            }
            return $value;
        }
        throw new TransformationFailedException('No ids given');
    }
}