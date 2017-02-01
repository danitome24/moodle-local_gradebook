@local @local_gradebook
Feature: Display local gradebook tree

  Scenario: Display local gradebook tree
    Given the following "courses" exist:
      | id | fullname | shortname | category | groupmode |
      | 1  | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "grade categories" exist:
      | fullname       | course |
      | Sub category 1 | C1     |
      | Sub category 2 | C1     |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I select gradebook on navigation
    Then I am on local gradebook home with course "1"
