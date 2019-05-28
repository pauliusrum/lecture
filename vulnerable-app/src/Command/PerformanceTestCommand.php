<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable;

class PerformanceTestCommand extends Command
{
    protected static $defaultName = 'performance:test';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterations = 1000;
        $em = $this->entityManager;
        $em->getConnection()->beginTransaction();

        // NO ORM
//        foreach (range(0, $iterations) as $i) {
//            $em->getConnection()->executeQuery(
//                'INSERT INTO comment (contents, user_id, created_at) VALUES (?, null, ?)',
//                ["Test $i", (new DateTimeImmutable())->format('Y-m-d H:i:s')]
//            );
//        }

        // NO ORM, BATCH
//        $batch = [];
//        $params = [];
//        foreach (range(0, $iterations) as $i) {
//            $batch[] = ' (?, null, ?)';
//            $params[] = "Test $i";
//            $params[] = (new DateTimeImmutable())->format('Y-m-d H:i:s');
//        }
//        $inserts = implode(', ', $batch);
//        $em->getConnection()->executeQuery("INSERT INTO comment (contents, user_id, created_at) VALUES $inserts", $params);

        // ORM
//        foreach (range(0, $iterations) as $i) {
//            $comment = new Comment();
//            $comment->setContents("Test $i");
//            $comment->setUser(null);
//
//            $em->persist($comment);
//            $em->flush();
//        }
    }
}