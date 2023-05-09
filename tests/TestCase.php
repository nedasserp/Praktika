<?php

namespace Tests;
use App\Models\TestResult;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    //public function tearDown(): void
   // {
        // Save the test result to the database
        //$testResult = new TestResult([
            //'Testas' => $this->getName(),
           // 'Rezultatas' => $this->getStatus(),
           // 'Laikas' => '999',
           // 'Testo_parametrai' => $this->getStatusMessage(),
       // ]);
       // $testResult->save();
        
       // parent::tearDown();
    //}
}
