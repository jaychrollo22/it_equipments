<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">Receive</h2>
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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Transaction Logs</a>
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
            <div class="container inventories-container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">List
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-primary mr-2" @click="receiveItems">Receive Items</button>
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

                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Transfer Code</th>
                                        <th class="text-center">Requested By</th>
                                        <th class="text-center">Date of Transfer</th>
                                        <th class="text-center">Date Requested</th>
                                        <th class="text-center">Location</th>
                                        <th class="text-center">Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(transfer, i) in filteredReceiveQueues" :key="i" >
                                       <td class="text-center"><small>{{transfer.transfer_code}}</small></td>
                                       <td class="text-center"><small>{{transfer.requested_by.name}}</small></td>
                                       <td class="text-center"><small>{{transfer.date_of_transfer}}</small></td>
                                       <td class="text-center"><small>{{transfer.date_requested}}</small></td>
                                       <td class="text-center"><small>{{transfer.transfer_location}}</small></td>
                                       <td class="text-center"><a href="#" @click="viewReceiveItems(transfer)">{{transfer.inventory_transfer_items.length}}</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                         <div class="row col-md-12" v-if="filteredReceiveQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Inventories : {{ filteredReceives.length }} </span><br>
                            </div>
                        </div>

                    </div>
                                
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="receive-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Receive Items</h2>
                </div>
                <div class="modal-body">
                    <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                        <!--begin::Wizard Nav-->
                        <div class="wizard-nav border-bottom">
                            <div class="wizard-steps p-8 p-lg-10">
                                <!--begin::Wizard Step 1 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="receive_step_1">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-search"></i>
                                        <h3 class="wizard-title">1. Search Transfer Code</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 1 Nav-->
                                <!--begin::Wizard Step 2 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="receive_step_2">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-list"></i>
                                        <h3 class="wizard-title">2. Select Items to Receive</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow last">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 2 Nav-->
                            </div>
                        </div>
                    </div>

                    <!-- Step 1 -->
                    <div v-if="receive_step_1 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-address-card"></i>
                                    </span>
                                </div>
                                <input id="return_id_number" type="text" class="form-control" v-model="transferCode" placeholder="Transfer Code" @keyup.enter="searchTransfer()">
                                <div class="input-group-append">
                                    <a href="#" @click="searchTransfer()" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div v-if="receive_step_2 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            
                        </div>
                        <div class="col-lg-12 mt-2">
                            <h4>Items to Receive</h4>
                            <div class="table-responsive" v-if="transferData.inventory_transfer_items">
                                <table class="table table-bordered table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                            <th class="text-center">From</th>
                                            <th class="text-center">To</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in transferData.inventory_transfer_items" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.type}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.model}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.serial_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.location_from}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.location_to}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button v-if="item.status != 'Received'" class="btn btn-md btn-primary" @click="receiveTransfer(item)">Receive</button>
                                                <button v-else class="btn btn-md btn-primary" disabled>Received</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <button type="button" class="btn btn-warning btn-md float-left" @click="previousStep1Receive">Back</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data() {
            return {
                keywords : '',
                receiveData : '',
                transferCode : '',
                transferData : '',

                //Receive
                receive_step_1 : 'current',
                receive_step_2 : '',

                errors : [],

                currentPage: 0,
                itemsPerPage: 10,
            }
        },
        created () {
            this.getInventoryTransferData();
        },
        methods: {
            viewReceiveItems(item){
                let v = this;
                v.receive_step_1 = 'done';
                v.receive_step_2 = 'current';
                v.transferData = item;
                v.transferCode = item.transfer_code;
                $('#receive-modal').modal('show');
            },
            getInventoryTransferData(){
                let v = this;
                v.receiveData = [];
                axios.get('/inventory-receive-data')
                .then(response => { 
                    v.receiveData = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            receiveTransfer(item){
                let v = this;
                var index = v.transferData.inventory_transfer_items.findIndex(selected_item => selected_item.id == item.id);
                let formData = new FormData();
                formData.append('inventory_transfer_id', item ? item.id : "");
                axios.post(`/save-receive-item`, formData)
                .then(response =>{
                    if(response.data.status == "success"){
                        v.transferData.inventory_transfer_items.splice(index,1,response.data.item);
                    }else{
                        Swal.fire('Error: Cannot returned. Please try again.', '', 'error');
                    }
                })
                .catch(error => {
                    v.errors = error.response.data.errors;
                })
            },
            searchTransfer(){
                let v = this;
                v.transferData = '';
                if(v.transferCode){
                    axios.get('/search-transfer-code?transfer_code=' + v.transferCode)
                    .then(response => { 
                        v.transferData = response.data;
                        if(v.transferData){
                            v.receive_step_1 = 'done';
                            v.receive_step_2 = 'current';
                        }else{
                            Swal.fire('No Transfer Details Found! Please try again', '', 'warning');
                        }
                    })
                    .catch(error => { 
                        v.errors = error.response.data.error;
                    })
                }else{
                    Swal.fire('Transfer code is required', '', 'error');
                }  
            },
            receiveItems() {
                $('#receive-modal').modal('show');
            },
            previousStep1Receive(){
                let v = this;
                v.receive_step_1 = 'current';
                v.receive_step_2 = '';
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
            },
        },
        computed: {
        filteredReceives(){
            let self = this;
            if(self.receiveData){
                return Object.values(self.receiveData).filter(item => {
                    return item.transfer_code.toLowerCase().includes(this.keywords.toLowerCase()) 
                });
            }else{
                return [];
            }
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredReceives).length / this.itemsPerPage)
        },
        filteredReceiveQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredReceives.slice(index, index + this.itemsPerPage);
            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }
            if(this.currentPage == -1) {
                this.currentPage = 0;
            }
            return queues_array;
        },
    },
    }
</script>

<style lang="scss" scoped>
    @media (min-width: 1400px){
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>