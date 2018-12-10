<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'nimda'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'resu'));

        $manager->persist($user);

        $vip = new User();
        $vip->setEmail('vip@example.com');
        $vip->setPassword($this->passwordEncoder->encodePassword($user, 'piv'));
        $vip->setRoles(['ROLE_VIP']);

        $manager->persist($vip);

        $manager->flush();
    }
}