@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .circle-progress {
        position: relative;
        display: inline-block;
        width: 70px; 
        height: 70px;
        text-align: center;
    }
    
    .circle-progress svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .card-slide {
        transition: transform 0.3s ease;
        width: 320px !important; 
        height: 120px;
    }
    
    .hr-vertial {
        width: 1px;
        height: 50px;
        background-color: #e9ecef;
    }
    
    .profile-dots-pills {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border-width: 2px;
        border-style: solid;
    }
    
    .card-slie-arrow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-80%, -80%);
    }

    .swiper-button {
        position: absolute;
        top: 50%;
        width: 40px;
        height: 40px;
        margin-top: -10px; 
        z-index: 10;
        cursor: pointer;
        background-size: 20px 20px;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .swiper-button-next {
        right: 10px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23000'%3E%3Cpath d='M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z'/%3E%3C/svg%3E");
    }
    
    .swiper-button-prev {
        left: 10px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23000'%3E%3Cpath d='M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z'/%3E%3C/svg%3E");
    }
    
    .swiper-button-disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .d-slider1 {
        padding: 10px 0;
        overflow: hidden;
        position: relative;
        margin-bottom: 0 !important; 
        padding-bottom: 0 !important; 
   }
    
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .progress-widget {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }
    
    .progress-detail {
        margin-left: 15px; 
    }
    
    .content-inner {
        padding: 1.5rem;
    }

    .progress-detail p {
        font-size: 16px; 
        margin-bottom: 4px;
        color: #6c757d;
    }
    
    .progress-detail h4 {
        font-size: 20px;
        margin: 0;
        font-weight: 600;
    }
</style>

<div class="conatiner-fluid content-inner mt-n5 py-0">
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="row row-cols-1">
            <div class="overflow-hidden d-slider1 swiper-container">
                <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                    {{-- Card 1: Total Sales --}}
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div id="circle-progress-01" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="90" data-type="percent">
                                    <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                    </svg>
                                    {{-- SVG UNTUK PROGRESS CIRCLE --}}
                                    <svg class="circle-progress-svg" version="1.1" width="100" height="100" viewBox="0 0 100 100">
                                        <circle class="circle-progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                        <path d="M 50 4 A 46 46 0 1 1 22.96 12.78" class="circle-progress-value" fill="none" stroke="#00E699" stroke-width="8"></path>
                                        <text class="circle-progress-text" x="50" y="50" font-size="16px" font-family="Arial, sans-serif" text-anchor="middle" fill="#999" dy="0.4em">90%</text>
                                    </svg>
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Total Sales</p>
                                    <h4 class="counter">Rp{{ number_format($totalSales, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>
                    
                    {{-- Card 2: Total Profit --}}
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div id="circle-progress-02" class="text-center circle-progress-01 circle-progress circle-progress-info" data-min-value="0" data-max-value="100" data-value="80" data-type="percent">
                                    <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                    </svg>
                                    {{-- SVG UNTUK PROGRESS CIRCLE --}}
                                    <svg class="circle-progress-svg" version="1.1" width="100" height="100" viewBox="0 0 100 100">
                                        <circle class="circle-progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                        <path d="M 50 4 A 46 46 0 1 1 6.25 35.78" class="circle-progress-value" fill="none" stroke="#00E699" stroke-width="8"></path>
                                        <text class="circle-progress-text" x="50" y="50" font-size="16px" font-family="Arial, sans-serif" text-anchor="middle" fill="#999" dy="0.4em">80%</text>
                                    </svg>
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Total Profit</p>
                                    <h4 class="counter">Rp{{ number_format($totalProfit, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Card 3: Total Cost --}}
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div id="circle-progress-03" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="70" data-type="percent">
                                    <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                    </svg>
                                    {{-- SVG UNTUK PROGRESS CIRCLE --}}
                                    <svg class="circle-progress-svg" version="1.1" width="100" height="100" viewBox="0 0 100 100">
                                        <circle class="circle-progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                        <path d="M 50 4 A 46 46 0 1 1 6.25 64.21" class="circle-progress-value" fill="none" stroke="#00E699" stroke-width="8"></path>
                                        <text class="circle-progress-text" x="50" y="50" font-size="16px" font-family="Arial, sans-serif" text-anchor="middle" fill="#999" dy="0.4em">70%</text>
                                    </svg>
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Total Cost</p>
                                    <h4 class="counter">Rp{{ number_format($totalPurchases, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Card 4: Net Income --}}
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1000">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div id="circle-progress-05" class="text-center circle-progress-01 circle-progress circle-progress-primary" data-min-value="0" data-max-value="100" data-value="50" data-type="percent">
                                    <svg class="card-slie-arrow icon-24" width="24px" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                    </svg>
                                    {{-- SVG UNTUK PROGRESS CIRCLE --}}
                                    <svg class="circle-progress-svg" version="1.1" width="100" height="100" viewBox="0 0 100 100">
                                        <circle class="circle-progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                        <path d="M 50 4 A 46 46 0 0 1 50 96" class="circle-progress-value" fill="none" stroke="#00E699" stroke-width="8"></path>
                                        <text class="circle-progress-text" x="50" y="50" font-size="16px" font-family="Arial, sans-serif" text-anchor="middle" fill="#999" dy="0.4em">50%</text>
                                    </svg>
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Net Income</p>
                                    <h4 class="counter">Rp{{ number_format($netIncome, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Card 5: Today's Sales --}}
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1100">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div id="circle-progress-06" class="text-center circle-progress-01 circle-progress circle-progress-info" data-min-value="0" data-max-value="100" data-value="40" data-type="percent">
                                    <svg class="card-slie-arrow icon-24" width="24" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                    </svg>
                                    {{-- SVG UNTUK PROGRESS CIRCLE --}}
                                    <svg class="circle-progress-svg" version="1.1" width="100" height="100" viewBox="0 0 100 100">
                                        <circle class="circle-progress-circle" cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                        <path d="M 50 4 A 46 46 0 0 1 77.03 87.21" class="circle-progress-value" fill="none" stroke="#00E699" stroke-width="8"></path>
                                        <text class="circle-progress-text" x="50" y="50" font-size="16px" font-family="Arial, sans-serif" text-anchor="middle" fill="#999" dy="0.4em">40%</text>
                                    </svg>
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Today</p>
                                    <h4 class="counter">Rp{{ number_format($todaySales, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                {{-- Tombol Navigasi Slider --}}
                <div class="swiper-button swiper-button-next"></div>
                <div class="swiper-button swiper-button-prev"></div>
            </div>
        </div>
    </div>  

    <div class="col-md-12 col-lg-8">
      <div class="row">
         <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="800">
               <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                  <div class="header-title">
                     <h4 class="card-title">Rp{{ number_format($monthSales, 0, ',', '.') }}</h4>
                     <p class="mb-0">Gross Sales This Month</p>          
                  </div>
                  <div class="d-flex align-items-center align-self-center">
                     <div class="d-flex align-items-center text-primary">
                        <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor">
                           <g>
                              <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                           </g>
                        </svg>
                        <div class="ms-2">
                           <span class="text-gray">Sales</span>
                        </div>
                     </div>
                     <div class="d-flex align-items-center ms-3 text-info">
                        <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor">
                           <g>
                              <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                           </g>
                        </svg>
                        <div class="ms-2">
                           <span class="text-gray">Cost</span>
                        </div>
                     </div>
                  </div>
                  <div class="dropdown">
                     <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton22" data-bs-toggle="dropdown" aria-expanded="false">
                     This Week
                     </a>
                     <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton22">
                        <li><a class="dropdown-item" href="#">This Week</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                     </ul>
                  </div>
               </div>
               <div class="card-body">
                  <div id="d-main" class="d-main">
                      <canvas id="salesChart" height="100"></canvas>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-12 col-lg-12">
            <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600">
               <div class="flex-wrap card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="mb-2 card-title">Recent Orders</h4>
                     <p class="mb-0">
                        <svg class="me-2 text-primary icon-24" width="24" viewBox="0 0 24 24">
                           <path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                        </svg>
                        {{ $recentOrders->count() }} recent orders
                     </p>           
                  </div>
               </div>
               <div class="p-0 card-body">
                  <div class="mt-4 table-responsive">
                     <table id="basic-table" class="table mb-0 table-striped" role="grid">
                        <thead>
                           <tr>
                              <th>ORDER #</th>
                              <th>CUSTOMER</th>
                              <th>DATE</th>
                              <th>AMOUNT</th>
                              <th>STATUS</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($recentOrders as $order)
                           <tr>
                              <td>
                                 <div class="d-flex align-items-center">
                                    <span class="badge bg-primary">{{ $order->order_number }}</span>
                                 </div>
                              </td>
                              <td>{{ $order->customer->name ?? 'Guest' }}</td>
                              <td>{{ $order->order_date->format('d M Y') }}</td>
                              <td>Rp{{ number_format($order->total_cost, 0, ',', '.') }}</td>
                              <td>
                                 <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                     {{ ucfirst($order->status) }}
                                 </span>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-12 col-lg-4">
      <div class="row">
         <div class="col-md-12 col-lg-12">
            <div class="card" data-aos="fade-up" data-aos-delay="500">
               <div class="text-center card-body d-flex justify-content-around">
                  <div>
                     <h2 class="mb-2">{{ $newCustomersThisMonth }}</h2>
                     <p class="mb-0 text-gray">New Customers</p>
                  </div>
                  <hr class="hr-vertial">
                  <div>
                     <h2 class="mb-2">{{ $totalSuppliers }}</h2>
                     <p class="mb-0 text-gray">Suppliers</p>
                  </div>
               </div>
            </div> 
         </div>
         <div class="col-md-12 col-lg-12">
            <div class="card" data-aos="fade-up" data-aos-delay="600">
               <div class="flex-wrap card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="mb-2 card-title">Activity overview</h4>
                     <p class="mb-0">
                        <svg class="me-2 icon-24" width="24" height="24" viewBox="0 0 24 24">
                           <path fill="#17904b" d="M13,20H11V8L5.5,13.5L4.08,12.08L12,4.16L19.92,12.08L18.5,13.5L13,8V20Z" />
                        </svg>
                        Recent activities
                     </p>
                  </div>
               </div>
               <div class="card-body">
                  @foreach($recentLogHistories as $log)
                  <div class="mb-2 d-flex profile-media align-items-top">
                     <div class="mt-1 profile-dots-pills border-primary"></div>
                     <div class="ms-4">
                        <h6 class="mb-1">{{ $log->action }} on {{ $log->table_name }}</h6>
                        <span class="mb-0">{{ $log->created_at->format('d M Y, H:i') }}</span>
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
         </div>
      </div>
   </div>   
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="{{ asset('js/dashboard.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swiper = new Swiper('.d-slider1', {
            slidesPerView: 'auto',           
            spaceBetween: 24,
         
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        AOS.init({
            duration: 800,
            once: true 
        });
    });
</script>
@endsection