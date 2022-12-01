@extends('kctadmin::layouts.email')
@section('content')
<table role="presentation" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">

                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table role="presentation">
                                <tr>
                                </tr>
                                        <tr>
                                            <td>{{ __("usermanagement::messages.pwd_reset_desc") }}</td>
                                        </tr>
                                        <tr>
                                            <td>Please <a href="{{ $link }}">{{'Click Here'}}</a> for password reset.</td>
                                        </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- END CENTERED WHITE CONTAINER -->


            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
@endsection
