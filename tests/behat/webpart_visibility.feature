@mod @mod_webpart

Feature: Check webpart visibility works
  In order to check webpart visibility works
  As a teacher
  I should create webpart activity

  Background:
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
      | activity | course | section | intro          | idnumber | visible |
      | webpart    | C1     | 1       | Swanky webpart   | 1        | 1       |
      | webpart    | C1     | 1       | Swanky webpart 2 | 2        | 0       |

  Scenario: Hidden webpart activity should be show as hidden.
    Given I log in as "teacher"
    When I am on "Test" course homepage with editing mode on
    Then "Swanky webpart 2" webpart should be hidden
    And I turn editing mode off
    And "Swanky webpart 2" webpart should be hidden
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should not see "Swanky webpart 2"
    And I log out

  Scenario: Visible webpart activity should be shown as visible.
    Given I log in as "teacher"
    When I am on "Test" course homepage with editing mode on
    Then "Swanky webpart" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And "Swanky webpart" activity should be visible
    And I log out

  @javascript
  Scenario: Teacher can not show webpart inside the hidden section
    Given I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    When I hide section "1"
    Then "Swanky webpart" webpart should be hidden
    And I open "Swanky webpart" actions menu
    And "Swanky webpart" actions menu should not have "Show" item
    And "Swanky webpart" actions menu should not have "Hide" item
    And "Swanky webpart" actions menu should not have "Make available" item
    And "Swanky webpart" actions menu should not have "Make unavailable" item
    And I click on "Edit settings" "link" in the "Swanky webpart" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Hide on course page"
    And the "Availability" select box should not contain "Make available but don't show on course page"
    And the "Availability" select box should not contain "Show on course page"
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should not see "Swanky webpart"
    And I log out
