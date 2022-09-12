@extends('layouts.backend.app')
@section('page_vendor_css')
<link rel="stylesheet" type="text/css" href="{{asset('public/assets/backend/app-assets/css/pages/app-user.min.css')}}">
@endsection
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        
    </div>
    <div class="content-body">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Avaliku saidi URL</h4>
                <p class="card-text" id="public_url">{{ env('APP_URL') }}</p>
                <button type="button" class="btn btn-primary waves-effect waves-float waves-light" onclick="copyToClipboard('#public_url')">Kopeeri</button>
            </div>
        </div>

        <div class="card mb-4 app-user-view">
            <div class="card-body">
                <h4 class="card-title">Kasutajateave</h4>
                <div class="col-xl-12 col-lg-12 mt-2 mt-xl-0">
                    <div class="user-info-wrapper">
                        <div class="d-flex flex-wrap">
                            <div class="user-info-title">
                                <i data-feather="book" class="mr-1"></i>
                                <span class="card-text user-info-title font-weight-bold mb-0">ettevõtte nimi</span>
                            </div>
                            <p class="card-text mb-0">{{ Auth::user()->company }}</p>
                        </div>
                        <div class="d-flex flex-wrap my-50">
                            <div class="user-info-title">
                                <i data-feather="user" class="mr-1"></i>
                                <span class="card-text user-info-title font-weight-bold mb-0">Kasutajanimi</span>
                            </div>
                            <p class="card-text mb-0">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="d-flex flex-wrap my-50">
                            <div class="user-info-title">
                                <i data-feather="mail" class="mr-1"></i>
                                <span class="card-text user-info-title font-weight-bold mb-0">Email</span>
                            </div>
                            <p class="card-text mb-0">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="d-flex flex-wrap my-50">
                            <div class="user-info-title">
                                <i data-feather="star" class="mr-1"></i>
                                <span class="card-text user-info-title font-weight-bold mb-0">Roll</span>
                            </div>
                            <p class="card-text mb-0">{{ Auth::user()->role == 'A' || Auth::user()->role == 'SA' ? "Admin" : "Client" }}</p>
                        </div>
                    </div>
                  </div>
                </div>                
            </div>
        </div>

        <div class="card mb-4 app-user-view">
            <div class="card-body">
                <h4 class="card-title">Uue broneeringu teavituse email</h4>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 mt-2 mt-xl-0">
                        <div class="form-group">
                            <input type="text" class="form-control" id="notification_email" value="{{ Auth::user()->notification_email }}">
                        </div>
                    </div>                
                    <div class="col-xl-8 col-lg-8 mt-2 mt-xl-0">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light mr-2" onclick="saveNotificationEmail()">Salvesta</button>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input status" id="need_notification"
                            @if (Auth::user()->need_notification)
                              checked
                            @endif onchange="setNeedification()"/>
                            <label class="custom-control-label" for="need_notification">Lülita teavitus sisse</label>
                        </div>
                    </div>
                    <input type="hidden" value={{ Auth::user()->id }} id="userid">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(element) {
        console.log($(element).text())
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Your Public Site Url Copied")
    }

    function saveNotificationEmail() {
        $.ajax({
        type: "post",
        url: appUrl + '/admin/seaded/saveNotificationEmail',
        data: {id: $("#userid").val(), email: $("#notification_email").val()},
        success: (res) => {
            Swal.fire({
                icon: 'success',
                title: 'Save',
                text: 'Successfully Done!',
                customClass: {
                confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        },
        error: (err) => {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            customClass: {
            confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
        }
      })
    }

    function setNeedification() {
        $.ajax({
        type: "post",
        url: appUrl + '/admin/seaded/setNeedification',
        data: {id: $("#userid").val(), needNotification: $("#need_notification").prop("checked")},
        success: (res) => {
            Swal.fire({
                icon: 'success',
                title: 'Save',
                text: 'Successfully Done!',
                customClass: {
                confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        },
        error: (err) => {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            customClass: {
            confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
        }
      })
    }
</script>

@endsection

@section('page_vendor_js')
<script src="{{asset('public/assets/backend/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
@endsection