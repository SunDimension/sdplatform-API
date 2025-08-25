#!/bin/bash

# Sync System Test Runner
# This script helps test various sync scenarios and configurations

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR=$(pwd)
SYNC_COMMAND="php artisan sync:auto"
LOG_FILE="storage/logs/sync-test.log"

# Helper functions
print_header() {
    echo -e "\n${BLUE}=== $1 ===${NC}\n"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the right directory
check_environment() {
    print_header "Environment Check"
    
    if [ ! -f "artisan" ]; then
        print_error "Not in Laravel project directory. Please run this script from the project root."
        exit 1
    fi
    
    if [ ! -f "config/sync.php" ]; then
        print_warning "Sync configuration not found. Please publish the config first."
        print_info "Run: php artisan vendor:publish --tag=sync-config"
    fi
    
    print_success "Environment check completed"
}

# Test basic sync functionality
test_basic_sync() {
    print_header "Testing Basic Sync Functionality"
    
    print_info "Testing full sync mode..."
    $SYNC_COMMAND --mode=full --detailed 2>&1 | tee -a "$LOG_FILE"
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        print_success "Full sync test completed"
    else
        print_error "Full sync test failed"
        return 1
    fi
}

# Test individual sync modes
test_sync_modes() {
    print_header "Testing Individual Sync Modes"
    
    local modes=("push-only" "pull-only" "queue-only")
    
    for mode in "${modes[@]}"; do
        print_info "Testing $mode mode..."
        $SYNC_COMMAND --mode="$mode" --detailed 2>&1 | tee -a "$LOG_FILE"
        
        if [ ${PIPESTATUS[0]} -eq 0 ]; then
            print_success "$mode mode test completed"
        else
            print_warning "$mode mode test failed (this might be expected if service is unavailable)"
        fi
    done
}

# Test health check functionality
test_health_check() {
    print_header "Testing Health Check Functionality"
    
    print_info "Running health check..."
    $SYNC_COMMAND --health-check --mode=queue-only 2>&1 | tee -a "$LOG_FILE"
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        print_success "Health check completed"
    else
        print_warning "Health check failed (this might be expected if service is unavailable)"
    fi
}

# Test retry functionality
test_retry_functionality() {
    print_header "Testing Retry Functionality"
    
    print_info "Testing retry failed items..."
    $SYNC_COMMAND --retry-failed --mode=queue-only 2>&1 | tee -a "$LOG_FILE"
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        print_success "Retry test completed"
    else
        print_warning "Retry test failed (this might be expected if no failed items exist)"
    fi
}

# Test force sync
test_force_sync() {
    print_header "Testing Force Sync"
    
    print_info "Testing force sync (ignoring offline status)..."
    $SYNC_COMMAND --force --mode=queue-only 2>&1 | tee -a "$LOG_FILE"
    
    if [ ${PIPESTATUS[0]} -eq 0 ]; then
        print_success "Force sync test completed"
    else
        print_warning "Force sync test failed (this might be expected if service is unavailable)"
    fi
}

# Test with different batch sizes
test_batch_sizes() {
    print_header "Testing Different Batch Sizes"
    
    local batch_sizes=(25 50 100 200)
    
    for size in "${batch_sizes[@]}"; do
        print_info "Testing with batch size: $size"
        SYNC_BATCH_SIZE="$size" $SYNC_COMMAND --mode=queue-only --detailed 2>&1 | tee -a "$LOG_FILE"
        
        if [ ${PIPESTATUS[0]} -eq 0 ]; then
            print_success "Batch size $size test completed"
        else
            print_warning "Batch size $size test failed"
        fi
    done
}

# Test error handling
test_error_handling() {
    print_header "Testing Error Handling"
    
    print_info "Testing with invalid mode..."
    $SYNC_COMMAND --mode=invalid-mode 2>&1 | tee -a "$LOG_FILE"
    
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
        print_success "Error handling test passed (invalid mode rejected)"
    else
        print_error "Error handling test failed (invalid mode should have been rejected)"
    fi
}

# Run PHPUnit tests
run_unit_tests() {
    print_header "Running Unit Tests"
    
    if [ -f "vendor/bin/phpunit" ]; then
        print_info "Running AutoSync tests..."
        vendor/bin/phpunit --filter=AutoSyncTest 2>&1 | tee -a "$LOG_FILE"
        
        if [ ${PIPESTATUS[0]} -eq 0 ]; then
            print_success "Unit tests passed"
        else
            print_error "Unit tests failed"
        fi
    else
        print_warning "PHPUnit not found. Skipping unit tests."
        print_info "Install PHPUnit with: composer require --dev phpunit/phpunit"
    fi
}

# Performance testing
test_performance() {
    print_header "Performance Testing"
    
    print_info "Testing sync performance with timing..."
    
    local start_time=$(date +%s.%N)
    $SYNC_COMMAND --mode=queue-only --detailed 2>&1 | tee -a "$LOG_FILE"
    local exit_code=${PIPESTATUS[0]}
    local end_time=$(date +%s.%N)
    
    local duration=$(echo "$end_time - $start_time" | bc -l)
    
    if [ $exit_code -eq 0 ]; then
        print_success "Performance test completed in ${duration}s"
    else
        print_warning "Performance test failed after ${duration}s"
    fi
}

# Generate test report
generate_report() {
    print_header "Generating Test Report"
    
    local report_file="sync-test-report-$(date +%Y%m%d-%H%M%S).txt"
    
    {
        echo "Sync System Test Report"
        echo "Generated: $(date)"
        echo "=================================="
        echo ""
        echo "Test Log:"
        echo "---------"
        if [ -f "$LOG_FILE" ]; then
            tail -50 "$LOG_FILE"
        else
            echo "No log file found"
        fi
        echo ""
        echo "Configuration:"
        echo "---------------"
        if [ -f "config/sync.php" ]; then
            echo "Sync config exists"
        else
            echo "Sync config missing"
        fi
        echo ""
        echo "Environment Variables:"
        echo "----------------------"
        env | grep -i sync || echo "No sync environment variables found"
    } > "$report_file"
    
    print_success "Test report generated: $report_file"
}

# Main test runner
main() {
    print_header "Sync System Test Runner"
    print_info "Starting comprehensive sync system testing..."
    
    # Create log file
    touch "$LOG_FILE"
    echo "=== Sync Test Started: $(date) ===" > "$LOG_FILE"
    
    # Run tests
    check_environment
    test_basic_sync || true
    test_sync_modes
    test_health_check
    test_retry_functionality
    test_force_sync
    test_batch_sizes
    test_error_handling
    run_unit_tests
    test_performance
    
    # Generate report
    generate_report
    
    print_header "Test Summary"
    print_success "All tests completed. Check the log file and report for details."
    print_info "Log file: $LOG_FILE"
    print_info "Report: sync-test-report-*.txt"
}

# Check if bc is available for floating point math
if ! command -v bc &> /dev/null; then
    print_warning "bc command not found. Performance timing will be limited."
    # Simple fallback for timing
    test_performance() {
        print_header "Performance Testing"
        print_warning "bc not available. Skipping precise timing."
        $SYNC_COMMAND --mode=queue-only --detailed 2>&1 | tee -a "$LOG_FILE"
    }
fi

# Run main function
main "$@"
