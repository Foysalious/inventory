<?php

namespace Tests\Unit;


use App\Services\Category\Creator;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;

class UnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_set_partner()
    {
        $request = new Request();
        $request->merge([
            'partner_id' => 38371,
        ]);
        $partner= app(Creator::class)->setPartner($request->partner_id);
        $this->assertIsArray($partner);
    }
}
