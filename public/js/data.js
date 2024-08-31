var controller = new Vue({
    el: "#controller",
    data: {
        datas: [],
        data: {},
        actionUrl,
        apiUrl,
        editStatus: false,
    },
    mounted: function () {
        this.datatable();
        this.setupSelectAll(); // Tambahkan ini untuk menginisialisasi event listener select all
    },
    methods: {
        datatable() {
            const _this = this;
            _this.table = $("#datatable")
                .DataTable({
                    ajax: {
                        url: _this.apiUrl,
                        type: "GET",
                    },
                    columns,
                })
                .on("xhr", function () {
                    _this.datas = _this.table.ajax.json().data;
                });
        },
        setupSelectAll() {
            $("[name=select_all]").on("click", function () {
                $(":checkbox").prop("checked", this.checked);
            });
        },
        addData(event) {
            this.data = {};
            this.editStatus = false;
            $("#modal-default").addClass("block");
        },
        editData(event, row) {
            this.data = this.datas[row];
            this.editStatus = true;
            $("#modal-default").modal();
        },
        deleteData(event, id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $(event.target).parents("tr").remove();
                    axios
                        .post(this.actionUrl + "/" + id, {
                            _method: "DELETE",
                        })
                        .then((response) => {
                            Swal.fire(
                                "Deleted!",
                                "Your file has been deleted.",
                                "success"
                            );
                        });
                }
            });
        },
        deleteSession() {
            axios.post('/your-endpoint-to-delete-session')
                .then(response => {
                    Swal.fire({
                        title: "Session Deleted",
                        text: "Your session has been deleted successfully.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        // Redirect atau tindakan lain setelah session dihapus
                        window.location.href = '/your-redirect-url';
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error",
                        text: "There was an error deleting the session.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        },    
        submitForm(event, id) {
            event.preventDefault();
            const _this = this;
            var actionUrl = !this.editStatus
                ? this.actionUrl
                : this.actionUrl + "/" + id;
            axios
                .post(actionUrl, new FormData($(event.target)[0]))
                .then((response) => {
                    $("#modal-default").modal("hide");
                    $(".modal-backdrop").remove();
                    _this.table.ajax.reload();
                    if (!_this.editStatus) {
                        Swal.fire(
                            "Added!",
                            "Your file has been added.",
                            "success"
                        );
                    } else {
                        Swal.fire(
                            "Edited!",
                            "Your file has been edited.",
                            "success"
                        );
                    }
                });
        },
        cetakBarcode(url) {
            // Mengumpulkan semua checkbox yang dipilih
            const selectedIds = $('input[name="selected_product"]:checked')
                .map(function () {
                    return $(this).val();
                })
                .get();

            // Validasi jumlah data yang dipilih
            if (selectedIds.length < 1) {
                alert("Pilih data yang akan dicetak");
                return;
            } else if (selectedIds.length < 3) {
                alert("Pilih minimal 3 data untuk dicetak");
                return;
            } else {
                // Konversi array ID menjadi query string
                const queryString = selectedIds
                    .map((id) => `ids[]=${id}`)
                    .join("&");
                const fullUrl = `${url}?${queryString}`;

                // Membuka halaman baru untuk mencetak barcode
                window.open(fullUrl, "_blank");
            }
        },
        cetakKartu(url) {
            const selectedIds = $('input[name="selected_member"]:checked')
                .map(function () {
                    return $(this).val();
                })
                .get();

            if (selectedIds.length < 1) {
                alert("Pilih member yang akan dicetak");
                return;
            }

            const queryString = selectedIds
                .map((id) => `ids[]=${id}`)
                .join("&");
            const fullUrl = `${url}?${queryString}`;
            window.open(fullUrl, "_blank");
        },
    },
});
