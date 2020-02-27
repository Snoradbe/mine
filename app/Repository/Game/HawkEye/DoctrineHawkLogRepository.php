<?php


namespace App\Repository\Game\HawkEye;


use App\Repository\PaginatedDoctrineConstructor;
use App\Services\Game\HawkLogs\HawkSearch;
use Doctrine\ORM\Query\ResultSetMapping;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelDoctrine\ORM\Pagination\PaginatesFromParams;

class DoctrineHawkLogRepository implements HawkLogRepository
{
    use PaginatedDoctrineConstructor, PaginatesFromParams;

    private const PER_PAGE = 100;

    public function getAll(HawkSearch $search, int $page): LengthAwarePaginator
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('data_id', 'id');
        $rsm->addScalarResult('timestamp', 'date');
        $rsm->addScalarResult('action', 'action');
        $rsm->addScalarResult('world', 'world_id');
        $rsm->addScalarResult('x', 'x');
        $rsm->addScalarResult('y', 'y');
        $rsm->addScalarResult('z', 'z');
        $rsm->addScalarResult('data', 'data');
        $rsm->addScalarResult('player', 'name');

        $rsm2 = new ResultSetMapping();
        $rsm2->addScalarResult('c', 'c');

        $limit = ($page - 1) * static::PER_PAGE;

        $where = '';

        if (!empty($search->getActions())) {
            $where .= 'AND he.action IN (:actions) ';
        }

        if (!empty($search->getUsers())) {
            $where .= 'AND hp.player IN (:users) ';
        }

        if (!empty($search->getIgnoredUsers())) {
            $where .= 'AND hp.player NOT IN (:ig_users) ';
        }

        if (!empty($search->getWorlds())) {
            $where .= 'AND hw.world IN (:worlds) ';
        }

        if (!empty($search->getIgnoredWorlds())) {
            $where .= 'AND hw.world NOT IN (:ig_worlds) ';
        }

        if (!is_null($search->getData())) {
            $where .= 'AND he.data LIKE :data ';
        }

        if (!empty($search->getExcludedData())) {
            foreach ($search->getExcludedData() as $i => $excludedData)
            {
                $where .= 'AND he.data NOT LIKE :data_' . $i . ' ';
            }
        }

        if (!empty($search->getDateFrom())) {
            $where .= 'AND he.timestamp >= :date_from ';
        }

        if (!empty($search->getDateTo())) {
            $where .= 'AND he.timestamp <= :date_to ';
        }

        if (!is_null($search->getX())) {
            [$p1, $p2] = $search->getRangedX();
            $where .= "AND he.x BETWEEN $p1 AND $p2 ";
        }

        if (!is_null($search->getY())) {
            [$p1, $p2] = $search->getRangedY();
            $where .= "AND he.y BETWEEN $p1 AND $p2 ";
        }

        if (!is_null($search->getZ())) {
            [$p1, $p2] = $search->getRangedZ();
            $where .= "AND he.z BETWEEN $p1 AND $p2 ";
        }

        if (!empty($where)) {
            $where = 'WHERE ' . substr($where, 3);
        }

        $sql = sprintf(
            '
            SELECT he.*, hp.player, hw.world
             FROM hawkeye he
              LEFT JOIN hawk_players hp ON he.player_id = hp.player_id
              LEFT JOIN hawk_worlds hw ON he.world_id = hw.world_id
               %s
                ORDER BY data_id DESC
                 LIMIT %d, %d',
            $where,
            $limit,
            static::PER_PAGE
        );

        $sql2 = sprintf(
            '
            SELECT count(*) as c
             FROM hawkeye he
              LEFT JOIN hawk_players hp ON he.player_id = hp.player_id
              LEFT JOIN hawk_worlds hw ON he.world_id = hw.world_id
               %s',
            $where
        );

        $q = $this->em->createNativeQuery($sql, $rsm);
        $q2 = $this->em->createNativeQuery($sql2, $rsm2);

        if (!empty($search->getActions())) {
            $q->setParameter('actions', $search->getActions());
            $q2->setParameter('actions', $search->getActions());
        }

        if (!empty($search->getUsers())) {
            $q->setParameter('users', $search->getUsers());
            $q2->setParameter('users', $search->getUsers());
        }

        if (!empty($search->getIgnoredUsers())) {
            $q->setParameter('ig_users', $search->getIgnoredUsers());
            $q2->setParameter('ig_users', $search->getIgnoredUsers());
        }

        if (!empty($search->getWorlds())) {
            $q->setParameter('worlds', $search->getWorlds());
            $q2->setParameter('worlds', $search->getWorlds());
        }

        if (!empty($search->getIgnoredWorlds())) {
            $q->setParameter('ig_worlds', $search->getIgnoredWorlds());
            $q2->setParameter('ig_worlds', $search->getIgnoredWorlds());
        }

        if (!is_null($search->getData())) {
            $q->setParameter('data', "%{$search->getData()}%");
            $q2->setParameter('data', "%{$search->getData()}%");
        }

        if (!empty($search->getExcludedData())) {
            foreach ($search->getExcludedData() as $i => $excludedData)
            {
                $q->setParameter('data_' . $i, $excludedData);
                $q2->setParameter('data_' . $i, $excludedData);
            }
        }

        if (!empty($search->getDateFrom())) {
            $q->setParameter('date_from', $search->getDateFrom());
            $q2->setParameter('date_from', $search->getDateFrom());
        }

        if (!empty($search->getDateTo())) {
            $q->setParameter('date_to', $search->getDateTo());
            $q2->setParameter('date_to', $search->getDateTo());
        }

        return new LengthAwarePaginator(
            $q->getResult(),
            (int) $q2->getOneOrNullResult()['c'],
            static::PER_PAGE,
            $page
        );


        /*$query = $this->createQueryBuilder('hlog')
            ->select('hlog', 'hplayer')
            ->join('hlog.hawkPlayer', 'hplayer')
            ->where('hlog.hawkPlayer != 3')
            ->orderBy('hlog.dataId', 'DESC');

        if (!empty($actions)) {
            $query->andWhere('hlog.action IN (:actions)')
                ->setParameter('actions', $actions);
        }

        if (!empty($user)) {
            $query->andWhere('hplayer.player = :user')
                ->setParameter('user', $user);
        }

        if (!empty($data)) {
            $query->andWhere('hlog.data LIKE :data')
                ->setParameter('data', "%$data%");
        }

        $r =  $this->paginate(
            $query->getQuery(),
            static::PER_PAGE,
            $page,
            false
        );

        dd($r);

        return $r;*/
    }
}