<?php

namespace Tests\Unit\V2;

use App\Http\Requests\StoreChecklistRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Models\TravelCompany;
use Illuminate\Support\Facades\Validator;
use Tests\Support\RunsV2Migrations;
use Tests\TestCase;

class FormRequestTest extends TestCase
{
    use RunsV2Migrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runV2Migrations();
        $this->seedAdminUser();
    }

    public function test_store_inspection_request_requires_core_fields(): void
    {
        $request = new StoreInspectionRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('travel_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('inspection_no', $validator->errors()->toArray());
        $this->assertArrayHasKey('inspection_date', $validator->errors()->toArray());
    }

    public function test_store_inspection_request_accepts_valid_payload(): void
    {
        $admin = $this->seedAdminUser();
        $this->actingAs($admin);

        $travel = TravelCompany::first();
        $request = new StoreInspectionRequest();
        $validator = Validator::make([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-9001',
            'inspection_date' => now()->format('Y-m-d'),
            'inspection_type' => 'ROUTINE',
        ], $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_store_checklist_request_rejects_invalid_input_type(): void
    {
        $request = new StoreChecklistRequest();
        $validator = Validator::make([
            'category_id' => 1,
            'title' => 'Item tidak valid',
            'input_type' => 'INVALID',
            'weight' => 5,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('input_type', $validator->errors()->toArray());
    }
}
