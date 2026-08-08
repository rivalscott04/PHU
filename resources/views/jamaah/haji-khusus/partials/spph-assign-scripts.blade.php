<script>
function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function readJamaahContext(trigger) {
    const dataset = trigger.dataset;

    return {
        id: dataset.id,
        nama: dataset.nama || '',
        nik: dataset.nik || '',
        paspor: dataset.paspor || '-',
        travel: dataset.travel || '-',
        kabupaten: dataset.kabupaten || '-',
        jenisKelamin: dataset.jenisKelamin || '-',
        spph: dataset.spph || '-',
        statusPendaftaran: dataset.statusPendaftaran || '-',
        buktiSetor: dataset.buktiSetor || '-',
    };
}

function buildKanwilReviewHtml(context) {
    return buildSppHContextHtml(context) + `
        <div class="text-start row g-2 small mb-1">
            <div class="col-sm-6"><span class="text-muted">Bukti Setor</span><br><strong>${escapeHtml(context.buktiSetor)}</strong></div>
            <div class="col-sm-6"><span class="text-muted">No. SPPH</span><br><code>${escapeHtml(context.spph)}</code></div>
            <div class="col-sm-6"><span class="text-muted">Status Saat Ini</span><br><strong>${escapeHtml(context.statusPendaftaran)}</strong></div>
        </div>
    `;
}

function buildSppHContextHtml(context) {
    return `
        <div class="text-start border rounded p-3 mb-3 bg-light">
            <div class="d-flex align-items-start gap-2 mb-2">
                <i class="bx bx-user-circle fs-4 text-primary"></i>
                <div>
                    <div class="fw-semibold">${escapeHtml(context.nama)}</div>
                    <small class="text-muted">${escapeHtml(context.jenisKelamin)}</small>
                </div>
            </div>
            <div class="row g-2 small">
                <div class="col-sm-6"><span class="text-muted">NIK</span><br><code>${escapeHtml(context.nik)}</code></div>
                <div class="col-sm-6"><span class="text-muted">Paspor</span><br><code>${escapeHtml(context.paspor)}</code></div>
                <div class="col-12 mt-2 pt-2 border-top">
                    <span class="text-muted">PIHK / Travel</span><br>
                    <strong>${escapeHtml(context.travel)}</strong>
                    <small class="text-muted d-block">${escapeHtml(context.kabupaten)}</small>
                </div>
            </div>
        </div>
    `;
}

function tetapkanSppH(trigger) {
    const context = readJamaahContext(trigger);
    const tahunDefault = new Date().getFullYear();

    Swal.fire({
        title: 'Tetapkan Nomor SPPH',
        html: `
            ${buildSppHContextHtml(context)}
            <div class="text-start">
                <label for="swal-spph-nomor" class="form-label fw-semibold">Nomor SPPH <span class="text-danger">*</span></label>
                <input id="swal-spph-nomor" type="text" class="form-control mb-3" maxlength="9" inputmode="numeric" data-digits-only="9" placeholder="9 digit angka, contoh: 123456789">
                <label for="swal-spph-tahun" class="form-label fw-semibold">Tahun Pendaftaran <span class="text-danger">*</span></label>
                <input id="swal-spph-tahun" type="number" class="form-control" min="2000" max="2099" value="${tahunDefault}">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan SPPH',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#34c38f',
        focusConfirm: false,
        didOpen: () => {
            const nomorInput = document.getElementById('swal-spph-nomor');
            nomorInput?.focus();
            nomorInput?.addEventListener('input', () => {
                nomorInput.value = nomorInput.value.replace(/\D/g, '').slice(0, 9);
            });
        },
        preConfirm: () => {
            const nomor = document.getElementById('swal-spph-nomor').value.trim();
            const tahun = document.getElementById('swal-spph-tahun').value.trim();

            if (!nomor) {
                Swal.showValidationMessage('Nomor SPPH wajib diisi');
                return false;
            }
            if (!/^\d{9}$/.test(nomor)) {
                Swal.showValidationMessage('Nomor SPPH harus tepat 9 digit angka');
                return false;
            }
            if (!tahun || tahun.length !== 4) {
                Swal.showValidationMessage('Tahun pendaftaran harus 4 digit');
                return false;
            }

            return { nomor_porsi: nomor, tahun_pendaftaran: tahun };
        },
    }).then((result) => {
        if (!result.isConfirmed || !result.value) {
            return;
        }

        fetch(`/jamaah/haji-khusus/${context.id}/assign-porsi`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(result.value),
        })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok || !data.success) {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                        || 'Gagal menetapkan nomor SPPH';
                    throw new Error(message);
                }
                return data;
            })
            .then((data) => {
                Swal.fire({
                    title: 'Berhasil',
                    html: `Nomor SPPH <strong>${escapeHtml(data.nomor_porsi)}</strong> berhasil ditetapkan untuk <strong>${escapeHtml(context.nama)}</strong>.`,
                    icon: 'success',
                    confirmButtonColor: '#556ee6',
                }).then(() => location.reload());
            })
            .catch((error) => {
                Swal.fire({
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan saat menetapkan nomor SPPH',
                    icon: 'error',
                    confirmButtonColor: '#556ee6',
                });
            });
    });
}

function verifikasiBuktiSetor(trigger) {
    const context = readJamaahContext(trigger);

    Swal.fire({
        title: 'Verifikasi Bukti Setor',
        html: buildSppHContextHtml(context),
        input: 'select',
        inputOptions: { verified: 'Terverifikasi', rejected: 'Ditolak' },
        inputPlaceholder: 'Pilih status verifikasi',
        showCancelButton: true,
        confirmButtonText: 'Lanjut',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#556ee6',
    }).then((statusResult) => {
        if (!statusResult.isConfirmed || !statusResult.value) {
            return;
        }

        const status = statusResult.value;

        Swal.fire({
            title: 'Catatan Verifikasi',
            input: 'textarea',
            inputPlaceholder: 'Catatan verifikasi (opsional)',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#556ee6',
        }).then((catatanResult) => {
            if (!catatanResult.isConfirmed) {
                return;
            }

            fetch(`/jamaah/haji-khusus/${context.id}/verify-bukti-setor`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    status_verifikasi_bukti: status,
                    catatan_verifikasi: catatanResult.value,
                }),
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        const message = data.message
                            || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                            || 'Gagal memperbarui status verifikasi';
                        throw new Error(message);
                    }
                    return data;
                })
                .then(() => {
                    if (status === 'verified') {
                        Swal.fire({
                            title: 'Bukti setor terverifikasi',
                            text: 'Lanjut tetapkan nomor SPPH untuk jamaah ini?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Tetapkan SPPH',
                            cancelButtonText: 'Nanti saja',
                            confirmButtonColor: '#34c38f',
                        }).then((lanjut) => {
                            if (lanjut.isConfirmed) {
                                tetapkanSppH(trigger);
                                return;
                            }
                            location.reload();
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Status verifikasi berhasil diperbarui',
                        icon: 'success',
                        confirmButtonColor: '#556ee6',
                    }).then(() => location.reload());
                })
                .catch((error) => {
                    Swal.fire({
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat memperbarui status verifikasi',
                        icon: 'error',
                        confirmButtonColor: '#556ee6',
                    });
                });
        });
    });
}

function ubahStatusPendaftaran(trigger) {
    const context = readJamaahContext(trigger);

    Swal.fire({
        title: 'Persetujuan Kanwil',
        html: `
            ${buildKanwilReviewHtml(context)}
            <p class="text-muted small text-start mb-0">Verifikasi bukti setor dilakukan admin kabupaten/kota. Kanwil memutuskan status pendaftaran jamaah.</p>
        `,
        input: 'select',
        inputOptions: {
            approved: 'Disetujui',
            rejected: 'Ditolak',
            completed: 'Selesai',
        },
        inputValue: 'approved',
        showCancelButton: true,
        confirmButtonText: 'Simpan Status',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#556ee6',
    }).then((result) => {
        if (!result.isConfirmed || !result.value) {
            return;
        }

        fetch(`/jamaah/haji-khusus/${context.id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ status_pendaftaran: result.value }),
        })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok || !data.success) {
                    const message = data.message
                        || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                        || 'Gagal memperbarui status pendaftaran';
                    throw new Error(message);
                }
                return data;
            })
            .then((data) => {
                Swal.fire({
                    title: 'Berhasil',
                    html: `Status pendaftaran <strong>${escapeHtml(context.nama)}</strong> diperbarui menjadi <strong>${escapeHtml(data.status_text)}</strong>.`,
                    icon: 'success',
                    confirmButtonColor: '#556ee6',
                }).then(() => location.reload());
            })
            .catch((error) => {
                Swal.fire({
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan saat memperbarui status pendaftaran',
                    icon: 'error',
                    confirmButtonColor: '#556ee6',
                });
            });
    });
}
</script>
