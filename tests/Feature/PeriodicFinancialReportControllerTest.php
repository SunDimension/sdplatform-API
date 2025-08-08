<?php

namespace Tests\Feature;

use App\Models\PeriodicFinancialReport;
use App\Models\FinancialPeriod;
use App\Models\Store;
use App\Models\Branch;
use App\Models\Region;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\Transaction;
use App\Services\PeriodicFinancialReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;

class PeriodicFinancialReportControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_generate_periodic_financial_reports()
    {
        // Create test data
        $financialPeriod = FinancialPeriod::factory()->create();
        $store = Store::factory()->create();
        $branch = Branch::factory()->create();
        $region = Region::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/generate', [
                'financial_period_id' => $financialPeriod->id,
                'store_id' => $store->id,
                'branch_id' => $branch->id,
                'region_id' => $region->id,
                'report_types' => ['profit_loss', 'balance_sheet', 'trial_balance']
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Periodic financial reports generated and stored successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'reports' => [
                        '*' => [
                            'id',
                            'report_type',
                            'financial_period_id',
                            'store_id',
                            'branch_id',
                            'region_id',
                            'report_data',
                            'generated_at',
                            'generated_by',
                            'status'
                        ]
                    ],
                    'count'
                ]
            ]);

        $this->assertDatabaseHas('periodic_financial_reports', [
            'financial_period_id' => $financialPeriod->id,
            'store_id' => $store->id,
            'branch_id' => $branch->id,
            'region_id' => $region->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_for_generation()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/generate', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['financial_period_id']);
    }

    /** @test */
    public function it_can_generate_reports_for_branch()
    {
        $financialPeriod = FinancialPeriod::factory()->create();
        $branch = Branch::factory()->create();
        $stores = Store::factory()->count(3)->create(['branch_id' => $branch->id]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/generate-for-branch', [
                'financial_period_id' => $financialPeriod->id,
                'branch_id' => $branch->id,
                'report_types' => ['profit_loss', 'balance_sheet']
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Reports generated for all stores in branch successfully'
            ]);

        // Should create reports for each store in the branch
        $this->assertDatabaseCount('periodic_financial_reports', 6); // 3 stores × 2 report types
    }

    /** @test */
    public function it_can_generate_reports_for_region()
    {
        $financialPeriod = FinancialPeriod::factory()->create();
        $region = Region::factory()->create();
        $branches = Branch::factory()->count(2)->create(['region_id' => $region->id]);
        
        // Create stores for each branch
        foreach ($branches as $branch) {
            Store::factory()->count(2)->create(['branch_id' => $branch->id]);
        }

        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/generate-for-region', [
                'financial_period_id' => $financialPeriod->id,
                'region_id' => $region->id,
                'report_types' => ['trial_balance']
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Reports generated for all branches in region successfully'
            ]);

        // Should create reports for each store in each branch
        $this->assertDatabaseCount('periodic_financial_reports', 4); // 2 branches × 2 stores × 1 report type
    }

    /** @test */
    public function it_can_retrieve_reports_with_filters()
    {
        // Create test reports
        $financialPeriod = FinancialPeriod::factory()->create();
        $store = Store::factory()->create();
        
        PeriodicFinancialReport::factory()->count(5)->create([
            'financial_period_id' => $financialPeriod->id,
            'store_id' => $store->id,
            'status' => PeriodicFinancialReport::STATUS_FINAL
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/reports?store_id=' . $store->id . '&status=final');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Periodic financial reports retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'report_type',
                            'financial_period_id',
                            'store_id',
                            'report_data',
                            'status'
                        ]
                    ],
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_can_retrieve_specific_report()
    {
        $report = PeriodicFinancialReport::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/reports/' . $report->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Periodic financial report retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'report_type',
                    'financial_period_id',
                    'report_data',
                    'generated_at',
                    'status'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_report_status()
    {
        $report = PeriodicFinancialReport::factory()->create([
            'status' => PeriodicFinancialReport::STATUS_DRAFT
        ]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/periodic-financial-reports/reports/' . $report->id . '/status', [
                'status' => PeriodicFinancialReport::STATUS_FINAL,
                'notes' => 'Reviewed and approved by management'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Report status updated successfully'
            ]);

        $this->assertDatabaseHas('periodic_financial_reports', [
            'id' => $report->id,
            'status' => PeriodicFinancialReport::STATUS_FINAL,
            'notes' => 'Reviewed and approved by management'
        ]);
    }

    /** @test */
    public function it_can_delete_report()
    {
        $report = PeriodicFinancialReport::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/periodic-financial-reports/reports/' . $report->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);

        $this->assertSoftDeleted('periodic_financial_reports', [
            'id' => $report->id
        ]);
    }

    /** @test */
    public function it_can_get_report_summary()
    {
        // Create test reports
        PeriodicFinancialReport::factory()->count(3)->profitLoss()->final()->create();
        PeriodicFinancialReport::factory()->count(2)->balanceSheet()->draft()->create();
        PeriodicFinancialReport::factory()->count(1)->trialBalance()->archived()->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/summary');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Report summary retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_reports',
                    'reports_by_type',
                    'reports_by_status',
                    'balanced_reports',
                    'unbalanced_reports'
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(6, $data['total_reports']);
        $this->assertArrayHasKey('profit_loss', $data['reports_by_type']);
        $this->assertArrayHasKey('final', $data['reports_by_status']);
    }

    /** @test */
    public function it_can_archive_old_reports()
    {
        // Create old final reports
        PeriodicFinancialReport::factory()->count(5)->create([
            'status' => PeriodicFinancialReport::STATUS_FINAL,
            'generated_at' => now()->subDays(400) // Older than 365 days
        ]);

        // Create recent final reports
        PeriodicFinancialReport::factory()->count(3)->create([
            'status' => PeriodicFinancialReport::STATUS_FINAL,
            'generated_at' => now()->subDays(100) // Recent
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/archive-old', [
                'days_old' => 365
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Old reports archived successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'archived_count',
                    'days_old'
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(5, $data['archived_count']);
    }

    /** @test */
    public function it_can_get_filter_options()
    {
        // Create test data
        Store::factory()->count(3)->create();
        Branch::factory()->count(2)->create();
        Region::factory()->count(2)->create();
        FinancialPeriod::factory()->count(4)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/filter-options');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Filter options retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'stores',
                    'branches',
                    'regions',
                    'financial_periods',
                    'report_types',
                    'statuses'
                ]
            ]);

        $data = $response->json('data');
        $this->assertCount(3, $data['stores']);
        $this->assertCount(2, $data['branches']);
        $this->assertCount(2, $data['regions']);
        $this->assertCount(4, $data['financial_periods']);
        $this->assertArrayHasKey('profit_loss', $data['report_types']);
        $this->assertArrayHasKey('draft', $data['statuses']);
    }

    /** @test */
    public function it_validates_report_types()
    {
        $financialPeriod = FinancialPeriod::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson('/api/periodic-financial-reports/generate', [
                'financial_period_id' => $financialPeriod->id,
                'report_types' => ['invalid_type']
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['report_types.0']);
    }

    /** @test */
    public function it_validates_status_values()
    {
        $report = PeriodicFinancialReport::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson('/api/periodic-financial-reports/reports/' . $report->id . '/status', [
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_handles_missing_report()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/reports/' . Str::uuid());

        $response->assertStatus(404);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/periodic-financial-reports/generate', []);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_filter_reports_by_date_range()
    {
        // Create reports with different dates
        PeriodicFinancialReport::factory()->create([
            'generated_at' => now()->subDays(10)
        ]);
        PeriodicFinancialReport::factory()->create([
            'generated_at' => now()->subDays(5)
        ]);
        PeriodicFinancialReport::factory()->create([
            'generated_at' => now()->subDays(1)
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/reports?date_from=' . now()->subDays(7)->format('Y-m-d') . '&date_to=' . now()->subDays(2)->format('Y-m-d'));

        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        $this->assertCount(1, $data); // Should only return the report from 5 days ago
    }

    /** @test */
    public function it_can_paginate_results()
    {
        // Create more reports than default per_page
        PeriodicFinancialReport::factory()->count(25)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/periodic-financial-reports/reports?per_page=10');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals(10, $data['per_page']);
        $this->assertEquals(25, $data['total']);
        $this->assertCount(10, $data['data']);
    }
} 