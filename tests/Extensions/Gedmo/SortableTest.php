<?php

namespace Tests\Extensions\Gedmo;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use LaravelDoctrine\Fluent\Builders\Field;
use LaravelDoctrine\Fluent\Extensions\ExtensibleClassMetadata;
use LaravelDoctrine\Fluent\Extensions\Gedmo\Sortable;
use LaravelDoctrine\Fluent\Extensions\Gedmo\SortableGroup;
use LaravelDoctrine\Fluent\Extensions\Gedmo\SortablePosition;

class SortableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function test_enables_position_and_group()
    {
        $field = Field::make(new ClassMetadataBuilder(
            new ExtensibleClassMetadata('Foo')
        ), 'string', 'category')->build();

        Sortable::enable();

        $this->assertTrue($field->hasMacro(SortablePosition::MACRO_METHOD));
        $this->assertTrue($field->hasMacro(SortableGroup::MACRO_METHOD));
    }
}