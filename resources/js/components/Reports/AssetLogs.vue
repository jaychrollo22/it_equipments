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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Employee Asset Logs | Active Assigned and Borrowed Items</a>
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
                                :data   = "filteredAssetLogs"
                                :fields = "exportAssetLogs"
                                class   = "btn btn-success mr-2"
                                name    = "Asset Logs.xls">
                                    Download Excel ({{ filteredAssetLogs.length }})
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
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-left">Employee Name</th>
                                        <th class="text-left">Ticket No.</th>
                                        <th class="text-left">Serial No.</th>
                                        <th class="text-left">Model</th>
                                        <th class="text-left">Type</th>
                                        <th class="text-left">Date</th>    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredQueues" :key="i" >
                                        <td align="left"><small>{{item.employee_info.first_name + ' ' + item.employee_info.last_name}}</small></td>
                                        <td align="left"><small>{{item.ticket_number}}</small></td>
                                        <td align="left"><small>{{item.inventory_info.serial_number}}</small></td>
                                        <td align="left"><small>{{item.inventory_info.model}}</small></td>
                                        <td align="left"><small>{{item.inventory_info.type}}</small></td>
                                        <td align="left"><small>{{item.borrow_date}}</small></td>
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
                                <span class="mr-2">Total Asset Logs : {{ filteredAssetLogs.length }} </span><br>
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
                assetLogs: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                exportAssetLogs : {
                    'Employee Name' : {
                        callback: (value) => {
                            if(value.employee_info){
                                return value.employee_info.first_name + ' ' + value.employee_info.last_name;
                            }else{
                                return '';
                            }
                        }
                    },
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
                    'Date' : 'borrow_date',
                },
            }
        },
        created () {
            this.getAssetLogs();
        },
        methods: {
            getAssetLogs() {
                let v = this;
                v.assetLogs = [];
                axios.get('/reports-asset-logs-data')
                .then(response => { 
                    v.assetLogs = response.data;
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
            filteredAssetLogs(){
                let self = this;
                return Object.values(self.assetLogs).filter(item => {
                    if(item.employee_info && item.inventory_info){
                        let full_name = item.employee_info.first_name + ' ' +  item.employee_info.last_name;
                        return item.employee_info.first_name.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.employee_info.last_name.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || full_name.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.inventory_info.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.ticket_number == this.keywords
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredAssetLogs).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredAssetLogs.slice(index, index + this.itemsPerPage);

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