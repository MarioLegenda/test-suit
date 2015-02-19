<?php

namespace App\ToolsBundle\Repositories;

use Doctrine\ORM\Query;

class TestRepository extends Repository
{
    public function getTestByIdentifier($identifier) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('tc'))
            ->from('AppToolsBundle:TestControl', 'tc')
            ->where($qb->expr()->eq('tc.identifier', ':identifier'))
            ->setParameter(':identifier', $identifier)
            ->getQuery()
            ->getSingleResult(Query::HYDRATE_ARRAY);

        if(empty($result)) {
            return null;
        }

        return $result;
    }

    public function getTestById($id) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('tc'))
            ->from('AppToolsBundle:TestControl', 'tc')
            ->where($qb->expr()->eq('tc.test_id', ':test_id'))
            ->setParameter(':test_id', $id)
            ->getQuery()
            ->getSingleResult();

        if(empty($result)) {
            return null;
        }

        return $result;
    }
} 