Feature: Creates ServiceLocator based on a Component

  Scenario: Two classes
    Given I have the following code
      """
      final class Y {}
      """
    And I have the following code
      """
      final class X {
        public function __construct(Y $y) {}
      }
      """
    And I have the following code
      """
      interface Component {
          public function getStuff(): X;
      }
      """
    And The component is Component
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      namespace Pedigree;

      class PedigreeComponent implements \Component
      {
          private ?\Y $_Y = null;
          private ?\X $_X = null;

          protected function getY(): \Y {
              return $this->_Y ??= new \Y();
          }

          public function getStuff(): \X {
            return $this->_X ??= new \X($this->getY());
          }
      }
      """