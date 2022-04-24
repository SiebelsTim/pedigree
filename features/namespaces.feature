Feature: Creates a component with namespaces

  Scenario: Two classes with namespaces. X depends on Y.
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

      interface Component {
          public function getStuff(): X;
      }
      """
    And The component is "NS1\Component"
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      namespace Pedigree;

      class PedigreeComponent implements \NS1\Component {
          private ?\NS1\NS2\Y $_NS1_NS2_Y = null;
          private ?\NS1\X $_NS1_X = null;

          protected function getNS1_NS2_Y(): \NS1\NS2\Y {
              return $this->_NS1_NS2_Y ??= new \NS1\NS2\Y();
          }

          public function getStuff(): \NS1\X {
            return $this->_NS1_X ??= new \NS1\X($this->getNS1_NS2_Y());
          }
      }
      """