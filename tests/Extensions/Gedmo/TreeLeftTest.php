<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Tree\Mapping\Driver\Fluent as TreeDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\TreeLeft;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
class TreeLeftTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var ExtensibleClassMetadata
     */
    protected $classMetadata;

    /**
     * @var TreeLeft
     */
    private $extension;

    protected function setUp(): void
    {
        $this->fieldName     = 'lft';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'integer', $this->fieldName)->build();

        $this->extension = new TreeLeft($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        TreeLeft::enable();

        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')), 'integer', $this->fieldName
        )->build();

        $this->assertInstanceOf(
            TreeLeft::class,
            call_user_func([$field, TreeLeft::MACRO_METHOD])
        );
    }

    public function test_can_mark_a_field_as_left()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'left' => 'lft',
        ]);
    }

    public function test_left_should_be_integer()
    {
        $this->expectException(InvalidMappingException::class);
        $this->expectExceptionMessage('Tree left field must be \'integer\' in class - foo');

        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', $this->fieldName)->build();
        $this->extension = new TreeLeft($this->classMetadata, $this->fieldName);

        $this->getExtension()->build();
    }

    /**
     * Assert that the resulting build matches exactly with the given array.
     *
     * @param array $expected
     *
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    protected function assertBuildResultIs(array $expected)
    {
        $this->assertEquals($expected, $this->classMetadata->getExtension(
            $this->getExtensionName()
        ));
    }

    /**
     * @return TreeLeft
     */
    protected function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    protected function getExtensionName()
    {
        return TreeDriver::EXTENSION_NAME;
    }
}
