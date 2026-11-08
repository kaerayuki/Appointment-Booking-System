var dateInput = document.getElementById('appointment_date');
var slotBox = document.getElementById('slotBox');

function loadSlots() {
    var date = dateInput.value;
    if (date === '') {
        slotBox.innerHTML = '<div class="text-muted small">Pick a date first to see the available times.</div>';
        return;
    }

    var selected = slotBox.getAttribute('data-selected');
    var ignoreId = slotBox.getAttribute('data-ignore');
    var base = slotBox.getAttribute('data-base');
    var adminMode = document.getElementById('status') !== null ? '&admin=1' : '';

    slotBox.innerHTML = '<div class="text-muted small">Loading available times...</div>';

    fetch(base + 'slots.php?date=' + encodeURIComponent(date) + '&ignore=' + ignoreId + adminMode)
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.closed) {
                slotBox.innerHTML = '<div class="text-danger small">' + data.message + '</div>';
                return;
            }

            var html = '<div class="slot-grid">';
            var anyFree = false;
            data.slots.forEach(function (slot, i) {
                var id = 'slot' + i;
                var checked = (slot.time === selected && slot.available) ? ' checked' : '';
                var disabled = slot.available ? '' : ' disabled';
                if (slot.available) { anyFree = true; }
                html += '<input type="radio" class="btn-check" name="appointment_time" id="' + id + '" value="' + slot.time + '" autocomplete="off"' + checked + disabled + ' required>';
                html += '<label class="btn btn-outline-primary btn-sm" for="' + id + '">' + slot.label + (slot.available ? '' : '<br><small>Taken</small>') + '</label>';
            });
            html += '</div>';
            if (!anyFree) {
                html += '<div class="text-danger small mt-2">No more free slots on this day. Please pick another date.</div>';
            }
            slotBox.innerHTML = html;
        })
        .catch(function () {
            slotBox.innerHTML = '<div class="text-danger small">Could not load times. Please refresh the page.</div>';
        });
}

dateInput.addEventListener('change', function () {
    slotBox.setAttribute('data-selected', '');
    loadSlots();
});

if (dateInput.value !== '') {
    loadSlots();
}
