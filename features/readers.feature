Feature:
  As a reader of our magazine
  I want control of how I engage with its content
  In order to faciliate my need for safety or my lack of need for safety at my choice.

Scenario:
  Given a feature with a trigger warning at the top
  When I decide to reveal the warning
  But do not perminantly accept the warning's context
  Then then when I reload the page the warning will appear.

Scenario:
  Given a feature with trigger warnings assigned with two different contexts.
  When I decide to reveal the warning
  And I indicate that I accept one of the contexts
  Then when the page is reloaded the warning I accepted will not appear
  But the unaccepted warning be visible.

Scenario:
  Given a feature with trigger warnings assigned two different contexts
  When I decide to reveal the warning
  And I indicate that I want all contexts to be accepted
  Then when the page is reloaded the trigger warning will not appear.
