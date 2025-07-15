export function initTimePicker() {
  const modal = `
    <div class="modal fade timepicker-modal" tabindex="-1" id="timepickerModal" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
          <div class="modal-header pt-2">
            <h4 class="modal-title text-uppercase fw-bold">CHOOSE TIME</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <div class="d-flex justify-content-center gap-5 align-items-center py-4">
              <div class="d-flex justify-content-center gap-4 align-items-center py-4">
                <!-- Hour Picker -->
                <div class="text-center">
                  <button class="btn btn-sm btn-outline-primary up-hour">▲</button>
                  <input type="number" class="form-control form-control-lg fw-bold text-center hour-display my-4" style="width: 120px; font-size: 40px;" />
                  <button class="btn btn-sm btn-outline-primary down-hour">▼</button>
                </div>

                <div>
                  <h1 class="fw-bold">:</h1>
                </div>

                <!-- Minute Picker -->
                <div class="text-center">
                  <button class="btn btn-sm btn-outline-primary up-minute">▲</button>
                  <input type="number" class="form-control form-control-lg fw-bold text-center minute-display my-4" style="width: 120px; font-size: 40px;" />
                  <button class="btn btn-sm btn-outline-primary down-minute">▼</button>
                </div>
              </div>

              <!-- AM/PM Box -->
              <div style="min-width: 80px;">
                <div class="text-center">
                  <div class="d-flex flex-column gap-2">
                    <div style="cursor: pointer;" class="ampm-box am fw-bold py-3 px-4 border rounded text-center">AM</div>
                    <div style="cursor: pointer;" class="ampm-box pm fw-bold py-3 px-4 border rounded text-center">PM</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-primary done-btn text-uppercase fw-bold mt-2 px-5 py-3">Done</button>
          </div>
        </div>
      </div>
    </div>`;

  if (!$('#timepickerModal').length) {
    $('body').append(modal);
  }

  const $modalEl = document.getElementById('timepickerModal');
  const timepickerModal = new bootstrap.Modal($modalEl);
  let $activeInput = null;

  $('.timepicker').attr('placeholder', 'CLICK TO CHOOSE TIME');

  // Open modal and populate time
  $(document).on('click', '.timepicker', function () {
    $activeInput = $(this);
    const existing = $activeInput.val().trim() || '12:00 AM';
    const [time, meridian] = existing.split(' ');
    const [hour, minute] = time.split(':');

    $('.hour-display').val(parseInt(hour));
    $('.minute-display').val(minute.padStart(2, '0'));

    $('.ampm-box').removeClass('border-primary fw-bold');
    if (meridian === 'PM') {
      $('.ampm-box.pm').addClass('border-primary fw-bold');
    } else {
      $('.ampm-box.am').addClass('border-primary fw-bold');
    }

    timepickerModal.show();
  });

  // Limit to 2 digits
  $(document).on('keyup', '.hour-display, .minute-display', function () {
    let val = $(this).val().replace(/\D/g, '');
    if (val.length > 2) val = val.slice(0, 2);
    $(this).val(val);
  });

  // Hour controls
  $(document).on('click', '.up-hour', function () {
    const $hourDisplay = $('.hour-display');
    let val = parseInt($hourDisplay.val()) || 1;
    $hourDisplay.val(val >= 12 ? '01' : (val + 1).toString().padStart(2, '0'));
  });

  $(document).on('click', '.down-hour', function () {
    const $hourDisplay = $('.hour-display');
    let val = parseInt($hourDisplay.val()) || 1;
    $hourDisplay.val(val <= 1 ? '12' : (val - 1).toString().padStart(2, '0'));
  });

  // Minute controls
  $(document).on('click', '.up-minute', function () {
    const $minuteDisplay = $('.minute-display');
    let val = parseInt($minuteDisplay.val()) || 0;
    $minuteDisplay.val(val >= 59 ? '00' : (val + 1).toString().padStart(2, '0'));
  });

  $(document).on('click', '.down-minute', function () {
    const $minuteDisplay = $('.minute-display');
    let val = parseInt($minuteDisplay.val()) || 0;
    $minuteDisplay.val(val <= 0 ? '59' : (val - 1).toString().padStart(2, '0'));
  });

  // AM/PM toggle
  $(document).on('click', '.ampm-box', function () {
    $('.ampm-box').removeClass('border-primary fw-bold');
    $(this).addClass('border-primary fw-bold');
  });

  // DONE: apply time to input and sync with Livewire
  $(document).on('click', '.done-btn', function () {
    const hour = $('.hour-display').val().toString().padStart(2, '0');
    const minute = $('.minute-display').val().toString().padStart(2, '0');
    const ampm = $('.ampm-box.border-primary').text();
    const newValue = `${hour}:${minute} ${ampm}`;

    if ($activeInput) {
        $activeInput.val(newValue).trigger('input');

        const wireModel = $activeInput.attr('wire:model');
        const componentId = $activeInput.closest('[wire\\:id]').attr('wire:id');

        if (componentId && wireModel) {
            Livewire.find(componentId).set(wireModel, newValue);
        }

        $activeInput = null;
    }

    timepickerModal.hide();
  });
}
