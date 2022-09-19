<?php

namespace App\DataTransformer;

use App\Entity\User;
use Exception;
use Symfony\Component\Form\DataTransformerInterface;

class ShibbolethUserTransformer implements DataTransformerInterface
{
    /**
     * @throws Exception
     */
    public function transform($value)
    {
        $user = !array_key_exists('user', $value) ? new User() : $value['user'];

        if (!array_key_exists('attributes', $value)) {
            throw new Exception('attributes key must be defined in array');
        }
        $attributes = $value['attributes'];

        foreach ($attributes as $attribute => $data) {
            switch ($attribute) {
                case 'eppn':
                    $user->setUsername($data);
                    break;
                case 'givenName':
                    $user->setFirstname($data);
                    break;
                case 'mail':
                    $user->setEmail($data);
                    break;
                case 'sn':
                    $user->setLastname($data);
            }
        }

        return $user;
    }

    public function reverseTransform($value)
    {
        return [];
    }
}