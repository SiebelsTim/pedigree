<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Assert;
use Siebels\Pedigree\Application;
use Siebels\Pedigree\Config;
use Siebels\Pedigree\IO\File;
use Siebels\Pedigree\IO\Files;
use Siebels\Pedigree\IO\InMemoryOutputStream;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var array<File> */
    private array $files = [];
    private ?int $exitCode = null;
    private InMemoryOutputStream $outputStream;
    /**
     * @var array<string>
     */
    private array $components = [];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->outputStream = new InMemoryOutputStream();
    }

    /**
     * @Given I have the following code
     */
    public function iHaveTheFollowingCode(PyStringNode $code): void
    {
        $this->files[] = new File("<?php\n" . $code->getRaw());
    }

    /**
     * @When I run pedigree
     */
    public function iRunPedigree(): void
    {
        $config = (new Config())
            ->setOutput($this->outputStream)
        ;
        foreach ($this->components as $component) {
            $config->addComponent($component);
        }

        $this->exitCode = (new Application())->run($config, new Files($this->files));
    }

    /**
     * @Then I see no errors
     */
    public function iSeeNoErrors(): void
    {
        Assert::assertEquals(0, $this->exitCode);
    }

    /**
     * @Then I expect this output
     */
    public function iExpectThisOutput(PyStringNode $output): void
    {
        $expect = trim($output->getRaw());
        $actual = trim($this->outputStream->getContent());

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $expectAST = $parser->parse("<?php\n" . $expect);
        $actualAST = $parser->parse($actual);

        $prettyPrinter = new Standard();
        Assert::assertEquals($prettyPrinter->prettyPrintFile($expectAST), $prettyPrinter->prettyPrintFile($actualAST));
    }

    /**
     * @Given The component is :component
     */
    public function theComponentIs(string $componentClassName): void
    {
        $this->components[] = $componentClassName;
    }
}
