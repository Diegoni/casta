<?php
require_once(__DIR__ . '/../autorun.php');
require_once(__DIR__ . '/support/test1.php');

class LoadIfIncludedTestCase extends UnitTestCase {
    function test_load_if_included() {
	    $tests = new GroupTest();
        $tests->addFile(__DIR__ . '/support/test1.php');
        $this->assertEqual($tests->getSize(), 1);
    }
}

?>