<?php

namespace App\ToolsBundle\Repositories;

use App\ToolsBundle\Helpers\Observer\Observables\PermissionObservable;
use App\ToolsBundle\Helpers\Observer\Observers\PermissionObserver;
use App\ToolsBundle\Entity\AssignedTests;
use App\ToolsBundle\Helpers\Observer\Exceptions\ObserverException;
use App\ToolsBundle\Helpers\EntityToArray;
use Doctrine\ORM\Query;
use URLify;

class TestRepository extends Repository
{
    public function createAssignedTests($testControlId, $testSolvers) {
        if($testSolvers === 'public') {
            $assignedTests = new AssignedTests();
            $assignedTests->setTestControlId($testControlId);
            $assignedTests->setPublicTest(1);
            $assignedTests->setUserId(null);

            $this->em->persist($assignedTests);
            $this->em->flush();
            return true;
        }

        foreach($testSolvers as $userId) {
            $assignedTests = new AssignedTests();
            $assignedTests->setTestControlId($testControlId);
            $assignedTests->setPublicTest(0);
            $assignedTests->setUserId($userId);

            $this->em->persist($assignedTests);
        }

        $this->em->flush();
    }

    public function getPermittedUsers($testControlId) {
        $qb = $this->em->createQueryBuilder();
        $assignedTests = $qb->select(array('at'))
            ->from('AppToolsBundle:AssignedTests', 'at')
            ->where($qb->expr()->eq('at.testControlId', ':test_control_id'))
            ->setParameter(':test_control_id', $testControlId)
            ->getQuery()
            ->getResult();

        $observable = new PermissionObservable();
        $observable->attach(new PermissionObserver($assignedTests));
        $observable->notify();

        $returnData = array(
            'permission' => $observable->getStatus()
        );

        if($observable->getStatus() === 'restricted') {
            $eta = new EntityToArray($assignedTests, array('getUserId'));
            $returnData['assigned_users'] = $eta
                ->config(array(
                    'numeric-keys' => true,
                    'use-temp' => false
                ))
                ->toArray();
        }

        return $returnData;
    }

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

    public function getBasicTestInformation($userId, UserRepository $userRepo = null) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('t'))
            ->from('AppToolsBundle:TestControl', 't')
            ->where($qb->expr()->eq('t.user_id', ':user_id'))
            ->orderBy('t.created', 'DESC')
            ->setParameter(':user_id', $userId)
            ->getQuery()
            ->getResult();

        if(empty($result)) {
            return array();
        }

        $tests = array();
        foreach($result as $res) {
            $temp = array();

            $temp['test_id'] = $res->getTestControlId();
            $temp['test_name'] = $res->getTestName();
            /*$temp['visibility'] = $userRepo->getUsernamesById($res->getVisibility());
            if($temp['visibility'] === null) {
                $temp['visibility'] = array("public");
            }*/
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
        $tests = $qb->select(array('t'))
            ->from('AppToolsBundle:Test', 't')
            ->where($qb->expr()->eq('t.test_control_id', ':test_control_id'))
            ->setParameter(':test_control_id', $id)
            ->getQuery()
            ->getResult();

        foreach($tests as $test) {
            $this->em->remove($test);
        }

        $qb = $this->em->createQueryBuilder();
        $assignedTests = $qb->select(array('at'))
            ->from('AppToolsBundle:AssignedTests', 'at')
            ->where($qb->expr()->eq('at.testControlId', ':test_control_id'))
            ->setParameter(':test_control_id', $id)
            ->getQuery()
            ->getResult();

        foreach($assignedTests as $assigned) {
            $this->em->remove($assigned);
        }

        $this->em->remove($testControl);

        $this->em->flush();
    }

    public function updateTestById($testControlId, array $testArray) {
        $qb = $this->em->createQueryBuilder();
        $assignedTests = $qb->select(array('at'))
            ->from('AppToolsBundle:AssignedTests', 'at')
            ->where($qb->expr()->eq('at.testControlId', ':test_control_id'))
            ->setParameter(':test_control_id', $testControlId)
            ->getQuery()
            ->getResult();

        foreach($assignedTests as $at) {
            $this->em->remove($at);
        }

        $testSolvers = $testArray['test_solvers'];
        if($testSolvers === 'public') {
            $assignedTests = new AssignedTests();
            $assignedTests->setTestControlId($testControlId);
            $assignedTests->setPublicTest(1);
            $assignedTests->setUserId(null);

            $this->em->persist($assignedTests);
        }
        else if(is_array($testSolvers)) {
            foreach($testSolvers as $userId) {
                $assignedTests = new AssignedTests();
                $assignedTests->setTestControlId($testControlId);
                $assignedTests->setPublicTest(0);
                $assignedTests->setUserId($userId);

                $this->em->persist($assignedTests);
            }
        }

        $testControl = $this->doctrine->getRepository('AppToolsBundle:TestControl')->find($testControlId);
        $testControl->setTestName($testArray['test_name']);
        $testControl->setRemarks($testArray['remarks']);
        $this->em->persist($testControl);

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