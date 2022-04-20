Feature: blabla
  blabla
  bla

  Scenario: blabla

    Given I have the following code
      """
      <?php
      final class Y {}
      final class X {
        public function __construct(Y $y) {}
      }
      """
    When I run pedigree
    Then I see no errors
    Then I expect this output
      """
      <?php


      class ServiceLocator {
          private ?Y $_Y = null;
          public function getY(): Y {
              return $this->_Y ??= new Y();
          }

          private ?X $_X = null;
          public function getX(): X {
              return $this->_X ??= new X($this->getY());
          }

      }

      """