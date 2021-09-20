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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Diposed Logs</a>
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
                            <h3 class="card-label">Diposed Logs
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <download-excel
                                :data   = "filteredDisposedLogs"
                                :fields = "exportDisposedLogs"
                                class   = "btn btn-success mr-2"
                                name    = "Disposed Logs.xls">
                                    Download Excel ({{ filteredDisposedLogs.length }})
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
                                <button class="btn btn-md btn-primary" @click="getDisposedLogs">Apply Filter</button>
                            </div>
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Disposal Date</th>
                                        <th class="text-center">Serial Number</th>
                                        <th class="text-center">Model</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredDisposedLogs" :key="i" >
                                       <td align="center"><small>{{item.disposal_date}}</small></td>
                                       <td align="center"><small>{{item.serial_number}}</small></td>
                                       <td align="center"><small>{{item.model}}</small></td>
                                       <td align="center"><small>{{item.type}}</small></td>
                                       <td align="center">
                                        <span class="label label-danger label-pill label-inline mr-2" :title="item.status">{{item.status}}</span>   
                                        </td>
                                       
                                       <td align="center"><small>{{item.disposed_by_info.name}}</small></td>
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
                                <span class="mr-2">Total Disposed Logs : {{ filteredDisposedLogs.length }} </span><br>
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
                disposedLogs: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                exportDisposedLogs : {
                    'Disposal Date' : 'disposal_date',
                    'Serial Number' : 'serial_number',
                    'Model' : 'model',
                    'Type' : 'type',
                    'Status' : 'status',
                    'Action By' : {
                        callback: (value) => {
                            if(value.disposed_by_info){
                                return value.disposed_by_info.name;
                            }else{
                                return '';
                            }
                        }
                    },
                },
            }
        },
        created () {
            this.getDisposedLogs();
        },
        methods: {
            getDisposedLogs() {
                let v = this;
                v.disposedLogs = [];
                axios.get('/reports-disposed-logs-data?date_from='+ v.date_from + '&date_to='+ v.date_to)
                .then(response => { 
                    v.disposedLogs = response.data;
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
            filteredDisposedLogs(){
                let self = this;
                return Object.values(self.disposedLogs).filter(item => {
                 
                    return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) || item.model.toLowerCase().includes(this.keywords.toLowerCase()) || item.type.toLowerCase().includes(this.keywords.toLowerCase())
                    
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredDisposedLogs).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredDisposedLogs.slice(index, index + this.itemsPerPage);

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