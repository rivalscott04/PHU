@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="menu" class="mb-3">
                        <span class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary move-day" data-action="move-prev">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-primary move-today">Hari Ini</button>
                                <button type="button" class="btn btn-primary move-day" data-action="move-next">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <h4 id="renderRange" class="render-range fw-bold pt-1 mx-3"></h4>
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary" data-action="toggle-monthly">
                                    Tampilan Bulanan
                                </button>
                            </div>
                        </span>
                    </div>
                    <div id="calendar" style="height: 800px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .toastui-calendar-popup-detail .travel-info {
            margin-top: 8px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #0066cc;
        }

        .travel-title {
            font-weight: 700;
            color: #0066cc;
            margin-bottom: 8px;
        }

        .travel-detail {
            color: #555;
            margin: 4px 0;
        }

        .jamaah-count {
            color: #28a745;
            font-weight: 500;
        }

        .airlines-info {
            color: #dc3545;
            font-weight: 500;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
    <script>
        $(document).ready(function() {
            // Bootstrap colors for events
            const colors = [{
                    color: '#ffffff',
                    bgColor: '#556ee6',
                    borderColor: '#556ee6'
                }, // primary
                {
                    color: '#ffffff',
                    bgColor: '#34c38f',
                    borderColor: '#34c38f'
                }, // success
                {
                    color: '#ffffff',
                    bgColor: '#f46a6a',
                    borderColor: '#f46a6a'
                }, // danger
                {
                    color: '#000000',
                    bgColor: '#f1b44c',
                    borderColor: '#f1b44c'
                }, // warning
                {
                    color: '#ffffff',
                    bgColor: '#50a5f1',
                    borderColor: '#50a5f1'
                }, // info
                {
                    color: '#ffffff',
                    bgColor: '#74788d',
                    borderColor: '#74788d'
                }, // secondary
            ];

            // Data dari controller
            const scheduleData = @json($schedules);

            // Format data untuk calendar
            const calendarEvents = scheduleData.map((item, index) => {
                const colorSet = colors[index % colors.length];
                return {
                    id: item.id,
                    title: item.ppiuname,
                    start: item.datetime,
                    end: item.returndate,
                    jamaahCount: item.people,
                    airlines: item.airline,
                    category: 'allday',
                    backgroundColor: colorSet.bgColor,
                    borderColor: colorSet.borderColor,
                    color: colorSet.color
                };
            });

            // Inisialisasi kalender
            const cal = new tui.Calendar('#calendar', {
                defaultView: 'month',
                isReadOnly: true,
                useDetailPopup: true,
                template: {
                    popupDetailBody: function(schedule) {
                        return `
                        <div class="travel-info">
                            <div class="travel-title">${schedule.title}</div>
                            <div class="travel-detail">
                                <span class="jamaah-count">${schedule.jamaahCount} Jamaah</span> |
                                <span class="airlines-info">${schedule.airlines}</span>
                            </div>
                            <div class="travel-detail">
                                <i class="fas fa-plane-departure"></i> ${moment(schedule.start.toDate()).format('DD MMM YYYY')}
                            </div>
                            <div class="travel-detail">
                                <i class="fas fa-plane-arrival"></i> ${moment(schedule.end.toDate()).format('DD MMM YYYY')}
                            </div>
                        </div>
                    `;
                    }
                }
            });

            // Fungsi update tanggal
            function updateRenderRange() {
                const dateRange = cal.getDateRange();
                $('#renderRange').text(moment(dateRange.start.toDate()).format('MMMM YYYY'));
            }

            // Navigasi kalender
            $('.move-today').click(() => {
                cal.today();
                updateRenderRange();
            });

            $('[data-action="move-prev"]').click(() => {
                cal.prev();
                updateRenderRange();
            });

            $('[data-action="move-next"]').click(() => {
                cal.next();
                updateRenderRange();
            });

            // Load events
            cal.createEvents(calendarEvents);
            updateRenderRange();
        });
    </script>
@endpush
