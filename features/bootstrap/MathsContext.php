<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class MathsContext implements Context
{
    private $a;

    private $b;

    private $result;

    /**
     * @Given /^I have two variables A=(\d+) and B=(\d+)$/
     */
    public function iHaveTwoVariablesAAndB(int $a, int $b): void
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @When /^I add A and B$/
     */
    public function iAddAAndB(): void
    {
        $this->result = $this->a + $this->b;
    }

    /**
     * @Then /^the result should be (\d+)$/
     */
    public function theResultShouldBe(int $arg1): void
    {
        Assert::assertEquals($arg1, $this->result);
    }
}
