@once
@push('footer')
<script>

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function showModal(url, size) {
    size = (typeof size == 'undefined' || size == '') ? 'modal-lg' : size;
    var title = $('.page-title').text();
    $.ajax({
        url: url,
        success: function(response) {

            $('#modal-body').html(response);
            $('.modal-title').text(title);
            $('#modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('.modal-dialog').addClass(size);

            $(".flatpickr-calendar").not(':first').remove();
        },
        complete: function() {
            // $('#loader').hide();
        },
        error: function(jqXHR, testStatus, error) {
            toastr.error(error);
        },
        timeout: 8000
    });
}

// Make sure only one backdrop is rendered
$(document).on('show.bs.modal', '.modal', function () {
    if ($(".modal-backdrop").length > 1) {
        $(".modal-backdrop").not(':first').remove();
        $(".flatpickr-calendar").not(':first').remove();
    }
});
// Remove all backdrop on close
$(document).on('hide.bs.modal', '.modal', function () {
    if ($(".modal-backdrop").length > 1) {
        $(".modal-backdrop").remove();
        $(".flatpickr-calendar").remove();
    }
});

$('body').on('click', '.button-update', function(event) {
    event.preventDefault();
    showModal($(this).attr('href'), $(this).attr('size'));
});

$('body').on('click', '.button-create', function(event) {
    event.preventDefault();
    showModal($(this).attr('href'), $(this).attr('size'));
});

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

@endpush
@endonce