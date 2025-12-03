@extends('layouts.app')

@section('content')
    <x-block title="Deals" >
        <table class="table table-bordered table-vcenter" id="dealsTbl">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Deal owner') }}</th>
                <th>{{ __('Creation Date') }}</th>
                <th>{{ __('Close Date') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Deals') }}');
        addBreadcrumbItem('{{ __('Deals') }}', null);

        let tableDeals = $('#dealsTbl').DataTable({
            ajax: {
                url: "{{ route('client.deal.datatable') }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                //data: function (d) {
                //d.listAllCustomers = 1
                //}
            },
            serverSide: true,
            pageLength: 20,
            responsive: false,
            paging: true,
            lengthChange: false,
            searching: false,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search Deals",
                emptyTable: "<div class='empty-table-block'><div class='empty-table'><span class='empty-table-text'>There is nothing here</span><img width='29' height='29' src='{{asset('images/emoji-icons/neutral.svg')}}' alt='' class='empty-table-img'></div></div>"
            },
            columns: [
                {data: "id", visible: false, width: '100px'},
                {data: 'name'},
                {data: 'owner_name'},
                {data: 'createdate'},
                {data: 'closedate'},
                {data: 'amount'},
                {data: 'status'},
            ],
            order: [[1, "desc"]],
            initComplete: function () {
                new $.fn.dataTable.Buttons(tableDeals, [
                    { text:'<i class="fa fa-fw fa-plus"></i> {{ __('Sync') }}',
                      className: 'el-button el-button--info',
                      action: function () {
                          $.get( "{{ route('client.deal.sync') }}", function( data ) {
                              alert( "Load was performed." );
                              $("#dealsTbl").DataTable().ajax.reload()
                          });
                      }
                    }
                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }

                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }
                ]);

                tableDeals.buttons().container().appendTo( $('.col-md-6:eq(0)', tableDeals.table().container() ) );

                $('.buttons-create').each(function() {
                    $(this).removeClass('btn-secondary').addClass('btn-primary')
                });

            }
        });

        $('.empty-table').css('height', '60px');

    </script>
@endsection
