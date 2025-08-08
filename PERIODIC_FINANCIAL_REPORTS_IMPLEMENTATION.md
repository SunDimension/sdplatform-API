# Periodic Financial Reports Implementation

## Overview

The Periodic Financial Reports feature has been successfully implemented to store Profit and Loss, Balance Sheet, and Trial Balance data periodically by store, branch, and region. This system allows for historical tracking, efficient retrieval, and comprehensive financial reporting across different organizational levels.

## Features Implemented

### 1. **Multi-Level Reporting**
- **Store Level**: Generate reports for individual stores
- **Branch Level**: Generate reports for all stores within a branch
- **Region Level**: Generate reports for all branches within a region
- **Global Level**: Generate reports for the entire organization

### 2. **Report Types Supported**
- **Profit and Loss Statements**: Revenue and expense analysis
- **Balance Sheets**: Asset, liability, and equity statements
- **Trial Balance**: Account balance verification

### 3. **Data Storage and Management**
- **JSON Storage**: Complete report data stored in JSON format
- **Status Management**: Draft, Final, and Archived statuses
- **Version Control**: Historical tracking of report generations
- **Audit Trail**: User tracking and generation timestamps

### 4. **Advanced Filtering and Search**
- Filter by report type, store, branch, region
- Filter by financial period and date ranges
- Filter by status and generation date
- Pagination support for large datasets

### 5. **Bulk Operations**
- Generate reports for all stores in a branch
- Generate reports for all branches in a region
- Bulk status updates and archival

## Database Schema

### PeriodicFinancialReport Model

```php
protected $fillable = [
    'report_type',           // 'profit_loss', 'balance_sheet', 'trial_balance'
    'financial_period_id',   // UUID reference to financial period
    'store_id',             // Optional store reference
    'branch_id',            // Optional branch reference
    'region_id',            // Optional region reference
    'report_data',          // JSON data of the complete report
    'generated_at',         // Timestamp of generation
    'generated_by',         // User who generated the report
    'is_balanced',          // Boolean for trial balance verification
    'total_debits',         // Total debits (for trial balance)
    'total_credits',        // Total credits (for trial balance)
    'difference',           // Difference between debits and credits
    'status',               // 'draft', 'final', 'archived'
    'notes',                // Optional notes
];
```

## API Endpoints

### Generate Reports

#### Generate Single Report
```
POST /api/periodic-financial-reports/generate
```

**Request Body:**
```json
{
    "financial_period_id": "uuid",
    "store_id": 1,
    "branch_id": 2,
    "region_id": 3,
    "report_types": ["profit_loss", "balance_sheet", "trial_balance"]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Periodic financial reports generated and stored successfully",
    "data": {
        "reports": [
            {
                "id": "uuid",
                "report_type": "profit_loss",
                "financial_period_id": "uuid",
                "store_id": 1,
                "branch_id": 2,
                "region_id": 3,
                "report_data": {...},
                "generated_at": "2024-01-15T10:30:00Z",
                "generated_by": 1,
                "is_balanced": true,
                "status": "draft"
            }
        ],
        "count": 3
    }
}
```

#### Generate Reports for Branch
```
POST /api/periodic-financial-reports/generate-for-branch
```

**Request Body:**
```json
{
    "financial_period_id": "uuid",
    "branch_id": 2,
    "report_types": ["profit_loss", "balance_sheet"]
}
```

#### Generate Reports for Region
```
POST /api/periodic-financial-reports/generate-for-region
```

**Request Body:**
```json
{
    "financial_period_id": "uuid",
    "region_id": 3,
    "report_types": ["trial_balance"]
}
```

### Retrieve Reports

#### Get Reports with Filters
```
GET /api/periodic-financial-reports/reports?report_type=profit_loss&store_id=1&status=final
```

**Query Parameters:**
- `report_type`: profit_loss, balance_sheet, trial_balance
- `store_id`: Store ID
- `branch_id`: Branch ID
- `region_id`: Region ID
- `financial_period_id`: Financial period UUID
- `status`: draft, final, archived
- `date_from`: Start date (YYYY-MM-DD)
- `date_to`: End date (YYYY-MM-DD)
- `per_page`: Number of results per page (1-100)

#### Get Specific Report
```
GET /api/periodic-financial-reports/reports/{reportId}
```

### Manage Reports

#### Update Report Status
```
PUT /api/periodic-financial-reports/reports/{reportId}/status
```

**Request Body:**
```json
{
    "status": "final",
    "notes": "Reviewed and approved by management"
}
```

#### Delete Report
```
DELETE /api/periodic-financial-reports/reports/{reportId}
```

### Summary and Analytics

#### Get Report Summary
```
GET /api/periodic-financial-reports/summary?report_type=profit_loss&region_id=3
```

**Response:**
```json
{
    "success": true,
    "message": "Report summary retrieved successfully",
    "data": {
        "total_reports": 150,
        "reports_by_type": {
            "profit_loss": 50,
            "balance_sheet": 50,
            "trial_balance": 50
        },
        "reports_by_status": {
            "draft": 30,
            "final": 100,
            "archived": 20
        },
        "balanced_reports": 45,
        "unbalanced_reports": 5
    }
}
```

#### Archive Old Reports
```
POST /api/periodic-financial-reports/archive-old
```

**Request Body:**
```json
{
    "days_old": 365
}
```

#### Get Filter Options
```
GET /api/periodic-financial-reports/filter-options
```

**Response:**
```json
{
    "success": true,
    "message": "Filter options retrieved successfully",
    "data": {
        "stores": [...],
        "branches": [...],
        "regions": [...],
        "financial_periods": [...],
        "report_types": {
            "profit_loss": "Profit and Loss",
            "balance_sheet": "Balance Sheet",
            "trial_balance": "Trial Balance"
        },
        "statuses": {
            "draft": "Draft",
            "final": "Final",
            "archived": "Archived"
        }
    }
}
```

## Technical Implementation

### Service Layer (`PeriodicFinancialReportService`)

#### Key Methods:

1. **`generateAndStoreReports()`**: Generate and store multiple report types
2. **`generateAndStoreReport()`**: Generate and store a single report
3. **`getStoredReports()`**: Retrieve reports with advanced filtering
4. **`getStoredReport()`**: Retrieve a specific report
5. **`updateReportStatus()`**: Update report status and notes
6. **`deleteReport()`**: Delete a stored report
7. **`generateReportsForBranch()`**: Generate reports for all stores in a branch
8. **`generateReportsForRegion()`**: Generate reports for all branches in a region
9. **`getReportSummary()`**: Get summary statistics
10. **`archiveOldReports()`**: Archive reports older than specified days

### Enhanced FinancialReportingService

The existing `FinancialReportingService` has been enhanced to support scope filtering:

- **`generateProfitAndLoss()`**: Now accepts store, branch, and region parameters
- **`generateBalanceSheet()`**: Now accepts store, branch, and region parameters
- **`generateTrialBalance()`**: Now accepts store, branch, and region parameters
- **`getPeriodTransactions()`**: Enhanced to filter transactions by scope

### Controller Layer (`PeriodicFinancialReportController`)

Comprehensive controller with full CRUD operations and specialized endpoints for bulk operations.

## Usage Examples

### 1. Generate Monthly Reports for All Stores

```bash
# Generate all report types for a specific store
curl -X POST http://localhost:8000/api/periodic-financial-reports/generate \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "financial_period_id": "uuid",
    "store_id": 1,
    "report_types": ["profit_loss", "balance_sheet", "trial_balance"]
  }'
```

### 2. Generate Quarterly Reports for All Branches in a Region

```bash
# Generate reports for all branches in a region
curl -X POST http://localhost:8000/api/periodic-financial-reports/generate-for-region \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "financial_period_id": "uuid",
    "region_id": 3,
    "report_types": ["profit_loss", "balance_sheet"]
  }'
```

### 3. Retrieve Final Reports for a Store

```bash
# Get final reports for a specific store
curl -X GET "http://localhost:8000/api/periodic-financial-reports/reports?store_id=1&status=final&per_page=20" \
  -H "Authorization: Bearer {token}"
```

### 4. Update Report Status

```bash
# Mark a report as final
curl -X PUT http://localhost:8000/api/periodic-financial-reports/reports/{reportId}/status \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "status": "final",
    "notes": "Reviewed by CFO"
  }'
```

## Benefits

### 1. **Historical Tracking**
- Complete audit trail of financial reports
- Version control for report generations
- Historical comparison capabilities

### 2. **Performance Optimization**
- Pre-calculated reports reduce computation time
- Efficient retrieval with indexing
- Reduced database load for repeated queries

### 3. **Organizational Hierarchy Support**
- Multi-level reporting (Store → Branch → Region)
- Hierarchical data aggregation
- Flexible scope filtering

### 4. **Data Integrity**
- Status management (Draft → Final → Archived)
- Balance verification for trial balances
- User tracking and accountability

### 5. **Scalability**
- Efficient storage with JSON data
- Pagination for large datasets
- Archive functionality for old reports

## Integration with Existing Features

The Periodic Financial Reports feature integrates seamlessly with:

- **Financial Reporting System**: Uses existing `FinancialReportingService`
- **Account Management**: Leverages existing account structure
- **Financial Periods**: Supports period-based reporting
- **User Management**: Tracks report generation by users
- **Store/Branch/Region Hierarchy**: Utilizes existing organizational structure

## Error Handling

The implementation includes comprehensive error handling:

- **Validation Errors**: Proper input validation with detailed error messages
- **Database Errors**: Graceful handling of database connection issues
- **Calculation Errors**: Safe mathematical operations with rounding considerations
- **Missing Data**: Handles scenarios with no transactions or opening balances
- **Authorization Errors**: Proper permission checking for sensitive operations

## Performance Considerations

- **Efficient Queries**: Optimized database queries with proper indexing
- **JSON Storage**: Fast retrieval of complete report data
- **Pagination**: Handles large datasets efficiently
- **Caching Ready**: Framework supports caching for frequently accessed data
- **Archive Strategy**: Automatic archival of old reports to maintain performance

## Future Enhancements

Potential future improvements:

1. **Real-time Updates**: WebSocket integration for live report updates
2. **Advanced Analytics**: Trend analysis and forecasting capabilities
3. **Export Features**: PDF, Excel, and CSV export options
4. **Scheduled Generation**: Automated report generation on schedules
5. **Comparative Analysis**: Built-in period-over-period comparisons
6. **Custom Dashboards**: Interactive financial dashboards
7. **Mobile Support**: Mobile-optimized report viewing
8. **API Rate Limiting**: Enhanced API security and performance

## Conclusion

The Periodic Financial Reports feature provides a robust foundation for storing and managing financial data across different organizational levels. It ensures data integrity, provides historical tracking, and offers efficient retrieval mechanisms for comprehensive financial reporting. The implementation follows best practices for scalability, performance, and maintainability while integrating seamlessly with the existing financial reporting system. 