$(document).ready(function () {
    $("#datatable").DataTable({
        scrollX: true, // Tambahkan ini agar tabel bisa di-scroll horizontal
        responsive: false, // Matikan responsivitas bawaan kalau masih bermasalah
    });

    $("#datatable-buttons")
        .DataTable({
            scrollX: true, // Tambahkan ini juga di tabel dengan tombol export
            lengthChange: false,
            buttons: ["copy", "excel", "pdf", "colvis"],
        })
        .buttons()
        .container()
        .appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");

    $(".dataTables_length select").addClass("form-select form-select-sm");
});
