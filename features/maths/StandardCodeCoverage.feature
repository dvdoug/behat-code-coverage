Feature: behat-code-coverage
  In order to ensure that my code handles all cases correctly
  As a developer
  I need to be able to be able to check that I have written scenarios covering all code paths

  Scenario: Some maths
    Given I have two variables A=1 and B=1
    When I add A and B
    Then the result should be 2
