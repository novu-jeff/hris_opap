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

document.addEventListener('livewire:load', function () {
    $('html, body').animate({ scrollTop: 0 });
});

document.addEventListener('livewire:update', function () {
    $('html, body').animate({ scrollTop: 0 });
});

$(function() {
    $('.select-2').select2();
    $('.select-2').on('change', function() {
        var field = $(this).attr('id');
        var value = $(this).val(); 
        Livewire.dispatch('populateField', [field, value]);
    });
});
