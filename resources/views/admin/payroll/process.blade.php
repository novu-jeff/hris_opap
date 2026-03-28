    @extends('layouts.admin', [
        'title' => 'HRIS | Payroll for ' . $payroll_date
    ])

    @section('content')
    <div class="main-content flex-grow-1 p-4">
        <div class="container pb-5">
            <div class="d-lg-flex justify-content-between align-items-center mt-5 mb-4">
                <div class="section-title">
                    <h1>Payroll Computation</h1>
                </div>
            </div>

            <div class="mt-3">
                @livewire('admin.payroll.process', [
                    'payroll_id' => $id,
                    'type' => $type
                ])
            </div>

            {{-- Tooltip outside Livewire --}}
            <div class="name-tooltip" id="nameTooltip"></div>
        </div>
    </div>
    @endsection

   <style>
.current-row {
    background-color: #fff3cd !important;
}

.name-tooltip {
    position: fixed;          /* 🔥 STICKY */
    top: 16px;
    left: 275px;
    background: #343a40;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    z-index: 9999;
    font-weight: 600;
    display: none;
    pointer-events: none;
    max-width: 320px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-shadow: 0 4px 10px rgba(0,0,0,.2);
}

/* REQUIRED for sticky headers */
.table-responsive {
    max-height: 80vh; /* adjust as needed */
    overflow: auto;
}

/* Make table layout predictable */
.payroll-table {
    border-collapse: separate;
    border-spacing: 0;
    width: max-content;
}

/* Sticky header rows */
.payroll-table thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
    z-index: 20;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}

/* Second header row must sit below first */
.payroll-table thead tr:nth-child(2) th {
    top: 42px; /* height of first header row */
    z-index: 21;
}

/* Sticky section headers (blue rows) */
.payroll-table tbody tr.sticky-top {
    position: sticky;
    top: 84px; /* header row 1 + row 2 */
    z-index: 15;
}

/* Inputs don’t overflow */
.payroll-table td input {
    min-width: 110px;
}

/* Vertical text headers */
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    text-align: center;
}

.payroll-table-wrapper {
    max-height: 75vh;
    overflow: auto;
    border: 1px solid #dee2e6;
}

/* Table base */
.payroll-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 13px;
}

/* Header */
.payroll-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
    color: #333;
    text-align: center;
    font-weight: 600;
    border: 1px solid #dee2e6;
    padding: 6px;
    white-space: nowrap;
}

/* Second header row adjustment */
.payroll-table thead tr:nth-child(2) th {
    top: 38px;
    z-index: 11;
}

/* Body */
.payroll-table tbody td {
    border: 1px solid #e5e7eb;
    padding: 4px;
    white-space: nowrap;
    background: #fff;
}

/* Zebra rows */
.payroll-table tbody tr:nth-child(even) td {
    background: #fafafa;
}

/* Hover */
.payroll-table tbody tr:hover td {
    background: #eef6ff;
}

/* Sticky first columns */
.payroll-table th:nth-child(1),
.payroll-table td:nth-child(1) {
    position: sticky;
    left: 0;
    z-index: 12;
    background: #fff;
}

.payroll-table th:nth-child(2),
.payroll-table td:nth-child(2) {
    position: sticky;
    left: 50px;
    z-index: 12;
    background: #fff;
}

.payroll-table th:nth-child(3),
.payroll-table td:nth-child(3) {
    position: sticky;
    left: 110px;
    z-index: 12;
    background: #fff;
}

/* Section header */
.section-header {
    position: sticky;
    top: 76px;
    z-index: 9;
    background: #0d6efd;
    color: #fff;
    font-weight: bold;
}

/* Inputs */
.payroll-table input {
    border: 1px solid #ced4da;
    font-size: 12px;
    padding: 2px 4px;
    height: 28px;
}

.payroll-table input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 2px rgba(13,110,253,.5);
}

/* Vertical text fix */
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    text-align: center;
}

/* Colors (cleaned) */
.green { background: #e6f4ea !important; color: #dee2e6 !important}
.yellow { background: #fff8e1 !important; color: #dee2e6 !important}
.skyblue { background: #e3f2fd !important; color: #dee2e6 !important}
.red { background: #fdecea !important;  color: #dee2e6 !important}
.grey { background: #f1f3f5 !important; color: #dee2e6 !important}
.section-c{background: #e3f2fd !important; color:#343a40 !important}
.f-green{color: hsl(111, 36%, 12%) !important}

.payroll-table th,
.payroll-table td {
    min-width: 80px;
}

.payroll-table td:nth-child(3) {
    min-width: 180px; /* Name */
}

</style>

<script>
function initStickyRowTooltip() {
    const tooltip = document.getElementById("nameTooltip");
    const table = document.querySelector("table");
    if (!table || !tooltip) return;

    let clickedRow = null;

    function showTooltipForRow(row) {
        const nameCell = row.querySelector("td:nth-child(3)");
        if (!nameCell) return;

        tooltip.textContent = nameCell.innerText.trim();
        tooltip.style.display = "block";
    }

    // CLICK → highlight + sticky tooltip
    table.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        if (!row || row.classList.contains("sticky-top")) return;

        table.querySelectorAll("tbody tr").forEach(r =>
            r.classList.remove("current-row")
        );

        row.classList.add("current-row");
        clickedRow = row;

        showTooltipForRow(row);
    });

    // OPTIONAL: auto-highlight first visible row on scroll (only if none clicked)
    window.addEventListener("scroll", () => {
        if (clickedRow) return;

        const rows = table.querySelectorAll("tbody tr:not(.sticky-top)");
        for (let row of rows) {
            const rect = row.getBoundingClientRect();
            if (rect.top >= 60 && rect.top < window.innerHeight / 2) {
                row.classList.add("current-row");
                showTooltipForRow(row);
                break;
            }
        }
    });
}

// Init
document.addEventListener("DOMContentLoaded", () => {
    initStickyRowTooltip();

    // Livewire-safe re-init
    if (window.Livewire) {
        Livewire.hook("message.processed", () => {
            initStickyRowTooltip();
        });
    }
});
</script>


  
