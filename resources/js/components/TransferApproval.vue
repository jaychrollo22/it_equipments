<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">Transfer Approval</h2>
                            <div class="d-flex align-items-center font-weight-bold my-2">
                                <a href="#" class="opacity-75 hover-opacity-100">
                                    <i class="flaticon2-shelter text-white icon-1x"></i>
                                </a>
                                <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                                <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="container inventories-container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">Details
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <!-- <button class="btn btn-primary mr-2" @click="newTransfer">New</button> -->
                            </div>
                        </div>

                        <div class="card-body" v-if="inventoryTransfer">
                           <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><small>Transfer Code</small> </td>
                                        <td><small>{{inventoryTransfer.transfer_code}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Requested Name</small> </td>
                                        <td><small>{{inventoryTransfer.requested_by_info.name}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Department</small></td>
                                        <td><small>{{inventoryTransfer.transfer_department}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Company</small></td>
                                        <td><small>{{inventoryTransfer.transfer_company}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Date Requested</small></td>
                                        <td><small>{{inventoryTransfer.date_requested}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Local No.</small> </td>
                                        <td><small>{{inventoryTransfer.local_number}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Date of Transfer</small> </td>
                                        <td><small>{{inventoryTransfer.date_of_transfer}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Transfer Location</small> </td>
                                        <td><small>{{inventoryTransfer.transfer_location}}</small></td>
                                    </tr>
                                    <tr>
                                        <td><small>Remarks</small> </td>
                                        <td><small>{{inventoryTransfer.remarks}}</small></td>
                                    </tr>
                                </table>
                                <h5>Items Lists</h5>     
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                            <th class="text-center">Current Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in inventoryTransfer.inventory_transfer_items" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.id}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.type}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.model}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.serial_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.location}}</small></td>
                                         </tr>
                                    </tbody>
                                </table>
                                <h5>System Approver</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><small>IT Head Approver</small></td>
                                        <td><small>{{inventoryTransfer.approved_by_it_head_info.name}}</small></td>
                                        <td v-if="inventoryTransfer.status == 'For Approval'">
                                            <span v-if="inventoryTransfer.approved_by_it_head_status =='Pending' && isCurrentUserITHead" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" @click="showApproval('IT')"> {{inventoryTransfer.approved_by_it_head_status}} </span>
                                            <span v-else :class="getColorStatus(inventoryTransfer.approved_by_it_head_status)"> {{inventoryTransfer.approved_by_it_head_status}} </span>
                                        </td>
                                        <td v-else>
                                            <span :class="getColorStatus(inventoryTransfer.approved_by_it_head_status)"> {{inventoryTransfer.approved_by_it_head_status}} </span>
                                            <br>
                                            <div v-if="inventoryTransfer.approved_by_it_head_status =='Approved' || inventoryTransfer.approved_by_it_head_status =='Disapproved'">
                                                <small>Remarks : {{inventoryTransfer.approved_by_it_head_remarks}}</small><br>
                                                <small>Date : {{inventoryTransfer.approved_by_it_head_date}}</small>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><small>Finance Head Approver</small></td>
                                        <td><small>{{inventoryTransfer.approved_by_finance_info.name}}</small></td>
                                        <td v-if="inventoryTransfer.status == 'Pre-approved'">
                                            <span v-if="inventoryTransfer.approved_by_finance_status =='Pending' && isCurrentUserFinance" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" @click="showApproval('Finance')"> {{inventoryTransfer.approved_by_finance_status}} </span>
                                            <span v-else :class="getColorStatus(inventoryTransfer.approved_by_finance_status)"> {{inventoryTransfer.approved_by_finance_status}} </span>
                                        </td>
                                         <td v-else>
                                            <span :class="getColorStatus(inventoryTransfer.approved_by_finance_status)"> {{inventoryTransfer.approved_by_finance_status}} </span>
                                            <br>
                                            <div v-if="inventoryTransfer.approved_by_finance_status =='Approved' || inventoryTransfer.approved_by_finance_status =='Disapproved'">
                                                <small>Remarks : {{inventoryTransfer.approved_by_finance_remarks}}</small><br>
                                                <small>Date : {{inventoryTransfer.approved_by_finance_date}}</small>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                </table>
                           </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="for-approval-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">For Approval ({{approval_type}})</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="approval_type == 'IT'">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Approval Remarks</label> 
                                    <textarea v-model="approval_form.approved_by_it_head_remarks" class="form-control" placeholder="Input here" cols="20" rows="5"></textarea>
                                    <span class="text-danger" v-if="errors.approved_by_it_head_remarks">{{ errors.approved_by_it_head_remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="approval_type == 'Finance'">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Approval Remarks</label> 
                                    <textarea v-model="approval_form.approved_by_finance_remarks" class="form-control" placeholder="Input here" cols="20" rows="5"></textarea>
                                    <span class="text-danger" v-if="errors.approved_by_finance_remarks">{{ errors.approved_by_finance_remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-3" @click="approveTransfer">Approve</button>
                        <button class="btn btn-danger" @click="disapproveTransfer">Disapprove</button>
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
                inventoryTransfer: '',
                errors: [],
                currentUser : '',
                isCurrentUserITHead : false,
                isCurrentUserFinance : false,

                approval_type : '',
                approval_form : {
                    'approved_by_it_head_remarks' : '',
                    'approved_by_finance_remarks' : '',
                },
            }
        },
        created () {
            this.getCurrentUser();
            this.getTransferApproval();
        },
        methods: {
            getColorStatus(item){
                if(item == 'For Approval' || item == 'Pending'){
                    return 'label label-default label-pill label-inline mr-2';
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
            approveTransfer(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to approve?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.inventoryTransfer.id);
                        formData.append('approval_type', v.approval_type);
                        if(v.approval_type == 'IT'){
                            formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        }
                        if(v.approval_type == 'Finance'){
                            formData.append('approved_by_finance_remarks', v.approval_form.approved_by_finance_remarks);
                        }
                        axios.post(`/approve-request-for-transfer`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                               
                                Swal.fire('For transfer has been approved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getTransferApproval();
                                            this.approval_type = '';
                                            this.approval_form.approved_by_it_head_remarks = '';
                                            this.approval_form.approved_by_finance_remarks = '';
                                            
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                        })

                    }
                })
            },
            disapproveTransfer(){
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
                        formData.append('id', v.inventoryTransfer.id);
                        formData.append('approval_type', v.approval_type);
                        if(v.approval_type == 'IT'){
                            formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        }
                        if(v.approval_type == 'Finance'){
                            formData.append('approved_by_finance_remarks', v.approval_form.approved_by_finance_remarks);
                        }
                        axios.post(`/disapprove-request-for-transfer`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                               
                                Swal.fire('For transfer has been disapproved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getTransferApproval();
                                            this.approval_type = '';
                                            this.approval_form.approved_by_it_head_remarks = '';
                                            this.approval_form.approved_by_finance_remarks = '';
                                            
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                        })

                    }
                })
            },
            showApproval(approval_type){
                this.approval_type = approval_type;
                $('#for-approval-modal').modal('show');
            },
            getCurrentUser(){
                this.currentUser = '';
                axios.get('/current-user')
                .then(response => { 
                    if(response.data){
                        this.currentUser = response.data;
                    }
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                })
            },
            getTransferApproval() {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var transfer_code = urlParams.get('transfer_code');
                let v = this;
                v.inventoryTransfer = '';
                axios.get('/transfer-approval-data?transfer_code='+transfer_code)
                .then(response => { 
                    v.inventoryTransfer = response.data;
                    if(v.inventoryTransfer.approved_by_it_head == v.currentUser.user_id){
                        this.isCurrentUserITHead = true;
                    }
                    if(v.inventoryTransfer.approved_by_finance == v.currentUser.user_id){
                        this.isCurrentUserFinance = true;
                    }
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })

            }
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