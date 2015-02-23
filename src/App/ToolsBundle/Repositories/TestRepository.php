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

    public function getTestControlById($id) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:TestControl', 't')
            ->where($qb->expr()->eq('t.test_control_id', ':test_control_id'))
            ->setParameter(':test_control_id', $id)
            ->getQuery()
            ->getSingleResult();

        if(empty($result)) {
            return null;
        }

        return $result;
    }

    public function getTestRange($id) {
        $conn = $this->em->getConnection();

        $range = $conn->fetchAssoc("SELECT MIN(test_id) AS 'min', MAX(test_id) AS 'max' FROM tests WHERE test_control_id=" . $id);

        if($range['min'] === null OR $range['max'] === null) {
            return array(
                'min' => 0,
                'max' => 0
            );
        }

        return $range;
    }

    public function getTestById($id) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:Test', 't')
            ->where($qb->expr()->eq('t.test_id', ':test_id'))
            ->setParameter(':test_id', $id)
            ->getQuery()
            ->getSingleResult();

        if(empty($result)) {
            return null;
        }

        return $result;
    }
} 