<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $superAdmin = new User();
        $superAdmin->setFirstName('Symfony')
            ->setLastName('Administrator')
            ->setProfilePicture('https://symfony.com/logos/symfony_black_03.png')
            ->setEmail('admin@example.com')
            ->setIsEmailVerified(true)
            ->setEmailVerificationToken(null)
            ->setEmailVerificationTokenExpiredAt(null)
            ->setPassword(password_hash('password', PASSWORD_BCRYPT))
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setGender('male')
            ->setIsTosAccepted(true)
            ->setTimezone('UTC');

        $manager->persist($superAdmin);
        $manager->flush();
    }
}
