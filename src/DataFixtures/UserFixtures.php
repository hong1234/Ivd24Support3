<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();//--------admin  'vuanhde@yahoo.de' 'vuanh123'

        $user->setEmail('vuanhde@yahoo.de');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'vuanh123'
        ));

        $manager->persist($user);
        $manager->flush();

        // $user = new User();//-------support 'hong2' 'hong3'

        // $user->setEmail('hong2@yahoo.de');
        // $user->setRoles(['ROLE_SUPPORT']);
        // $user->setPassword($this->passwordEncoder->encodePassword(
        //     $user,
        //     'hong2'
        // ));

        // $manager->persist($user);
        // $manager->flush();

        // $user = new User();//-------statistic  'hong4'  'hong5'

        // $user->setEmail('hong4@yahoo.de');
        // $user->setRoles(['ROLE_STATISTIC']);
        // $user->setPassword($this->passwordEncoder->encodePassword(
        //     $user,
        //     'hong4'
        // ));

        // $manager->persist($user);
        // $manager->flush();
    }
}
