<?php
namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Translatable\Mapping\Driver\Fluent as TranslatableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Translatable;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
class TranslatableTest extends \PHPUnit\Framework\TestCase
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
     * @var Translatable
     */
    private $extension;

    protected function setUp(): void
    {
        $this->fieldName     = 'title';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        Field::make(new ClassMetadataBuilder($this->classMetadata), 'string', 'title')->build();

        $this->extension = new Translatable($this->classMetadata, $this->fieldName, 'name');
    }

    public function test_it_should_add_itself_as_a_field_macro()
    {
        Translatable::enable();

        $field = Field::make(new ClassMetadataBuilder(new ExtensibleClassMetadata('Foo')), 'string',
            $this->fieldName)->build();

        $this->assertInstanceOf(
            Translatable::class,
            call_user_func([$field, Translatable::MACRO_METHOD])
        );
    }

    public function test_it_should_add_translatable_to_the_given_field()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'fields' => ['title'],
        ]);
    }

    public function can_have_many_translatable_fields()
    {
        $field1 = new Translatable($this->classMetadata, 'field1', 'name');
        $field2 = new Translatable($this->classMetadata, 'field2', 'name');

        $field1->build();
        $this->assertCount(1, $this->classMetadata->getExtension($this->getExtensionName())['fields']);

        $field2->build();

        $this->assertCount(2, $this->classMetadata->getExtension($this->getExtensionName())['fields']);
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
     * @return Translatable
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
        return TranslatableDriver::EXTENSION_NAME;
    }
}
