<?php


namespace App\Repository\Site\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Repository\PaginatedDoctrineConstructor;
use App\Services\Shop\Search;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineProductRepository implements ProductRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 10;

    private $perPage;

    public function setPerPage(?int $perPage): ProductRepository
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function find(int $id): ?Product
    {
        return $this->er->find($id);
    }

    public function getAll(Search $search, bool $onlyEnabled, int $page): LengthAwarePaginator
    {
        $query = $this->er->createQueryBuilder('p')
            ->select('p', 'c', 'i')
            ->join('p.category', 'c')
            ->leftJoin('p.item', 'i');

        $where = '';

        if(!empty($search->getValute())) {
            if($search->getValute() == 'coins') {
                $where .= 'AND p.priceCoins IS NOT NULL ';
            } else {
                $where .= 'AND p.price IS NOT NULL ';
            }
        }

        if(!empty($search->getServer())) {
            $where .= 'AND (p.server IS NULL OR p.server = :server) ';
            $query->setParameter('server', $search->getServer());
        }

        if(!empty($search->getCategory())) {
            $where .= 'AND (p.category = :category OR c.parentCategory = :parent) ';
            $query->setParameter('category', $search->getCategory())
                ->setParameter('parent', $search->getCategory());
        }

        if(!empty($search->getName())) {
            $where .= 'AND (p.name LIKE :name OR i.name LIKE :name) ';
            $query->setParameter('name', "%{$search->getName()}%");
        }

        if($onlyEnabled) {
            $where .= 'AND p.enabled = 1 ';
        }

        if(!empty($where)) {
            $where = substr($where, 3);
            $query->where($where);
        }

        //dd($where);

        $query->orderBy('c.weight', 'DESC');

        return $this->paginate($query->getQuery(), !is_int($this->perPage) ? static::PER_PAGE : $this->perPage, $page, false);
    }

    public function getAllWithDiscount(): array
    {
        return $this->createQueryBuilder('product')
            ->where('product.discount > 0 AND product.discountTime >= :date')
            ->setParameter('date', date('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
    }

    public function getTopBuysProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.buys', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function randomDiscounts(?Server $server, int $min, int $max, int $days, string $order): void
    {
        $a = $max - $min;

        $where = '';
        if (!is_null($server)) {
            $where = ' WHERE product.server_id = :server';
        }

        $rsm = new ResultSetMapping();

        $query = $this->em->createNativeQuery(
            "UPDATE `pr_shop_products` product SET product.discount = (RAND() * 100 % $a + $min), product.discount_time = :time $where $order",
            $rsm
        );

        if (!is_null($server)) {
            $query->setParameter('server', $server->getId());
        }
        $query->setParameter('time', date('Y-m-d H:i:s', time() + ($days * 86400)));

        $query->execute();
    }

    public function removeExpiredDiscounts(): void
    {
        $this->em->createQuery(sprintf(
            'UPDATE %s product SET product.discount = 0, product.discountTime = null WHERE product.discountTime < :now',
            Product::class
        ))->setParameter('now', date('Y-m-d H:i:s'))->execute();
    }

    public function create(Product $product, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($product);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(Product $product, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($product);
        }

        return $this->em;
    }

    public function delete(Product $product, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($product);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }
}