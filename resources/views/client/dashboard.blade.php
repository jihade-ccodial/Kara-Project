@extends('layouts.app')

@section('content')
    <livewire:dashboard.dashboard />
    <x-block>
        <div class="row">
            <div class="col warnings">
                <x-dashboard.deals-widget />
            </div>
        </div>
    </x-block>
    <x-deals-grid tableid="modal-deals-grid"></x-deals-grid>
@endsection

@section('scripts')
    <script>
        setPageTitle('Dashboard');
        addBreadcrumbItem('Dashboard', null);
        $("div.content").addClass("dashboard-page");

    </script>
@endsection
