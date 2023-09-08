@extends('layout')
@section('title', 'Top Distributor')
@section('content')


<div class="container">
    <div class="row mt-5">
        <h1>Top Selling</h1>
    </div>

    <div class="row">
        <table class="table  table-hover">
        <thead>
            <tr>
            <th>Top</th>
            <th>Distributor Name</th>
            <th>Total Sale</th>

            </tr>
        </thead>
        <tbody>
            @php
                $count = 1;
                $index = 0;
                $total = count($users);
            @endphp
            @forelse ($users as $topSelling)
            <tr>

                <th>
                    @if($index == 0)
                    {{  $count++  }}
                    @elseif($users[$total - 1 == $index ? $index: $index + 1]?->total_sales == $topSelling->total_sales)
                    {{  $count  }}
                    @else
                    {{  $count++  }}
                    @endif
                </th>
                <th>{{ $topSelling->full_name }}</th>

                <th>{{ $topSelling->total_sales }}</th>

            </tr>
            @php $index += 1; @endphp
            @empty
            
            @endforelse


        </tbody>
        </table>

    </div>
    <div class="row mb-5">
       
    </div>
</div>



@endsection

