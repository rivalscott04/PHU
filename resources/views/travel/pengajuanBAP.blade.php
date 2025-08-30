@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0">
                    <h5 class="mb-0">Form Data BAP</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('post.bap') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? $travelData->Pimpinan : '' }}"
                                    {{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                    value="{{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? 'Direktur' : '' }}"
                                    {{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ppiuname" class="form-label">PPIU</label>
                                @if (in_array(auth()->user()->role, ['admin', 'kabupaten']))
                                    <select class="form-control" id="ppiuname" name="ppiuname">
                                        <option value="">Pilih PPIU</option>
                                        @foreach ($ppiuList as $ppiu)
                                            <option value="{{ $ppiu->penyelenggara }}">{{ $ppiu->penyelenggara }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" id="ppiuname" name="ppiuname"
                                        value="{{ $travelData->Penyelenggara }}" readonly>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address_phone" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="address_phone" name="address_phone"
                                    value="{{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? $travelData->Telepon : '' }}"
                                    {{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kab_kota" class="form-label">Kab/Kota</label>
                                <input type="text" class="form-control" id="kab_kota" name="kab_kota"
                                    value="{{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? $travelData->kab_kota : '' }}"
                                    {{ !in_array(auth()->user()->role, ['admin', 'kabupaten']) ? 'readonly' : '' }}>
                            </div>

                            <!-- Modified form fields with new logic -->
                            <div class="col-md-6 mb-3">
                                <label for="people" class="form-label">Jumlah Jamaah</label>
                                <input type="number" class="form-control" id="people" name="people"
                                    value="{{ $jamaahCount }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="days" class="form-label">Jumlah Hari</label>
                                <input type="number" class="form-control" id="days" name="days" required
                                    min="1">
                                <small class="form-text text-muted">Masukkan jumlah hari perjalanan</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <input type="text" class="form-control" id="price" name="price_display" required
                                    oninput="formatPrice(this)">
                                <input type="hidden" id="price_hidden" name="price">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="datetime" class="form-label">Tanggal Berangkat</label>
                                <input type="date" class="form-control" id="datetime" name="datetime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="airlines" class="form-label">Nama Airline</label>
                                <input type="text" class="form-control" id="airlines" name="airlines" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="returndate" class="form-label">Tanggal Kepulangan</label>
                                <input type="date" class="form-control" id="returndate" name="returndate" readonly>
                                <small class="form-text text-muted">Otomatis dihitung berdasarkan jumlah hari dan tanggal
                                    keberangkatan</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="airlines2" class="form-label">Nama Airline Kepulangan</label>
                                <input type="text" class="form-control" id="airlines2" name="airlines2" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function formatPrice(input) {
            // Remove non-numeric characters except dots
            let rawValue = input.value.replace(/[^0-9]/g, '');

            // Format the value for display
            let formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Update the visible input with the formatted value
            input.value = formattedValue;

            // Update the hidden input with the raw numeric value
            document.getElementById('price_hidden').value = rawValue;
        }

        function calculateReturnDate() {
            const departureDate = document.getElementById('datetime').value;
            const days = document.getElementById('days').value;

            if (departureDate && days && days > 0) {
                // Buat objek Date dari tanggal keberangkatan
                const startDate = new Date(departureDate);

                // Tambahkan jumlah hari (dikurangi 1 karena hari keberangkatan sudah dihitung)
                const returnDate = new Date(startDate);
                returnDate.setDate(startDate.getDate() + parseInt(days) - 1);

                // Format tanggal untuk input type="date" (YYYY-MM-DD)
                const formattedReturnDate = returnDate.toISOString().split('T')[0];

                // Set nilai tanggal kepulangan
                document.getElementById('returndate').value = formattedReturnDate;
            } else {
                // Kosongkan tanggal kepulangan jika input tidak lengkap
                document.getElementById('returndate').value = '';
            }
        }

        // Tambahkan event listener untuk input hari dan tanggal keberangkatan
        document.getElementById('days').addEventListener('input', calculateReturnDate);
        document.getElementById('datetime').addEventListener('change', calculateReturnDate);

        // Validasi tambahan untuk memastikan jumlah hari minimal 1
        document.getElementById('days').addEventListener('input', function() {
            if (this.value < 1) {
                this.value = '';
                document.getElementById('returndate').value = '';
            }
        });
    </script>
@endpush
