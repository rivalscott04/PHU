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

                            <!-- Rest of the form remains the same -->
                            <div class="col-md-6 mb-3">
                                <label for="people" class="form-label">Jumlah Jamaah</label>
                                <input type="number" class="form-control" id="people" name="people"
                                    value="{{ $jamaahCount }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package" class="form-label">Paket (Jumlah Hari)</label>
                                <input type="number" class="form-control" id="package" name="package" required>
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
                                <input type="date" class="form-control" id="returndate" name="returndate" required>
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
            const packageDays = parseInt(document.getElementById('package').value) || 0;

            if (departureDate && packageDays > 0) {
                // Buat objek Date dari tanggal keberangkatan
                const returnDate = new Date(departureDate);
                // Tambahkan jumlah hari paket
                returnDate.setDate(returnDate.getDate() + packageDays);

                // Format tanggal untuk input date (YYYY-MM-DD)
                const year = returnDate.getFullYear();
                const month = String(returnDate.getMonth() + 1).padStart(2, '0');
                const day = String(returnDate.getDate()).padStart(2, '0');

                // Atur nilai tanggal kepulangan
                document.getElementById('returndate').value = `${year}-${month}-${day}`;
            }
        }

        // Tambahkan event listener untuk tanggal berangkat dan paket
        document.getElementById('datetime').addEventListener('change', calculateReturnDate);
        document.getElementById('package').addEventListener('input', calculateReturnDate);
    </script>
@endpush
