@php
    use App\Enums\ChecklistInputType;
    use App\Support\ChecklistScoring;
@endphp

@if ($errors->has('checklist'))
    <div class="alert alert-danger">{{ $errors->first('checklist') }}</div>
@endif

@if ($canFillChecklist)
    <form method="POST" action="{{ route('v2.pengawasan.checklist.update', $inspection) }}">
        @csrf
        @method('PUT')
@endif

@forelse ($checklistGroups as $categoryName => $items)
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $categoryName }}</h5>
        </div>
        <div class="card-body">
            @foreach ($items as $index => $item)
                @php
                    $master = $item->masterChecklist;
                    $inputType = $master?->input_type;
                    $fieldName = "items[{$item->id}]";
                @endphp
                <div class="border rounded p-3 mb-3 {{ !$loop->last ? '' : 'mb-0' }}">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                        <div>
                            <strong>{{ $master?->title }}</strong>
                            @if ($master?->required)
                                <span class="text-danger">*</span>
                            @endif
                            @if ($master?->description)
                                <div class="text-muted small">{{ $master->description }}</div>
                            @endif
                        </div>
                        @if ($item->score !== null)
                            <span class="badge bg-light text-dark">Skor: {{ $item->score }}/{{ $master?->weight }}</span>
                        @endif
                    </div>

                    @if ($canFillChecklist)
                        <input type="hidden" name="{{ $fieldName }}[id]" value="{{ $item->id }}">

                        @if ($inputType === ChecklistInputType::Boolean)
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $fieldName }}[answer]" id="checklist-{{ $item->id }}-ya" value="1" @checked(old("items.{$item->id}.answer", $item->answer) === '1') @required($master?->required)>
                                    <label class="form-check-label" for="checklist-{{ $item->id }}-ya">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $fieldName }}[answer]" id="checklist-{{ $item->id }}-tidak" value="0" @checked(old("items.{$item->id}.answer", $item->answer) === '0') @required($master?->required)>
                                    <label class="form-check-label" for="checklist-{{ $item->id }}-tidak">Tidak</label>
                                </div>
                            </div>
                        @elseif ($inputType === ChecklistInputType::Option)
                            <select name="{{ $fieldName }}[answer]" class="form-select" @required($master?->required)>
                                <option value="">Pilih jawaban</option>
                                @foreach ($master->options as $option)
                                    <option value="{{ $option->value }}" @selected(old("items.{$item->id}.answer", $item->answer) === $option->value)>{{ $option->label }}</option>
                                @endforeach
                            </select>
                        @elseif ($inputType === ChecklistInputType::Number)
                            <input type="number" name="{{ $fieldName }}[answer]" class="form-control" value="{{ old("items.{$item->id}.answer", $item->answer) }}" @required($master?->required)>
                        @elseif ($inputType === ChecklistInputType::Text)
                            <textarea name="{{ $fieldName }}[answer]" class="form-control" rows="2" @required($master?->required)>{{ old("items.{$item->id}.answer", $item->answer) }}</textarea>
                        @elseif ($inputType === ChecklistInputType::File)
                            <input type="text" name="{{ $fieldName }}[answer]" class="form-control" value="{{ old("items.{$item->id}.answer", $item->answer) }}" placeholder="Tulis nama atau keterangan berkas bukti" @required($master?->required)>
                        @elseif ($inputType === ChecklistInputType::Photo)
                            <input type="text" name="{{ $fieldName }}[answer]" class="form-control" value="{{ old("items.{$item->id}.answer", $item->answer) }}" placeholder="Tulis keterangan foto bukti lapangan" @required($master?->required)>
                        @else
                            <input type="text" name="{{ $fieldName }}[answer]" class="form-control" value="{{ old("items.{$item->id}.answer", $item->answer) }}">
                        @endif

                        <div class="mt-2">
                            <label class="form-label small text-muted mb-1">Catatan (opsional)</label>
                            <input type="text" name="{{ $fieldName }}[note]" class="form-control form-control-sm" value="{{ old("items.{$item->id}.note", $item->note) }}" placeholder="Tambahkan catatan jika perlu">
                        </div>
                    @else
                        <p class="mb-1">
                            <span class="text-muted">Jawaban:</span>
                            <strong>{{ ChecklistScoring::formatAnswer($item->answer, $inputType ?? ChecklistInputType::Text, $master?->options) }}</strong>
                        </p>
                        @if ($item->note)
                            <p class="mb-0 small text-muted">Catatan: {{ $item->note }}</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="alert alert-warning mb-0">Belum ada daftar periksa untuk pengawasan ini. Pastikan master checklist sudah diaktifkan oleh admin.</div>
@endforelse

@if ($canFillChecklist && $checklistGroups->isNotEmpty())
        <button type="submit" class="btn btn-primary">Simpan Daftar Periksa</button>
    </form>
@endif
