@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Jadwal Keberangkatan</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if($guide = \App\Support\RoleWorkflowGuide::for('keberangkatan'))
            @include('partials.workflow-guide', ['guide' => $guide])
        @endif

        <div class="card">
            <div class="card-body">
                <p class="text-muted small mb-3">Kalender keberangkatan jamaah dari BA Pemberangkatan yang telah disetujui.</p>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="eventModalBody"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="yearSelectModal" tabindex="-1" aria-labelledby="yearSelectModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yearSelectModalTitle">Pilih Tahun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="year-grid" id="yearGrid"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css" rel="stylesheet" />
    <style>
        #calendar {
            min-height: 700px;
        }

        .fc .fc-toolbar {
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem 0.75rem;
            border-radius: var(--bs-border-radius);
            transition: background-color 0.2s ease;
        }

        .fc .fc-toolbar-title:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.08);
        }

        .fc .fc-button-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        .fc .fc-button-primary:hover,
        .fc .fc-button-primary:active,
        .fc .fc-button-primary.fc-button-active {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
            filter: brightness(0.92);
        }

        .fc .fc-button-primary:disabled {
            opacity: 0.65;
            filter: none;
        }

        .fc .fc-col-header-cell-cushion {
            color: var(--bs-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .fc .fc-daygrid-day-number {
            color: var(--bs-body-color);
            text-decoration: none;
        }

        .fc .fc-day-other .fc-daygrid-day-number {
            color: var(--bs-secondary-color);
        }

        .fc .fc-day-today {
            background-color: rgba(var(--bs-primary-rgb), 0.06) !important;
        }

        .fc-event,
        .fc-h-event {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            cursor: pointer;
        }

        .fc-event .fc-content {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fc-event .fc-description {
            margin-top: 2px;
            opacity: 0.85;
            font-size: 0.8em;
        }

        .fc .fc-multimonth {
            border-radius: var(--bs-border-radius);
        }

        .year-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .year-button {
            padding: 0.5rem;
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .year-button:hover {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }

        .year-button.current-year {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
            font-weight: 600;
        }

        .keberangkatan-detail dt {
            color: var(--bs-secondary-color);
            font-weight: 500;
        }

        .keberangkatan-detail dd {
            margin-bottom: 0.75rem;
        }

        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
            }

            .fc-header-toolbar {
                margin-bottom: 1.5em !important;
            }

            .fc .fc-button {
                padding: 0.4em 0.65em;
            }

            .fc-event {
                font-size: 0.85em;
            }

            .fc-toolbar-title {
                font-size: 1.1em !important;
            }

            .year-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var yearSelectModalEl = document.getElementById('yearSelectModal');
            var yearSelectModal = new bootstrap.Modal(yearSelectModalEl);
            var eventModalEl = document.getElementById('eventModal');
            var eventModal = new bootstrap.Modal(eventModalEl);
            var yearGrid = document.getElementById('yearGrid');

            function setupYearGrid() {
                const currentYear = new Date().getFullYear();
                yearGrid.innerHTML = '';

                for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'year-button' + (year === currentYear ? ' current-year' : '');
                    btn.textContent = year;
                    btn.onclick = function() {
                        calendar.gotoDate(year + '-01-01');
                        yearSelectModal.hide();
                        calendar.changeView('multiMonth');
                    };
                    yearGrid.appendChild(btn);
                }
            }

            function showYearModal() {
                setupYearGrid();
                yearSelectModal.show();
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,multiMonth'
                },
                views: {
                    multiMonth: {
                        type: 'multiMonth',
                        duration: { months: 12 },
                        multiMonthMaxColumns: 3,
                        multiMonthMinWidth: 350,
                        showNonCurrentDates: false
                    }
                },
                locale: 'id',
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    multiMonth: 'Tahun'
                },
                events: {
                    url: '{{ route('calendar.events') }}',
                    method: 'GET',
                    failure: function() {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Error mengambil data keberangkatan!',
                            icon: 'error'
                        });
                    }
                },
                eventClick: function(info) {
                    showEventModal(info.event);
                },
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-content">' +
                            '<div class="fc-title">' + arg.event.title + '</div>' +
                            '<div class="fc-description">' + arg.event.extendedProps.days + ' hari</div>' +
                            '</div>'
                    };
                },
                titleFormat: {
                    year: 'numeric',
                    month: 'long'
                },
                dayMaxEvents: true,
                displayEventTime: false,
                titleRender: function(info) {
                    info.el.onclick = showYearModal;
                },
                viewDidMount: function(info) {
                    if (info.view.type === 'multiMonth') {
                        showYearModal();
                    }
                }
            });

            calendar.render();

            function showEventModal(event) {
                const departureDate = new Date(event.extendedProps.returndate).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                document.getElementById('eventModalTitle').textContent = event.title;
                document.getElementById('eventModalBody').innerHTML =
                    '<dl class="row keberangkatan-detail mb-0">' +
                        '<dt class="col-sm-5"><i class="bx bx-user me-1"></i>Penanggung Jawab</dt>' +
                        '<dd class="col-sm-7">' + event.extendedProps.name + ' (' + event.extendedProps.jabatan + ')</dd>' +
                        '<dt class="col-sm-5"><i class="bx bx-calendar-check me-1"></i>Tanggal Kepulangan</dt>' +
                        '<dd class="col-sm-7">' + departureDate + '</dd>' +
                        '<dt class="col-sm-5"><i class="bx bx-group me-1"></i>Jumlah Jamaah</dt>' +
                        '<dd class="col-sm-7">' + event.extendedProps.people + ' orang</dd>' +
                        '<dt class="col-sm-5"><i class="bx bx-time me-1"></i>Durasi</dt>' +
                        '<dd class="col-sm-7">' + event.extendedProps.days + ' Hari</dd>' +
                        '<dt class="col-sm-5"><i class="bx bx-plane-take-off me-1"></i>Maskapai Keberangkatan</dt>' +
                        '<dd class="col-sm-7">' + event.extendedProps.airlines + '</dd>' +
                        '<dt class="col-sm-5"><i class="bx bx-plane-land me-1"></i>Maskapai Kepulangan</dt>' +
                        '<dd class="col-sm-7">' + event.extendedProps.airlines2 + '</dd>' +
                    '</dl>';

                eventModal.show();
            }
        });
    </script>
@endpush
