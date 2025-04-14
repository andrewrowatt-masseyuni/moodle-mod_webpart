@mod @mod_simplelabel

Feature: set simplelabel idnumber
  In order to set simplelabel idnumber
  As a teacher
  I should create simplelabel activity and set an ID number

  Scenario: simplelabel ID number input box should be shown.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | First | teacher1@example.com |
      | student | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
      | student | C1 | student |
    And the following "activities" exist:
      | activity | course | section | intro                    | idnumber |
      | simplelabel    | C1     | 1       | Simple label with ID number set | C1SIMPLELABEL1 |
    When I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    Then "Simple label with ID number set" activity should be visible
    And I turn editing mode off
    And "Simple label with ID number set" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should see "Simple label with ID number set"
    And I log out
    And I am on the "Simple label with ID number set" "simplelabel activity editing" page logged in as teacher
    And I expand all fieldsets
    And I should see "ID number" in the "Common module settings" "fieldset"
    And the field "ID number" matches value "C1SIMPLELABEL1"
