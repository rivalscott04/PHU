@extends('layouts.app')

@section('content')
    @php
        $editing = isset($bap);
        $formAction = $editing ? route('put.bap', $bap->id) : route('post.bap');
        $isStaff = in_array(auth()->user()->role, ['admin', 'kabupaten']);
        $value = fn (string $field, mixed $fallback = '') => old($field, $editing ? $bap->{$field} : $fallback);
        $priceDisplay = old(
            'price_display',
            $editing ? number_format((float) $bap->price, 0, ',', '.') : ''
        );
    @endphp
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            <div class="card">
                <div class="card-header ps-0 d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h5 class="mb-0">{{ $editing ? 'Ubah Draf BA Pemberangkatan' : 'Form BA Pemberangkatan' }}</h5>
                        <small class="text-muted d-block">Langkah 1 dari 3 — isi data keberangkatan jamaah.</small>
                    </div>
                    @if ($editing)
                        <a href="{{ route($bap->pdf_file_path ? 'bap.wizard.review' : 'bap.wizard.upload', $bap->id) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali ke wizard
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @include('travel.partials.bap-wizard-progress', ['currentStep' => 1])

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $formAction }}" id="bapForm" onsubmit="return prepareBapSubmit()">
                        @csrf
                        @if ($editing)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                    value="{{ $value('name', $isStaff ? '' : ($travelData->Pimpinan ?? '')) }}"
                                    {{ $isStaff ? '' : 'readonly' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                    value="{{ $value('jabatan', $isStaff ? '' : 'Direktur') }}"
                                    {{ $isStaff ? '' : 'readonly' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ppiuname" class="form-label">PPIU</label>
                                @if ($isStaff)
                                    <select class="form-control" id="ppiuname" name="ppiuname">
                                        <option value="">Pilih PPIU</option>
                                        @foreach ($ppiuList as $ppiu)
                                            <option value="{{ $ppiu->penyelenggara }}"
                                                {{ $value('ppiuname') === $ppiu->penyelenggara ? 'selected' : '' }}>
                                                {{ $ppiu->penyelenggara }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" id="ppiuname" name="ppiuname"
                                        value="{{ $value('ppiuname', $travelData->Penyelenggara ?? '') }}" readonly>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_phone" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="address_phone" name="address_phone"
                                    value="{{ $value('address_phone', $isStaff ? '' : ($travelData->Telepon ?? '')) }}"
                                    {{ $isStaff ? '' : 'readonly' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kab_kota" class="form-label">Kab/Kota</label>
                                <input type="text" class="form-control" id="kab_kota" name="kab_kota"
                                    value="{{ $value('kab_kota', $isStaff ? '' : ($travelData->kab_kota ?? '')) }}"
                                    {{ $isStaff ? '' : 'readonly' }}>
                            </div>

                            @include('travel.partials.bap-jamaah-picker', [
                                'selectedJamaah' => $selectedJamaah ?? collect(),
                                'ignoreBapId' => $bap->id ?? null,
                                'jamaahTotalCount' => $jamaahTotalCount ?? 0,
                            ])

                            <div class="col-md-6 mb-3">
                                <label for="days" class="form-label">Jumlah Hari</label>
                                <input type="number" class="form-control @error('days') is-invalid @enderror" id="days" name="days" required
                                    min="1" value="{{ $value('days') }}">
                                @error('days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Masukkan jumlah hari perjalanan</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Harga per Orang</label>
                                <input type="text" class="form-control @error('price') is-invalid @enderror" id="price" name="price_display" required
                                    value="{{ $priceDisplay }}"
                                    oninput="formatPrice(this)">
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Harga paket umroh per jamaah (bukan total keseluruhan)</small>
                                <input type="hidden" id="price_hidden" name="price" value="{{ old('price', $editing ? $bap->price : '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="datetime" class="form-label">Tanggal Berangkat</label>
                                <input type="date" class="form-control @error('datetime') is-invalid @enderror" id="datetime" name="datetime"
                                    value="{{ $value('datetime') ? \Carbon\Carbon::parse($value('datetime'))->format('Y-m-d') : '' }}" required>
                                @error('datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            @include('travel.partials.bap-airline-fields', [
                                'airlineOptions' => $airlineOptions ?? [],
                                'value' => $value,
                                'editing' => $editing,
                            ])
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ $editing ? 'Simpan Perubahan' : 'Simpan & Lanjutkan' }}
                                <i class="bx bx-right-arrow-alt ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function prepareBapSubmit() {
            const display = document.getElementById('price');
            const hidden = document.getElementById('price_hidden');
            if (display && hidden) {
                hidden.value = display.value.replace(/[^0-9]/g, '');
            }

            const selected = document.querySelectorAll('#jamaahIdsHidden input[name="jamaah_ids[]"]').length;
            if (selected < 1) {
                Swal.fire({ title: 'Perhatian', text: 'Pilih minimal satu jamaah untuk keberangkatan ini.', icon: 'warning', confirmButtonColor: '#556ee6' });
                return false;
            }

            if (!validateAirlineFields()) {
                return false;
            }

            enableReturnAirlineForSubmit();

            return true;
        }

        function enableReturnAirlineForSubmit() {
            const returnSelect = document.getElementById('airlines2_select');
            const returnOther = document.getElementById('airlines2_other');
            if (returnSelect) {
                returnSelect.disabled = false;
            }
            if (returnOther) {
                returnOther.disabled = false;
            }
        }

        function validateAirlineFields() {
            const departureSelect = document.getElementById('airlines_select');
            const departureOther = document.getElementById('airlines_other');
            const returnSelect = document.getElementById('airlines2_select');
            const returnOther = document.getElementById('airlines2_other');
            const sameReturn = document.getElementById('same_return_airline');

            if (sameReturn?.checked) {
                syncReturnAirline();
            }

            const departureValue = departureSelect.value === '__other__'
                ? departureOther.value.trim()
                : departureSelect.value.trim();
            const returnValue = returnSelect.value === '__other__'
                ? returnOther.value.trim()
                : returnSelect.value.trim();

            if (!departureValue) {
                Swal.fire({ title: 'Perhatian', text: 'Pilih maskapai keberangkatan.', icon: 'warning', confirmButtonColor: '#556ee6' });
                return false;
            }

            if (!returnValue) {
                Swal.fire({ title: 'Perhatian', text: 'Pilih maskapai kepulangan.', icon: 'warning', confirmButtonColor: '#556ee6' });
                return false;
            }

            return true;
        }

        function toggleAirlineOther(selectId, otherId) {
            const select = document.getElementById(selectId);
            const other = document.getElementById(otherId);
            if (!select || !other) {
                return;
            }

            const isOther = select.value === '__other__';
            other.hidden = !isOther;
            other.required = isOther;
            if (!isOther) {
                other.value = '';
            }
        }

        function syncReturnAirline() {
            const sameReturn = document.getElementById('same_return_airline');
            const returnSelect = document.getElementById('airlines2_select');
            const returnOther = document.getElementById('airlines2_other');
            const departureSelect = document.getElementById('airlines_select');
            const departureOther = document.getElementById('airlines_other');
            const returnGroup = document.getElementById('returnAirlineGroup');

            if (!sameReturn || !returnSelect || !departureSelect) {
                return;
            }

            if (sameReturn.checked) {
                returnSelect.value = departureSelect.value;
                returnOther.value = departureOther.value;
                returnSelect.disabled = true;
                returnOther.hidden = departureSelect.value !== '__other__';
                returnOther.disabled = departureSelect.value !== '__other__';
                if (returnGroup) {
                    returnGroup.classList.add('opacity-50');
                }
            } else {
                returnSelect.disabled = false;
                returnOther.disabled = false;
                toggleAirlineOther('airlines2_select', 'airlines2_other');
                if (returnGroup) {
                    returnGroup.classList.remove('opacity-50');
                }
            }
        }

        function formatPrice(input) {
            let rawValue = input.value.replace(/[^0-9]/g, '');
            let formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = formattedValue;
            document.getElementById('price_hidden').value = rawValue;
        }

        function calculateReturnDate() {
            const departureDate = document.getElementById('datetime').value;
            const days = document.getElementById('days').value;

            if (departureDate && days && days > 0) {
                const startDate = new Date(departureDate);
                const returnDate = new Date(startDate);
                returnDate.setDate(startDate.getDate() + parseInt(days) - 1);
                document.getElementById('returndate').value = returnDate.toISOString().split('T')[0];
            } else {
                document.getElementById('returndate').value = '';
            }
        }

        document.getElementById('days').addEventListener('input', calculateReturnDate);
        document.getElementById('datetime').addEventListener('change', calculateReturnDate);

        document.getElementById('days').addEventListener('input', function() {
            if (this.value < 1) {
                this.value = '';
                document.getElementById('returndate').value = '';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const priceInput = document.getElementById('price');
            const priceHidden = document.getElementById('price_hidden');
            if (priceInput && priceHidden && priceInput.value && !priceHidden.value) {
                priceHidden.value = priceInput.value.replace(/[^0-9]/g, '');
            }
            calculateReturnDate();

            ['airlines_select', 'airlines2_select'].forEach(function (selectId) {
                const otherId = selectId === 'airlines_select' ? 'airlines_other' : 'airlines2_other';
                const select = document.getElementById(selectId);
                if (select) {
                    select.addEventListener('change', function () {
                        toggleAirlineOther(selectId, otherId);
                        syncReturnAirline();
                    });
                }
            });

            const sameReturn = document.getElementById('same_return_airline');
            if (sameReturn) {
                sameReturn.addEventListener('change', syncReturnAirline);
            }

            const departureSelect = document.getElementById('airlines_select');
            const departureOther = document.getElementById('airlines_other');
            if (departureSelect) {
                departureSelect.addEventListener('change', syncReturnAirline);
            }
            if (departureOther) {
                departureOther.addEventListener('input', syncReturnAirline);
            }

            toggleAirlineOther('airlines_select', 'airlines_other');
            toggleAirlineOther('airlines2_select', 'airlines2_other');
            syncReturnAirline();
        });
    </script>
@endpush
