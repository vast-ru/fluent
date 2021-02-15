<?php

namespace Tests\Relations;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;
use LaravelDoctrine\Fluent\Relations\JoinColumn;

class JoinColumnTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var JoinColumn
     */
    protected $column;

    protected function setUp(): void
    {
        $this->column = new JoinColumn(
            new DefaultNamingStrategy(),
            'parent',
            'parent_id',
            'id'
        );
    }

    public function test_can_create_new_join_column()
    {
        $this->assertEquals('parent_id', $this->column->getJoinColumn());
        $this->assertEquals('id', $this->column->getReferenceColumn());
        $this->assertTrue($this->column->isNullable());
        $this->assertFalse($this->column->isUnique());
        $this->assertNull($this->column->getOnDelete());
    }

    public function test_can_set_foreign_key()
    {
        $this->column->foreignKey('foreign_id');

        $this->assertEquals('foreign_id', $this->column->getJoinColumn());
    }

    public function test_can_set_target()
    {
        $this->column->target('target');

        $this->assertEquals('target', $this->column->getJoinColumn());
    }

    public function test_can_set_local_key()
    {
        $this->column->localKey('local_id');

        $this->assertEquals('local_id', $this->column->getReferenceColumn());
    }

    public function test_can_set_source()
    {
        $this->column->source('source');

        $this->assertEquals('source', $this->column->getReferenceColumn());
    }

    public function test_can_set_join_column()
    {
        $this->column->setJoinColumn('join');

        $this->assertEquals('join', $this->column->getJoinColumn());
    }

    public function test_can_set_reference_column()
    {
        $this->column->setReferenceColumn('ref');

        $this->assertEquals('ref', $this->column->getReferenceColumn());
    }

    public function test_can_return_default_join_column()
    {
        $this->column = new JoinColumn(
            new DefaultNamingStrategy(),
            'parent'
        );

        $this->assertEquals('parent_id', $this->column->getJoinColumn());
    }

    public function test_can_return_default_reference_column()
    {
        $this->column = new JoinColumn(
            new DefaultNamingStrategy(),
            'parent'
        );

        $this->assertEquals('id', $this->column->getReferenceColumn());
    }

    public function test_can_set_nullable()
    {
        $this->column->nullable();

        $this->assertTrue($this->column->isNullable());
    }

    public function test_can_set_unique()
    {
        $this->column->unique();

        $this->assertTrue($this->column->isUnique());
    }

    public function test_can_set_onDelete()
    {
        $this->column->onDelete('CASCADE');

        $this->assertEquals('CASCADE', $this->column->getOnDelete());
    }
}
