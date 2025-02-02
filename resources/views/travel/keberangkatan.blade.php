@extends('layouts.app')



@section('content')
    <div class="container">

        <div id='calendar'></div>

    </div>



    <div class="event-popup-overlay" id="popupOverlay" onclick="closePopup()"></div>

    <div id="eventPopup" class="event-popup">

        <button class="close-btn" onclick="closePopup()">

            <i class="fas fa-times"></i>

        </button>

        <div id="popupContent" class="event-details"></div>

    </div>
@endsection



@push('styles')
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/main.min.css' rel='stylesheet' />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {

            --primary-color: #2563eb;

            --secondary-color: #1e40af;

            --success-color: #059669;

            --background-color: #f8fafc;

            --card-background: #ffffff;

            --text-primary: #1e293b;

            --text-secondary: #64748b;

            --border-radius: 12px;

            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);

            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);

            --transition: all 0.3s ease;

        }



        #calendar {

            background: var(--card-background);

            border-radius: var(--border-radius);

            box-shadow: var(--shadow-md);

            padding: 20px;

            min-height: 700px;

            margin-bottom: 20px;

        }



        .fc .fc-toolbar-title {

            font-size: 1.5em;

            margin: 0;

            padding: 0;

        }



        .fc .fc-button-primary {

            background-color: var(--primary-color);

            border-color: var(--primary-color);

        }



        .fc .fc-button-primary:hover {

            background-color: var(--secondary-color);

            border-color: var(--secondary-color);

        }



        .event-popup {

            display: none;

            position: fixed;

            top: 50%;

            left: 50%;

            transform: translate(-50%, -50%);

            background: white;

            padding: 20px;

            border-radius: 12px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);

            z-index: 1000;

            width: 90%;

            max-width: 500px;

        }



        .event-popup-overlay {

            display: none;

            position: fixed;

            top: 0;

            left: 0;

            right: 0;

            bottom: 0;

            background: rgba(0, 0, 0, 0.5);

            z-index: 999;

        }



        .detail-item {

            margin-bottom: 15px;

            padding-bottom: 15px;

            border-bottom: 1px solid #eee;

        }



        .detail-label {

            font-weight: 600;

            color: var(--text-secondary);

            margin-bottom: 5px;

        }



        .detail-value {

            color: var(--text-primary);

        }



        .close-btn {

            position: absolute;

            right: 15px;

            top: 15px;

            background: none;

            border: none;

            font-size: 20px;

            cursor: pointer;

            color: var(--text-secondary);

        }



        @media (max-width: 768px) {

            .fc .fc-toolbar {

                flex-direction: column;

                gap: 10px;

            }



            .fc-header-toolbar {

                margin-bottom: 1.5em !important;

            }



            .fc .fc-button {

                padding: 0.4em 0.65em;

            }



            .event-popup {

                width: 95%;

                padding: 15px;

            }

        }
    </style>
@endpush



@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js'></script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {

                initialView: 'dayGridMonth',

                headerToolbar: {

                    left: 'prev,next today',

                    center: 'title',

                    right: 'dayGridMonth'

                },

                buttonText: {

                    today: 'Hari Ini',

                    month: 'Bulan'

                },

                events: {

                    url: '{{ route('calendar.events') }}',

                    method: 'GET',

                    failure: function() {

                        alert('Error mengambil data keberangkatan!');

                    }

                },

                eventClick: function(info) {

                    showPopup(info.event);

                },

                eventContent: function(arg) {

                    return {

                        html: `<div class="fc-content">

                    <div class="fc-title">${arg.event.title}</div>

                    <div class="fc-description" style="font-size: 0.8em;">

                        ${arg.event.extendedProps.package} hari

                    </div>

                </div>`

                    };

                }

            });



            calendar.render();

        });



        function showPopup(event) {

            const popup = document.getElementById('eventPopup');

            const overlay = document.getElementById('popupOverlay');

            const content = document.getElementById('popupContent');



            const departureDate = new Date(event.start).toLocaleDateString('id-ID', {

                weekday: 'long',

                year: 'numeric',

                month: 'long',

                day: 'numeric'

            });



            content.innerHTML = `

        <h3 style="margin-bottom: 20px; color: var(--primary-color);">

            ${event.title}

        </h3>

        <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-user"></i> Penanggung Jawab

            </div>

            <div class="detail-value">

                ${event.extendedProps.name} (${event.extendedProps.jabatan})

            </div>

        </div>

        <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-map-marker-alt"></i> Durasi

            </div>

            <div class="detail-value">

                ${event.start}<br>

                <small>${event.extendedProps.returndate}</small>

            </div>

        </div>

        <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-users"></i> Jumlah Jamaah

            </div>

            <div class="detail-value">

                ${event.extendedProps.people} orang

            </div>

        </div>

           <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-people"></i> Hari

            </div>

            <div class="detail-value">

                ${event.extendedProps.package} Hari

            </div>

        </div>

        <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-plane"></i> Maskapai

            </div>

            <div class="detail-value">

                ${event.extendedProps.airlines}

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">

                <i class="fas fa-tag"></i> Harga

            </div>

            <div class="detail-value">

                Rp ${new Intl.NumberFormat('id-ID').format(event.extendedProps.price)}

            </div>

        </div>

    `;



            overlay.style.display = 'block';

            popup.style.display = 'block';

        }



        function closePopup() {

            document.getElementById('eventPopup').style.display = 'none';

            document.getElementById('popupOverlay').style.display = 'none';

        }



        document.addEventListener('keydown', function(e) {

            if (e.key === 'Escape') {

                closePopup();

            }

        });
    </script>
@endpush
