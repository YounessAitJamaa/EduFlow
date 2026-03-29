@extends('layouts.base')

@section('title', 'Payment Cancelled')

@section('content')
<div class="text-center" style="padding: 100px 0;">
    <div style="font-size: 5rem; color: #f59e0b; margin-bottom: 30px;">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h1 style="font-size: 2.5rem; margin-bottom: 15px;">Payment Cancelled</h1>
    <p style="font-size: 1.2rem; color: var(--gray); margin-bottom: 40px;">The payment process was cancelled. You have not been charged.</p>
    
    <div>
        <a href="/courses" class="btn-primary" style="padding: 15px 30px; text-decoration: none;">Return to Catalog</a>
    </div>
</div>
@endsection
