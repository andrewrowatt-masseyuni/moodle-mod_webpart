@mod @mod_webpart @core_completion
Feature: View activity completion information for the webpart
  In order to have visibility of Web part completion requirements
  As a student
  I need to be able to view my Web part completion progress

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity       | webpart         |
      | course         | C1            |
      | idnumber       | mh1           |
      | section        | 1             |
      | completion     | 1             |

  @javascript
  Scenario: The manual completion button will be shown on the course page if the Show activity completion conditions is set to No
    Given I am on the "Course 1" course page logged in as teacher1
    # Teacher view.
    And the manual completion button for "Test webpart 1" should exist
    And the manual completion button for "Test webpart 1" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Test webpart 1" should exist
    And the manual completion button of "Test webpart 1" is displayed as "Mark as done"
    And I toggle the manual completion state of "Test webpart 1"
    And the manual completion button of "Test webpart 1" is displayed as "Done"

  @javascript
  Scenario: The manual completion button will be shown on the course page if the Show activity completion conditions is set to Yes
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "Yes"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Test webpart 1" should exist
    And the manual completion button for "Test webpart 1" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Test webpart 1" should exist
    And the manual completion button of "Test webpart 1" is displayed as "Mark as done"
    And I toggle the manual completion state of "Test webpart 1"
    And the manual completion button of "Test webpart 1" is displayed as "Done"
