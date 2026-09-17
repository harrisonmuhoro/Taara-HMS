<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardMetrics($branchId, $startDate, $endDate)
    {
        $totalRooms = Room::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->count();
        $occupiedRooms = Room::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->where('operational_status', 'OCCUPIED')->count();
        
        $revenue = Invoice::when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->where('status', 'PAID')
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->sum('grand_total');

        // Simple Occupancy Calculation
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;
        
        return [
            'total_revenue' => $revenue,
            'occupancy_rate' => $occupancyRate,
            'occupied_rooms' => $occupiedRooms,
            'total_rooms' => $totalRooms,
        ];
    }

    public function getRevenueData($branchId, $startDate, $endDate)
    {
        return Invoice::when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->where('status', 'PAID')
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->selectRaw('DATE(issued_at) as date, sum(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    public function getInventoryValuation($branchId)
    {
        return Product::when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->selectRaw('sum(current_stock * cost_price) as total_value')
            ->value('total_value') ?? 0;
    }

    public function getLowStockProducts($branchId)
    {
        return Product::when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->get();
    }
}
