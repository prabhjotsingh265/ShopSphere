@extends('admin.layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="row">
        @include('admin.layouts.sidebar')
        <div class="col-md-9 ss-main">
            <div class="ss-page-header ss-reveal">
                <div class="title-block">
                    <div class="icon-badge"><i class="fas fa-gauge-high"></i></div>
                    <h3>Dashboard</h3>
                </div>
                <span class="count-pill"><i class="fas fa-clock"></i> {{ now()->format('D, M j Y') }}</span>
            </div>

            <div class="ss-stat-grid">
                <div class="ss-stat-card pine ss-reveal">
                    <div class="stat-icon"><i class="fas fa-cart-shopping"></i></div>
                    <div class="stat-label">Today</div>
                    <div class="stat-value">{{ $todayOrders->count() }}</div>
                    <div class="stat-sub">${{ number_format($todayOrders->sum('total'), 2) }} in orders</div>
                </div>
                <div class="ss-stat-card brass ss-reveal">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-label">Yesterday</div>
                    <div class="stat-value">{{ $yesterdayOrders->count() }}</div>
                    <div class="stat-sub">${{ number_format($yesterdayOrders->sum('total'), 2) }} in orders</div>
                </div>
                <div class="ss-stat-card rust ss-reveal">
                    <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ $monthOrders->count() }}</div>
                    <div class="stat-sub">${{ number_format($monthOrders->sum('total'), 2) }} in orders</div>
                </div>
                <div class="ss-stat-card emerald ss-reveal">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-label">This Year</div>
                    <div class="stat-value">{{ $yearOrders->count() }}</div>
                    <div class="stat-sub">${{ number_format($yearOrders->sum('total'), 2) }} in orders</div>
                </div>
            </div>

            <div class="ss-section-title">Quick Links</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{route('admin.products.index')}}" class="text-decoration-none">
                        <div class="ss-stat-card" style="--accent: var(--pine); --accent-ink:#EEF0E8;">
                            <div class="stat-icon"><i class="fas fa-tag"></i></div>
                            <div class="stat-label">Catalog</div>
                            <div class="stat-sub" style="font-size:.95rem;color:var(--ink);font-weight:600;margin-top:4px;">Manage Products &rarr;</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{route('admin.coupons.index')}}" class="text-decoration-none">
                        <div class="ss-stat-card" style="--accent: var(--brass); --accent-ink:var(--brass-ink);">
                            <div class="stat-icon"><i class="fas fa-ticket"></i></div>
                            <div class="stat-label">Promotions</div>
                            <div class="stat-sub" style="font-size:.95rem;color:var(--ink);font-weight:600;margin-top:4px;">Manage Coupons &rarr;</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{route('admin.reviews.index')}}" class="text-decoration-none">
                        <div class="ss-stat-card" style="--accent: var(--rust); --accent-ink:#fff;">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-label">Feedback</div>
                            <div class="stat-sub" style="font-size:.95rem;color:var(--ink);font-weight:600;margin-top:4px;">Moderate Reviews &rarr;</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{route('admin.orders.index')}}" class="text-decoration-none">
                        <div class="ss-stat-card" style="--accent: var(--emerald); --accent-ink:#fff;">
                            <div class="stat-icon"><i class="fas fa-cart-shopping"></i></div>
                            <div class="stat-label">Fulfillment</div>
                            <div class="stat-sub" style="font-size:.95rem;color:var(--ink);font-weight:600;margin-top:4px;">Manage Orders &rarr;</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{route('admin.users.index')}}" class="text-decoration-none">
                        <div class="ss-stat-card" style="--accent: var(--amber); --accent-ink:#2E2306;">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-label">Customers</div>
                            <div class="stat-sub" style="font-size:.95rem;color:var(--ink);font-weight:600;margin-top:4px;">Manage Users &rarr;</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
