<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use RuntimeException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[]
     */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }

    public function save(?int $userId, string $contents): void
    {
        $createdAt = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $userIdParam = $userId ?: 'null';

        $query = $this->getEntityManager()->getConnection()->prepare("INSERT INTO comment (contents, user_id, created_at) VALUES ('$contents', $userIdParam, '$createdAt')");
        $query->execute();
    }

    public function remove(int $id): void
    {
        $comment = $this->find($id);
        if ($comment === null) throw new RuntimeException("Comment#$id not found.");

        $em = $this->getEntityManager();
        $em->remove($comment);
        $em->flush();
    }

    public function update(int $id, string $contents): void
    {
        $comment = $this->find($id);
        if ($comment === null) throw new RuntimeException("Comment#$id not found.");

        $comment->setContents($contents);
        $this->getEntityManager()->flush();
    }
}
