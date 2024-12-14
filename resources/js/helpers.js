export function copy_link() {
    $('.copy-link').on('click', function(e) {
        e.preventDefault();
        const link = $(this).data('target');

        const tempInput = document.createElement('input');
        tempInput.value = link;
        document.body.appendChild(tempInput);

        tempInput.select();
        tempInput.setSelectionRange(0, 99999); 
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        Swal.fire({
            icon: "success",
            title: 'Job Link Copied',
            html: 'You can now paste or send the link you copied!',  
            confirmButtonText: 'GOT IT',   
            confirmButtonColor: '#143953', 
            cancelButtonColor: '#d33',      
            reverseButtons: true,  
        });  
    });
}

export function reinitializeDataTable() {
    if ($.fn.DataTable.isDataTable('.data-tables')) {
        $('.data-tables').DataTable().destroy();
    }
    $('.data-tables').DataTable({
        scrollX: true,
        pageLength: 20 
    });
}

export function resetErrorsAndFields() {
    $('input[type="text"], input[type="number"], textarea').val('');
    $('select').prop('selectedIndex', 0);
    $('.error-field span').text('');
    $('.ck-content').empty();
}

export function ckeditor(isReadOnly = false) {

    let editEditor;

    // Check if an existing instance of CKEditor exists and destroy it
    const ckeditorElement = document.querySelector('#ckeditor');
    if (ckeditorElement && ckeditorElement.ckeditorInstance) {
        ckeditorElement.ckeditorInstance.destroy()
        .then(() => {
            console.log('Existing CKEditor instance destroyed.');
        })
        .catch(error => {
            console.error('Error destroying the existing editor:', error);
        });
    }

    // Create a new instance of CKEditor
    ClassicEditor
    .create(ckeditorElement, {
        toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote'],
        height: '500px'
    })
    .then(editor => {
        editEditor = editor;

        // Store the editor instance on the DOM element for future reference
        ckeditorElement.ckeditorInstance = editor;

        editor.model.document.on('change:data', () => {
            Livewire.dispatch('ckeditor', [editor.getData()]);
        });
        if (isReadOnly) {
            editor.isReadOnly = true;
        }
    })
    .catch(error => {
        console.error('Error creating the CKEditor instance:', error);
    });
}


export function removeRowDT(id) {
    var table = $('table').DataTable();
    var row = table.row($('tr[data-id="' + id + '"]'));
    if (row.node()) {
        row.remove().draw(false); 
    } else {
        console.log('Row not found!');
    }
}

export function reloadDT() {
    location.reload();
}

export function hideModal() {
    // $('body').css('overflow-y', 'scroll');
    $('.modal').removeClass('show').css('display', 'none');
    $('.modal-backdrop').remove(); 
    $('.modal-backdrop').css({
        'position': 'relative',
        'height': '100%'
    });
}

export function formatTime(date) {
    if (!date) return '';  // If no date is provided, return an empty string

    // Check if the date is already in a string format (e.g., "06:58 AM")
    if (typeof date === 'string' && /\d{2}:\d{2} (AM|PM)/.test(date)) {
        return date;  // Return the string as is if it's already in the correct format
    }

    // If it's a Date object, format it
    if (date instanceof Date) {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    // In case the date is neither a string nor a Date object, return an empty string
    return '';
}