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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Returned Items</a>
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
                                :data   = "filteredReturnLogs"
                                :fields = "exportReturnLogs"
                                class   = "btn btn-success mr-2"
                                name    = "Return Logs.xls">
                                    Download Excel ({{ filteredReturnLogs.length }})
                            </download-excel>
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
                                <button class="btn btn-md btn-primary" @click="getreturnLogs">Apply Filter</button>
                            </div>
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th>Return Date</th>
                                        <th>Employee Name</th>
                                        <th>ID</th>
                                        <th>Series No.</th>
                                        <th>Model</th>
                                        <th>Type</th>
                                        <th>Check By</th>
                                        <th>Check Status</th>
                                        <th>Defect Value Deduction</th>
                                        <th>Remarks</th>
                                        <th>Return Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredReturnLogs" :key="i" >
                                       <td><small>{{item.return_date}}</small></td>
                                       <td><small>{{item.employee_info.first_name + ' ' + item.employee_info.last_name}}</small></td>
                                       <td><small>{{item.inventory_info.id}}</small></td>
                                       <td><small>{{item.inventory_info.serial_number}}</small></td>
                                       <td><small>{{item.inventory_info.model}}</small></td>
                                       <td><small>{{item.inventory_info.type}}</small></td>
                                       <td><small>{{ item.check_by_info ? item.check_by_info.name : ""}}</small></td>
                                       <td><small>{{ item.check_status}}</small></td>
                                       <td><small>{{ item.defect_value_deduction}}</small></td>
                                       <td><small>{{ item.check_remarks}}</small></td>
                                       <td><small>{{ item.return_location}}</small></td>
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
                                <span class="mr-2">Total Return Logs : {{ filteredReturnLogs.length }} </span><br>
                            </div>
                        </div>
                    </div>
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
                returnLogs: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                exportReturnLogs : {
                    'Return Date' : 'return_date',
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
                    'Check By' : {
                        callback: (value) => {
                            if(value.check_by_info){
                                return value.check_by_info.name;
                            }else{
                                return '';
                            }
                        }
                    },
                    'Check Status' : 'check_status',
                    'Defect Value Deduction' : 'defect_value_deduction',
                    'Remarks' : 'check_remarks',
                    'Location' : 'return_location',
                },
            }
        },
        created () {
            this.getreturnLogs();
        },
        methods: {
            getreturnLogs() {
                let v = this;
                v.returnLogs = [];
                axios.get('/reports-return-logs-data?date_from='+ v.date_from + '&date_to='+ v.date_to)
                .then(response => { 
                    v.returnLogs = response.data;
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
            filteredReturnLogs(){
                let self = this;
                return Object.values(self.returnLogs).filter(item => {
                    if(item.employee_info && item.inventory_info){
                        let full_name = item.employee_info.first_name + ' ' +  item.employee_info.last_name;
                        return item.employee_info.first_name.toLowerCase().includes(this.keywords.toLowerCase()) || item.employee_info.last_name.toLowerCase().includes(this.keywords.toLowerCase()) || full_name.toLowerCase().includes(this.keywords.toLowerCase())
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredReturnLogs).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredReturnLogs.slice(index, index + this.itemsPerPage);

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