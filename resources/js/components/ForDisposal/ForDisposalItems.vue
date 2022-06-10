<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">For Disposal Items</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">For Diposal Items</a>
                        </div>
                    </div>
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
                        <div class="card-toolbar" v-if="for_disposal">
                            <button v-if="for_disposal.status == null || for_disposal.status == '' || for_disposal.status == 'Disapproved'" class="btn btn-primary mr-2" @click="placeRequestForDisposal" :disabled="disablePlaceRequest">Place Request</button>
                            <button v-if="for_disposal.status == null || for_disposal.status == '' || for_disposal.status == 'For Approval' || for_disposal.status == 'Disapproved' && isCurrentUserRequestorVisible" class="btn btn-danger" @click="deleteRequestForDisposal">Delete Request</button>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" v-if="for_disposal">
                                <tr>
                                    <td><small>Requested Date</small> </td>
                                    <td>
                                        <small>{{for_disposal.requested_date}}</small> 
                                    </td>
                                </tr>
                                <tr>
                                    <td><small>Requested By</small> </td>
                                    <td>
                                        <small>{{for_disposal.requested_by_info.name}}</small>
                                        
                                    </td>
                                </tr>
                                <tr v-if="for_disposal.status">
                                    <td><small>Status</small> </td>
                                    <td>
                                        <small>{{for_disposal.status}}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <small>Upload RDF</small>
                                    </td>
                                    <td>
                                        <span v-if="for_disposal.attachment">
                                            <small>
                                                <a :href="'storage/for_disposals/rdf_file/'+for_disposal.attachment" target="_blank">View File</a> 
                                                <a v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible"  href="#" @click="deleteRDFAttachment" class="text-danger">| Delete File</a>
                                            </small>
                                        </span>
                                        <span v-else>
                                            <input  v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible" id="rdf_file" type="file" accept="pdf/*" class="form-control" v-on:change="rdfFileUpload()">
                                            <small v-else>No RDF uploaded</small>
                                        </span>
                                        
                                    </td>
                                </tr>
                            </table>
                          
                            <!-- <div class="form-group">
                                <label>Search</label>
                                <input type="text" class="form-control" placeholder="Input here..." v-model="keywords">
                            </div>
                               -->
                            <h5>Items Lists</h5>
                            <table class="table table-bordered" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th>Inventory Details</th>
                                        <th class="text-center">File</th>
                                        <th class="text-center" v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible">Action</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredForDisposalItemsQueues" :key="i" >
                                       <td>
                                        <div v-if="item.inventory">
                                            <small>TYPE : {{item.inventory.type}}</small><br>
                                            <small>SERIAL NUMBER : {{item.inventory.serial_number}}</small> <br>
                                            <small>MODEL : {{item.inventory.model}}</small>
                                        </div>
                                       </td>
                                       <td width="200" align="center">
                                            <span class="text-center" v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible">
                                                <input v-if="item.attachment == '' || item.attachment == '' || item.attachment == null" type="file" :id="'picture_item_' + item.id" class="form-control" v-on:change="uploadPictureAttachment(item)">
                                                <small v-else>
                                                    <a :href="'storage/for_disposals/picture_file/'+item.attachment" target="_blank">View File</a>  
                                                    <a v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible" href="#" @click="deletePictureAttachment(item)" class="text-danger">| Delete File</a>
                                                </small>
                                            </span>
                                            <span v-else>
                                                <small v-if="item.attachment">
                                                    <a :href="'storage/for_disposals/picture_file/'+item.attachment" target="_blank">View File</a>
                                                    <a v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible" href="#" @click="deletePictureAttachment(item)" class="text-danger">| Delete File</a>
                                                </small>
                                            </span>
                                        </td>
                                        <td align="center" v-if="for_disposal.status ==null || for_disposal.status == '' || for_disposal.status == 'For Approval' && isCurrentUserRequestorVisible">
                                            <button class="btn btn-sm btn-danger" @click="deleteForDisposalItem(item)">Remove</button>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
                                <tr>
                                    <td><small>IT Head Approver</small> </td>
                                    <td v-if="for_disposal.status ==null || for_disposal.status == ''">
                                       <select class="form-control" v-model="for_disposal.approved_by_it_head">
                                           <option value="">Choose IT Approver</option>
                                           <option v-for="(item, i) in systemApproversIT" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                       <span class="text-danger" v-if="errors.approved_by_it_head">{{ errors.approved_by_it_head[0] }}</span>
                                    </td>
                                    <td v-else>
                                        <small>{{for_disposal.approved_by_it_head_info.name}}</small>
                                    </td>
                                    <!-- <td align="center" v-if="for_disposal.approved_by_it_head_status">
                                        <span v-if="for_disposal.approved_by_it_head_status =='Pending' && isCurrentUserRequestorVisible" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" @click="showApproval('IT')"> {{for_disposal.approved_by_it_head_status}} </span>
                                        <span v-else class="label label-default label-pill label-inline mr-2"> {{for_disposal.approved_by_it_head_status}} </span>
                                        <br>
                                        <small v-if="for_disposal.approved_by_it_head_remarks">
                                             Remarks: {{for_disposal.approved_by_it_head_remarks}}
                                        </small>
                                    </td> -->
                                </tr>
                                <tr>
                                    <td><small>Finance Head Approver</small> </td>
                                    <td v-if="for_disposal.status ==null || for_disposal.status == ''">
                                        <select class="form-control" v-model="for_disposal.approved_by_finance">
                                           <option value="">Choose Finance Approver</option>
                                           <option v-for="(item, i) in systemApproversFinance" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                       <span class="text-danger" v-if="errors.approved_by_finance">{{ errors.approved_by_finance[0] }}</span>
                                    </td>
                                    <td v-else>
                                        <small>{{for_disposal.approved_by_finance_info.name}}</small>
                                    </td>
                                    <!-- <td align="center" v-if="for_disposal.approved_by_finance_status">
                                        <span v-if="for_disposal.approved_by_finance_status =='Pending' && isCurrentUserRequestorVisible" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" @click="showApproval('Finance')"> {{for_disposal.approved_by_finance_status}} </span>
                                        <span v-else class="label label-default label-pill label-inline mr-2"> {{for_disposal.approved_by_finance_status}} </span>
                                        <br>
                                        <small v-if="for_disposal.approved_by_finance_remarks">
                                            Remarks: {{for_disposal.approved_by_finance_remarks}}
                                        </small>
                                    </td> -->
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
                    <button class="btn btn-primary mr-3" @click="approveForDisposal">Approve</button>
                    <button class="btn btn-danger" @click="disapproveForDisposal">Disapprove</button>
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
                keywords: '',
                for_disposal_id : '',
                errors : [],
                for_disposal : '',
                currentPage: 0,
                itemsPerPage: 10,

                systemApproversIT : [],
                systemApproversFinance : [],

                disablePlaceRequest : false,

                currentUser : '',

                isCurrentUserRequestorVisible : false,

                approval_type : '',
                approval_form : {
                    'approved_by_it_head_remarks' : '',
                    'approved_by_finance_remarks' : '',
                },
            }
        },
        created () {
            this.getCurrentUser();
            this.getForDisposalItems();
            this.getSystemApproversIT();
            this.getSystemApproversFinance();
        },
        methods: {
            deletePictureAttachment(item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this attachment?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', item.id);
                        axios.post(`/delete-picture-for-disposal-item`, formData)
                        .then(response =>{
                           if(response.data.status=='removed'){
                                Swal.fire('Attachment has bee removed', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        this.getForDisposalItems();
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                        })
                    }
                });
            },
            uploadPictureAttachment(item){
                let v = this;
                var picture_upload = document.getElementById("picture_item_" + item.id);
                var attachment = picture_upload.files[0];
                if(attachment){
                    let formData = new FormData();
                    formData.append('id', item.id);
                    formData.append('attachment', attachment ? attachment : "");
                    axios.post(`/save-picture-for-disposal-item`, formData)
                            .then(response =>{
                                if(response.data.status=='saved'){
                                    Swal.fire('Attachment has bee saved', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            this.getForDisposalItems();
                                        }
                                    });
                                }
                            })
                }
            },
            deleteRDFAttachment(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this RDF?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.for_disposal.id);
                        axios.post(`/delete-rdf-for-disposal`, formData)
                        .then(response =>{
                           if(response.data.status=='removed'){
                                Swal.fire('RFD has bee removed', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        this.getForDisposalItems();
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                        })
                    }
                });
            },
            rdfFileUpload(){
                let v = this;
                var rdf_file = document.getElementById("rdf_file");
                var attachment = rdf_file.files[0];
                if(attachment){
                    let formData = new FormData();
                    formData.append('id', v.for_disposal.id);
                    formData.append('attachment', attachment ? attachment : "");
                    axios.post(`/save-rfd-for-disposal`, formData)
                            .then(response =>{
                                if(response.data.status=='saved'){
                                    Swal.fire('RFD has bee saved', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            this.getForDisposalItems();
                                        }
                                    });
                                }
                            })
                }
            },
            approveForDisposal(){
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
                        formData.append('id', v.for_disposal.id);
                        formData.append('approval_type', v.approval_type);
                        if(v.approval_type == 'IT'){
                            formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        }
                        if(v.approval_type == 'Finance'){
                            formData.append('approved_by_finance_remarks', v.approval_form.approved_by_finance_remarks);
                        }
                        axios.post(`/approve-request-for-disposal-item`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                               
                                Swal.fire('For disposal items has been approved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getForDisposalItems();
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
            disapproveForDisposal(){
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
                        formData.append('id', v.for_disposal.id);
                        formData.append('approval_type', v.approval_type);
                        if(v.approval_type == 'IT'){
                            formData.append('approved_by_it_head_remarks', v.approval_form.approved_by_it_head_remarks);
                        }
                        if(v.approval_type == 'Finance'){
                            formData.append('approved_by_finance_remarks', v.approval_form.approved_by_finance_remarks);
                        }
                        axios.post(`/disapprove-request-for-disposal-item`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                               
                                Swal.fire('For disposal items has been disapproved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getForDisposalItems();
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
            deleteForDisposalItem(item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this item?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', item.id);
                        axios.post(`/delete-request-for-disposal-item`, formData)
                        .then(response =>{
                            if(response.data.status=='removed'){
                                Swal.fire('Item has been deleted. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            this.getForDisposalItems();
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
            deleteRequestForDisposal(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this request for disposal items?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.for_disposal.id);
                        axios.post(`/delete-request-for-disposal`, formData)
                        .then(response =>{
                            if(response.data.status=='removed'){
                                v.disablePlaceRequest = false;
                                Swal.fire('Items for disposal has been deleted. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            window.location.href = "/for-disposal";
                                        }
                                     });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                v.disablePlaceRequest = false;
                            }
                        })
                        .catch(error => {
                            v.disablePlaceRequest = false;
                            v.errors = error.response.data.errors;
                        })
                    }
                });
            },
            placeRequestForDisposal(){
                let v = this;
                v.disablePlaceRequest = true;
                Swal.fire({
                title: 'Are you sure you want to place this request for disposal items?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.for_disposal.id);
                        formData.append('approved_by_it_head', v.for_disposal.approved_by_it_head ? v.for_disposal.approved_by_it_head : "");
                        formData.append('approved_by_finance', v.for_disposal.approved_by_finance ? v.for_disposal.approved_by_finance : "");
                        axios.post(`/place-request-for-disposal`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                                v.disablePlaceRequest = false;
                                Swal.fire('Items for disposal has been placed for approval. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            window.location.href = "/for-disposal";
                                        }
                                     });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                v.disablePlaceRequest = false;
                            }
                        })
                        .catch(error => {
                            v.disablePlaceRequest = false;
                            v.errors = error.response.data.errors;
                            Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                        })

                    }else
                    {
                        v.disablePlaceRequest = false;
                    }
                });
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
            getSystemApproversFinance(){
                this.systemApproversFinance = [];
                 axios.get('/system-approver-finance-data')
                .then(response => { 
                    if(response.data){
                        this.systemApproversFinance = response.data;
                    }
                   
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                })
            },
            getForDisposalItems() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                v.for_disposal_id = urlParams.get('id');
                v.for_disposal = [];
                axios.get('/for-disposal-items-data?id='+v.for_disposal_id)
                .then(response => { 
                    if(response.data){
                        v.for_disposal = response.data;

                        if(v.for_disposal.requested_by == v.currentUser.user_id){
                            v.isCurrentUserRequestorVisible = true;
                        }else{
                            v.isCurrentUserRequestorVisible = false;
                        }
                    }
                   
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
            filterForDisposalItems(){
                let self = this;
                if(self.for_disposal){
                    if(self.for_disposal.items.length > 0){
                        self.resetStartRowUser();
                        return Object.values(self.for_disposal.items).filter(item => {
                            if(item.inventory){
                                return item.inventory.type.toLowerCase().includes(this.keywords.toLowerCase()) || item.inventory.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                            }
                        });
                    }else{
                        return [];
                    }
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filterForDisposalItems).length / Number(this.itemsPerPage))
            },
            filteredForDisposalItemsQueues() {
                var index = this.currentPage * Number(this.itemsPerPage);
                var queues_array = this.filterForDisposalItems.slice(index, index + Number(this.itemsPerPage));
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