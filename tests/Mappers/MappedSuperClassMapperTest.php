<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Mappers\MappedSuperClassMapper;
use LaravelDoctrine\Fluent\Mappers\Mapper;
use Tests\Stubs\MappedSuperClasses\StubMappedSuperClass;
use Tests\Stubs\Mappings\StubMappedSuperClassMapping;

class MappedSuperClassMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MappedSuperClassMapper
     */
    protected $mapper;

    protected function setUp(): void
    {
        $mapping      = new StubMappedSuperClassMapping();
        $this->mapper = new MappedSuperClassMapper($mapping);
    }

    public function test_it_should_be_a_mapper()
    {
        $this->assertInstanceOf(Mapper::class, $this->mapper);
    }

    public function test_it_should_not_be_transient()
    {
        $this->assertFalse($this->mapper->isTransient());
    }

    public function test_it_should_delegate_the_proper_mapping_to_the_mapping_class()
    {
        $metadata = new ClassMetadataInfo(StubMappedSuperClass::class);
        $builder  = new Builder(new ClassMetadataBuilder($metadata));

        $this->mapper->map($builder);

        $this->assertContains('name', $metadata->fieldNames);
    }
}
