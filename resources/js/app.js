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


$(function() {
    $('.select-2').select2();
    $('.select-2').on('change', function() {
        var field = $(this).attr('id');
        var value = $(this).val(); 
        Livewire.dispatch('populateField', [field, value]);
    });
});
