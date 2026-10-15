<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bordash - Admin Dashboard Template</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('assets/media/image/favicon.png') }}"/>

    <!-- Plugin styles -->
    <link rel="stylesheet" href="{{ url('vendors/bundle.css') }}" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css">

    <style>
      .tr-custom{
         font-family: 'Helvetica Neue', Helvetica,Arial,sans-serif;
         box-sizing: border-box;
         font-size: 14px;
         margin: 0;
      }
      td{
         font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
         box-sizing: border-box;
         color: #3f5db3;
         font-size: 14px;
         vertical-align: top;
         margin: 0;
         padding: 10px 10px;
      }
    </style>
</head>
<body class="bg-white">

<div class="w-50 m-auto">
    <!-- email template -->
    <table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #eaf0f7; margin: 0;" bgcolor="#eaf0f7">
        <tr class="tr-custom">
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
            <td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;"
                valign="top">
                <div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 50px;">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px dashed #4d79f6;"
                           bgcolor="#fff">
                        <tr class="tr-custom">
                            <td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                <meta itemprop="name" content="Confirm Email" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <tr>
                                        <td><a href="#"><img src="{{ url('assets/media/image/logo.png') }}" alt="image" style="margin-left: auto; margin-right: auto; display:block; margin-bottom: 10px; height: 30px;"></a></td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; color: #004deb; font-size: 24px; font-weight: 700; text-align: center; vertical-align: top; margin: 0; padding: 0 0 10px;"
                                            valign="top">NOTIFICATION MOVEMENT</td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block"  valign="top">
                                          <b>Product : </b>{{ ($product_name ?? '-') }}
                                       </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block"  valign="top">
                                          <b>Old Location : </b>{{ ($location_old ?? '-') }}
                                        </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block"  valign="top">
                                          <b>New Location : </b>{{ ($location_new ?? '-') }}
                                        </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block"  valign="top">
                                          <b>Status : </b>{{ $data->field_status==1 ? 'Approve' : ($data->field_status==2 ? 'Pending' : 'Reject') }}
                                       </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 10px 10px;" valign="top">
                                          <b>Deskripsi : </b>{{ $data->field_description ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 10px 10px;" valign="top">
                                          <b>Reason : </b>{{ $data->field_reason ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 10px 10px;"
                                            valign="top"><a href="http://localhost/admin/transaction/movement/update/{{ $data->field_primary ?? '' }}" itemprop="url" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: block; border-radius: 5px; text-transform: capitalize; background-color: #004deb; margin: 0; border-color: #004deb; border-style: solid; border-width: 10px 20px;">Code : {{ strtoupper($data->field_primary) ?? '' }}</a></td>
                                    </tr>
                                    <tr class="tr-custom">
                                        <td class="content-block" style=" color: #3f5db3; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; padding-top: 5px; vertical-align: top; margin: 0; text-align: right;" valign="top">{{ $data->field_requested_at ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <!-- ./ email template -->
</div>

<!-- Plugin scripts -->
<script src="{{ url('vendors/bundle.js') }}"></script>

<!-- App scripts -->
<script src="{{ url('assets/js/app.min.js') }}"></script>
</body>
</html>