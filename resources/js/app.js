import './bootstrap';
import {post, put, _delete} from './actions';
import './livewire';
import { initTimePicker } from './timepicker.js';
import { 
    reinitializeDataTable, 
    copy_link, 
    ckeditor, 
    formatTime,
    convertToHoursAndMinutes,
    getGPSCoordinates,
    setupMap
}
from './helpers';

window.post = post;
window.put = put
window._delete = _delete;
window.ckeditor = ckeditor;
window.copy_link = copy_link;
window.reinitializeDataTable = reinitializeDataTable;
window.formatTime = formatTime;
window.convertToHoursAndMinutes = convertToHoursAndMinutes;
window.getGPSCoordinates = getGPSCoordinates;
window.setupMap = setupMap;

$(function() {

    initTimePicker();

    Fancybox.bind('[data-fancybox]', {
        
    });  

    reinitializeDataTable();

    $(document).on('livewire:navigated', function() {
        reinitializeDataTable();
    })

    $(document).on('livewire:poll', function() {
        reinitializeDataTable();
    })

    $(window).scroll(function() {
        $(this).scrollTop() > 150 ?
            $('.scroll-top').addClass('show')
        :   $('.scroll-top').removeClass('show');
    });

    $('.scroll-top').on('click', function() {
        $('html, body').animate({ scrollTop: 0 });
    })

    if ($.fn.select2) {
        $('.select-2').select2();
        $('.select-2').on('change', function() {
            var field = $(this).attr('id');
            var value = $(this).val(); 
            Livewire.dispatch('populateField', [field, value]);
        });
    }

    if($('.sidebar').hasClass('active')) {
        toggleSidebar();
    }

    $('.sidebar .close-icon, .hamburger-wrapper').on('click', function() {
        toggleSidebar();
    });

    function toggleSidebar() {
        $('.sidebar').toggleClass('active');
        setTimeout(() => {
            $('.overlay').css('background-color', $('.sidebar').hasClass('active') ? 'rgba(0, 0, 0, 0.5)' : '');
        }, 300);
    }

    $('.submenu').hide();

    $('.toggle-link').on('click', function (e) {
        e.preventDefault();
        const submenu = $(this).siblings('.submenu');

        $(this).closest('.list-item').siblings().find('.submenu').slideUp();

        submenu.find('.submenu').slideUp();

        submenu.slideToggle();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.list-item').length) {
            $('.submenu').slideUp();
        }
    });

    window.addEventListener('wheel', function (e) {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
        }
    }, { passive: false });

    window.addEventListener('keydown', function (e) {
        const zoomKeys = ['+', '-', '=', '0'];
        if ((e.ctrlKey || e.metaKey) && zoomKeys.includes(e.key)) {
            e.preventDefault();
        }
    });

    document.addEventListener('gesturestart', function (e) {
        e.preventDefault();
    });

});
