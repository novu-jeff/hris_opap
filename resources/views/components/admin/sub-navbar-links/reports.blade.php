@canany([
    'read dtr',
    'read bir-2316'
])
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-chart-line"></i>
        Reports
    </a>
    <ul class="dropdown-menu">
        @can('read dtr')
            <li><a href="{{ route('reports.dtr') }}" class="dropdown-item">Daily Time Record (DTR)</a></li>
        @endcan
        @if($product == 'private')
            <li><a href="{{ route('reports.bir') }}" class="dropdown-item">BIR</a></li>
            <li><a href="{{ route('reports.philhealth') }}" class="dropdown-item">PhilHeath</a></li>
            <li><a href="{{ route('reports.sss') }}" class="dropdown-item">SSS</a></li>
            <li><a href="{{ route('reports.pagibig') }}" class="dropdown-item">Pagibig</a></li>
            <li><a href="#" class="dropdown-item">Official Business Slip</a></li>
            <li><a href="#" class="dropdown-item">Application for Leave</a></li>
            <li><a href="#" class="dropdown-item">Monetization Form</a></li>
            <li><a href="#" class="dropdown-item">Authority to Render Overtime</a></li>
            <li><a href="#" class="dropdown-item">Statement of Overtime</a></li>
            <li><a href="#" class="dropdown-item">Accomplishment Report</a></li>
            <li><a href="#" class="dropdown-item">Overtime Work Program</a></li>
            <li><a href="#" class="dropdown-item">Certificate of Clearance</a></li>
            <li><a href="#" class="dropdown-item">Certificate of Completion of Service</a></li>
            <li><a href="#" class="dropdown-item">New Employee Onboarding Checklist</a></li>
            <li><a href="#" class="dropdown-item">Courtesy Call</a></li>
            <li><a href="#" class="dropdown-item">Terms Of Reference</a></li>
            <li><a href="#" class="dropdown-item">(TBD)</a></li>
            <li><a href="#" class="dropdown-item">(TBD)</a></li> 
        @endif
    </ul>    
</li>
@endcanany