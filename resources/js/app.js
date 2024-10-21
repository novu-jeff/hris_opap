import './bootstrap';
import {post, put, _delete} from './actions';
import './livewire';
import { 
    reinitializeDataTable, 
    copy_link, 
    ckeditor, 
}
from './helpers';

window.post = post;
window.put = put
window._delete = _delete;
window.ckeditor = ckeditor;
window.copy_link = copy_link;
window.reinitializeDataTable = reinitializeDataTable;

reinitializeDataTable();

$(document).on('livewire:navigated', function() {
    reinitializeDataTable();
})

$(document).on('livewire:poll', function() {
    reinitializeDataTable();
})

