<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap reports-container">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">Reports</h2>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <!--begin::Item-->
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Borrowed Items</a>
                            <!--end::Item-->
                        </div>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Heading-->
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container reports-container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Logs
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <download-excel
                                :data   = "filteredBorrowLogs"
                                :fields = "exportBorrowLogs"
                                class   = "btn btn-success mr-2"
                                name    = "Borrow Logs.xls">
                                    Download Excel ({{ filteredBorrowLogs.length }})
                            </download-excel>

                            <button class="btn btn-info mr-2" @click="uploadInventory">Upload Excel</button>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" class="form-control" placeholder="Input here..." v-model="keywords">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" v-model="date_from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" v-model="date_to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-md btn-primary" @click="getBorrowLogs">Apply Filter</button>
                            </div>
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Employee Name</th>
                                        <th class="text-center">Ticket No.</th>
                                        <th class="text-center">Series No.</th>
                                        <th class="text-center">Model</th>
                                        <th class="text-center">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredQueues" :key="i" >
                                       <td align="center"><small>{{item.borrow_date}}</small></td>
                                       <td align="center"><small>{{item.employee_info.first_name + ' ' + item.employee_info.last_name}}</small></td>
                                       <td align="center"><small>{{item.ticket_number}}</small></td>
                                       <td align="center"><small>{{item.inventory_info.serial_number}}</small></td>
                                       <td align="center"><small>{{item.inventory_info.model}}</small></td>
                                       <td align="center"><small>{{item.inventory_info.type}}</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Borrow Logs : {{ filteredBorrowLogs.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>  
        </div>  
    </div>

    <div class="modal fade" id="upload-inventories-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Upload Inventories</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Upload Template (Excel File)*</label> 
                                <input type="file" id="upload_inventory_file" class="form-control" ref="file" accept=".xlsx" v-on:change="inventoryFileUpload()"/>
                                <span class="text-danger" v-if="errors.upload_inventory_file">{{ errors.upload_inventory_file[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" :disabled="uploadDisable" @click="saveUploadInventory">Save</button>
                </div>
            </div>
        </div>
    </div>

</div>
</template>

<script>
    import JsonExcel from 'vue-json-excel'
    export default {
        components: {
            'downloadExcel': JsonExcel
        },
        data() {
            return {
                keywords : '',
                date_from : '',
                date_to : '',
                borrowLogs: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                exportBorrowLogs : {
                    'Date' : 'borrow_date',
                    'Employee Name' : {
                        callback: (value) => {
                            if(value.employee_info){
                                return value.employee_info.first_name + ' ' + value.employee_info.last_name;
                            }else{
                                return '';
                            }
                        }
                    },
                    'Ticket No.' : 'ticket_number',
                    'Serial No.' : {
                        callback: (value) => {
                            if(value.inventory_info){
                                return value.inventory_info.serial_number;
                            }else{
                                return '';
                            }
                        }
                    },
                    'Model' : {
                        callback: (value) => {
                            if(value.inventory_info){
                                return value.inventory_info.model;
                            }else{
                                return '';
                            }
                        }
                    },
                    'Type' : {
                        callback: (value) => {
                            if(value.inventory_info){
                                return value.inventory_info.type;
                            }else{
                                return '';
                            }
                        }
                    },
                },

                //Upload User Inventories
                upload_inventory_file : '',
                uploadDisable : false,
            }
        },
        created () {
            this.getBorrowLogs();
        },
        methods: {
            inventoryFileUpload(){
                var file = document.getElementById("upload_inventory_file");
                this.upload_inventory_file = file.files[0];
            },
            uploadInventory(){
                let v = this;
                v.uploadDisable = false;
                v.upload_inventory_file = '';
                document.getElementById("upload_inventory_file").value = '';
                $('#upload-inventories-modal').modal('show');
            },
            saveUploadInventory(){
                let v = this;
                v.uploadDisable = true;
                Swal.fire({
                title: 'Are you sure you want to upload this Inventory?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                            let formData = new FormData();
                            if(v.upload_inventory_file){
                                formData.append('upload_inventory_file', v.upload_inventory_file);
                            }
                            axios.post(`/save-upload-user-inventories`, formData)
                            .then(response =>{
                                if(response.data > 0){
                                    $('#upload-inventories-modal').modal('hide');
                                    v.getInventories();
                                    v.performance_eval_file = '';
                                    document.getElementById("upload_inventory_file").value = '';
                                    v.uploadDisable = false;
                                    Swal.fire(response.data + ' inventories has been saved!', '', 'success');             
                                    
                                }else{
                                    Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                    v.uploadDisable = false;
                                }
                            })
                            .catch(error => {
                                v.uploadDisable = false;
                                this.errors = error.response.data.errors;
                            })
                    }else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info');
                        v.uploadDisable = false;
                    }
                })    

            },
            getBorrowLogs() {
                let v = this;
                v.borrowLogs = [];
                axios.get('/reports-borrow-logs-data?date_from='+ v.date_from + '&date_to='+ v.date_to)
                .then(response => { 
                    v.borrowLogs = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPage(pageNumber) {
                this.currentPage = pageNumber;
            },
            resetStartRow() {
                this.currentPage = 0;
            },
            showPreviousLink() {
                return this.currentPage == 0 ? false : true;
            },
            showNextLink() {
                return this.currentPage == (this.totalPages - 1) ? false : true;
            } 
        },
        computed:{
            filteredBorrowLogs(){
                let self = this;
                return Object.values(self.borrowLogs).filter(item => {
                    if(item.employee_info && item.inventory_info){
                        let full_name = item.employee_info.first_name + ' ' +  item.employee_info.last_name;
                        return item.employee_info.first_name.toLowerCase().includes(this.keywords.toLowerCase()) || item.employee_info.last_name.toLowerCase().includes(this.keywords.toLowerCase()) || full_name.toLowerCase().includes(this.keywords.toLowerCase())
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredBorrowLogs).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredBorrowLogs.slice(index, index + this.itemsPerPage);

                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }

                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }

                return queues_array;
            },
        }
    }
</script>

<style lang="scss" scoped>
    @media (min-width: 1400px){
        .reports-container{
            max-width: 1840px!important;
        }
    }
</style>