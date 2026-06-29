@extends('layouts.admin',[
    'title'=>$title
])

@section('content')

<div class="main-content flex-grow-1 p-4">

    <div class="container pb-5">

        <div class="mt-5 d-lg-flex justify-content-between">

            <div>

                <h1>{{ $header }}</h1>

                <p>{{ $sub }}</p>

            </div>

            @if($action=='view')

                {{--@can('write offset-credits')--}}

                <div class="d-flex align-items-center">
                    <a
                        href="{{ route('ess.offset-credits.create') }}"
                        class="btn btn-success btn-lg">
                        <i class="fa-solid fa-plus me-2"></i>
                        Add Credits
                    </a>
                </div>

                {{--@endcan--}}

            @endif

        </div>

        @if($action=='view')

            @livewire('admin.ess.offset-credits.index')

        @else

            @livewire('admin.ess.offset-credits.form',[
                'record_id'=>$id ?? null
            ])

        @endif

    </div>

</div>

@endsection



