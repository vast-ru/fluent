<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Builders\Delay;
use LaravelDoctrine\Fluent\Mappers\EntityMapper;
use LaravelDoctrine\Fluent\Mappers\Mapper;
use Mockery as m;
use Tests\Stubs\Entities\StubEntity;
use Tests\Stubs\Mappings\StubEntityMapping;

class EntityMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EntityMapper
     */
    protected $mapper;

    protected function setUp(): void
    {
        $mapping      = new StubEntityMapping;
        $this->mapper = new EntityMapper($mapping);
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
        $metadata = new ClassMetadataInfo(StubEntity::class);
        $builder  = new Builder(new ClassMetadataBuilder($metadata));

        $this->mapper->map($builder);

        $this->assertContains('id', $metadata->fieldNames);
        $this->assertContains('name', $metadata->fieldNames);
        $this->assertStringContainsString(StubEntity::class, $metadata->associationMappings['parent']['targetEntity']);
        $this->assertStringContainsString(StubEntity::class, $metadata->associationMappings['children']['targetEntity']);
        $this->assertStringContainsString(StubEntity::class, $metadata->associationMappings['one']['targetEntity']);
        $this->assertStringContainsString(StubEntity::class, $metadata->associationMappings['many']['targetEntity']);
    }
}
