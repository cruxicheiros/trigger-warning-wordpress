Feature:
  In order to provide vulenerable readers with the ability to safely read our magazine
  As a editor
  I want to be able to mark a post with a trigger warning so readers can choose to read material.

Scenario:
  Given that the editor has a post
  When they mark that post with a trigger warning
  And they provide no additional context about the content
  Then a generic trigger warning will be shown at the top.


Scenario:
  Given that the editor has a post
  When they mark that post with a trigger warning
  And they provide context that the material contains "something"
  Then the trigger warning will be shown at the top available for the reader to reveal that "something".

Scenario:
  Given that the editor has a post
  When they mark a passage of text previously marked with a trigger warning as not requiring it
  Then the post will have the warning at the top removed.
