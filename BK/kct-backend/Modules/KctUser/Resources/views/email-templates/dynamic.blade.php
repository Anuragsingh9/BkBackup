@extends('kctadmin::layouts.email')
@section('content')
<table style="width:600px">

    <tbody>
    <tr>
        <td> {{ $text_before_link ?? '' }}</td>
    </tr>
    <tr>
        <td>
            <a href="{{ $link?? '#' }}"><strong>{{ $linkLabel ?? 'Click Here' }}</strong></a>
        </td>
    </tr>
    <tr>
        <td> {{ $text_after_link ?? '' }}</td>
    </tr>

    </tbody>

</table>
@endsection

