<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">All Assign/Borrow Requests</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="refresh" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="container inventories-container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">List
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search Request No. | Ticket No. | Details | Location" v-model="keywords">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-checkable table-bordered" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Request No.</th>
                                            <th class="text-center">Request Details</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;">
                                                <small>{{item.request_number}}</small>
                                            </td>
                                            <td align="center">
                                                <tr>
                                                    <td align="left"><small><strong>Requested Date</strong> </small></td>
                                                    <td align="left"><small>{{item.created_at}}</small></td>
                                                    <td align="left"><small><strong>Requested By</strong> </small></td>
                                                    <td align="left"><small>{{item.user.name}}</small></td>
                                                </tr>
                                                <tr>
                                                    <td align="left"><small><strong>Ticket No.</strong> </small></td>
                                                    <td align="left"><small>{{item.ticket_number}}</small></td>
                                                    <td align="left"><small><strong>Details</strong> </small></td>
                                                    <td align="left"><small>{{item.details}}</small></td>
                                                </tr>
                                                <tr>
                                                    <td align="left"><small><strong>Location</strong> </small></td>
                                                    <td align="left"><small>{{item.location}}</small></td>
                                                    <td align="left"><small><strong>Status</strong> </small></td>
                                                    <td align="left"><span :class="getColorStatus(item.status)">{{item.status}}</span></td>
                                                </tr>
                                                <tr v-if="item.acknowledge_by_it_support_info">
                                                    <td align="left"><small><strong>Acknowledge By</strong></small></td>
                                                    <td align="left"><small>{{item.acknowledge_by_it_support_info.name}}</small></td>
                                                    <td align="left"><small><strong>Remarks</strong></small></td>
                                                    <td align="left">
                                                        <span :class="getColorStatus(item.acknowledge_by_it_support_status)">{{item.acknowledge_by_it_support_status}}</span><br>
                                                        <small>{{item.acknowledge_by_it_support_remarks}}</small>
                                                    </td>
                                                </tr>
                                                <tr v-if="item.approved_by_it_head_info">
                                                    <td align="left"><small><strong>Approve By</strong></small></td>
                                                    <td align="left"><small>{{item.approved_by_it_head_info.name}}</small></td>
                                                    <td align="left"><small><strong>Remarks</strong></small></td>
                                                    <td align="left">
                                                        <span :class="getColorStatus(item.approved_by_it_head_status)">{{item.approved_by_it_head_status ? item.approved_by_it_head_status : 'Pending'}}</span><br>
                                                        <small>{{item.approved_by_it_head_remarks}}</small>
                                                    </td>
                                                </tr>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <!-- <button type="button" :class="getColorSetupApprover(item)" @click="setupApprover(item)" title="Setup Approver"><i class="flaticon-user"></i></button> -->
                                                <button v-if="item.status=='For Approval'" type="button" :class="getColorSetupApprover(item)" @click="setupApprover(item)" title="Setup Approver"><i class="flaticon-user"></i></button>
                                                <button v-else disabled type="button" :class="getColorSetupApprover(item)" @click="setupApprover(item)" title="Setup Approver"><i class="flaticon-user"></i></button>

                                                <button v-if="item.status=='For Approval'" type="button" class="btn btn-light-danger btn-icon btn-sm" @click="showDisapprove(item)" title="Disapprove"><i class="flaticon-cancel"></i></button>
                                                <button v-else disabled type="button" class="btn btn-light-danger btn-icon btn-sm" @click="showDisapprove(item)" title="Disapprove"><i class="flaticon-cancel"></i></button>
                                           
                                                <a :href="'/borrow-request-for-approval?id='+item.id" class="btn btn-light-info btn-icon btn-sm" :title="item.status =='Approved' ? 'Approved' : 'For Approval'"><i class="flaticon-list"></i></a>
                                            </td>
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
                                    <span class="mr-2">Total : {{ filteredBorrowRequestData.length }} </span><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="setup-approver-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">Setup Details and Approver ({{request.request_number}})</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Select Items</h5>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-shopping-cart"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="itemName" placeholder="Search Item (ID/Serial Number/Model)" @keyup.enter="searchBorrowItem">
                                    <div class="input-group-append">
                                        <a href="#" @click="searchBorrowItem" class="btn font-weight-bold btn-success btn-icon">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered table-checkable" id="kt_datatable">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Model</th>
                                                <th class="text-center">Serial No.</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in items" :key="i" >
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <button v-if="validateBorrowItem(item)" class="btn btn-light-primary btn-icon btn-sm" @click="addBorrowItem(item)"><i class="flaticon-plus"></i></button>
                                                    <button v-else class="btn btn-light-primary btn-icon btn-sm" disabled><i class="flaticon-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr v-if="items.length == 0">
                                                <td colspan="5" align="center">No Items Found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-lg-12 mt-2" v-if="selectedItems.length > 0">
                                    <h5>Selected Items ({{selectedItems.length}})</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-checkable" id="kt_datatable">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th class="text-center">Type</th>
                                                    <th class="text-center">Model</th>
                                                    <th class="text-center">Serial No.</th>
                                                    <th class="text-center">Assign/Borrow</th>
                                                    <th class="text-center">Validity Until</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, i) in selectedItems" :key="i" >
                                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <div class="form-group mt-4">
                                                            <div class="form-check form-check-custom form-check-solid">
                                                                <input class="form-check-input" type="checkbox" v-model="item.is_assigned" :id="'item_' + item[i]"/>
                                                                <label class="form-check-label" for="flexCheckDefault">
                                                                    Is Assigned?
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <div class="form-group mt-3" v-if="item.is_assigned == false" >
                                                            <small><input type="date" v-model="item.validity_end_date"></small>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <button class="btn btn-light-danger btn-icon btn-sm" @click="removeBorrowItem(item)"><i class="flaticon-cancel"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Select Approver</label> 
                                    <select class="form-control" v-model="request.approved_by_it_head">
                                        <option value="">Choose</option>
                                        <option v-for="(item, i) in systemApproversIT" :key="i"  :value="item.user_id" >{{item.user.name }} ({{item.user.email }})</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.approved_by_it_head">{{ errors.approved_by_it_head[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" v-model="request.notify_approver" id="flexCheckDefault"/>
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Check to notify approver in Webex
                                        </label>
                                    </div>
                                    <span class="text-danger" v-if="errors.notify_approver">{{ errors.notify_approver[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                               <div class="form-group">
                                    <label for="role">Remarks</label> 
                                    <textarea class="form-control" cols="20" rows="5" placeholder="Input Remarks" v-model="request.acknowledge_by_it_support_remarks"></textarea>
                                    <span class="text-danger" v-if="errors.acknowledge_by_it_support_remarks">{{ errors.acknowledge_by_it_support_remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-md" @click="saveSetupApprover" :disabled="saveDisable">Save</button>
                        <button class="btn btn-danger btn-md" data-dismiss="modal" aria-label="Close">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="disapprove-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">Disapprove</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                               <div class="form-group">
                                    <label for="role">Remarks</label> 
                                    <textarea class="form-control" cols="20" rows="5" placeholder="Input Remarks" v-model="request.acknowledge_by_it_support_remarks"></textarea>
                                    <span class="text-danger" v-if="errors.acknowledge_by_it_support_remarks">{{ errors.acknowledge_by_it_support_remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-md" @click="disapproveRequest" :disabled="saveDisable">Disapprove</button>
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
                borrowRequestsData: [],
                request : '',
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,

                systemApproversIT : [],
                saveDisable : false,

                itemName : '',
                items : '',
                selectedItems : [],

            }
        },
        created () {
            this.getBorrowRequestsData();
            this.getSystemApproversIT();
        },
        methods: {
            addBorrowItem(item){
                let v = this;
                v.selectedItems.push({
                    id : item.id,
                    model : item.model,
                    serial_number : item.serial_number,
                    type : item.type,
                    is_assigned : '',
                    validity_end_date : '',
                });
                this.items.splice(item, 1);
            },
            removeBorrowItem(item){
                this.selectedItems.splice(item, 1);
            },
            validateBorrowItem(item){
                var hasMatch = this.selectedItems.filter(function (val) {
                    return !!(val.id === item.id);                               
                });
                if(hasMatch.length > 0){
                    return false;
                }else{
                    return true;
                }
            },
            searchBorrowItem(){
                let v = this;
                v.items=[];
                if(v.itemName){
                    axios.get('/item-search?item_name=' + v.itemName)
                    .then(response => { 
                        if(response.data){
                        v.items = response.data;
                        }else{
                            Swal.fire('No Item Found! Please try again', '', 'warning');
                        }
                    })
                    .catch(error => { 
                        v.errors = error.response.data.error;
                    })
                }else{
                    Swal.fire('Item is required', '', 'error');
                }
            },
            getColorSetupApprover(item){
                if(item.approved_by_it_head_info){
                    return 'btn btn-light-success btn-icon btn-sm';
                }else{
                    return 'btn btn-light-warning btn-icon btn-sm';
                }  
            },
            showDisapprove(item){
                this.request = Object.assign({}, item);
                $('#disapprove-modal').modal('show');
            },
            disapproveRequest(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to disapprove?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.request.id ? v.request.id : "");
                        formData.append('acknowledge_by_it_support_remarks', v.request.acknowledge_by_it_support_remarks ? v.request.acknowledge_by_it_support_remarks : "");
                        axios.post(`/borrow-requests-disapprove-by-it-support`, formData)
                        .then(response =>{
                            if(response.data.status == 'success'){
                                Swal.fire('Request has been changed!', '', 'success');        
                                $('#disapprove-modal').modal('hide');
                                var index = this.borrowRequestsData.findIndex(item => item.id == v.request.id);
                                this.borrowRequestsData.splice(index,1,response.data.borrow_request);
                            }
                        })
                    }
                })
            },
            saveSetupApprover(){
                let v = this;
                v.saveDisable = true;

                Swal.fire({
                title: 'Are you sure you want to save?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.request.id ? v.request.id : "");
                        formData.append('approved_by_it_head', v.request.approved_by_it_head ? v.request.approved_by_it_head : "");
                        formData.append('acknowledge_by_it_support_remarks', v.request.acknowledge_by_it_support_remarks ? v.request.acknowledge_by_it_support_remarks : "");
                        formData.append('notify_approver', v.request.notify_approver ? v.request.notify_approver : "");
                        formData.append('borrow_request_items', v.selectedItems ? JSON.stringify(v.selectedItems) : "");
                        axios.post(`/borrow-requests-update-approver`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Request has been saved!', '', 'success');        
                                $('#setup-approver-modal').modal('hide');
                                var index = this.borrowRequestsData.findIndex(item => item.id == v.request.id);
                                this.borrowRequestsData.splice(index,1,response.data.borrow_request);
                                v.saveDisable = false;
                                v.request = '';
                            }else{
                                Swal.fire('Error: Cannot changed. Please try again.', '', 'error'); 
                                v.saveDisable = false;  
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                            v.saveDisable = false;
                        })
                    }else{
                        v.saveDisable = false;
                    }
                })
            },
            setupApprover(item){
                this.request = Object.assign({}, item);
                this.request.notify_approver = true;
                $('#setup-approver-modal').modal('show');
            },
            getSystemApproversIT(){
                this.systemApproversIT = [];
                 axios.get('/system-approver-it-data')
                .then(response => { 
                    if(response.data){
                        this.systemApproversIT = response.data;
                    }
                   
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                })
            },
            getColorStatus(item){
                if(item == 'For Approval' || item == 'Pending'){
                    return 'label label-warning label-pill label-inline mr-2';
                }else if(item == 'Pre-approved'){
                    return 'label label-info label-pill label-inline mr-2';
                }else if(item == 'Approved'){
                    return 'label label-primary label-pill label-inline mr-2';
                }else if(item == 'Disapproved'){
                    return 'label label-danger label-pill label-inline mr-2';
                }else{
                    return 'label label-default label-pill label-inline mr-2';
                }
            },
            refresh(){
                window.location.href = '/borrow-requests';
            },
            getBorrowRequestsData() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var request_number = urlParams.get('request_number');
                if(request_number){
                    v.keywords = request_number;
                }

                v.borrowRequestsData = [];
                v.itemName = '';
                v.items = '';
                v.selectedItems = [];
                axios.get('/borrow-requests-data')
                .then(response => { 
                    v.borrowRequestsData = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPage(pageNumber) {
                this.currentPage = pageNumber;
            },
            resetStartRowUser() {
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
            filteredBorrowRequestData(){
                let self = this;
                if(self.borrowRequestsData){
                    return Object.values(self.borrowRequestsData).filter(item => {
                        return item.details.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.ticket_number.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.request_number.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.location.toLowerCase().includes(this.keywords.toLowerCase())
                                
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredBorrowRequestData).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredBorrowRequestData.slice(index, index + this.itemsPerPage);
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
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>