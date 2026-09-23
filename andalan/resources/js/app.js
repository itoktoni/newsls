'use strict';

(function ($) {

    var wind_ = $(window),
        body_ = $('body');

    // toastr.options = {
    //     timeOut: shared('timer'),
    //     progressBar: true,
    //     showDuration: 200,
    //     hideDuration: 200
    // };

    // flatpickr(".datejs", {
    //     altInput: true,
    //     defaultDate : 'today',
    //     altFormat: "j F Y",
    //     dateFormat: "Y-m-d"
    // });

    // flatpickr(".datetime", {
    //     enableTime: true,
    //     altInput: true,
    //     defaultDate : 'today',
    //     altFormat: "j M Y H:i",
    //     dateFormat: "Y-m-d H:i"
    // });

    $('.table').cardtable();
    $(".search").selectize({
        allowEmptyOption:true,
        create: false,
        plugins: ["remove_button"],
    });

    $(".tag").selectize({
        allowEmptyOption:false,
        create: false,
        plugins: ["remove_button"],
    });

    $(document).on('click', '.btn-check-m, .btn-check-d', function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $(document).on('click', '[data-toggle="fullscreen"]', function () {
        $(this).toggleClass('active-fullscreen');
        if (document.fullscreenEnabled) {
            if ($(this).hasClass("active-fullscreen")) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        } else {
            alert("Your browser does not support fullscreen.");
        }
        return false;
    });

    $(document).on('click', '.overlay', function () {
        $.removeOverlay();
        if (body_.hasClass('hidden-navigation')) {
            $('.navigation .navigation-menu-body').remove();
        }
        body_.removeClass('navigation-show');
    });

    /*------------- create/remove overlay -------------*/
    $.createOverlay = function () {
        if ($('.overlay').length < 1) {
            body_.addClass('no-scroll').append('<div class="overlay"></div>');
            $('.overlay').addClass('show');
        }
    };

    $.removeOverlay = function () {
        body_.removeClass('no-scroll');
        $('.overlay').remove();
    };
    /*------------- create/remove overlay -------------*/

    $('[data-backround-image]').each(function (e) {
        $(this).css("background", 'url(' + $(this).data('backround-image') + ')');
    });

    $(document).on('click', '[data-nav-target]', function (e) {
        e.preventDefault();
        var $this = $(this),
            target = $this.data('nav-target');
        if (body_.hasClass('navigation-toggle-one')) {
            body_.addClass('navigation-show');
        }
        $('.navigation .navigation-menu-body .navigation-menu-group > div').removeClass('open');
        $('.navigation .navigation-menu-body .navigation-menu-group ' + target).addClass('open');
        $('[data-nav-target]').removeClass('active');
        $this.addClass('active');
        $this.tooltip('hide');
    });

    $(document).on('click', '.navigation-toggler a', function () {
        if (wind_.width() < 1200) {
            $.createOverlay();
            body_.addClass('navigation-show');
        } else {
            if (!body_.hasClass('navigation-toggle-one') && !body_.hasClass('navigation-toggle-two')) {
                body_.addClass('navigation-toggle-one');
            } else if (body_.hasClass('navigation-toggle-one') && !body_.hasClass('navigation-toggle-two')) {
                body_.addClass('navigation-toggle-two');
                body_.removeClass('navigation-toggle-one');
            } else if (!body_.hasClass('navigation-toggle-one') && body_.hasClass('navigation-toggle-two')) {
                body_.removeClass('navigation-toggle-two');
                body_.removeClass('navigation-toggle-one');
            }
        }
        return false;
    });

    $(document).on('click', '.header-toggler a', function () {
        $('.header ul.navbar-nav').toggleClass('open');
        return false;
    });

    $(document).on('click', '*', function (e) {
        if (!$(e.target).is($('.navigation, .navigation *, .navigation-toggler *')) && body_.hasClass('navigation-toggle-one')) {
            body_.removeClass('navigation-show');
        }
    });

    $(document).on('click', '*', function (e) {
        if (!$(e.target).is('.header ul.navbar-nav, .header ul.navbar-nav *, .header-toggler, .header-toggler *')) {
            $('.header ul.navbar-nav').removeClass('open');
        }
    });

    /*------------- header search -------------*/
    $(document).on('click', '[data-toggle="search"], [data-toggle="search"] *', function () {
        $('.header .header-body .header-search').show().find('.form-control').focus();
        return false;
    });

    $(document).on('click', '.close-header-search, .close-header-search svg', function () {
        $('.header .header-body .header-search').hide();
        return false;
    });

    $(document).on('click', '*', function (e) {
        if (!$(e.target).is($('.header, .header *, [data-toggle="search"], [data-toggle="search"] *'))) {
            $('.header .header-body .header-search').hide();
        }
    });
    /*------------- header search -------------*/

    /*------------- custom accordion -------------*/
    $(document).on('click', '.accordion.custom-accordion .accordion-row a.accordion-header', function () {
        var $this = $(this);
        $this.closest('.accordion.custom-accordion').find('.accordion-row').not($this.parent()).removeClass('open');
        $this.parent('.accordion-row').toggleClass('open');
        return false;
    });
    /*------------- custom accordion -------------*/

    /*------------- responsive table dropdown -------------*/
    var dropdownMenu,
        table_responsive = $('.table-responsive');

    table_responsive.on('show.bs.dropdown', function (e) {
        dropdownMenu = $(e.target).find('.dropdown-menu');
        body_.append(dropdownMenu.detach());
        var eOffset = $(e.target).offset();
        dropdownMenu.css({
            'display': 'block',
            'top': eOffset.top + $(e.target).outerHeight(),
            'left': eOffset.left,
            'width': '184px',
            'font-size': '14px'
        });
        dropdownMenu.addClass("mobPosDropdown");
    });

    table_responsive.on('hide.bs.dropdown', function (e) {
        $(e.target).append(dropdownMenu.detach());
        dropdownMenu.hide();
    });
    /*------------- responsive table dropdown -------------*/

    /*------------- chat -------------*/
    $(document).on('click', '.chat-app-wrapper .btn-chat-sidebar-open', function () {
        $('.chat-app-wrapper .chat-sidebar').addClass('chat-sidebar-opened');
        return false;
    });

    $(document).on('click', '*', function (e) {
        if (!$(e.target).is('.chat-app-wrapper .chat-sidebar, .chat-app-wrapper .chat-sidebar *, .chat-app-wrapper .btn-chat-sidebar-open, .chat-app-wrapper .btn-chat-sidebar-open *')) {
            $('.chat-app-wrapper .chat-sidebar').removeClass('chat-sidebar-opened');
        }
    });
    /*------------- chat -------------*/

    /*------------- aside menu toggle -------------*/
    $(document).on('click', '.navigation ul li a', function () {
        var $this = $(this);
        if ($this.next('ul').length) {
            var sub_menu_arrow = $this.find('.sub-menu-arrow');
            sub_menu_arrow.toggleClass('rotate-in');
            $this.next('ul').toggle(200);
            // $this.parent('li').siblings().find('ul').not($this.parent('li').find('ul')).slideUp(200);
            $this.next('ul').find('li ul').slideUp(200);
            $this.next('ul').find('li>a').find('.sub-menu-arrow').removeClass('fa-minus').addClass('fa-chevron-down');
            $this.next('ul').find('li>a').find('.sub-menu-arrow').removeClass('rotate-in');
            // $this.parent('li').siblings().not($this.parent('li').find('ul')).find('>a').find('.sub-menu-arrow').removeClass('fa-minus').addClass('fa-chevron-down');
            // $this.parent('li').siblings().not($this.parent('li').find('ul')).find('>a').find('.sub-menu-arrow').removeClass('rotate-in');
            if (sub_menu_arrow.hasClass('rotate-in')) {
                setTimeout(function () {
                    sub_menu_arrow.removeClass('fa-chevron-down').addClass('fa-minus');
                }, 200);
            } else {
                sub_menu_arrow.removeClass('fa-minus').addClass('fa-chevron-down');
            }
            if (!body_.hasClass('horizontal-side-menu') && wind_.width() >= 1200) {
                setTimeout(function (e) {
                    $('.navigation .navigation-menu-body').resize();
                }, 300);
            }
            return false;
        }
    });

    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget),
            recipient = button.data('whatever'),
            modal = $(this);

        modal.find('.modal-title').text('New message to ' + recipient);
        modal.find('.modal-body input').val(recipient);
    });

    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });

    $('[data-toggle="popover"]').popover();

    $(document).on('click', '.filter', function () {
        $(".collapse").collapse('toggle'); // toggle collapse
    });

    $(document).on('click', '.navigation-menu-group ul li .link', function () {
        $('.navigation-menu-group ul li .link.active').removeClass('active');
        $(this).addClass('active'); // toggle collapse
    });

})(jQuery);

document.body.addEventListener('htmx:configRequest', (event) => {
    event.detail.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
});

addEventListener("htmx:beforeRequest", (event) => {
    NProgress.start();
    NProgress.set(0.9);
});

addEventListener("htmx:afterRequest", (event) => {
    if (event.detail.successful) {
        NProgress.done();
        NProgress.remove();
    }

    if (event.detail.xhr.responseURL.indexOf('login') != -1) {
        window.location = event.detail.xhr.responseURL;
    }
});

addEventListener("htmx:responseError", (event) => {
    NProgress.done();
    NProgress.remove();

    var errdiv = document.getElementById("errormessages");
        errdiv.innerHTML = `
    <div id="errormessagealert" class="container mt-3 mb-3">
        <h4>Request Error: ` + event.detail.xhr.status + `
            <span style="float:right;"><a class="cursor" onclick="errormessagealert_off()">Close</a></span>
        </h4>
        <code>` + event.detail.xhr.responseText + `</code>
    </div>`;

        document.getElementById("errormessagealert").style.display = "block";

    if($('#errormessagealert').length == 0){
        window.location = event.detail.pathInfo['requestPath'];
    }

});

function errormessagealert_off() {
    document.getElementById("errormessagealert").style.display = "none";
}

addEventListener("htmx:afterSettle", (event) => {

    $('.table').cardtable();
    $(".search").selectize({
        allowEmptyOption: true,
        create: false,
        plugins: ["remove_button"],
    });
    $(".tag").selectize({
        allowEmptyOption: false,
        create: false,
        plugins: ["remove_button"],
    });
});

document.onreadystatechange = function() {
    if (document.readyState !== "complete") {
        NProgress.start();
        NProgress.set(0.9);
    } else {
        NProgress.done();
        NProgress.remove();
    }
    NProgress.done();
    NProgress.remove();
};