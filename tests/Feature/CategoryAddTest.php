<?php


namespace Tests\Feature;


class CategoryAddTest extends FeatureTestCase
{
    public function setUp():void
    {
        parent::setUp();
        $this->createPartner();
    }
    public function testDummy()
    {
        $this->assertEquals(1,1);
    }



}
