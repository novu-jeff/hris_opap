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

                <a
                    href="{{ route('ess.offset-credits.create') }}"
                    class="btn btn-success px-5 py-3">

                    <i class="fa-solid fa-plus"></i>

                    Add Credits

                </a>

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



