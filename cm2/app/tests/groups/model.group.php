<?php
class ModelGroupTest extends GroupTest
{
    function ModelGroupTest() {
        TestManager::addTestCasesFromDirectory($this, APP_TEST_CASES . DS . 'models');
    }
}