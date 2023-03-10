<x-layout>
    <x-sidenav>
        <div>
            @if (auth()->user()->role == "Admin")
                <a href="/dashboard/export" style="float: right; top:0">
                    <button style="padding: 5px 10px; background-color: #2980B9;
                        border: 1px solid#2980B9;
                        border-radius: 5px;
                        color: white;
                        width: fit-content;">
                        Generate Report
                    </button>
                </a>

                {{-- <a href="/generate/dashboard" style="float: right; top:0">
                    <button style="padding: 5px 10px; background-color: #2980B9;
                        border: 1px solid#2980B9;
                        border-radius: 5px;
                        color: white;
                        width: fit-content;
                        margin-right:10px">
                        Generate PDF
                    </button>
                </a> --}}

                {{-- <button style="padding: 5px 10px; background-color: #2980B9;
                    border: 1px solid#2980B9;
                    border-radius: 5px;
                    color: white;
                    width: 162px;
                    float: right; top:0">
                    Generate Report&nbsp;&nbsp;
                    <i class='bx-fw bx bxs-down-arrow bx-xs'></i>
                </button>

                <div class="">
                    <a href="/dashboard/export">.XSLX</a>
                </div> --}}
            @endif
            <p style="font-size: 22px; font-weight:bold">Dashboard</p> 
        </div>
        <br>
        <hr style="width: 100%; background-color: #C4C4C4; border: 0.1px solid #C4C4C4;
        transform: rotate(0.08deg); ">
        <br>

        <div class="row">
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Student Satisfaction</p>
                    <p class="h4">{{ $studentSatisfaction }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Average Student Rating</p>
                    <p class="h4">{{ $averageRating }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Average Response Time</p>
                    <p class="h4">{{ $averageResponseTime }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Average Times Reopened</p>
                    <p class="h4">{{ $averageReopen }}</p>
                </div>
            </div>
        </div>
        
        <br>

        <div class="row">
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6">
                    <p class="font-weight-bold h4">Total Tickets</p>
                    <p class="h4">{{ $totalTickets }}</p>
                    {{-- <a href="/" class="text-primary h4">View</a> --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6">
                    <p class="font-weight-bold h4">New Tickets</p>
                    <p class="h4">{{ $newTickets }}</p>
                    {{-- <a href="/" class="text-primary h4">View</a> --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6">
                    <p class="font-weight-bold h4">Resolved Tickets</p>
                    <p class="h4">{{ $resolvedTickets }}</p>
                    {{-- <a href="/" class="text-primary h4">View</a> --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Reopened Tickets</p>
                    <p class="h4">{{ $reopenedTickets }}</p>
                    {{-- <a href="/" class="text-primary h4">View</a> --}}
                </div>
            </div>
        </div>

        <br>
        <p class="attribute">This Month</p>
        <hr style="width: 100%; background-color: #d5d5d5; border: 0.1px solid #C4C4C4;
        transform: rotate(0.08deg); ">
        <br>

        <div class="row">
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Request Tickets</p>
                    <p class="h4">{{ $requestThisMonth }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Inquiry Tickets</p>
                    <p class="h4">{{ $inquiryThisMonth }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Concern Tickets</p>
                    <p class="h4">{{ $concernThisMonth }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-body border rounded-10 bg-white mb-3 p-6" >
                    <p class="font-weight-bold h4">Other Tickets</p>
                    <p class="h4">{{ $otherThisMonth }}</p>
                </div>
            </div>
        </div>

        <br>
        <br>
        <div class="row">
            <div class="col-lg-6">
                <h1>{{ $chart1->options['chart_title'] }}</h1>
                {!! $chart1->renderHtml() !!}
            </div>
            <div class="col-lg-6">
                <h1>{{ $chart2->options['chart_title'] }}</h1>
                {!! $chart2->renderHtml() !!}
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-4">
                <h1>{{ $chart3->options['chart_title'] }}</h1>
                {!! $chart3->renderHtml() !!}
            </div>
            <div class="col-lg-4">
                <h1>{{ $chart4->options['chart_title'] }}</h1>
                {!! $chart4->renderHtml() !!}
            </div>
            <div class="col-lg-4">
                <h1>{{ $chart5->options['chart_title'] }}</h1>
                {!! $chart5->renderHtml() !!}
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
    </x-sidenav>
    @{{
    
    {!! $chart1->renderChartJsLibrary() !!}
    {!! $chart1->renderJs() !!}
    {!! $chart2->renderJs() !!}
    {!! $chart3->renderJs() !!}
    {!! $chart4->renderJs() !!}
    {!! $chart5->renderJs() !!}
    
    
    
    
    }}
</x-layout>
