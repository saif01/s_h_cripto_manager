
import { watch } from 'vue';
import { mapGetters } from 'vuex';


export default {
    data() {
        return {

            paginate_custom: 5,
            search_custom: "",
            search_field: null,
            sort_direction: "desc",
            sort_field: "id",
            currentPageNumber: null,
            v_total: null,
            paginateGoToInputVal: 1,
            // Our data object that holds the Laravel paginator data
            allData: [],
            totalValue: "",
            dataShowFrom: "",
            dataShowTo: "",
            // Tbl number of data show
            tblItemNumberShow: [5, 10, 15, 25, 50, 100, 500, 1000],


            dataModalDialog: false,
            dataModelTitle: "Store Data",
            editmode: false,
            dataModalLoading: false,


            v_rules: {
                required: (value) => !!value || "Required.",
                counter: (value) => value.length <= 30 || "Max 30 characters",
                min5: (v) => v.length >= 5 || "Min 5 characters",
                min8: (v) => v.length >= 8 || "Min 8 characters",
                max30: (value) => value.length <= 30 || "Max 30 characters",
                email: (value) => {
                    const pattern =
                        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return pattern.test(value) || "Invalid e-mail.";
                },
                bdid: (value) =>
                    /^BD\d{7}$/.test(value) ||
                    "BDID must start with 'BD' followed by 7 digits.",
                nid: (value) => {
                    const pattern = /^(?:\d{10}|\d{13}|\d{17})$/;
                    return (
                        pattern.test(value) ||
                        "Invalid NID format. NID should be 10, 13, or 17 digits."
                    );
                },
                phone: (value) => {
                    const pattern = /^(?:\+8801\d{11}|01\d{9})$/;
                    return (
                        pattern.test(value) ||
                        "Invalid phone number format. Use +8801XXXXXXXX or 01XXXXXXXXX."
                    );
                },
                otp: (value) =>
                    /^\d{6}$/.test(value) || "OTP must be a 6-digit number.",
            },


            // Date selector
            isMenuOpen: null,
            isMenuOpen2: null,
            start_date_raw: null,
            end_date_raw: null,

            start_date: null,
            end_date: null,
            sort_by_type: null,

            // end data
        }
    },

    

    methods: {

        // // Add Data Model
        addDataModel(title = null) {
            this.dataModelTitle = title ? title : "Store Data";
            this.editmode = false;
            this.dataModalDialog = true;
        },

        // Edit Data Modal
        editDataModel(singleData, title = null) {
            this.editmode = true;
            this.dataModelTitle = title ? title : "Update Data";
            this.form.fill(singleData);
            this.dataModalDialog = true;
        },

        // Create Data
        createData() {
            // Loading Animation
            this.dataModalLoading = true;
            // request send and get response
            this.form
                .post(this.currentUrl + "/store" + "")
                .then((response) => {
                    // Loading Animation
                    this.dataModalLoading = false;
                    // Hide model
                    this.dataModalDialog = false;
                    // Refresh Tbl Data with current page
                    this.getResults();
                    Toast.fire({
                        icon: response.data.icon,
                        title: response.data.msg,
                        willOpen: () => {
                            document.body.classList.add("swal-open");
                        },
                        willClose: () => {
                            document.body.classList.remove("swal-open");
                        },
                    });
                })
                .catch((data) => {
                    // Loading Animation
                    this.dataModalLoading = false;
                    Swal.fire({
                        icon: "error",
                        title: "Somthing Going Wrong<br>" + data.message,
                        customClass: "text-danger",
                        willOpen: () => {
                            document.body.classList.add("swal-open");
                        },
                        willClose: () => {
                            document.body.classList.remove("swal-open");
                        },
                    });
                    //Swal.fire("Failed!", data.message, "warning");
                });
        },

        // Update data
        updateData() {
            // Loading Animation
            this.dataModalLoading = true;
            // request send and get response
            this.form
                .post(this.currentUrl + "/update/" + this.form.id)
                .then((response) => {
                    // Loading Animation
                    this.dataModalLoading = false;
                    // Input field make empty
                    this.form.reset();
                    // Hide model dataModalDialog
                    this.dataModalDialog = false;
                    // Refresh Tbl Data with current page
                    this.getResults(this.currentPageNumber);
                    Toast.fire({
                        icon: response.data.icon,
                        title: response.data.msg,
                    });
                })
                .catch((data) => {
                    // Loading Animation
                    this.dataModalLoading = false;
                    Swal.fire({
                        icon: "error",
                        title: "Somthing Going Wrong<br>" + data.message,
                        customClass: "text-danger",
                        willOpen: () => {
                            document.body.classList.add("swal-open");
                        },
                        willClose: () => {
                            document.body.classList.remove("swal-open");
                        },
                    });
                    //Swal.fire("Failed!", data.message, "warning");
                });
        },

        // Delete DataDirict Data without form
        deleteDataDirict(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    //console.log(id);
                    this.$Progress.start();
                    axios.delete(this.currentUrl + '/destroy/' + id).then((response) => {
                        //console.log(response);
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );
                        // Refresh Tbl Data with current page
                        this.getResults(this.currentPageNumber);
                        this.$Progress.finish();

                    }).catch((data) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Somthing Going Wrong<br>' + data.message,
                            customClass: 'text-danger',
                            willOpen: () => {
                                document.body.classList.add("swal-open");
                            },
                            willClose: () => {
                                document.body.classList.remove("swal-open");
                            },
                        });
                        // Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },

        // Delete Data
        deleteDataTemp(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                // confirmButtonColor: '#d33',
                // cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn_delete'
                },
                willOpen: () => {
                    document.body.classList.add("swal-open");
                },
                willClose: () => {
                    document.body.classList.remove("swal-open");
                },
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    //console.log(id);
                    this.$Progress.start();
                    this.form.delete(this.currentUrl + '/destroy_temp/' + id).then((response) => {
                        //console.log(response);
                        Toast.fire({
                            icon: "success",
                            text: "Record has been deleted."
                        });
                        // Refresh Tbl Data with current page
                        this.getResults(this.currentPageNumber);
                        this.$Progress.finish();

                    }).catch((data) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Somthing Going Wrong<br>' + data.message,
                            customClass: 'text-danger',
                            willOpen: () => {
                                document.body.classList.add("swal-open");
                            },
                            willClose: () => {
                                document.body.classList.remove("swal-open");
                            },
                        });
                        //Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },

        // Get table data
        getResults(page = 1) {
            this.dataLoading = true;
            return axios
                .get(this.currentUrl + "/index", {
                    params: {
                        page: page,
                        paginate: this.paginate_custom,
                        search: this.search_custom,
                        sort_direction: this.sort_direction,
                        sort_field: this.sort_field,
                        sort_direction_custom: this.sort_direction_custom,
                        sort_field_custom: this.sort_field_custom,
                        start_date: this.start_date,
                        end_date: this.end_date,
                        sort_by_day: this.sort_by_day,
                        sort_by_type: this.sort_by_type,
                    },
                })
                .then((response) => {
                    //console.log(response.data.data);
                    //console.log(response.data.from, response.data.to);
                    this.allData = response.data;

                    if (response.data.total != 0) {
                        this.totalValue = response.data.total.toLocaleString();
                    } else {
                        this.totalValue = null;
                    }

                    this.dataShowFrom = response.data.from;
                    this.dataShowTo = response.data.to;
                    this.currentPageNumber = response.data.current_page;
                    this.v_total = response.data.last_page;
                    // Loading Animation
                    this.dataLoading = false;
                });
        },

        // Change Status
        statusChange(data) {
            // console.log('status', data.status)
            if (data.status == 1) {
                var text = "Are you want to inactive ?"
                var btnText = "Inactive"
                var customSwClass = "btn_inactive"

            } else {
                var text = "Are you want to active ?"
                var btnText = "Active"
                var customSwClass = "btn_active"
            }

            Swal.fire({
                title: 'Are you sure?',
                text: text,
                showCancelButton: true,
                showCloseButton: true,
                // confirmButtonColor: '#d33',
                // cancelButtonColor: '#3085d6',
                confirmButtonText: btnText,
                customClass: {
                    confirmButton: customSwClass
                },
                willOpen: () => {
                    document.body.classList.add("swal-open");
                },
                willClose: () => {
                    document.body.classList.remove("swal-open");
                },
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    //console.log(id);
                    this.$Progress.start();
                    this.form.post(this.currentUrl + '/status/' + data.id).then((response) => {
                        //console.log(response);
                        Toast.fire({
                            icon: "success",
                            text: "Status has been Changed."
                        });
                        // Refresh Tbl Data with current page
                        this.getResults(this.currentPageNumber);
                        this.$Progress.finish();

                    }).catch((data) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Somthing Going Wrong<br>' + data.message,
                            customClass: 'text-danger',
                            willOpen: () => {
                                document.body.classList.add("swal-open");
                            },
                            willClose: () => {
                                document.body.classList.remove("swal-open");
                            },
                        });
                        // Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },


        // DataTable Shorting by field name
        change_sort(field) {
            // this.$Progress.start();
            if (this.sort_field == field) {
                this.sort_direction = this.sort_direction == "asc" ? "desc" : "asc";
            } else {
                this.sort_field = field;
            }
            this.getResults();
            // this.$Progress.finish();
        },


        // frmtInsideDate
        frmtInsideDate(date) {
            if (!date) return ''
            // Convert the input string to a Date object
            const inputDate = new Date(date)

            // Extract day, month, and year components
            const dday = inputDate.getDate()
            const mmonth = inputDate.getMonth() + 1 // Note: Months are zero-indexed, so we add 1
            const YYYY = inputDate.getFullYear()

            const DD = dday.toString().padStart(2, '0')
            const MM = mmonth.toString().padStart(2, '0')

            // DD/MM/YYYY
            // const formattedDate = `${DD}/${MM}/${YYYY}`
            const formattedDate = `${YYYY}-${MM}-${DD}`
            // YYYY-MM-DD
            // this.dbFormatDate = `${YYYY}-${MM}-${DD}`

            //console.log(formattedDate) // Output: "28/12/2023"
            return formattedDate
        },


        // end methods
    },


    watch: {

        //Excuted When make change value 
        paginate_custom: function (value) {
            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

        search_custom: function (value) {
            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

        start_date: function (value) {
            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

        end_date: function (value) {
            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

        sort_by_type: function (value) {
            this.$Progress.start();
            this.getResults();
            this.$Progress.finish();
        },

    },




    computed: {

        // map this.count to store.state.count getLoading 
        ...mapGetters({
            'auth': 'getAuth',
        }),

    },

}