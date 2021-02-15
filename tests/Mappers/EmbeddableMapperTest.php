<?php

namespace Tests\Mappers;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LaravelDoctrine\Fluent\Builders\Builder;
use LaravelDoctrine\Fluent\Mappers\EmbeddableMapper;
use LaravelDoctrine\Fluent\Mappers\Mapper;
use Tests\Stubs\Embedabbles\StubEmbeddable;
use Tests\Stubs\Mappings\StubEmbeddableMapping;

class EmbeddableMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EmbeddableMapper
     */
    protected $mapper;

    protected function setUp(): void
    {
        $mapping      = new StubEmbeddableMapping();
        $this->mapper = new EmbeddableMapper($mapping);
    }

    public function test_it_should_be_a_mapper()
    {
        $this->assertInstanceOf(Mapper::class, $this->mapper);
    }

    public function test_it_should_be_transient()
    {
        $this->assertTrue($this->mapper->isTransient());
    }

    public function test_it_should_delegate_the_proper_mapping_to_the_mapping_class()
    {
        $metadata = new ClassMetadataInfo(StubEmbeddable::class);
        $builder  = new Builder(new ClassMetadataBuilder($metadata));

        $this->mapper->map($builder);

        $this->assertContains('name', $metadata->fieldNames);
    }
}
