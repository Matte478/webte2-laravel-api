@component('mail::message')
# API service usage statistics

<style>
    .table {
        margin: 30px 0;
        table-layout: fixed;
        width: 100%;
        text-align: center;
    }
    .table th {
        color: #26547c;
        border-bottom: 1px solid #26547c;
    }
</style>

<table class="table">
    <tr>
        <th>API Service</th>
        <th>Count of API calls</th>
    </tr>
@foreach($statistics as $stat)
    <tr>
        <td>{{ $stat->service['en'] }}</td>
        <td>{{ $stat->count }}</td>
    </tr>
@endforeach
</table>

With regards,<br>
{{ config('app.name') }}
@endcomponent
