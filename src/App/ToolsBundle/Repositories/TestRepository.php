<?php

namespace App\ToolsBundle\Repositories;

use Doctrine\ORM\Query;
use URLify;

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

    public function getTestById($testId, $testControlId) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:Test', 't')
            ->where($qb->expr()->eq('t.test_id', ':test_id'))
            ->setParameter(':test_id', $testId)
            ->getQuery()
            ->getResult();

        if(empty($result) OR $result === null) {
            return null;
        }

        if($testControlId === null) {
            return array(
                'test' => $result[0],
                'range' => array()
            );
        }

        $qb = $this->em->createQueryBuilder();
        $range = $qb->select('t.test_id')
            ->from('AppToolsBundle:Test', 't')
            ->where($qb->expr()->eq('t.test_control_id', ':test_control_id'))
            ->setParameter(':test_control_id', $testControlId)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $trueRange = array();
        foreach($range as $r) {
            $trueRange[] = $r['test_id'];
        }

        return array(
            'test' => $result[0],
            'range' => $trueRange
        );
    }

    public function getBasicTestInformation($userId) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:TestControl', 't')
            ->where($qb->expr()->eq('t.user_id', ':user_id'))
            ->orderBy('t.created', 'DESC')
            ->setParameter(':user_id', $userId)
            ->getQuery()
            ->getResult();

        if(empty($result)) {
            return null;
        }

        $tests = array();
        foreach($result as $res) {
            $temp = array();

            $temp['test_id'] = $res->getTestControlId();
            $temp['test_name'] = $res->getTestName();
            $temp['visibility'] = $res->getVisibility();
            $temp['user']['username'] = $res->getUser()->getUsername();
            $temp['url'] = '/test-managment/create-test/' . URLify::filter($res->getTestName()). '/' . $res->getTestControlId();
            $temp['modify_url'] = '/test-managment/modify-test/' . URLify::filter($res->getTestName()). '/' . $res->getTestControlId();
            $temp['user']['name'] = $res->getUser()->getName();
            $temp['user']['lastname'] = $res->getUser()->getLastname();
            $temp['finished'] = $res->getIsFinished();
            $temp['remarks'] = $res->getRemarks();
            $temp['created'] = $res->getCreated();

            $tests[] = $temp;
        }

        return $tests;
    }

    public function getBasicTestInformationById($testId) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:TestControl', 't')
            ->where($qb->expr()->eq('t.test_control_id', ':test_control_id'))
            ->setParameter(':test_control_id', $testId)
            ->getQuery()
            ->getResult();

        if(empty($result) OR $result === null) {
            return null;
        }

        $test = $result[0];

        $testArray = array();
        $testArray['test_control_id'] = $test->getTestControlId();
        $testArray['test_name'] = $test->getTestName();
        $testArray['remarks'] = $test->getRemarks();
        $testArray['visibility'] = $test->getVisibility();

        return $testArray;
    }

    public function deleteTestById($id) {
        $testControl = $this->doctrine->getRepository('AppToolsBundle:TestControl')->find($id);
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:Test', 't')
            ->where($qb->expr()->eq('t.test_control_id', ':test_control_id'))
            ->setParameter(':test_control_id', $id)
            ->getQuery()
            ->getResult();

        foreach($result as $test) {
            $this->em->remove($test);
        }

        $this->em->remove($testControl);

        $this->em->flush();

    }

    public function updateTestById($id, array $testArray) {
        $testControl = $this->em->getRepository('AppToolsBundle:TestControl')->find($id);

        $testControl->setTestName($testArray['test_name']);
        $testControl->setVisibility($testArray['test_solvers']);
        $testControl->setRemarks($testArray['remarks']);

        $this->em->flush();
    }

    public function deleteQuestionById($id) {
        $test = $this->em->getRepository('AppToolsBundle:Test')->find($id);
        $this->em->remove($test);
        $this->em->flush();
    }

    public function modifyTestById($id, $serializedTest) {
        $test = $this->em->getRepository('AppToolsBundle:Test')->find($id);

        $test->setTestSerialized($serializedTest);

        $this->em->flush();
    }

    public function finishTest($id) {
        $testControl = $this->em->getRepository('AppToolsBundle:TestControl')->find($id);

        $testControl->setIsFinished(1);

        $this->em->flush();
    }
} 