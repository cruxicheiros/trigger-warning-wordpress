Feature:
  As a reader of our magazine
  I want control of how I engage with its content
  In order to faciliate my need for safety or my lack of need for safety at my choice.

Scenario:
  Given a feature with a trigger warning in the text
  When I decide to read the marked text
  But do not perminantly accept the warning's context
  Then the text will be visible to me to read
  And when the page is reloaded there will be a trigger warning for that context.

Scenario:
  Given a feature with two trigger warnings in the text with two different contexts
  When I decide to read the marked text for one context
  And I indicate that I accept the warning's context
  Then the text marked will be visible to me to read for that context
  And when the page is reloaded the marked text will be visible in the accepted context
  But the unaccepted context will remain hidden.

Scenario:
  Given a feature with two trigger warnings in the text with two different contexts
  When I decide to read the marked text for one context
  And I indicate that I want all trigger warnings to be visible
  Then all of the markted text will be visible to me to read
  And when the page is reloaded all of the marked text will be visible.
