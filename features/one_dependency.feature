Feature: Creates ServiceLocator for simple classes with one dependency

  Scenario: Two classes. X depends on Y.
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
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      namespace Pedigree;

      class ServiceLocator
      {
          private ?\Y $_Y = null;
          private ?\X $_X = null;

          public function getY(): \Y {
              return $this->_Y ??= new \Y();
          }

          public function getX(): \X {
              return $this->_X ??= new \X($this->getY());
          }
      }
      """

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
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      namespace Pedigree;

      class ServiceLocator {
          private ?\NS1\NS2\Y $_NS1_NS2_Y = null;
          private ?\NS1\X $_NS1_X = null;

          public function getNS1_NS2_Y(): \NS1\NS2\Y {
              return $this->_NS1_NS2_Y ??= new \NS1\NS2\Y();
          }

          public function getNS1_X(): \NS1\X {
              return $this->_NS1_X ??= new \NS1\X($this->getNS1_NS2_Y());
          }

      }
      """