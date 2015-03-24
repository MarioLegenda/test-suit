<?php

require 'vendor/autoload.php';

use StrongType\String;
use StrongType\Integer;
use StrongType\Float;

use ControlFlowCompiler\Compiler;
use ControlFlowCompiler\Arguments\ArgumentFactory;


$company = new Company();
$mario = new Person(new Unique());
/*$mario->setName(new String('Mario'))
      ->setLastname(new String('Škrlec'))
      ->setAge(new Integer(28));*/

$martina = new Person(new Unique());
/*$martina->setName(new String('Martina'))
        ->setLastname(new String('Prezime'))
        ->setAge(new Integer(28));*/

//$company->hireEmployee($martina);
//$company->hireEmployee($mario);

//$martina->foundJob(new Job($company));
//$mario->foundJob(new Job($company));

//$company->fireEmployee($martina);

/*
 * Wish list:
 *
 *   - Multiple parameters as arguments in methods
 *   - Saving return values from objects and using them in other methods on client request
 *   - Working with runtime objects in a flexible manner
 *   - Better filtering algorithm for method execution with return values from other methods always available
 *   - A flexible design pattern that will have no problem with scaling to a larger api
 * */

$compiler = new Compiler();
$compiler->runObject($company)
    ->withMethods(
        $compiler->method()->name('setCompanyName')->withParameters(new String('Dealings Offshore'))->self()->save()
    )
    ->then()
    ->runObject($mario)
    ->withMethods(array(
        'setName' => new String('Mario'),
        'setLastname' => new String('Škrlec'),
        'setAge' => new Integer(28)
    ))
    ->then()
    ->runObject($martina)
    ->withMethods(array(
        'setName' => new String('Martina'),
        'setLastname' => new String('Strugačevac'),
        'setAge' => new Integer(25)
    ))
    ->then()
    ->runObject($company)
    ->withMethods(
        $compiler->method()->name('hireEmployee')->withParameters($mario)->void()->save(),
        $compiler->method()->name('hireEmployee')->withParameters($martina)->void()->save(),
        $compiler->method()->name('lookupEmployee')->withParameters($mario)->self()->save()
    )
    ->ifFailsRun(function() {
        die('propalo');
    })
    ->ifSuccedesRun(function() {
        die('uspjelo');
    })
    ->compile();
    /*->then()
    ->runObject($mario)
    ->withMethods(
        $compiler->method()->name('foundJob')->withParameter(new Job($company))->definition()->void()
    )
    ->then()
    ->ifFailsRun(function ($compiler) {
        return false;
    })
    ->ifSuccedesRun(function ($compiler) {
        return true;
    })
    ->then()*/

var_dump((string)$company);

var_dump($compiler->getResponse());

if($compiler->hasFailed()) {
    var_dump($compiler->getResponse());
}

if($compiler->hasSucceded()) {
    var_dump($compiler->getResponse());
}

$objectStorage = new \SplObjectStorage();
$objectStorage->attach($mario);
$objectStorage->attach($martina);



