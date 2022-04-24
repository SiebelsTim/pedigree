Feature: Component can be an abstract class

  Scenario: Component as abstract class. Already containing a function to provide a service.
    Given I have the following code
      """
      namespace NS1\NS2;

      final class Y {}
      """
    And I have the following code
      """
      namespace NS1;

      use NS1\NS2\Y;

      final class X {
        public function __construct(Y $y) {}
      }
      """
    And I have the following code
      """
      namespace NS1;

      abstract class Component {
          public abstract function getStuff(): X;
          protected function getY(): \NS1\NS2\Y {
            return new \NS1\NS2\Y();
          }
      }
      """
    And The component is "NS1\Component"
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      namespace Pedigree;

      class PedigreeComponent extends \NS1\Component {
          private ?\NS1\X $_NS1_X = null;

          public function getStuff(): \NS1\X {
            return $this->_NS1_X ??= new \NS1\X($this->getY());
          }
      }
      """