@mod @mod_webpart

Feature: set webpart idnumber
  In order to set webpart idnumber
  As a teacher
  I should create webpart activity and set an ID number

  Scenario: webpart ID number input box should be shown.
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
      | webpart    | C1     | 1       | Web part with ID number set | C1WEBPART1 |
    When I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    Then "Web part with ID number set" activity should be visible
    And I turn editing mode off
    And "Web part with ID number set" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should see "Web part with ID number set"
    And I log out
    And I am on the "Web part with ID number set" "webpart activity editing" page logged in as teacher
    And I expand all fieldsets
    And I should see "ID number" in the "Common module settings" "fieldset"
    And the field "ID number" matches value "C1WEBPART1"
