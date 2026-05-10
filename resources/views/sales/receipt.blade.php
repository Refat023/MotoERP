@extends('layouts.app')

@section('title', 'Print Receipt - Super Shop POS')

@section('content')
<div class="row mb-4">
    <div class="col-md-8 offset-md-2">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-receipt"></i> Receipt</h2>
            <div class="btn-group" role="group">
                <button id="autoPrintBtn" class="btn btn-success">
                    <i class="bi bi-printer"></i> Print Now
                </button>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div id="receipt" style="background: white; padding: 30px; border: 1px solid #ddd; font-family: 'Courier New', monospace; max-width: 80mm; margin: 0 auto;">
            <!-- Receipt Header -->
            <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px;">
                <h1 style="font-size: 24px; margin: 0; font-weight: bold;">
                    <i class="bi bi-shop"></i> SUPER SHOP
                </h1>
                <p style="margin: 5px 0; font-size: 12px;">Point of Sale System</p>
                <p style="margin: 5px 0; font-size: 11px; color: #666;">
                    Receipt #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <!-- Date & Time -->
            <div style="text-align: center; font-size: 11px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                <p style="margin: 3px 0;"><strong>Date:</strong> {{ $sale->created_at->format('m/d/Y') }}</p>
                <p style="margin: 3px 0;"><strong>Time:</strong> {{ $sale->created_at->format('h:i A') }}</p>
                <p style="margin: 3px 0;"><strong>Cashier:</strong> {{ $sale->user->name }}</p>
            </div>

            <!-- Customer Info -->
            @if($sale->customer)
            <div style="font-size: 11px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                <p style="margin: 3px 0;"><strong>Customer:</strong> {{ $sale->customer->name }}</p>
                @if($sale->customer->phone)
                <p style="margin: 3px 0;"><strong>Phone:</strong> {{ $sale->customer->phone }}</p>
                @endif
            </div>
            @else
            <div style="font-size: 11px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                <p style="margin: 3px 0;"><strong>Customer:</strong> Walk-in Customer</p>
            </div>
            @endif

            <!-- Separator -->
            <div style="text-align: center; margin: 10px 0;">
                <p style="margin: 0; font-size: 10px;">- - - - - - - - - - - - - - - - - - -</p>
            </div>

            <!-- Items Header -->
            <div style="font-size: 11px; margin-bottom: 8px; display: flex; justify-content: space-between; border-bottom: 1px solid #000; padding-bottom: 5px;">
                <span style="flex: 1;"><strong>Item</strong></span>
                <span style="width: 35px; text-align: center;"><strong>Qty</strong></span>
                <span style="width: 45px; text-align: right;"><strong>Price</strong></span>
                <span style="width: 45px; text-align: right;"><strong>Total</strong></span>
            </div>

            <!-- Items -->
            @foreach($sale->items as $item)
            <div style="font-size: 10px; margin-bottom: 4px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="flex: 1;">{{ $item->product->name }}</span>
                    <span style="width: 35px; text-align: center;">{{ $item->quantity }}</span>
                    <span style="width: 45px; text-align: right;">₱{{ number_format($item->price, 2) }}</span>
                    <span style="width: 45px; text-align: right;">₱{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @if($item->item_discount > 0)
                <div style="margin-left: 10px; color: #28a745; font-size: 9px;">
                    Item Discount: -৳{{ number_format($item->item_discount, 2) }}
                </div>
                @endif
            </div>
            @endforeach

            <!-- Separator -->
            <div style="margin: 10px 0;">
                <p style="margin: 0; border-bottom: 1px solid #000;"></p>
            </div>

            <!-- Totals -->
            <div style="font-size: 11px; margin-bottom: 5px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span>Subtotal:</span>
                    <span>৳{{ number_format($sale->total_amount, 2) }}</span>
                </div>
                @if($sale->item_discount_total > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px; color: #28a745;">
                    <span>Item Discounts:</span>
                    <span>-৳{{ number_format($sale->item_discount_total, 2) }}</span>
                </div>
                @endif
                @if($sale->discount > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px; color: #28a745;">
                    <span>Total Discount:</span>
                    <span>-৳{{ number_format($sale->discount, 2) }}</span>
                </div>
                @endif
                @if($sale->tax > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                    <span>VAT ({{ $sale->tax_rate }}%):</span>
                    <span>+৳{{ number_format($sale->tax, 2) }}</span>
                </div>
                @endif
            </div>

            <!-- Total Amount -->
            <div style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 0; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold;">
                    <span>TOTAL:</span>
                    <span>৳{{ number_format($sale->final_amount, 2) }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div style="font-size: 11px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
                <p style="margin: 3px 0;"><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
                <p style="margin: 3px 0;"><strong>Status:</strong> {{ ucfirst($sale->status) }}</p>
            </div>

            <!-- Notes -->
            @if($sale->notes)
            <div style="font-size: 10px; margin-bottom: 15px; padding: 8px; background: #f8f9fa; border-radius: 4px;">
                <p style="margin: 3px 0;"><strong>Notes:</strong></p>
                <p style="margin: 3px 0;">{{ $sale->notes }}</p>
            </div>
            @endif

            <!-- Footer -->
            <div style="text-align: center; margin-top: 20px; border-top: 2px solid #000; padding-top: 15px;">
                <p style="margin: 5px 0; font-size: 11px;">Thank you for your purchase!</p>
                <p style="margin: 5px 0; font-size: 10px;">Please visit us again</p>
               
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .navbar, .sidebar, .btn-group, h2, .row > .col-md-2 {
            display: none !important;
        }
        
        .main-content {
            padding: 0 !important;
            margin-left: 0 !important;
        }
        
        #receipt {
            box-shadow: none !important;
            border: none !important;
            max-width: 80mm !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        
        @page {
            size: 80mm 297mm;
            margin: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.getElementById('autoPrintBtn').addEventListener('click', function() {
        window.print();
    });
</script>
@endsection
