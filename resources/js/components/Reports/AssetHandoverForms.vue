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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Asset Handover Forms</a>
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
                            <h3 class="card-label">Asset Handover Forms
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <!-- <div class="card-toolbar">
                            <download-excel
                                :data   = "filteredAssetHandoverForms"
                                :fields = "exportAssetLogs"
                                class   = "btn btn-success mr-2"
                                name    = "Asset Logs.xls">
                                    Download Excel ({{ filteredAssetHandoverForms.length }})
                            </download-excel>
                        </div> -->
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
                                <button class="btn btn-md btn-primary" @click="getAssetHandoverForms">Apply Filter</button>
                            </div>
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-left">Employee Name</th>
                                        <th class="text-left">Handover Date</th>
                                        <th class="text-left">Details</th>
                                        <th class="text-left">Action</th>   
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredAssetHandoverForms" :key="i" >
                                        <td align="left"><small>{{item.employee_info.first_name + ' ' + item.employee_info.last_name}}</small></td>
                                        <td align="left"><small>{{item.hof_date}}</small></td>
                                        <td align="left"><a href="#" @click="showDetails(item)">View Details</a></td>
                                        <td align="left"><a :href="'print-asset-handover-form?id='+item.id" target="_blank" class="btn btn-sm btn-primary">Print</a></td>
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
                                <span class="mr-2">Total Asset Handover Forms Logs : {{ filteredAssetHandoverForms.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="asset-handover-form-details-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Details</h2>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="selectedassetHandoverFormsDetails">
                        <table class="table table-checkable" id="kt_datatable">
                            <thead>
                                <tr>
                                    <th class="text-left">Model</th>
                                    <th class="text-left">Serial Number</th>
                                    <th class="text-left">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in selectedassetHandoverFormsDetails" :key="i" >
                                    <td align="left"><small>{{item.model}}</small></td>
                                    <td align="left"><small>{{item.serial_number}}</small></td>
                                    <td align="left"><small>{{item.type}}</small></td>
                                </tr>
                            </tbody>
                        </table>
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
                assetHandoverForms: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                exportAssetHandoverForms : {
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
                selectedassetHandoverForms : '',
                selectedassetHandoverFormsDetails : ''
            }
        },
        created () {
            this.getAssetHandoverForms();
        },
        methods: {
            showDetails(asset_handover_form){
                let v = this;
                v.selectedassetHandoverForms = asset_handover_form;
                v.selectedassetHandoverFormsDetails = JSON.parse(asset_handover_form.details);
                $('#asset-handover-form-details-modal').modal('show');
            },
            getAssetHandoverForms() {
                let v = this;
                v.assetHandoverForms = [];
                axios.get('/reports-asset-handover-forms-data?date_from='+ v.date_from + '&date_to='+ v.date_to)
                .then(response => { 
                    v.assetHandoverForms = response.data;
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
            filteredAssetHandoverForms(){
                let self = this;
                return Object.values(self.assetHandoverForms).filter(item => {
                    if(item.employee_info){
                        let full_name = item.employee_info.first_name + ' ' +  item.employee_info.last_name;
                        return item.employee_info.first_name.toLowerCase().includes(this.keywords.toLowerCase()) || item.employee_info.last_name.toLowerCase().includes(this.keywords.toLowerCase()) || full_name.toLowerCase().includes(this.keywords.toLowerCase())
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredAssetHandoverForms).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredAssetHandoverForms.slice(index, index + this.itemsPerPage);

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

</style>