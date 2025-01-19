import './bootstrap';
import {post, put, _delete} from './actions';
import './livewire';
import { 
    reinitializeDataTable, 
    copy_link, 
    ckeditor, 
    formatTime,
    convertToHoursAndMinutes
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


$(function() {

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

    // Handle clicks on toggle links
    $('.toggle-link').on('click', function (e) {
        e.preventDefault();
        const submenu = $(this).siblings('.submenu');

        // Close other submenus on the same level
        $(this).closest('.list-item').siblings().find('.submenu').slideUp();

        // Close nested submenus inside the current submenu
        submenu.find('.submenu').slideUp();

        // Toggle the current submenu
        submenu.slideToggle();
    });

    // Ensure submenus are closed when clicking outside the menu
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.list-item').length) {
            $('.submenu').slideUp();
        }
    });

});
