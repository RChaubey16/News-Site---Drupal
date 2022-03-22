<?php

namespace Drupal\Tests\facets_custom_label\Unit;

use Drupal\facets\Entity\Facet;
use Drupal\facets\Result\Result;
use Drupal\facets_custom_label\Plugin\facets\processor\FacetsCustomLabelProcessor;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the custom label processor.
 */
class FacetsCustomLabelProcessorTest extends UnitTestCase {

  /**
   * The processor to be tested.
   *
   * @var \Drupal\facets_custom_label\Plugin\facets\processor\FacetsCustomLabelProcessor
   */
  protected $processor;

  /**
   * An array containing the results before the processor has ran.
   *
   * @var \Drupal\facets\Result\Result[]
   */
  protected $originalResults;

  /**
   * Prepare the processor object for testing.
   */
  protected function setUp() {
    parent::setUp();

    $facet = new Facet([], 'facets_facet');
    $this->originalResults = [
      new Result($facet, 0, 'Numerical raw value 0', 5),
      new Result($facet, 'literal raw value 0', 'Literal raw value 0', 199),
      new Result($facet, 'literal raw value 1', 'Literal raw value 1', 2),
      new Result($facet, 1234, 'Numerical raw value 1234', 55),
    ];

    $this->processor = new FacetsCustomLabelProcessor(['replacement_values' => ''], 'facets_custom_label_processor', []);
  }

  /**
   * Tests defaultConfiguration().
   */
  public function testDefaultConfiguration() {
    $config = $this->processor->defaultConfiguration();
    $this->assertEquals(['replacement_values' => ''], $config);
  }

  /**
   * Tests build().
   */
  public function testBuild() {
    $facet = new Facet([], 'facets_facet');
    $facet->setResults($this->originalResults);

    // Invalid configs.
    $replacementValues = FacetsCustomLabelProcessor::ORIGIN__RAW . FacetsCustomLabelProcessor::SEPARATOR . '0' . FacetsCustomLabelProcessor::SEPARATOR . 'too much arguments / must be ignored' . FacetsCustomLabelProcessor::SEPARATOR . '' . "\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__RAW . FacetsCustomLabelProcessor::SEPARATOR . '0' . "\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__RAW . "\n";
    $replacementValues .= "\n";

    // Valid configs.
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__RAW . FacetsCustomLabelProcessor::SEPARATOR . '0' . FacetsCustomLabelProcessor::SEPARATOR . '0 replaced using raw value' . "\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__RAW . FacetsCustomLabelProcessor::SEPARATOR . 'literal raw value 0' . FacetsCustomLabelProcessor::SEPARATOR . 'literal raw value 0 replaced using raw value' . "\r\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__DISPLAY . FacetsCustomLabelProcessor::SEPARATOR . 'Literal raw value 1' . FacetsCustomLabelProcessor::SEPARATOR . 'literal raw value 1 replaced using display value' . "\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__RAW . FacetsCustomLabelProcessor::SEPARATOR . 'literal raw value 1' . FacetsCustomLabelProcessor::SEPARATOR . 'literal raw value 1 replaced using raw value' . "\n";
    $replacementValues .= FacetsCustomLabelProcessor::ORIGIN__DISPLAY . FacetsCustomLabelProcessor::SEPARATOR . 'Numerical raw value 1234' . FacetsCustomLabelProcessor::SEPARATOR . '1234 replaced using display value';

    $this->processor->setConfiguration(['replacement_values' => $replacementValues]);

    $filteredResults = $this->processor->build($facet, $this->originalResults);

    // Values replaced by raw values.
    $this->assertEquals('0 replaced using raw value', $filteredResults[0]->getDisplayValue());
    $this->assertEquals('literal raw value 0 replaced using raw value', $filteredResults[1]->getDisplayValue());

    // Regardless of order in the replacement values, the processor attempts
    // to replace using raw id before trying to replace using display value.
    // This is why this one is replaced by raw value and not by display value.
    $this->assertEquals('literal raw value 1 replaced using raw value', $filteredResults[2]->getDisplayValue());

    // Value replaced by display value.
    $this->assertEquals('1234 replaced using display value', $filteredResults[3]->getDisplayValue());
  }

}
