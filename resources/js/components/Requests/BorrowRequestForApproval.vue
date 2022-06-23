<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">{{borrow_request.status == 'Approved' ? 'Approved' : 'For Approval' }} : Borrow/Assigned Request</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <!-- <a href="#" @click="getBorrowRequestsData" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a> -->
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">View Request
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <a v-if="borrow_request.approved_by_it_head_status == 'Approved'" :href="'/letter-of-undertaking?request_number='+borrow_request.request_number" target="_blank" class="btn btn-primary mr-2 mt-1" >Letter of Undertaking</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="kt_datatable">  
                                    <tr>
                                        <td align="left"><small><strong>Requested Date</strong> </small></td>
                                        <td align="left"><small>{{borrow_request.created_at}}</small></td>
                                        <td align="left"><small><strong>Requested By</strong> </small></td>
                                        <td align="left"><small>{{borrow_request.user.name}}</small></td>
                                    </tr>
                                    <tr>
                                        <td align="left"><small><strong>Ticket No.</strong> </small></td>
                                        <td align="left"><small>{{borrow_request.ticket_number}}</small></td>
                                        <td align="left"><small><strong>Details</strong> </small></td>
                                        <td align="left"><small>{{borrow_request.details}}</small></td>
                                    </tr>
                                    <tr>
                                        <td align="left"><small><strong>Location</strong> </small></td>
                                        <td align="left"><small>{{borrow_request.location}}</small></td>
                                        <td align="left"><small><strong>Status</strong> </small></td>
                                        <td align="left"><span :class="getColorStatus(borrow_request.status)">{{borrow_request.status}}</span></td>
                                    </tr>
                                    <tr v-if="borrow_request.acknowledge_by_it_support_info">
                                        <td align="left"><small><strong>Acknowledge By</strong></small></td>
                                        <td align="left"><small>{{borrow_request.acknowledge_by_it_support_info.name}}</small></td>
                                        <td align="left"><small><strong>Remarks</strong></small></td>
                                        <td align="left">
                                            <span :class="getColorStatus(borrow_request.acknowledge_by_it_support_status)">{{borrow_request.acknowledge_by_it_support_status}}</span><br>
                                            <small>{{borrow_request.acknowledge_by_it_support_remarks}}</small>
                                        </td>
                                    </tr>
                                    <tr v-if="borrow_request.approved_by_it_head_info">
                                        <td align="left"><small><strong>Approve By</strong></small></td>
                                        <td align="left"><small>{{borrow_request.approved_by_it_head_info.name}}</small></td>
                                        <td align="left"><small><strong>Remarks</strong></small></td>
                                        <td align="left">
                                            <span :class="getColorStatus(borrow_request.approved_by_it_head_status)"  style="cursor:pointer" @click="showApproval(borrow_request.approved_by_it_head_status)">{{borrow_request.approved_by_it_head_status ? borrow_request.approved_by_it_head_status : 'For Approval'}}</span><br>
                                            <small>{{borrow_request.approved_by_it_head_remarks}}</small>
                                        </td>
                                    </tr> 
                                </table>
                                <h5>Items to Borrow</h5>
                                <div class="table-responsive">
                                    <table class="table table-checkable table-bordered" id="kt_datatable">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Model</th>
                                                <th class="text-center">Serial No.</th>
                                                <th class="text-center">Is Assigned?</th>
                                                <th class="text-center">Validity End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in borrow_request.borrow_request_items" :key="i" >
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.id}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.type}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.model}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.serial_number}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.is_assigned == 'true' ? 'Yes' : 'No' }}</small></td>
                                                <td style="text-align: center; vertical-align: middle;"><small>{{item.validity_end_date }}</small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

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
                        <h2 class="col-12 modal-title text-center">For Approval</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Approval Remarks</label> 
                                    <textarea v-model="approval_form.approved_by_it_head_remarks" class="form-control" placeholder="Input here" cols="20" rows="5"></textarea>
                                    <span class="text-danger" v-if="errors.approved_by_it_head_remarks">{{ errors.approved_by_it_head_remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-3" @click="approveRequest">Approve</button>
                        <button class="btn btn-danger" @click="disapproveRequest">Disapprove</button>
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
                currentUser: '',
                borrow_request: '',
                errors : [],
                approval_form : {
                    'approved_by_it_head_remarks' : ''
                }
            }
        },
        created () {
            this.getBorrowRequestForApproval();
            this.getCurrentUser();
        },
        methods: {
            approveRequest(){
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
                        formData.append('id', v.borrow_request.id);
                        formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        axios.post(`/approve-borrow-request`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                               
                                Swal.fire('Borrow request has been approved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getBorrowRequestForApproval();
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
                        formData.append('id', v.borrow_request.id);
                        formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        axios.post(`/disapprove-borrow-request`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                                Swal.fire('Borrow request has been disapproved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getBorrowRequestForApproval();  
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
            showApproval(status){
                if(status){
                    alert(status);
                }else{
                    $('#for-approval-modal').modal('show');   
                }
                 
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
                    return 'label label-warning label-pill label-inline mr-2';
                }
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
            getBorrowRequestForApproval() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var borrow_request_id = urlParams.get('id');
                v.borrow_request = '';
                axios.get('/borrow-request-for-approval-data?id='+borrow_request_id)
                .then(response => { 
                    if(response.data){
                        v.borrow_request = response.data;
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

</style>