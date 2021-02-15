<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Gedmo\Exception\InvalidMappingException;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use Gedmo\Translatable\Mapping\Driver\Fluent as TranslatableDriver;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Locale;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
class LocaleTest extends \PHPUnit\Framework\TestCase
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
     * @var Locale
     */
    protected $extension;

    /**
     * @var Builder
     */
    protected $builder;

    protected function setUp(): void
    {
        Locale::enable();

        $this->fieldName     = 'locale';
        $this->classMetadata = new ExtensibleClassMetadata('foo');
        $this->builder       = new Builder(new ClassMetadataBuilder($this->classMetadata));

        $this->extension = new Locale($this->classMetadata, $this->fieldName);
    }

    public function test_it_should_add_itself_as_a_builder_macro()
    {
        $this->assertInstanceOf(
            Locale::class,
            call_user_func([$this->builder, Locale::MACRO_METHOD], 'language')
        );
    }

    public function test_can_mark_a_field_as_locale()
    {
        $this->getExtension()->build();

        $this->assertBuildResultIs([
            'locale' => $this->fieldName,
        ]);
    }

    public function test_it_should_fail_when_trying_to_use_a_mapped_field_as_locale()
    {
        $this->expectException(InvalidMappingException::class);
        $this->builder->string('language');
        $this->builder->locale('language');

        $this->builder->build();
    }

    public function test_it_should_fail_when_trying_to_use_a_mapped_field_as_locale_even_if_its_mapped_afterwards()
    {
        $this->expectException(InvalidMappingException::class);
        $this->builder->locale('language');
        $this->builder->string('language');

        $this->builder->build();
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
     * @return Locale
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
