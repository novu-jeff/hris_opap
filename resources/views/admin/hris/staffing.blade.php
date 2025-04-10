@extends('layouts.admin', [
    'title' => 'HRIS | Staffing Records'
])

@section('content')
<div class="container pb-5">
    <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
        <div class="section-title">
            <h1>Employee Records</h1>
            <p></p>
        </div>
    </div>
    <div class="mt-3">
        <iframe src="https://docs.google.com/spreadsheets/d/e/2PACX-1vS6zd4G00InrzT0frNY2U5BR770JzPiTZJJQE4z1PA8iNGrLxIUCTZM1wPNgqi7CA/pubhtml?widget=true&amp;headers=false"></iframe>
        
    <style>
        iframe {
            width: 100%;
            height: 100vh;
        }
    </style>
</div>
@endsection


