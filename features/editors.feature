Feature:
  In order to provide vulenerable readers with the ability to safely read our magazine
  As a editor
  I want to be able to mark text with a trigger warning so readers can choose to read material.

Scenario:
  Given that the editor has some text to edit
  When they mark a passage of that text with a trigger warning
  And they provide no additional context about the content
  Then that text will be published hidden and with a generic warning.

Scenario:
  Given that the editor has some text to edit
  When they mark a passage of that text with a trigger warning
  And they provide context that the material contains "something"
  Then that text will be published hidden and with a warning about that "something".

Scenario:
  Given that the editor has some text to edit
  When they mark a passage of text previously marked with a trigger warning as not requiring it
  Then the text will be published as visible.
