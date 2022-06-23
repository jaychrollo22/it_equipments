<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">User Borrow Requests</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="refresh" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
                    </div>
                </div>
            </div>
            
            <div class="d-flex flex-column-fluid">
                <div class="container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">List
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-primary mr-2 mt-1" @click="newRequest">New</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search Request No. | Ticket No. | Details" v-model="keywords">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-checkable table-bordered" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th>Request Details</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td>
                                                <small>Request No. : {{item.request_number}}</small><br>
                                                <small>Ticket No. : {{item.ticket_number}}</small><br>
                                                <small>Details : {{item.details}}</small><br>
                                                <small>Location : {{item.location}}</small>
                                            </td>
                                            <td align="center"><span :class="getColorStatus(item.status)">{{item.status}}</span></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button v-if="item.status == 'For Approval'" type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editRequest(item)"><i class="flaticon-edit"></i></button>
                                                <button v-else disabled type="button" class="btn btn-light-primary btn-icon btn-sm"><i class="flaticon-edit"></i></button>

                                                <button v-if="item.status == 'For Approval' || item.status == 'Disapproved'" type="button" class="btn btn-light-danger btn-icon btn-sm" @click="deleteRequest(item)"><i class="flaticon-delete"></i></button>
                                                <button v-else disabled type="button" class="btn btn-light-danger btn-icon btn-sm" @click="deleteRequest(item)"><i class="flaticon-delete"></i></button>

                                                <a v-if="item.status == 'Approved'" :href="'/letter-of-undertaking?request_number='+item.request_number" target="_blank" class="btn btn-light-info btn-icon btn-sm" title="Letter of Undertaking"><i class="flaticon-list"></i></a>
                                                <button v-else disabled class="btn btn-light-info btn-icon btn-sm" title="Letter of Undertaking"><i class="flaticon-list"></i></button>
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
        <div class="modal fade" id="borrow-request-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">{{action}} Request</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Ticket No.</label> 
                                    <input type="text" class="form-control" v-model="request.ticket_number" placeholder="Ticket No.">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Details</label> 
                                    <textarea class="form-control" cols="20" rows="5" placeholder="Input Details (Laptop/Mouse/Keyboard)" v-model="request.details"></textarea>
                                    <span class="text-danger" v-if="errors.details">{{ errors.details[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Location</label> 
                                    <select class="form-control" v-model="request.location">
                                        <option value="">Choose</option>
                                        <option v-for="(location,b) in locations" v-bind:key="b" :value="location.name"> {{ location.name }}</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.location">{{ errors.location[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-md" @click="save" :disabled="saveDisable">Save</button>
                        <button class="btn btn-danger btn-md" data-dismiss="modal" aria-label="Close">Close</button>
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
                request: {
                    'id' : '',
                    'ticket_number' : '',
                    'details' : '',
                    'location' : ''
                },
                action : '',
                borrowRequestsData: [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,

                saveDisable : false,
                locations: [],
            }
        },
        created () {
            this.getLocations();    
            this.getBorrowRequestsData();
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
            deleteRequest(item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this request?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', item.id ? item.id : "");
                        axios.post(`/borrowed-request-delete`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Success: Request has been deleted. Thank you.', '', 'success'); 
                                this.getBorrowRequestsData();
                            }else{
                                Swal.fire('Error: Cannot delete. Please try again.', '', 'error'); 
                            }
                        })

                    }
                })
            },
            editRequest(item){
                let v = this;
                v.errors = [];
                v.request.id = item.id;
                v.request.ticket_number = item.ticket_number;
                v.request.details = item.details;
                v.request.location = item.location;
                v.action = 'Update';
                $('#borrow-request-modal').modal('show');
            },
            save(){
                let v = this;
                v.saveDisable = true;
                Swal.fire({
                title: 'Are you sure you want to save this request?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        var postURL = `/borrowed-request-store`;
                        if(v.action == "Update"){
                            formData.append('id', v.request.id ? v.request.id : "");
                            postURL = `/borrowed-request-update`;
                        }
                        formData.append('ticket_number', v.request.ticket_number ? v.request.ticket_number : "");
                        formData.append('details', v.request.details ? v.request.details : "");
                        formData.append('location', v.request.location ? v.request.location : "");
                        axios.post(postURL, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Borrow request has been saved!', '', 'success');        
                                $('#borrow-request-modal').modal('hide');
                                v.request.id = '';
                                v.request.details = '';  
                                v.request.location = '';  
                                v.saveDisable = false;
                                if(v.action == "Update"){
                                    var index = this.borrowRequestsData.findIndex(item => item.id == v.request.id);
                                    this.borrowRequestsData.splice(index,1,response.data.borrow_request);
                                }
                                else{
                                    this.getBorrowRequestsData();
                                }
                                
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
            newRequest(){
                let v = this;
                v.errors = [];
                v.request.id = '';
                v.request.ticket_number = '';
                v.request.details = '';
                v.request.location = '';
                v.action = 'New';
                $('#borrow-request-modal').modal('show');
            },
            getLocations() {
                let v = this;
                v.locations = [];
                axios.get('/setting-locations-data')
                .then(response => { 
                    v.locations = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            refresh(){
                window.location.href = '/home-borrow-requests';
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
                axios.get('/home-borrow-requests-data')
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

</style>