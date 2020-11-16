<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'the_new_password'
        ));
        $user->setEmail('xander-v.d.h@hotmail.com');
        $user->setLastName('Van der Herten');
        $user->setFirstName('Xander');

        $manager->persist($user);

        $manager->flush();
    }
}
